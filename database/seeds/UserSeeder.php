<?php

use Illuminate\Database\Seeder;
use App\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = [
            [
                'name' => 'Test',
                'email' => 'mailtest@gmail.com',
                'password' => 'test12345678',
                'role_id' => 1
            ],
          
        ];

        User::insert($user);
    }
}
