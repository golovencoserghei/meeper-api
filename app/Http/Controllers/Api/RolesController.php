<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleAssignPermissionRequest;
use App\Http\Requests\RoleAssignUserRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class RolesController extends Controller
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

        $role->save();
        $role->fresh();

        return Response::json(['data' => $role], HttpResponse::HTTP_CREATED);
    }

    public function assignPermissionToRole(RoleAssignPermissionRequest $request): JsonResponse
    {
        $role = Role::findById($request->role_id);

        $permissions = Permission::query()
            ->whereIn('id', $request->permission_ids)
            ->get();

        $role->givePermissionTo($permissions);

        return Response::json(
            ['message' => 'Permission was attached to a role successfully!'],
            HttpResponse::HTTP_CREATED
        );
    }

    public function assignRoleToUser(RoleAssignUserRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = User::query()->findOrFail($request->user_id);
        $role = Role::findById($request->role_id);

        $user->assignRole($role);

        return Response::json(
            ['message' => "$role->name was attached to user with id: $user->id"],
        );
    }

    public function unassignUserRole(RoleAssignUserRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = User::query()->findOrFail($request->user_id);
        $role = Role::findById($request->role_id);

        $user->removeRole($role);

        return Response::json(
            ['message' => "$role->name was unassigned from user with id: $user->id"],
        );
    }

    public function getRolePermissions(int $id): JsonResponse
    {
        return Response::json(['data' => Role::query()->where('id', $id)->with('permissions')->get()]);
    }

    public function getRoleUsers(int $id): JsonResponse
    {
        return Response::json(['data' => Role::query()->where('id', $id)->with('users')->get()]);
    }

    public function destroy(int $id): JsonResponse
    {
        Role::destroy($id);

        return Response::json(status:HttpResponse::HTTP_NO_CONTENT);
    }
}
