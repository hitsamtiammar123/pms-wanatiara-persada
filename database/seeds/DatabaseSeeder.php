<?php

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
        // $this->call(BasicSeeder::class);
        // $this->call(KPISeeder::class);
        // $this->call(AtasanSeeder::class);
        $this->call(EndorsementSeeder::class);
    }
}
