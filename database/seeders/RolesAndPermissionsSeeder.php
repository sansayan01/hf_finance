<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        \Spatie\Permission\Models\Role::create(['name' => 'super_admin']);
        \Spatie\Permission\Models\Role::create(['name' => 'org_admin']);
        \Spatie\Permission\Models\Role::create(['name' => 'loan_officer']);
        \Spatie\Permission\Models\Role::create(['name' => 'accountant']);
        \Spatie\Permission\Models\Role::create(['name' => 'collector']);

        // Define permissions later as needed for Filament
    }
}
