<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StandRequest;
use App\Http\Requests\StandStoreRequest;
use App\Http\Requests\StandUpdateRequest;
use App\Http\Resources\StandTemplateCollection;
use App\Models\StandTemplate;
use App\Services\Stand\StandTemplateService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Symfony\Component\HttpFoundation\Response;

class StandTemplateController extends Controller
{
    private const DATE_FORMAT = 'd-m-Y';

    public function indexV2(StandRequest $request, StandTemplateService $templateService): StandTemplateCollection
    {
        $dateDayEnd = Carbon::make($request->date_day_end);
        $period = CarbonPeriod::create(
            Carbon::make($request->date_day_start)?->format(self::DATE_FORMAT),
            $dateDayEnd?->format(self::DATE_FORMAT)
        );

        $determinedWeek = now()->diffInWeeks($dateDayEnd) + 1; // because if current week than diff = 0

        $standTemplates = $this->getStandTemplatesWithRelations($request);

        if ($standTemplates->isEmpty()) {
            return StandTemplateCollection::make([]);
        }

        $formattedStandTemplates = $templateService->formatTemplatesForResponse($standTemplates, $determinedWeek, $period);

        return StandTemplateCollection::make($formattedStandTemplates);
    }

    public function store(StandStoreRequest $request): JsonResponse
    {
        $storeData['week_schedule'] = $request->week_schedule; // @todo - add Validator rule for each decoded value
        $storeData['congregation_id'] = $request->congregation_id;
        $storeData['stand_id'] = $request->stand_id;

        if ($request->activation_at) {
            $storeData['activation_at'] = $request->activation_at; // @todo - add default properties
        }

        if ($request->publishers_at_stand) {
            $storeData['publishers_at_stand'] = $request->publishers_at_stand; // @todo - add default properties
        }

        /** @var StandTemplate $standTemplate */
        $standTemplate = StandTemplate::query()->create($storeData);

        return new JsonResponse(['data' => $standTemplate], Response::HTTP_CREATED); // @todo - refactor to custom resource
    }

    public function update(int $id, StandUpdateRequest $request): JsonResponse
    {
        $storeData = [];
        if ($request->activation_at) {
            $storeData['activation_at'] = $request->activation_at;
        }
        if ($request->week_schedule) {
            $storeData['week_schedule'] = $request->week_schedule; // @todo - add Validator rule for each decoded value
        }

        if ($request->publishers_at_stand) {
            $storeData['publishers_at_stand'] = $request->publishers_at_stand;
        }

        if ($request->is_reports_enabled) {
            $storeData['is_reports_enabled'] = $request->is_reports_enabled;
        }

        if ($request->is_last_week_default) {
            $storeData['is_last_week_default'] = $request->is_last_week_default;
        }

        $standTemplate = tap(StandTemplate::query()->where('id', $id))
            ->update($storeData)
            ->first();

        return new JsonResponse(['data' => $standTemplate], Response::HTTP_ACCEPTED);
    }

    public function show(int $id): JsonResponse
    {
        return new JsonResponse(['standTemplate' => StandTemplate::query()->findOrFail($id)]);
    }

    public function destroy(int $id): JsonResponse
    {
        StandTemplate::destroy($id);

        return new JsonResponse(status: Response::HTTP_NO_CONTENT);
    }

    public function weeklyRanges(Request $request): JsonResponse
    {
        $congregationId = $request->get('congregation_id'); // maybe add validation
        $standIds = $request->get('stand_ids', []); // maybe add validation
        /** @var Collection<StandTemplate> $standTemplates */
        $standTemplates = StandTemplate::query()
            ->select(['id', 'week_schedule'])
            ->where('congregation_id', $congregationId)
            ->when($standIds, fn($query) => $query->whereIn('stand_id', $standIds))
            ->get();

        $endDate = null;
        $weekRanges = [];
        foreach ($standTemplates as $standTemplate) {
            $weekSchedule = $standTemplate->week_schedule;

            foreach ($weekSchedule as $weekNumber => $week) {
                if ($weekNumber === 1) {
                    $startDate = Carbon::now()->format(self::DATE_FORMAT);
                    $endDate = Carbon::now()->endOfWeek()->format(self::DATE_FORMAT);
                    $weekRanges[] = $this->weekRangesFormat($startDate, $endDate);

                    continue;
                }

                $week = Carbon::now()->addWeeks($weekNumber - 1); // because we're adding to the current week
                $startDate = $week->startOfWeek()->format(self::DATE_FORMAT);
                $endDate = $week->endOfWeek()->format(self::DATE_FORMAT);

                $weekRanges[] = $this->weekRangesFormat($startDate, $endDate);
            }
        }

        return new JsonResponse([
            'weekly_ranges' => array_values(array_unique($weekRanges)),
            'last_day_available_for_registration' => $endDate
        ]);
    }

    private function weekRangesFormat(string $startDate, string $endDate): string
    {
        return sprintf('%s %s', $startDate, $endDate);
    }

    /**
     * @return EloquentCollection<StandTemplate>
     */
    private function getStandTemplatesWithRelations(StandRequest $request): EloquentCollection
    {
        return StandTemplate::query()
            ->with([
                'stand:id,name,location',
                'standRecords:id,stand_template_id,date_time',
                'standRecords.publishers:id,first_name,last_name',
            ])
            ->whereIn('stand_id', $request->stand_ids)
            ->where('congregation_id', $request->congregation_id)
            ->get();
    }
}
