<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StandRequest;
use App\Http\Requests\StandStoreRequest;
use App\Http\Requests\StandUpdateRequest;
use App\Models\StandTemplate;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class StandTemplateController extends Controller
{
    public function index(StandRequest $request): JsonResponse
    {
        $dateDayEnd = Carbon::make($request->date_day_end);
        $period = CarbonPeriod::create(
            Carbon::make($request->date_day_start)->format('Y-m-d'),
            $dateDayEnd->format('Y-m-d')
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
            $weekDayFromPeriod = $date->format('d-m');
            $determinedWeekDay = $date->dayOfWeekIso;
            [$day, $month] = explode('-', $weekDayFromPeriod);
            $year = $date->format('Y');
            $carbonFullTime = Carbon::createFromFormat('d-m-Y', "$day-$month-$year");

            $templatesInDeterminedWeekDay = [];
            /** @var StandTemplate $template */
            foreach ($standTemplates as $key => $template) {
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

            unset($template['day_times']);
            unset($template['publishers_records']);

            $templatesInDeterminedWeekDay = collect($templatesInDeterminedWeekDay)->map(function ($record) {
                unset($record['week_schedule']);
                unset($record['stand_records']);

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

    public function weekDays(): JsonResponse
    {
        $now = Carbon::now();
        $currentWeekDay = $now->copy()->dayOfWeek; // 0 (for Sunday) through 6 (for Saturday)
        $weekStartDate = $now->copy()->startOfWeek()->format('d-m-Y');
        $weekEndDate = $now->copy()->endOfWeek()->format('d-m-Y');
        $nextWeekStartDate = $now->copy()->addWeek()->startOfWeek()->format('d-m-Y');
        $nextWeekEndDate = $now->copy()->addWeek()->endOfWeek()->format('d-m-Y');

        return new JsonResponse([
            'currentNumberOfWeekDay' => $currentWeekDay,
            'currentWeekStartDate' => $weekStartDate,
            'currentWeekEndDate' => $weekEndDate,
            'nextWeekStartDate' => $nextWeekStartDate,
            'nextWeekEndDate' => $nextWeekEndDate,
        ]);
    }
}
