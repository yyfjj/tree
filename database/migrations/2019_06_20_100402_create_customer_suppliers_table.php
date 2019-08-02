<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_suppliers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->comment('公司/个人全程');
            $table->string('name_abbreviation')->comment('公司/个人缩写');
            $table->string('name_code')->comment('公司/个人代码');
            $table->string('tax_identification_number')->comment('纳税人识别号')->nullable();
            $table->string('contact')->comment('联系人')->nullable();
            $table->string('id_card_number')->comment('身份证号码')->nullable();
            $table->string('tel_area_code')->comment('固定电话区号')->nullable();
            $table->string('tel')->comment('固定电话')->nullable();
            $table->string('mobile')->comment('手机号')->nullable();
            $table->integer('city_id')->nullable();
            $table->string('address', 100)->nullable();
            $table->string('email')->nullable();
//            $table->enum('logistics_role',[1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18])->comment('1-委托人、2-船公司、3-订舱公司、4-换单公司、5-货代公司、6-车队、7-保险公司、8-仓储公司、9-铁路公司、10-开证公司、
//            11-提箱公司、12-还箱公司、13-检测公司、14-消毒公司、15-蒸熏公司、16-理货公司、17-装卸公司、18-其他');
            $table->string('logistics_role')->nullable()->comment('1-委托人、2-船公司、3-订舱公司、4-换单公司、5-货代公司、6-车队、7-保险公司、8-仓储公司、9-铁路公司、10-开证公司、11-提箱公司、12-还箱公司、13-检测公司、14-消毒公司、15-蒸熏公司、16-理货公司、17-装卸公司、18-其他');
            $table->enum('currency',['CNY','USB'])->nullable()->comment('币种');
            $table->boolean('is_customer')->nullable()->comment('是否是客户');
            $table->boolean('is_supplier')->nullable()->comment('是否是供应商');
            $table->boolean('is_invoice')->nullable()->comment('结算单位标志，就是按照是否需要开发票来判断');
            $table->string('bank_name')->nullable()->comment('开户行');
            $table->string('bank_account')->nullable()->comment('开户行账号');
//            $table->enum('pay_max_time',[15,30,45,60])->nullable()->comment('在最多多少天内付款');
            $table->smallInteger('pay_max_time')->nullable()->comment('在最多多少天内付款');
//            $table->enum('receive_max_time',[15,30,45,60])->nullable()->comment('在最多多少天内收款');
            $table->smallInteger('receive_max_time')->nullable()->comment('在最多多少天内收款');
            $table->integer('credit_max_money')->nullable()->comment('信控金额，单位分');
            $table->integer('credit_max_time')->nullable()->comment('信控宽限天数，单位天');
            $table->integer('created_user_id')->nullable()->comment('创建人id');
            $table->string('created_user_name')->nullable()->comment('创建人');
            $table->timestamp('created_time')->nullable()->comment('创建时间')->nullable();
            $table->integer('updated_user_id')->nullable()->comment('修改人id');
            $table->string('updated_user_name')->nullable()->comment('修改人');
            $table->timestamp('updated_time')->nullable()->comment('修改时间')->nullable();
//            $table->integer('reviewed_user_id')->nullable()->comment('审核人id');
//            $table->string('reviewed_user_name')->nullable()->comment('审核人');
//            $table->timestamp('reviewed_updated_at')->nullable()->default(null)->nullable();
//            $table->boolean('is_review')->nullable()->comment('生效状态0-未审核1-已审核');
//            $table->boolean('status')->nullable()->comment('生效状态:null:草稿,-1:退签,0:未办理,1:同意');

            $table->integer("process0_user_id")->comment("申请人id");
//            $table->string('process1_users_name')->comment("申请人名字");
            $table->string('process0_status')->default(0)->comment("申请状态(默认为0)-1:退签,0:未办理,1:同意");
            $table->dateTime('process0_time')->nullable()->comment('申请时间,null时无申请时间');
            $table->integer('process1_user_id')->nullable()->comment('审核人id');
//            $table->string('process2_users_name')->nullable()->comment('商务会签人名字');
            $table->string('process1_status')->nullable()->comment("申请状态null:未到该审批步骤,-1:退签,0:未办理,1:同意");
            $table->dateTime('process1_time')->nullable()->comment('审核时间');
            $table->smallInteger('status')->comment('0:启用1:禁止');
            $table->softDeletes();
            $table->timestamps();
            $table->engine = 'InnoDB';
        });
        DB::statement("ALTER TABLE `customer_suppliers` comment'客户供应商'"); // 表注释

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_suppliers');
    }
}
