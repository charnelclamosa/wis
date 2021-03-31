<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::truncate();
        $user = new User;
        $user->Username = '37871';
        $user->Password = Hash::make('37871');
        $user->RoleId = 1;
        $user->updated_by = '37871';
        $user->save();
    }
}
