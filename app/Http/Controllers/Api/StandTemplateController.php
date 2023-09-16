<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StandRequest;
use App\Models\StandTemplate;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class StandTemplateController extends Controller
{
    public function index(StandRequest $request): JsonResource
    {
        $templates = StandTemplate::with([
            'stand:id,name,location',
            'standPublishers:id,time,stand_template_id,day,user_1,user_2,date',
            'standPublishers.user:id,name,prename,email',
            'standPublishers.user2:id,name,prename,email',
            'congregation:id,name',
        ])
            ->select('id', 'type', 'week_schedule', 'stand_id', 'congregation_id')
            ->where([
                'congregation_id' => $request->congregationId,
                'stand_id' => $request->standId,
            ])
            ->groupBy(['stand_id', 'congregation_id', 'type'])
            ->get(); // `->get()` because model doesn't have `->map()` method

        $templates = $templates->map(static function ($template) {
            $template->stand_publishers = $template->standPublishers->keyBy(static function($standPublishers) {
                return $standPublishers->day . '_' . $standPublishers->time;
            });
            
            return $template;
        });

        $currentWeek = $templates->where('type', 'current');
        $nextWeek = $templates->where('type', 'next');

        return JsonResource::collection([
            'current' => $currentWeek->first(), // because we have one template for one stand+congregation
            'next' => $nextWeek->first(), // @todo - create unique indexes on template + publishers columns
        ]);
    }

    public function weekDays(): JsonResource
    {
        $now = Carbon::now();
        $currentWeekDay = $now->copy()->dayOfWeek; // 0 (for Sunday) through 6 (for Saturday)
        $weekStartDate = $now->copy()->startOfWeek()->format('d-m-Y');
        $weekEndDate = $now->copy()->endOfWeek()->format('d-m-Y');
        $nextWeekStartDate = $now->copy()->addWeek()->startOfWeek()->format('d-m-Y');
        $nextWeekEndDate = $now->copy()->addWeek()->endOfWeek()->format('d-m-Y');

        return new JsonResource([
            'currentNumberOfWeekDay' => $currentWeekDay,
            'currentWeekStartDate' => $weekStartDate,
            'currentWeekEndDate' => $weekEndDate,
            'nextWeekStartDate' => $nextWeekStartDate,
            'nextWeekEndDate' => $nextWeekEndDate,
        ]);
    }
}
