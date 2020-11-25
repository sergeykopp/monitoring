<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $prefix = config('database.connections.' . config('database.default') . '.prefix');

        DB::insert("insert into " . $prefix . "role_user (id_user, id_role) values (1, 1)");
        DB::insert("insert into " . $prefix . "role_user (id_user, id_role) values (1, 2)");
        DB::insert("insert into " . $prefix . "role_user (id_user, id_role) values (2, 2)");
        DB::insert("insert into " . $prefix . "role_user (id_user, id_role) values (3, 2)");
        DB::insert("insert into " . $prefix . "role_user (id_user, id_role) values (4, 2)");
        DB::insert("insert into " . $prefix . "role_user (id_user, id_role) values (5, 2)");
        DB::insert("insert into " . $prefix . "role_user (id_user, id_role) values (6, 2)");
        DB::insert("insert into " . $prefix . "role_user (id_user, id_role) values (7, 2)");
        DB::insert("insert into " . $prefix . "role_user (id_user, id_role) values (8, 2)");
        DB::insert("insert into " . $prefix . "role_user (id_user, id_role) values (9, 2)");
        DB::insert("insert into " . $prefix . "role_user (id_user, id_role) values (10, 2)");
        DB::insert("insert into " . $prefix . "role_user (id_user, id_role) values (11, 2)");
        DB::insert("insert into " . $prefix . "role_user (id_user, id_role) values (12, 2)");
        DB::insert("insert into " . $prefix . "role_user (id_user, id_role) values (13, 2)");
        DB::insert("insert into " . $prefix . "role_user (id_user, id_role) values (14, 2)");
        DB::insert("insert into " . $prefix . "role_user (id_user, id_role) values (15, 2)");
        DB::insert("insert into " . $prefix . "role_user (id_user, id_role) values (1, 3)");
        DB::insert("insert into " . $prefix . "role_user (id_user, id_role) values (3, 3)");
    }
}
