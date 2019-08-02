<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContractDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contract_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('contract_id');
            $table->integer('segment_business_id')->comment('业务板块id');
            $table->integer('master_business_id')->comment('主业务类型id');
            $table->integer('slaver_business_id')->comment('子业务类型id');
            $table->integer('charge_rule_id')->comment('价格协议号，其实就是收费规则');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contract_data');
    }
}
