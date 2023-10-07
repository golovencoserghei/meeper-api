<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddUserToCongregationRequest;
use App\Models\Congregation;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Response;

class CongregationsController extends Controller
{
    public function index(): JsonResource
    {
        $congregations = Congregation::query()->select('id', 'name')->get();

        return JsonResource::collection($congregations);
    }

    public function store(Request $request): JsonResource
    {
        $congregation = Congregation::query()->create([
            'name' => $request->post('name'),
        ]);

        return new JsonResource($congregation);
    }

    public function update(Request $request, int $id): JsonResource
    {
        $congregation = Congregation::query()->findOrFail($id);

        $congregation->update([
            'name' => $request->post('name'),
        ]);

        return new JsonResource($congregation);
    }

    public function destroy(int $id): JsonResponse
    {
        Congregation::destroy($id);

        return Response::json(['message' => 'Congregation was deleted.']);
    }

    public function addUserToCongregation(AddUserToCongregationRequest $request): JsonResponse
    {
        $user_code = trim($request->user_code);
        $congregation_id = $request->congregation_id;

        /** @var User $user */
        $user = User::query()
            ->where('code', $user_code)
            ->orWhere('id', $request->user_id)
            ->first();
        $user->congregation_id = $congregation_id;
        $user->save();

        return Response::json([
            'message' => "User with $user->id was attached to congregation with id $congregation_id"
        ]);
    }
}
