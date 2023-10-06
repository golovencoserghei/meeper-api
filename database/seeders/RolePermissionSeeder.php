<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'edit.stands',
            'create.stands',
            'destroy.stands',
            'see.stands',

            'edit.users',
            'create.users',
            'destroy.users',
            'see.users',

            'edit.stand_templates',
            'create.stand_templates',
            'destroy.stand_templates',
            'see.stand_templates',

            'edit.congregations',
            'create.congregations',
            'destroy.congregations',
            'see.congregations',

            'edit.stands_records',
            'create.stands_records',
            'destroy.stands_records',
            'see.stands_records',
        ];

        $roles = [
            'admin',
            'responsible-for-stand',
            'publisher',
        ];

        foreach ($permissions as $permission) {
            Permission::query()->create(['name' => $permission, 'guard_name' => 'api']);
        }

        foreach ($roles as $role) {
            Role::query()->create(['name' => $role, 'guard_name' => 'api']);
        }

        Role::findByName('admin')->givePermissionTo(Permission::all());
        Role::findByName('responsible-for-stand')->givePermissionTo(Permission::all());

        /** @var User $user */
        $user = User::query()->where('email', 'admin@gmail.com')->first();
        $user->assignRole('admin');
    }
}
