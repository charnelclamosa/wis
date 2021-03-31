<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::truncate();
        $roles =[
            ['RoleName' => 'Administrator', 'updated_by' => '37871'],
            ['RoleName' => 'User', 'updated_by' => '37871']
        ];
        foreach($roles as $role) {
            Role::create($role);
        }

    }
}
