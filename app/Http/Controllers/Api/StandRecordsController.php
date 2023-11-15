<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StandPublishersRequest;
use App\Http\Requests\StandPublishersUpdateRequest;
use App\Models\StandRecords;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class StandRecordsController extends Controller
{
    public function store(StandPublishersRequest $request): JsonResource
    {
        DB::beginTransaction();
        try {
            $attributes = $this->getStandRecordsAttributes($request);

            $this->checkIfRecordAlreadyExistAndRemove($attributes);

            /** @var StandRecords $standRecord */
            $standRecord = StandRecords::query()->create($attributes);

            $standRecord->publishers()->attach($request->publishers);

            DB::commit();

            return new JsonResource(['data' => $standRecord, 'publishers' => $request->publishers]);
        } catch (Exception $exception) {
            DB::rollBack();

            Log::error('stand publishers save error', [
                'message' => $exception->getMessage(),
                'trace' => $exception->getTrace(),
            ]);

            throw new Exception($exception->getMessage());
        }
    }

    public function update(int $id, StandPublishersUpdateRequest $request): JsonResource
    {
        /** @var StandRecords $standRecord */
        $standRecord = StandRecords::query()->findOrFail($id);

        $standRecord->publishers()->attach($request->publishers);

        return new JsonResource(['data' => $standRecord, 'publishers' => $request->publishers]);
    }

    public function show(int $id): JsonResponse
    {
        return new JsonResponse([
            'data' => StandRecords::query()
                ->with('publishers')
                ->findOrFail($id)
        ]);
    }

    public function removePublishers(int $id, Request $request): JsonResponse //@todo - add form request class
    {
        /** @var StandRecords $standRecord */
        $standRecord = StandRecords::query()->findOrFail($id);
        $standRecord->publishers()->detach($request->publishers);

        $standRecord->refresh();

        return new JsonResponse(['data' => $standRecord, 'publishers' => $standRecord->publishers()]);
    }

    public function destroy(int $id): JsonResponse
    {
        /** @var StandRecords $standRecords */
        $standRecords = StandRecords::query()->findOrFail($id);
        $standRecords->publishers()->detach();

        $standRecords->delete();

        return Response::json(['message' => 'Stand record was deleted.']);
    }

    private function getStandRecordsAttributes(StandPublishersRequest $request): array
    {
        return [
            'stand_template_id' => $request->stand_template_id,
            'day' => Carbon::parse($request->date_time)->dayOfWeekIso, // @todo - change name to week_day
            'date_time' => Carbon::parse($request->date_time)->format('Y-m-d H:i:s'),
        ];
    }

    private function checkIfRecordAlreadyExistAndRemove(array $attributes): void
    {
        $existingStandRecords = StandRecords::query()->with('publishers')->where($attributes)->get();

        if ($existingStandRecords->isEmpty()) {
            return;
        }

        $existingStandRecords->map(static function(StandRecords $standRecord) {
            $standRecord->publishers()->detach();

            $standRecord->delete();
        });
    }
}
