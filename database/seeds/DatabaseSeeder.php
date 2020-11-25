<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(DirectoratesSeeder::class);
        $this->call(FilialsSeeder::class);
        $this->call(CitiesSeeder::class);
        $this->call(OfficesSeeder::class);
        $this->call(SourcesSeeder::class);
        $this->call(GroupsServicesSeeder::class);
        $this->call(ServicesSeeder::class);
        $this->call(StatusesSeeder::class);
        $this->call(UsersSeeder::class);
        $this->call(RolesSeeder::class);
        $this->call(RoleUserSeeder::class);
        $this->call(CausesSeeder::class);
        $this->call(TroublesSeeder::class);
    }
}
