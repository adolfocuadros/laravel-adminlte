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
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        \App\User::truncate();

        factory(\App\User::class)->create([
            'name'  =>  'Admin',
            'email' => 'admin@test.com'
        ]);
    }
}
