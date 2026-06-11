<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'purchase_view',
            'purchase_create',
            'purchase_edit',
            'purchase_delete',
            'legacy_migrate',
        ];

        $permissionModels = [];

        foreach ($permissions as $title) {
            $permissionModels[$title] = Permission::updateOrCreate(
                ['title' => $title],
                ['is_active' => true],
            );
        }

        $adminRole = Role::updateOrCreate(
            ['title' => 'Admin'],
            ['is_active' => true],
        );

        $userRole = Role::updateOrCreate(
            ['title' => 'User'],
            ['is_active' => true],
        );

        $adminRole->permissions()->sync(collect($permissionModels)->pluck('id'));

        $userRole->permissions()->sync([
            $permissionModels['purchase_view']->id,
        ]);
    }
}
