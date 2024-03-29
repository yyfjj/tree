<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContractsTable extends Migration
{
    public function up(){
        Schema::create('contracts', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integerIncrements('id');
            $table->string('name')->comment('合同名称');
            $table->string('sn')->nullable()->comment('合同号');
            $table->string('sn_alias')->comment('对方合同号');
            $table->string('sn_inner')->nullable()->comment('内部合同编号');
            $table->string('type')->comment('合同类型customer:客户合同、supplier:供应商合同');
            $table->integer('clear_company_id')->comment('结算公司id');
            $table->string('attachment', 100)->nullable()->comment('合同附件');
            $table->date('begin_time')->nullable()->comment('合同开始时间');
            $table->date('end_time')->nullable()->comment('合同结束时间');
            $table->integer("process0_user_id")->comment("合同草拟申请人id");
//            $table->string('process1_users_name')->comment("申请人名字");
            $table->smallInteger('process0_status')->default(0)->comment("合同草拟(默认为0)0:未办理[保存状态],1:同意");
            $table->dateTime('process0_time')->nullable()->comment('合同草拟时间,null时无申请时间');
            $table->integer('credit_time_type')->nullable()->comment('信控基准日1:业务日期,2:开票日期,3:到港日期');
            $table->integer('credit_delay_type')->nullable()->comment('延迟类型1:延迟月份,2:延后自然日数,3:延后工作日数');
            $table->integer('credit_delay_data')->nullable()->comment('延后月份:延迟类型为1,1:次月,2:次月月底,3:次次月,4:次次月底,5:次次次月,6:次次次月底;延迟类型为2:表示延后自然日数;延迟类型为3:表示延后工作日数');
            $table->integer('credit_delay_data_data')->nullable()->comment('延后月份结算日:当是次月、次次月、次次次月才有具体数据天');
            $table->integer('process1_user_id')->nullable()->comment('商务会签人id');
            $table->smallInteger('process1_status')->default(0)->comment("商务会签状态0:未办理[保存状态],1:同意");
            $table->dateTime('process1_time')->nullable()->comment('商务会签时间');
            $table->integer('process2_user_id')->nullable()->comment('业务会签人id');
//            $table->string('process3_users_name')->nullable()->comment('业务会签人名字');
            $table->smallInteger('process2_status')->default(0)->comment("申请状态0:未办理,1:同意");
            $table->dateTime('process2_time')->nullable()->comment('业务会签时间');
            $table->integer('process3_user_id')->nullable()->comment('审批人id');
//            $table->string('process4_users_name')->nullable()->comment('审批人名字');
            $table->smallInteger('process3_status')->default(0)->comment("申请状态0:未办理,1:同意");
            $table->dateTime('process3_time')->nullable()->comment('审批会签时间');
            $table->integer('process4_user_id')->nullable()->comment('归档人id');
//            $table->string('process5_users_name')->nullable()->comment('归档人名字');
            $table->smallInteger('process4_status')->default(0)->comment("申请状态0:未办理,1:同意");
            $table->dateTime('process4_time')->nullable()->comment('归档时间');

            $table->softDeletes();
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `contracts` comment'合同'"); // 表注释
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up_bak()
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integerIncrements('id');
            $table->string('sn', 100)->nullable()->comment('合同编号');
            $table->string('inner_sn', 100)->nullable()->comment('用于内部管理的合同编号');
            $table->string('customer_sn',100)->nullable()->comment('对方合同编号');
            $table->string('name',100)->nullable()->comment('合同名称');
            $table->string('type')->comment('合同类型customer:客户合同、supplier:供应商合同');
            $table->integer('clear_company_id')->comment('结算公司id');
//            $table->string('businesses_id_string')->comment('业务id,中间用英文逗号分隔');
            $table->smallInteger('is_invoice')->comment("是否结算单位，就是是否需要开发票");
            $table->integer('part_a_customer_supplier_id')->comment('合同甲方');
            $table->integer('part_b_customer_supplier_id')->comment('合同已方');
            $table->integer('part_c_customer_supplier_id')->comment('合同丙方');
//            $table->string("charge_rules_id_string", 100)->comment('价格协议号id,中间用英文逗号分隔,所谓价格协议其实也就是收费规则');

            $table->integer("process0_user_id")->comment("申请人id");
//            $table->string('process1_users_name')->comment("申请人名字");
            $table->string('process0_status')->default(0)->comment("申请状态(默认为0)-1:退签,0:未办理,1:同意");
            $table->dateTime('process0_time')->nullable()->comment('申请时间,null时无申请时间');
            $table->integer('process1_user_id')->nullable()->comment('商务会签人id');
//            $table->string('process2_users_name')->nullable()->comment('商务会签人名字');
            $table->string('process1_status')->nullable()->comment("申请状态null:未到该审批步骤,-1:退签,0:未办理,1:同意");
            $table->dateTime('process1_time')->nullable()->comment('商务会签时间');
            $table->integer('process2_user_id')->nullable()->comment('业务会签人id');
//            $table->string('process3_users_name')->nullable()->comment('业务会签人名字');
            $table->string('process2_status')->nullable()->comment("申请状态null:未到该审批步骤,-1:退签,0:未办理,1:同意");
            $table->dateTime('process2_time')->nullable()->comment('业务会签时间');
            $table->integer('process3_user_id')->nullable()->comment('审批人id');
//            $table->string('process4_users_name')->nullable()->comment('审批人名字');
            $table->string('process3_status')->nullable()->comment("申请状态null:未到该审批步骤,-1:退签,0:未办理,1:同意");
            $table->dateTime('process3_time')->nullable()->comment('审批会签时间');
            $table->integer('process4_user_id')->nullable()->comment('归档人id');
//            $table->string('process5_users_name')->nullable()->comment('归档人名字');
            $table->string('process4_status')->nullable()->comment("申请状态null:未到该审批步骤,-1:退签,0:未办理,1:同意");
            $table->dateTime('process4_time')->nullable()->comment('归档时间');

            $table->string('from')->comment('揽货性质，其实就是业务来源company:公司揽货、person:销售揽货');
            $table->string('attachment', 100)->nullable()->comment('合同附件');
            $table->softDeletes();
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `contracts` comment'合同'"); // 表注释
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contracts');
    }
}
