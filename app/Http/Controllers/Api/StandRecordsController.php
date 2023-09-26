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
            /** @var StandRecords $standRecord */
            $standRecord = StandRecords::query()->create([
                'stand_template_id' => $request->stand_template_id,
                'day' => Carbon::parse($request->date_time)->dayOfWeekIso, // @todo - change name to week_day
                'date_time' => Carbon::parse($request->date_time)->format('Y-m-d H:i:s'),
            ]); // @todo - add validation if users already registered

            $standRecord->publishers()->attach($request->publishers);

            DB::commit();

            return new JsonResource(['data' => $standRecord, 'publishers' => $request->publishers]);
        } catch (Exception $exception) {
            DB::rollBack();

            Log::error('stand publiushers save error', [
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
        StandRecords::destroy($id);

        return Response::json(['message' => 'Stand record was deleted.']);
    }
}
