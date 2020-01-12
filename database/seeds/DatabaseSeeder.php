<?php

use App\Privilege;
use App\Role;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
//         $this->call(UsersTableSeeder::class);
//        $roles = ['administrator', 'customer'];
//        foreach($roles as $role) {
//            Role::create(['name' => $role]);
//        }

        $privileges = Privilege::getAllPrivilegesDetailed();
        foreach ($privileges as $privilege) {
            Privilege::create($privilege);
        }
    }
}
