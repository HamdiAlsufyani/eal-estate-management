<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]
            ->forgetCachedPermissions();

        $permissions = [
            'dashboard.view',

            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'users.approve',

            'properties.view',
            'properties.create',
            'properties.edit',
            'properties.delete',
            'properties.approve',

            'cities.manage',
            'districts.manage',
            'property_types.manage',

            'roles.manage',
            'settings.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $staff = Role::firstOrCreate(['name' => 'Staff']);
        $owner = Role::firstOrCreate(['name' => 'Owner']);

        $admin->givePermissionTo(Permission::all());

        $staff->givePermissionTo([
            'dashboard.view',
            'properties.view',
            'properties.create',
            'properties.edit',
            'properties.approve',
        ]);

        $owner->givePermissionTo([
            'properties.view',
            'properties.create',
            'properties.edit',
        ]);
    }
}