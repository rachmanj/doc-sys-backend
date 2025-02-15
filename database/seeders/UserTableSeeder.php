<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Cari role `admin` terlebih dahulu
         $superadminRole = Role::where('name', 'superadmin')->first();

         // Jika role `admin` belum ada, buat role baru
         if (!$superadminRole) {
             $superadminRole = Role::create(['name' => 'superadmin', 'guard_name' => 'sanctum']);
         }
 
         // Ambil semua izin yang ada
         $allPermissions = Permission::all();
 
         // Assign all permissions to `admin` role
         $superadminRole->syncPermissions($allPermissions);
 
         $itDepartmentId = DB::table('departments')->where('akronim', 'IT')->value('id');

        $user = User::factory()->create([
            'name' => 'Omanof Sullivans',
            'email' => 'oman@gmail.com',
            'username' => 'superadmin',
            'nik' => '11230',
            'password' => Hash::make('123456'),
            'project' => '000H',
            'department_id' => $itDepartmentId,
            'is_active' => true,
        ]);
 
         // Assign role `admin` ke user
         $user->assignRole($superadminRole);
    }
}
