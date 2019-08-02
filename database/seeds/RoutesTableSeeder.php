<?php

use Illuminate\Database\Seeder;

class RoutesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("routes")->truncate();
        $faker = \Faker\Factory::create();
        for ($i=0;$i<50;$i++){
            \App\Route::query()->create([
                'name'=>$faker->word,
                'status'=>rand(0,1),
            ]);
        }
    }
}
