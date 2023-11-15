<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StandTemplateResource extends JsonResource
{
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this['id'],
            'stand_id' => $this['stand_id'],
            'stand' => $this['stand'],
            'congregation_id' => $this['congregation_id'],
            'activation_at' => $this['activation_at'],
            'publishers_at_stand' => $this['publishers_at_stand'],
            'is_reports_enabled' => $this['is_reports_enabled'],
            'records' => StandRecordsResource::collection($this['records']),
        ];
    }
}
