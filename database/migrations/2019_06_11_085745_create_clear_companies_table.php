<?php

use /** @noinspection PhpUndefinedClassInspection */
    Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClearCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /** @noinspection PhpUndefinedClassInspection */
        Schema::create('clear_companies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name',50);
            $table->smallInteger('status');
            $table->integer('user_id')->comment('操作员');
            $table->softDeletes();
            $table->timestamps();
            $table->engine = 'InnoDB';
        });
        DB::statement("ALTER TABLE `clear_companies` comment'结算公司'"); // 表注释
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /** @noinspection PhpUndefinedClassInspection */
        Schema::dropIfExists('clear_companies');
    }
}
