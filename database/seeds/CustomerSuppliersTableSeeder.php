<?php

use Illuminate\Database\Seeder;

class CustomerSuppliersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("customer_suppliers")->truncate();
        $faker = \Faker\Factory::create();
        for ($i=0;$i<50;$i++){
            \App\CustomerSupplier::create([
                'name'=>$faker->word,
                'name_abbreviation'=>$faker->word,
                'name_code'=>$faker->word,
                'tax_identification_number'=>$faker->word,
                'contact'=>$faker->word,
                'id_card_number'=>rand(100000,999999),
                'tel_area_code'=>rand(1000,9999),
                'tel'=>rand(1300000,1400000),
                'mobile'=>rand(1300000,1400000),
                'city_id'=>rand(1,1000),
                'address'=>$faker->address,
                'email'=>$faker->email,
                'logistics_role'=>rand(1,18),
                'currency'=>'CNY',
                'is_customer'=>rand(0,1),
                'is_supplier'=>rand(0,1),
                'is_invoice'=>rand(0,1),
                'bank_name'=>$faker->word,
                'bank_account'=>"card",
                'pay_max_time'=>15*rand(1,4),
                'receive_max_time'=>15*rand(1,4),
                'credit_max_money'=>rand(10000,9999),
                'credit_max_time'=>rand(1,45),
                'created_user_id'=>rand(1,50),
                'created_user_name'=>$faker->word,
                'created_time'=>\Carbon\Carbon::now()->format("Y-m-d H:i:s"),
                'updated_user_id'=>rand(1,50),
                'updated_user_name'=>$faker->word,
                'updated_time'=>\Carbon\Carbon::now()->format('Y-m-d H:i:s'),
                'process0_user_id'=>rand(1,50),
                'process0_status'=>0,
                'process0_time'=>date('Y-m-d H:i:s'),
                'process1_user_id'=>rand(1,50),
                'process1_status'=>null,
                'process1_time'=>date('Y-m-d H:i:s'),
//                'reviewed_user_id'=>rand(1,50),
//                'reviewed_user_name'=>$faker->word,
//                'reviewed_updated_at'=>\Carbon\Carbon::now()->format("Y-m-d H:i:s"),
                'status'=>rand(0,1),
//                "is_review"=>rand(0,1),

            ]);
        }
    }
}
