<?php

namespace Database\Seeders;

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
        ];

        $roles = [
            'admin',
            'responsible-for-stand',
            'publisher',
        ];
//
//        foreach ($permissions as $permission) {
//            Permission::query()->create(['name' => $permission, 'guard_name' => $permission]);
//        }
//
//        foreach ($roles as $role) {
//            Role::query()->create(['name' => $role, 'guard_name' => $role]);
//        }

        Role::findByName('admin', 'admin')->givePermissionTo(Permission::all());
        Role::findByName('responsible-for-stand', 'responsible-for-stand')->givePermissionTo(Permission::all());
    }
}
