<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProcessLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('process_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('model')->comment('过程模块名称，通常对应表名');
            $table->string('name')->comment('过程名称');
            $table->integer('foreign_key')->comment('model表对应的自增id');
            $table->boolean('status')->comment('进度状态0:未到该进度，1:已到该进度');
            $table->timestamps();
            $table->engine = 'InnoDB';
        });
        DB::statement("ALTER TABLE `process_logs` comment'业务执行过程日志'"); // 表注释
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('process_logs');
    }
}
