<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StandRequest;
use App\Http\Requests\StandStoreRequest;
use App\Http\Requests\StandUpdateRequest;
use App\Models\Stand;
use App\Models\StandTemplate;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;

class StandTemplateController extends Controller
{
    private const DATE_FORMAT = 'd-m-Y';

    public function index(StandRequest $request): JsonResponse
    {
        $dateDayEnd = Carbon::make($request->date_day_end);
        $period = CarbonPeriod::create(
            Carbon::make($request->date_day_start)?->format(self::DATE_FORMAT),
            $dateDayEnd?->format(self::DATE_FORMAT)
        );

        $determinedWeek = now()->diffInWeeks($dateDayEnd) + 1; // because if current week than diff = 0

        $standTemplates = StandTemplate::query()
            ->with([
                'stand:id,name,location',
                'congregation:id,name',
                'standRecords:id,stand_template_id,day,date_time',
                'standRecords.publishers:id,first_name,last_name,phone_number,email',
            ])
            ->whereIn('stand_id', $request->stand_ids)
            ->where('congregation_id', $request->congregation_id)
            ->get();

        if ($standTemplates->isEmpty()) {
            return new JsonResponse(['data' => []]);
        }

        $results = [];
        foreach ($period as $date) {
            $weekDayFromPeriod = $date->format(self::DATE_FORMAT);
            $determinedWeekDay = $date->dayOfWeekIso;
            [$day, $month] = explode('-', $weekDayFromPeriod);
            $year = $date->format('Y');
            $carbonFullTime = Carbon::createFromFormat(self::DATE_FORMAT, "$day-$month-$year");

            $templatesInDeterminedWeekDay = [];
            /** @var StandTemplate $template */
            foreach ($standTemplates as $template) {
                if (!isset($template->week_schedule[$determinedWeek][$determinedWeekDay])) {
                    continue;
                }

                $dayTimes = $template->week_schedule[$determinedWeek][$determinedWeekDay]; // @todo - set attribute or work with arrays to avoid unpredictable behavior

                $neededPublishersByDay = $template
                    ->standRecords
                    ->whereBetween(
                        'date_time',
                        [
                            $carbonFullTime->startOfDay()->format('Y-m-d H:i:s'),
                            $carbonFullTime->endOfDay()->format('Y-m-d H:i:s')
                        ]
                    )
                    ->keyBy('date_time');

                $records = [];
                foreach ($dayTimes as $dayTime) {
                    $hour = explode(':', $dayTime)[0];
                    $minute = explode(':', $dayTime)[1] ?? '00';
                    $time = "$hour:$minute";
                    $fullDate = "$year-$month-$day $time:00";

                    $records[] = [
                        'time' => $time,
                        'publishers_records' => $neededPublishersByDay[$fullDate] ?? []
                    ];
                }

                $template['records'] = $records;
                $template['dateTimes'] = $dayTimes;

                $templatesInDeterminedWeekDay[] = $template->toArray();
            }

            unset($template['day_times'], $template['publishers_records']);

            $templatesInDeterminedWeekDay = collect($templatesInDeterminedWeekDay)->map(function ($record) {
                unset($record['week_schedule'], $record['stand_records']);

                return $record;
            });  // @todo - move results array into custom resource and remove columns there

            if ($templatesInDeterminedWeekDay->isNotEmpty()) {
                $results[$weekDayFromPeriod] = $templatesInDeterminedWeekDay->toArray();
            }
        }

        return new JsonResponse(['data' => $results]);
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
}
