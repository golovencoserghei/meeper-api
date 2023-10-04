<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class RoleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $roles = Role::query()
            ->when($request->has('name'), static function (Builder $query) use ($request) {
                $query->where('name', 'like', '%' . $request->get('name') . '%');
            })
            ->paginate($request->get('per_page', 20));

        return Response::json(['data' => $roles]);
    }

    public function store(Request $request): JsonResponse
    {
        $role = Role::query()->create([
            'name' => $request->get('name'),
            'guard_name' => $request->get('guard_name'),
        ]);

        return Response::json(['data' => $role], HttpResponse::HTTP_CREATED);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        /** @var Role $role */
        $role = Role::query()->findOrFail($id);

        if ($request->has('name')) {
            $role->name = $request->get('name');
        }

        if ($request->has('guard_name')) {
            $role->guard_name = $request->get('guard_name');
        }

        $role->save();
        $role->fresh();

        return Response::json(['data' => $role], HttpResponse::HTTP_CREATED);
    }

    public function assignPermissionToRole(Request $request): JsonResponse
    {
        $role = Role::findByName(
            $request->input('role_name'),
            $request->input('role_guard_name')
        );

        $permission = Permission::findByName(
            $request->input('permission_name'),
            $request->input('permission_guard_name')
        );

        $role->givePermissionTo($permission);

        return Response::json(
            ['message' => 'Permission was attached to a role successfully!'],
            HttpResponse::HTTP_CREATED
        );
    }

    public function getRolePermissions(int $id): JsonResponse
    {
        return Response::json(['data' => Role::query()->findOrFail($id)->with('permissions')]);
    }

    public function getRoleUsers(int $id): JsonResponse
    {
        return Response::json(['data' => Role::query()->findOrFail($id)->with('users')]);
    }

    public function destroy(int $id): JsonResponse
    {
        Role::destroy($id);

        return Response::json(status:HttpResponse::HTTP_NO_CONTENT);
    }
}
