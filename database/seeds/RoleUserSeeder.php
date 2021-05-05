<?php

use Illuminate\Database\Seeder;
use App\Role;

class RoleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            [
                'name' => 'Admin'
            ],
            [
                'name' => 'Buyer'
            ],
            [
                'name' => 'Seller'
            ],
        ];

        Role::insert($roles);
    }
}
