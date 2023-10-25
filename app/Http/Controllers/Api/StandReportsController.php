<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StandReports\IndexRequest;
use App\Http\Requests\StandReports\StoreRequest;
use App\Http\Requests\StandReports\UpdateRequest;
use App\Models\StandRecords;
use App\Models\StandReports;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class StandReportsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @todo - admin route
     */
    public function index(IndexRequest $request): LengthAwarePaginator
    {
        return StandReports::query()
            ->when($request->has('publisher_id'), static function (Builder $query) use ($request) {
                $query->where('reported_by', $request->publisher_id);
            })
            ->when($request->has('congregation_id'), static function (Builder $query) use ($request) {
                $query->where('congregation_id', $request->congregation_id);
            })
            ->when($request->has('stand_id'), static function (Builder $query) use ($request) {
                $query->where('stand_id', $request->stand_id);
            })
            ->when($request->has('stands_records_id'), static function (Builder $query) use ($request) {
                $query->where('stands_records_id', $request->stands_records_id);
            })
            ->when(
                $request->has('date_start') && !$request->has('date_end'),
                static function (Builder $query) use ($request) {
                    $dateStart = Carbon::parse($request->date_start)->format('Y-m-d H:i:s');

                    $query->where('report_date', '>=', $dateStart);
                }
            )
            ->when(
                $request->has('date_end') && !$request->has('date_start'),
                static function (Builder $query) use ($request) {
                    $dateEnd = Carbon::parse($request->date_end)->format('Y-m-d H:i:s');

                    $query->where('report_date', '<=', $dateEnd);
                }
            )
            ->when(
                $request->has('date_start') && $request->has('date_end'),
                static function (Builder $query) use ($request) {
                    $dateStart = Carbon::parse($request->date_start)->format('Y-m-d H:i:s');
                    $dateEnd = Carbon::parse($request->date_end)->format('Y-m-d H:i:s');

                    $query->whereBetween('report_date', [$dateStart, $dateEnd]);
                }
            )
            ->paginate(
               perPage: $request->get('per_page', 50),
               page: $request->get('page', 1),
            );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request): Model
    {
        $reportedBy = Auth::id();
        /** @var StandRecords $standRecord */
        $standRecord = StandRecords::query()->with('standTemplate')->find($request->stands_records_id);

        $dataForCreation = [
            'reported_by' => $reportedBy,
            'report_date' => $standRecord->date_time,
            'stands_records_id' => $standRecord->id,
            'congregation_id' => $standRecord->standTemplate->congregation_id,
            'stand_id' => $standRecord->standTemplate->stand_id,
        ];

        $dataForCreation = $this->fillDataToInsert($request, $dataForCreation);

        return StandReports::query()->create($dataForCreation);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): Model
    {
        return StandReports::query()->findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, string $id): Model
    {
        $standReport = StandReports::query()->findOrFail($id);

        $dataToUpdate = $this->fillDataToInsert($request);

        $standReport->update($dataToUpdate);
        $standReport->refresh();

        return $standReport;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): Response
    {
        StandReports::destroy($id);

        return new Response(status: \Symfony\Component\HttpFoundation\Response::HTTP_NO_CONTENT);
    }

    private function fillDataToInsert(FormRequest $request, array $dataToInsert = []): array
    {
        if ($request->has('publications')) {
            $dataToInsert['publications'] = $request->input('publications');
        }

        if ($request->has('videos')) {
            $dataToInsert['videos'] = $request->input('videos');
        }

        if ($request->has('return_visits')) {
            $dataToInsert['return_visits'] = $request->input('return_visits');
        }

        if ($request->has('bible_studies')) {
            $dataToInsert['bible_studies'] = $request->input('bible_studies');
        }

        return $dataToInsert;
    }
}
