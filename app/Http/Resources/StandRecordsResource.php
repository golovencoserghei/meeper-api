<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StandRecordsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'time' => $this['time'],
            'publishers_records' => !empty($this['publishers_records'])
                ? StandRecordsPublishersResource::make($this['publishers_records'])
                : [],
        ];
    }
}
