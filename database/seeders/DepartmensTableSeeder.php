<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class DepartmensTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            ['name' => 'Management / BOD', 'project' => '000H', 'akronim' => 'BOD', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Internal Audit & System', 'project' => '000H', 'akronim' => 'IAS', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Corporate Secretary', 'project' => '001H', 'akronim' => 'CORSEC', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'APS - Arka Project Support', 'project' => 'APS', 'akronim' => 'APS', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Relationship & Coordination', 'project' => '000H', 'akronim' => 'RNC', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Design & Construction', 'project' => '000H', 'akronim' => 'DNC', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Finance', 'project' => '001H', 'akronim' => 'FIN', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Human Capital & Support', 'project' => '000H', 'akronim' => 'HCS', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Logistic', 'project' => '000H', 'akronim' => 'LOG', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Warehouse 017C', 'project' => '017C', 'akronim' => 'WH017', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Warehouse 021C', 'project' => '021C', 'akronim' => 'WH021', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Warehouse 022C', 'project' => '022C', 'akronim' => 'WH022', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Warehouse 023C', 'project' => '023C', 'akronim' => 'WH023', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Accounting', 'project' => '000H', 'akronim' => 'ACC', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Plant', 'project' => '000H', 'akronim' => 'PLANT', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Procurement', 'project' => '000H', 'akronim' => 'PROC', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Operation & Production', 'project' => '000H', 'akronim' => 'OPR', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Safety', 'project' => '000H', 'akronim' => 'SHE', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Information Technology', 'project' => '000H', 'akronim' => 'IT', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Research & Development', 'project' => '000H', 'akronim' => 'RND', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('departments')->insert($departments);
    }
}
