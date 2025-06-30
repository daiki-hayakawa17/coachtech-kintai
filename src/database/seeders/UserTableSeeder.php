<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'name' => 'testuser1',
                'email' => 'test@example.com',
                'password' => Hash::make('password'),
                'role' => 'user',
            ],
        ]);

        DB::table('users')->insert([
            'name' => 'testadmin',
            'email' => 'testadmin@example.com',
            'password' => Hash::make('adminpassword'),
            'role' => 'admin',
        ]);
    }
}
