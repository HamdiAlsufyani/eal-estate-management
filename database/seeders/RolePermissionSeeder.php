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
            'properties.assign_owner',
            'properties.feature',
            'properties.change_availability',

            'property_types.manage',

            'cities.view',
            'cities.create',
            'cities.edit',
            'cities.delete',

            'districts.view',
            'districts.create',
            'districts.edit',
            'districts.delete',

            'amenities.view',
            'amenities.create',
            'amenities.edit',
            'amenities.delete',

            'roles.manage',
            'settings.manage',

            'inquiries.view',
            'inquiries.manage',
            'favorites.view',

            'analytics.view',
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
            'properties.assign_owner',
            'properties.feature',
            'properties.change_availability',
            'inquiries.view',
            'inquiries.manage',
            'favorites.view',

            'analytics.view',
        ]);

        $owner->givePermissionTo([
            'properties.view',
            'properties.create',
            'properties.edit',
            'properties.delete',
        ]);
    }
}