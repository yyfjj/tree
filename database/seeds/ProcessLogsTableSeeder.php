<?php

use Illuminate\Database\Seeder;

class ProcessLogsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("process_logs")->truncate();
        $faker = \Faker\Factory::create();
        for ($i=0;$i<50;$i++){
            \App\ProcessLog::create([
                'name'=>$faker->word,
                'model'=>$faker->word,
                'foreign_key'=>rand(1,50),
                'status'=>rand(0,1),
            ]);
        }
    }
}
