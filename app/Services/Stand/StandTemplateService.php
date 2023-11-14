<?php

namespace App\Services\Stand;

use App\Models\StandTemplate;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

class StandTemplateService
{
    private const DATE_FORMAT = 'd-m-Y';

    /**
     * @param EloquentCollection<StandTemplate> $standTemplates
     */
    public function getFormattedResults(
        EloquentCollection $standTemplates,
        int $determinedWeek,
        CarbonPeriod $period,
    ): array {
        $results = [];
        foreach ($period as $date) {
            $templatesInDeterminedWeekDay = $this->getTemplatesInDeterminedWeekDay($standTemplates, $date, $determinedWeek);

            $templatesInDeterminedWeekDay = collect($templatesInDeterminedWeekDay)->map(function ($record) {
                unset($record['week_schedule'], $record['stand_records']);

                return $record;
            });  // @todo - move results array into custom resource and remove columns there

            if ($templatesInDeterminedWeekDay->isNotEmpty()) {
                $formattedPeriodDate = $date->format(self::DATE_FORMAT);

                $results[$formattedPeriodDate] = $templatesInDeterminedWeekDay->toArray();
            }
        }

        return $results;
    }

    private function getNeededPublishersByDay(StandTemplate $template, Carbon $fullTime): Collection
    {
        return $template
            ->standRecords
            ->whereBetween(
                'date_time',
                [
                    $fullTime->startOfDay()->format('Y-m-d H:i:s'),
                    $fullTime->endOfDay()->format('Y-m-d H:i:s')
                ]
            )
            ->keyBy('date_time');
    }

    private function getTemplatesInDeterminedWeekDay(
        EloquentCollection $standTemplates,
        Carbon $date,
        int $determinedWeek,
    ): array {
        $templatesInDeterminedWeekDay = [];
        $determinedWeekDay = $date->dayOfWeekIso;
        /** @var StandTemplate $template */
        foreach ($standTemplates as $template) {
            if (!isset($template->week_schedule[$determinedWeek][$determinedWeekDay])) {
                continue;
            }

            $dayTimes = $template->week_schedule[$determinedWeek][$determinedWeekDay];

            $neededPublishersByDay = $this->getNeededPublishersByDay($template, $date);

            $records = $this->getAttachedRecords($neededPublishersByDay, $dayTimes, $date);

            $template['records'] = $records;
            $template['dateTimes'] = $dayTimes;

            $templatesInDeterminedWeekDay[] = $template->toArray();
        }

        return $templatesInDeterminedWeekDay;
    }

    private function getAttachedRecords(
        Collection $neededPublishersByDay,
        array $dayTimes,
        Carbon $date,
    ): array {
        $records = [];
        foreach ($dayTimes as $dayTime) {
            $hour = explode(':', $dayTime)[0];
            $minute = explode(':', $dayTime)[1] ?? '00';
            $time = "$hour:$minute";

            $fullDate = $date->format("Y-m-d $time:00"); // added :00 because in DB we store data in H:i:s format

            $records[] = [
                'time' => $time,
                'publishers_records' => $neededPublishersByDay[$fullDate] ?? []
            ];
        }

        return $records;
    }
}
