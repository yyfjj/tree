<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
/**
 * App\CustomerSupplier
 *
 * @property int $id
 * @property string $name 公司/个人全程
 * @property string $name_abbreviation 公司/个人缩写
 * @property string $name_code 公司/个人代码
 * @property string|null $tax_identification_number 纳税人识别号
 * @property string|null $contact 联系人
 * @property string|null $id_card_number 身份证号码
 * @property string|null $tel_area_code 固定电话区号
 * @property string|null $tel 固定电话
 * @property string|null $mobile 手机号
 * @property int|null $city_id
 * @property string|null $address
 * @property string|null $email
 * @property string|null $logistics_role 1-委托人、2-船公司、3-订舱公司、4-换单公司、5-货代公司、6-车队、7-保险公司、8-仓储公司、9-铁路公司、10-开证公司、11-提箱公司、12-还箱公司、13-检测公司、14-消毒公司、15-蒸熏公司、16-理货公司、17-装卸公司、18-其他
 * @property string|null $currency 币种
 * @property int|null $is_customer 是否是客户
 * @property int|null $is_supplier 是否是供应商
 * @property int|null $is_invoice 结算单位标志，就是按照是否需要开发票来判断
 * @property string|null $bank_name 开户行
 * @property string|null $bank_account 开户行账号
 * @property int|null $pay_max_time 在最多多少天内付款
 * @property int|null $receive_max_time 在最多多少天内收款
 * @property int|null $credit_max_money 信控金额，单位分
 * @property int|null $credit_max_time 信控宽限天数，单位天
 * @property int|null $created_user_id 创建人id
 * @property string|null $created_user_name 创建人
 * @property string|null $created_time 创建时间
 * @property int|null $updated_user_id 修改人id
 * @property string|null $updated_user_name 修改人
 * @property string|null $updated_time 修改时间
 * @property int $process0_user_id 申请人id
 * @property string $process0_status 申请状态(默认为0)-1:退签,0:未办理,1:同意
 * @property string|null $process0_time 申请时间,null时无申请时间
 * @property int|null $process1_user_id 审核人id
 * @property string|null $process1_status 申请状态null:未到该审批步骤,-1:退签,0:未办理,1:同意
 * @property string|null $process1_time 审核时间
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\CustomerSupplierShipData[] $customer_supplier_ship_data
 * @property-read \App\Business $master_business
 * @property-read \App\User $route_user
 * @property-read \App\Business $segment_business
 * @property-read \App\User $ship_user
 * @property-read \App\Business $slaver_business
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CustomerSupplier newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CustomerSupplier newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\CustomerSupplier onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CustomerSupplier query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CustomerSupplier whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CustomerSupplier whereBankAccount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CustomerSupplier whereBankName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CustomerSupplier whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CustomerSupplier whereContact($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CustomerSupplier whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CustomerSupplier whereCreatedTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CustomerSupplier whereCreatedUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CustomerSupplier whereCreatedUserName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CustomerSupplier whereCreditMaxMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CustomerSupplier whereCreditMaxTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CustomerSupplier whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CustomerSupplier whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CustomerSupplier whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CustomerSupplier whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CustomerSupplier whereIdCardNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CustomerSupplier whereIsCustomer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CustomerSupplier whereIsInvoice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CustomerSupplier whereIsSupplier($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CustomerSupplier whereLogisticsRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CustomerSupplier whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CustomerSupplier whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CustomerSupplier whereNameAbbreviation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CustomerSupplier whereNameCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CustomerSupplier wherePayMaxTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CustomerSupplier whereProcess0Status($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CustomerSupplier whereProcess0Time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CustomerSupplier whereProcess0UserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CustomerSupplier whereProcess1Status($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CustomerSupplier whereProcess1Time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CustomerSupplier whereProcess1UserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CustomerSupplier whereReceiveMaxTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CustomerSupplier whereTaxIdentificationNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CustomerSupplier whereTel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CustomerSupplier whereTelAreaCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CustomerSupplier whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CustomerSupplier whereUpdatedTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CustomerSupplier whereUpdatedUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CustomerSupplier whereUpdatedUserName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\CustomerSupplier withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\CustomerSupplier withoutTrashed()
 * @mixin \Eloquent
 */
class CustomerSupplier extends Model
{
    use SoftDeletes;

    function customer_supplier_ship_data(){
        $key = array_search('船公司',\Config::get('constants.LOGISTICS_ROLE'));
        if($key === false){
            throw new \Exception('请在constants.php文件内定义船公司常量');
        }
        return $this->hasMany(CustomerSupplierShipData::class);//->whereRaw("find_in_set({$key},logistics_role)");
    }

    function segment_business(){
        return $this->belongsTo(Business::class,"segment_business_id","id");
    }

    function master_business(){
        return $this->belongsTo(Business::class,"master_business_id","id");
    }

    function slaver_business(){
        return $this->belongsTo(Business::class,"slaver_business_id","id");
    }

    function ship_user(){
        return $this->belongsTo(User::class,"ship_user_id","id");
    }

    function route_user(){
        return $this->belongsTo(User::class,"route_user_id","id");
    }
}
