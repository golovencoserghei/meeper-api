<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StandRecordsPublishersResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'date_time' => $this->resource->date_time->format('d-m-Y H:i'),
            'publishers' => StandPublisherResource::collection($this->resource->publishers),
        ];
    }
}
