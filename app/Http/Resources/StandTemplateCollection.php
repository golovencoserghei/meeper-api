<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class StandTemplateCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * I tried to return Collection without `->toArray()` and it's possible.
     * But I want to keep Laravel requirements and return array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->collection->mapWithKeys(function ($items, $date) {
            return [
                $date => StandTemplateResource::collection($items->resource),
            ];
        })->toArray();
    }
}
