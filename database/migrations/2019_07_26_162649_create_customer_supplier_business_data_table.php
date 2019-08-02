<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerSupplierBusinessDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_supplier_business_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('customer_supplier_id');
            $table->integer('segment_business_id');
            $table->integer('master_business_id');
            $table->integer('slaver_business_id');
            $table->integer('charge_rule_id')->nullable();
            $table->integer('is_lock')->comment('1:锁定,不能编辑,0:可编辑');
            $table->timestamps();
            $table->engine = 'InnoDB';
        });
        DB::statement("ALTER TABLE `customer_supplier_business_data` comment'客户供应商和业务关联表'"); // 表注释
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_supplier_business_data');
    }
}
