<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Spatie\Permission\Models\Permission;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class PermissionsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $permissions = Permission::query()
            ->when($request->has('name'), static function (Builder $query) use ($request) {
                $query->where('name', 'like', '%' . $request->get('name') . '%');
            })
            ->paginate($request->get('per_page', 20));

        return Response::json(['data' => $permissions]);
    }

    public function store(Request $request): JsonResponse
    {
        $permission = Permission::query()->create(['name' => $request->get('name')]);

        return Response::json(['data' => $permission], HttpResponse::HTTP_CREATED);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        /** @var Permission $permission */
        $permission = Permission::query()->findOrFail($id);

        if ($request->has('name')) {
            $permission->name = $request->get('name');
        }

        $permission->save();
        $permission->fresh();

        return Response::json(['data' => $permission], HttpResponse::HTTP_CREATED);
    }

    public function destroy(int $id): JsonResponse
    {
        Permission::destroy($id);

        return Response::json(status:HttpResponse::HTTP_NO_CONTENT);
    }
}
