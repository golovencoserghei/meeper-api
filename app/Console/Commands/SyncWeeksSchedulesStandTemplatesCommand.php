<?php

namespace App\Console\Commands;

use App\Models\StandTemplate;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Date;

class SyncWeeksSchedulesStandTemplatesCommand extends Command
{
    protected $signature = 'stand:sync-stand-template-week-schedule';

    protected $description = 'Command for syncing week schedule template dates every end of the week';

    public function handle(): void
    {
        $standTemplates = StandTemplate::query()
            ->select(['id', 'week_schedule', 'default_week_schedule', 'is_last_week_default'])
            ->where('updated_at', '>', Date::now()->subWeek()->startOfDay())
            ->where('status', 1) // enabled
            ->get();

        $this->moveSchedulesInOrderOfAdminChanges($standTemplates);
    }

    private function moveSchedulesInOrderOfAdminChanges(Collection $standTemplates): void
    {
        $standTemplates->each(static function(StandTemplate $standTemplate): void {
            $weekSchedules = $standTemplate->week_schedule;

            $previousWeek = null;
            $migratedSchedule = [];
            $totalWeeks = count($weekSchedules);
            foreach ($weekSchedules as $weekNumber => $week) {
                $migratedSchedule[$weekNumber] = $week;

                // check if each iteration have changes
                if ($previousWeek !== null && $previousWeek !== $week) {
                    // rearranging order of modified week
                    $migratedSchedule[$weekNumber - 1] = $week;
                }

                // add default week schedule in the end of iteration
                if ($totalWeeks === $weekNumber) {
                    // determine which week to add in the end of schedule
                    $lastWeekValue = $standTemplate->default_week_schedule;
                    if ($standTemplate->is_last_week_default) {
                        $lastWeekValue = $week;
                        // apply changes for the next iterations
                        $standTemplate->default_week_schedule = $week;
                        $standTemplate->is_last_week_default = false;
                        $standTemplate->save();
                    }

                    $migratedSchedule[$weekNumber] = $lastWeekValue;
                }

                $previousWeek = $week;
            }

            if (empty($migratedSchedule)) {
                return;
            }

            $standTemplate->week_schedule = $migratedSchedule;
            $standTemplate->save();
        });
    }
}
