<?php

namespace App\Console\Commands;

use App\Enums\WeekStatusesEnum;
use App\Models\StandPublishers;
use App\Models\StandPublishersHistory;
use App\Models\StandTemplate;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncCurrentAndNextWeeksStandTemplatesCommand extends Command
{
    protected $signature = 'stand:sync-stand-template-weeks';

    protected $description = 'Command for syncing current and next week template data every end of the week and saving stand publishers to history.';

    public function handle(): void
    {
        // @todo - test it with many stands/congregations. Cover with unit tests?
        $standTemplatesForTheNextWeekQuery = StandTemplate::query()
            ->select(['id', 'days', 'times_range', 'stand_id', 'congregation_id'])
            ->where('type', WeekStatusesEnum::NEXT->value)
            ->groupBy(['stand_id', 'congregation_id']);

        $currentWeekTeplatesIdsToUpdate = [];
        $standPublishersFromCurrentWeekToRemove = [];
        foreach($standTemplatesForTheNextWeekQuery->get() as $standTemplateForTheNextWeek) {
            /** @var StandTemplate $standTemplateForTheCurrentWeek */
            $standTemplateForTheCurrentWeek = StandTemplate::query()
                ->select(['id', 'days', 'times_range', 'type'])
                ->where([
                    'stand_id' => $standTemplateForTheNextWeek->stand_id,
                    'congregation_id' => $standTemplateForTheNextWeek->congregation_id,
                    'type' => WeekStatusesEnum::CURRENT->value,
                ])
                ->with('standPublishers')
                ->first();

            $this->updateWeekScheduleForCurrentWeekTemplate($standTemplateForTheCurrentWeek, $standTemplateForTheNextWeek);

            $standPublishersFromCurrentWeekToRemove[] = $standTemplateForTheCurrentWeek->standPublishers->pluck('id')->all();

            $currentWeekTeplatesIdsToUpdate[] = $standTemplateForTheCurrentWeek->id;
        }

        $standPublishersIdsToRemove = array_merge([], ...$standPublishersFromCurrentWeekToRemove);

        $standPublishersToInsertInHistory = $this->getStandPublishersToSaveInHistory($standPublishersIdsToRemove);

        DB::beginTransaction();
        try {
            $standTemplatesForTheNextWeekQuery->update(['type' => WeekStatusesEnum::CURRENT->value]);

            StandTemplate::query()->whereIn('id', $currentWeekTeplatesIdsToUpdate)->update(['type' => WeekStatusesEnum::NEXT->value]);
            StandPublishers::query()->whereIn('id', $standPublishersIdsToRemove)->delete();
            StandPublishersHistory::query()->insert($standPublishersToInsertInHistory);

            Log::info('[Stand Templates Sync] - info', [
                'updatedCurrentWeeks' => $currentWeekTeplatesIdsToUpdate,
                'updatedNextWeeks' => $standTemplatesForTheNextWeekQuery->pluck('id'),
            ]);

            DB::commit();
        } catch (Exception $exception) {
            Log::critical('[Stand Templates Sync] - error', [
                'category' => __CLASS__,
                'exception' => $exception->getMessage(),
                'trace' => $exception,
            ]);

            DB::rollBack();
        }
    }

    private function updateWeekScheduleForCurrentWeekTemplate(StandTemplate $standTemplateForTheCurrentWeek, StandTemplate $standTemplateForTheNextWeek): void
    {
        $isWeekScheduleChanged = $standTemplateForTheNextWeek->updated_at < Date::now()->subWeek();

        if ($isWeekScheduleChanged) {
            $standTemplateForTheCurrentWeek->week_schedule = $standTemplateForTheNextWeek->week_schedule;

            $standTemplateForTheCurrentWeek->save();
        }
    }

    private function getStandPublishersToSaveInHistory(array $standPublishersIdsToRemove): array
    {
        $standPublishersWithTemplateInfos = StandPublishers::query()->whereIn('id', $standPublishersIdsToRemove)->with('standTemplate:id,stand_id,congregation_id')->get();

        $standPublishersHistoryToInsert = [];

        foreach ($standPublishersWithTemplateInfos as $standPublishersWithTemplateInfo) {
            $standPublishersHistoryToInsert[] = [
                'stand_id' => $standPublishersWithTemplateInfo->standTemplate->stand_id,
                'congregation_id' => $standPublishersWithTemplateInfo->standTemplate->congregation_id,
                'day' => $standPublishersWithTemplateInfo->day,
                'time' => $standPublishersWithTemplateInfo->time,
                'user_1' => $standPublishersWithTemplateInfo->user_1,
                'user_2' => $standPublishersWithTemplateInfo->user_2,
                'date' => $standPublishersWithTemplateInfo->date,
            ];
        }

        return $standPublishersHistoryToInsert;
    }
}
