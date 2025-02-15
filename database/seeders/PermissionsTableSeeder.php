<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $resources = [
            'dashboard' => ['index'],
            'documents' => ['index'],
            'deliveries' => ['index'],
            'reports' => ['index'],
            'master' => ['index'],
            'settings' => ['index'],
            'users' => ['index', 'create', 'edit', 'delete'],
            'roles' => ['index', 'create', 'edit', 'delete'],
            'permissions' => ['index', 'create'],
            'invoices' => ['index', 'create', 'edit', 'delete'],
            'addocs' => ['index', 'create', 'edit', 'delete'],
            'addocs_type' => ['index', 'create', 'edit', 'delete'],
            'inv_type' => ['index', 'create', 'edit', 'delete'],
            'spis' => ['index', 'create', 'edit', 'delete'],
            'lpd' => ['index', 'create', 'edit', 'delete'],
        ];

        foreach ($resources as $resource => $actions) {
            foreach ($actions as $action) {
                $permissionName = "{$resource}.{$action}";

                Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'sanctum']);
            }
        }
    }
}
