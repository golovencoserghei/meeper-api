<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PublishersRequest;
use App\Http\Requests\PublisherStoreRequest;
use App\Http\Requests\PublisherUpdateRequest;
use App\Models\StandRecords;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;

class PublishersController extends Controller
{
    public function index(PublishersRequest $request): JsonResource
    {
        // @todo - create index in db on congregation_id
        $users = User::query()->where('congregation_id', $request->congregationId)->get();

        return JsonResource::collection($users);
    }

    // @todo - should be in admin panel and manage by roles
    public function store(PublisherStoreRequest $request): JsonResource
    {
        $user = User::query()->create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'congregation_id' => $request->congregation_id,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
        ]);

        return new JsonResource($user);
    }

    // @todo - should be in admin panel and manage by roles
    public function update(PublisherUpdateRequest $request, int $id): JsonResource
    {
        $user = User::query()->findOrFail($id);

        $update = [];

        if ($request->congregation_id) {
            $update['congregation_id'] = $request->congregation_id;
        }

        if ($request->first_name) {
            $update['first_name'] = $request->first_name;
        }

        if ($request->last_name) {
            $update['last_name'] = $request->last_name;
        }

        if ($request->email) {
            $update['email'] = $request->email;
        }

        if ($request->password) {
            $update['password'] = Hash::make($request->password);
        }

        if ($request->phone) {
            $update['phone'] = $request->phone;
        }

        $user->update($update);

        return new JsonResource($user);
    }

    // @todo - should be in admin panel and manage by roles
    public function destroy(int $id): JsonResponse
    {
        User::destroy($id);

        return Response::json(['message' => 'Publisher was deleted.']);
    }

    public function myRecords(Request $request): JsonResponse
    {
        $standRecords = StandRecords::query()
            ->when($request->missing('date_time'), static function($query) use ($request) {
                $query->where('date_time', '>=', Date::now()->format('Y-m-h H:i'));
            })
            ->when($request->input('date_time_start'), static function($query) use ($request) {
                $query->where('date_time', '>=', $request->input('date_time_start'));
            })
            ->when($request->input('date_time_end'), static function($query) use ($request) {
                $query->where('date_time', '>=', $request->input('date_time_end'));
            })
            ->when($request->input('day'), static function ($query) use ($request) {
                $query->where($request->input('day'));
            })
            ->with('publishers')
            ->whereHas('publishers', static function ($query) {
                // @todo - get by auth user
            });

        return new JsonResponse($standRecords);
    }
}
