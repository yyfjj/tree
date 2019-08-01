<?php

namespace App\Http\Controllers;

use App\CustomerSupplier;
use App\ReviewLog;
use Illuminate\Http\Request;
use /** @noinspection PhpUndefinedClassInspection */
    Illuminate\Support\Facades\Config;

/**
 * @group 客户供应商管理021
 */
class CustomerSupplierController extends Controller
{
    /**
     * 列表02101
     *
     * @queryParam page 第几页，默认为1
     * @queryParam per_page 每页数，默认为10
     * @queryParam search 公司/个人(全程/简称/助记码)模糊搜索
     * @queryParam logistics_role 物流角色id，可多选，中间用英文逗号分割，1-委托人、2-船公司、3-订舱公司、4-换单公司、5-货代公司、6-车队、7-保险公司、8-仓储公司、9-铁路公司、10-开证公司、11-提箱公司、12-还箱公司、13-检测公司、14-消毒公司、15-蒸熏公司、16-理货公司、17-装卸公司、18-其他
     * @queryParam is_customer 客户标志 0-否、1-是
     * @queryParam is_supplier 供应商标志 0-否、1-是
     * @queryParam is_invoice 结算单位标志 0-否、1-是
     * @queryParam created_users_id 创建人
     * @queryParam begin_created_created_at 创建时间段开始时间
     * @queryParam end_created_created_at 创建时间段结束时间
     * @queryParam updated_users_id 修改人
     * @queryParam begin_updated_updated_at 修改时间段开始时间
     * @queryParam end_updated_updated_at 修改时间段结束时间
     * @queryParam review_users_id 审核人
     * @queryParam begin_review_updated_at 审核时间段开始时间
     * @queryParam end_review_updated_at 审核时间段结束时间
     * @response {
     * "data":[{
     *  "id": 4,
     *  "status": "生效状态0-未生效、1-已生效",
     *  "name": "公司/个人全称",
     *  "name_abbreviation": "公司/个人简称",
     *  "name_code": "公司/个人助记码",
     *  "tax_identification_number": "纳税人识别号",
     *  "logistics_role": "物流角色id，多个的话，用英文逗号分割",
     *  "is_customer": "客户标志 0-否、1-是",
     *  "is_supplier": "供应商标志 0-否、1-是",
     *  "is_invoice": "结算单位标志 0-否、1-是"
     * }],
     *  "current_page": 1,
     *  "first_page_url": "http://host/api/v1/customerSupplier?page=1",
     *  "from": 1,
     *  "last_page": 5,
     *  "last_page_url": "http://host/api/v1/customerSupplier?page=5",
     *  "next_page_url": "http://host/api/v1/customerSupplier?page=2",
     *  "path": "http://host/api/v1/customerSupplier",
     *  "per_page": 10,
     *  "prev_page_url": null,
     *  "to": 10,
     *  "total": 50
     * }
     * @param Request $request
     * @return \Illuminate\Pagination\AbstractPaginator
     */
    public function index(Request $request)
    {
        $search = $request->input('search');//公司/个人(全程/简称/助记码)
        $logistics_role = $request->input('logistics_role');//物流角色
        $is_customer = $request->input('is_customer');//客户标志
        $is_supplier = $request->input('is_supplier');//供应商标志
        $is_invoice = $request->input('is_invoice');//结算单位标志
        $created_users_id = $request->input('created_users_id');//创建人
        $begin_created_created_at = $request->input('begin_created_created_at');//创建时间段
        $end_created_created_at = $request->input('end_created_created_at');
        $updated_users_id = $request->input('updated_users_id');//修改人
        $begin_updated_updated_at = $request->input('begin_updated_updated_at');//修改时间段
        $end_updated_updated_at = $request->input('end_updated_updated_at');
        $review_users_id = $request->input('review_users_id');//审核人
        $begin_review_updated_at = $request->input('begin_review_updated_at');//审核时间段
        $end_review_updated_at = $request->input('end_review_updated_at');
        $list = CustomerSupplier::query()->when($search,function ($q)use($search){
            /** @type \Illuminate\Database\Eloquent\Builder $q */
            return $q->where([["name","like","%{$search}%"],["name_abbreviation","like","%{$search}%"],["name_code","like","%{$search}%"]]);
        })->when($logistics_role,function ($q)use($logistics_role){
            /** @type \Illuminate\Database\Eloquent\Builder $q */
            return $q->whereRaw("find_in_set({$logistics_role},logistics_role)");
        })->when($is_customer,function ($q)use($is_customer){
            /** @type \Illuminate\Database\Eloquent\Builder $q */
            return $q->where("is_customer",$is_customer);
        })->when($is_supplier,function ($q)use($is_supplier){
            /** @type \Illuminate\Database\Eloquent\Builder $q */
            return $q->where("is_supplier",$is_supplier);
        })->when($is_invoice,function ($q)use($is_invoice){
            /** @type \Illuminate\Database\Eloquent\Builder $q */
            return $q->where("is_invoice",$is_invoice);
        })->when($created_users_id,function ($q)use($created_users_id){
            /** @type \Illuminate\Database\Eloquent\Builder $q */
            return $q->where("created_users_id",$created_users_id);
        })->when($begin_created_created_at,function ($q)use($begin_created_created_at){
            /** @type \Illuminate\Database\Eloquent\Builder $q */
            return $q->where("created_created_at",">=",$begin_created_created_at);
        })->when($end_created_created_at,function($q)use($end_created_created_at){
            /** @type \Illuminate\Database\Eloquent\Builder $q */
            return $q->where("created_created_at","<=",$end_created_created_at);
        })->when($updated_users_id,function($q)use($updated_users_id){
            /** @type \Illuminate\Database\Eloquent\Builder $q */
            return $q->where("updated_users_id",$updated_users_id);
        })->when($begin_updated_updated_at,function($q)use($begin_updated_updated_at){
            /** @type \Illuminate\Database\Eloquent\Builder $q */
            return $q->where("updated_updated_at",">=",$begin_updated_updated_at);
        })->when($end_updated_updated_at,function($q)use($end_created_created_at){
            /** @type \Illuminate\Database\Eloquent\Builder $q */
            return $q->where("updated_updated_at","<=",$end_created_created_at);
        })->when($review_users_id,function($q)use($review_users_id){
            /** @type \Illuminate\Database\Eloquent\Builder $q */
            return $q->where("review_users_id",$review_users_id);
        })->when($begin_review_updated_at,function($q)use($begin_review_updated_at){
            /** @type \Illuminate\Database\Eloquent\Builder $q */
            return $q->where("review_updated_at",">=",$begin_review_updated_at);
        })->when($end_review_updated_at,function($q)use($end_review_updated_at){
            /** @type \Illuminate\Database\Eloquent\Builder $q */
            return $q->where("review_updated_at","<=",$end_review_updated_at);
        })->orderBy("updated_at","desc")->paginate($request->get('per_page',10));


        /** @var \Illuminate\Pagination\AbstractPaginator $list */
        $collection = $list->setCollection($list->getCollection()->map(function ($list){
            /** @var CustomerSupplier $list */
            $logistics_role = collect(explode(",",$list->logistics_role))->map(function ($item){
                return \Config::get('constants.LOGISTICS_ROLE.'.$item);
            })->join(",");
            $data = [
                'id'=>$list->id,
                'status'=>$list->status ?? '',
                'name' =>$list->name ?? '',
                'name_abbreviation' => $list->name_abbreviation ?? '',
                'name_code' => $list->name_code ?? '',
                'tax_identification_number' => $list->tax_identification_number ?? '',
//                'logistics_role_id'=>$list->logistics_roAZle ?? "",
                'logistics_role' => $logistics_role ?? '',
                'is_customer'   => $list->is_customer ?? '',
                'is_supplier'   => $list->is_supplier ?? '',
                'is_invoice'   => $list->is_invoice ?? '',
            ];
            return $data;
        }));
        return $collection;
    }

    /**
     * 插入02102
     *
     * @bodyParam name string required 公司/个人全称
     * @bodyParam name_abbreviation string required 公司/个人简称
     * @bodyParam name_code required 公司/个人助记码
     * @bodyParam tax_identification_number string required 公司纳税人识别号
     * @bodyParam contact string 联系人
     * @bodyParam id_card_number string 身份证号
     * @bodyParam tel_area_code string 电话区号
     * @bodyParam tel string 电话
     * @bodyParam mobile string 手机号
     * @bodyParam city_id int 城市自增id
     * @bodyParam address string 地址
     * @bodyParam email string email
     * @bodyParam status int 0:禁止1:启用 Example:1
     * @bodyParam logistics_role string 物流角色，数字用英文逗号分割
     * @bodyParam currency string 货币单位，CNY或者USB. Example: CNY
     * @bodyParam is_customer int 0-不是客户 1-是客户
     * @bodyParam is_supplier int 0-不是供应商 1-是供应商
     * @bodyParam is_invoice int 0-不是结算单位 1-是结算单位
     * @bodyParam bank_name string 银行名称
     * @bodyParam bank_account string 银行账号
     * @bodyParam pay_max_time int 最多多少天内付款，值为15,30,45,60
     * @bodyParam receive_max_time int 最多多少天内收款，值为15,30,45,60
     * @bodyParam credit_max_money int 信控金额
     * @bodyParam credit_max_time int 信控宽限天数
     * @response {
     * }
     * @param Request $request
     * @return array
     */
    public function store(Request $request)
    {
        $customerSupplier = new CustomerSupplier();
        $request->has('name') && $request->input('name') && $customerSupplier->name = $request->input('name');
        $request->has('name_abbreviation') && $request->input('name_abbreviation') && $customerSupplier->name_abbreviation = $request->input('name_abbreviation');
        $request->has('name_code') && $request->input('name_code') && $customerSupplier->name_code = $request->input('name_code');
        $request->has('tax_identification_number') && $request->input('tax_identification') && $customerSupplier->tax_identification_number = $request->input('tax_identification_number');
        $request->has('contact') && $request->input('contact') && $customerSupplier->contact = $request->input('contact');
        $request->has('id_card_number') && $request->input('id_card_number') && $customerSupplier->id_card_number = $request->input('id_card_number');
        $request->has('tel_area_code') && $request->input('tel_area_code') && $customerSupplier->tel_area_code = $request->input('tel_area_code');
        $request->has('tel') && $request->input('tel') && $customerSupplier->tel = $request->input('tel');
        $request->has('mobile') && $request->input('mobile') && $customerSupplier->mobile = $request->input('mobile');
        $request->has('city_id') && $request->input('city_id') && $customerSupplier->city_id = $request->input('city_id');
        $request->has('address') && $request->input('address') && $customerSupplier->address = $request->input('address');
        $request->has('email') && $request->input('email') && $customerSupplier->email = $request->input('email');
        $request->has('logistics_role') && $request->input('logistics_role') && $customerSupplier->logistics_role = $request->input('logistics_role');
        $request->has('currency') && $request->input('currency') && $customerSupplier->currency = $request->input('currency');
        $request->has('is_customer') && $request->input('is_customer') && $customerSupplier->is_customer = $request->input('is_customer');
        $request->has('is_supplier') && $request->input('is_supplier') && $customerSupplier->is_supplier = $request->input('is_supplier');
        $request->has('is_invoice') && $request->input('is_invoice') && $customerSupplier->is_invoice = $request->input('is_invoice');
        $request->has('bank_name') && $request->input('bank_name') && $customerSupplier->bank_name = $request->input('bank_name');
        $request->has('bank_account') && $request->input('bank_account') && $customerSupplier->bank_account = $request->input('bank_account');
        $request->has('pay_max_time') && $request->input('pay_max_time') && $customerSupplier->pay_max_time = $request->input('pay_max_time');
        $request->has('receive_max_time') && $request->input('receive_max_time') && $customerSupplier->receive_max_time = $request->input('receive_max_time');
        $request->has('credit_max_money') && $request->input('credit_max_money') && $customerSupplier->credit_max_money = $request->input('credit_max_money');
        $request->has('credit_max_time') && $request->input('credit_max_time') && $customerSupplier->credit_max_time = $request->input('credit_max_time');
        $request->has('created_user_id') && $request->input('created_user_id') && $customerSupplier->created_user_id = $request->input('created_user_id');
        $request->has('created_user_name') && $request->input('created_user_name') && $customerSupplier->created_user_name = $request->input('created_user_name');
        $request->has('created_time') && $request->input('created_time') && $customerSupplier->created_time = $request->input('created_time');
        $request->has('updated_user_id') && $request->input('updated_user_id') && $customerSupplier->updated_user_id = $request->input('updated_user_id');
        $request->has('updated_user_name') && $request->input('updated_user_name') && $customerSupplier->updated_user_name = $request->input('updated_user_name');
        $request->has('updated_time') && $request->input('updated_time') && $customerSupplier->updated_time = $request->input('updated_time');
        $customerSupplier->process0_user_id = $request->get('user_id');
        $customerSupplier->process0_status = 0;
        $customerSupplier->process0_time = \date('Y-m-d H:i:s');
//        $request->has('reviewed_user_id') && $request->input('reviewed_user_id') && $customerSupplier->reviewed_user_id = $request->input('reviewed_user_id');
//        $request->has('reviewed_user_name') && $request->input('reviewed_user_name') && $customerSupplier->reviewed_user_name = $request->input('reviewed_user_name');
//        $request->has('reviewed_updated_at') && $request->input('reviewed_updated_at') && $customerSupplier->reviewed_updated_at = $request->input('reviewed_updated_at');
//        $request->has('status') && $request->input('status') && $customerSupplier->status = $request->input('status');
//        $request->has('is_review') && $request->input('is_review') && $customerSupplier->is_review = $request->input('is_review');
        $customerSupplier->save();
        return [];
    }

    /**
     * 复制02103
     *
     * @queryParam customerSupplier int 客户供应商id
     * @response {
     * }
     * @param CustomerSupplier $customerSupplier
     * @return array
     */
    function copy(CustomerSupplier $customerSupplier){
        $c = $customerSupplier->toArray();
        unset($c['id']);//todo-benjamin 还需去掉一些值
        CustomerSupplier::query()->insertGetId($c);
        return [];
    }

    /**
     * 提交审核02104
     * 只有发起者有这个动作
     * @queryParam customerSupplier required int客户供应商id
     * @response {
     * }
     * @param Request $request
     * @param CustomerSupplier $customerSupplier
     * @return array
     * @throws \Exception
     */
    function preview(Request $request,CustomerSupplier $customerSupplier){
        $role_id = $request->get('role_id');
        $user_id = $request->get('user_id');

        #当前角色，是否可提交审批
        /** @noinspection PhpUndefinedClassInspection */
        $role_ids = array_keys(Config::get('constants.CUSTOMER_SUPPLIER_REVIEW'));
        //登录角色所处的审批流程位置
        $process_location = array_search($role_id,$role_ids);

        if($process_location > 0){
            throw new \Exception("只有销售部业务员才能发起提交审核");
        }

        //<editor-fold desc="登录角色提交当前审批">
        $can = [0,-1];
        if($process_location === 0){
            if(in_array($customerSupplier->process0_status, $can) ){
                $customerSupplier->process0_status = 1;
                $customerSupplier->process0_time   = date('Y-m-d H:i:s');
                $customerSupplier->process0_user_id= $user_id;
            }else{
                throw new \Exception("做为申请人的您，不可提交审批");
            }
        }
        $customerSupplier->save();

        $this->_preview($request,$customerSupplier);

        return [];
        //</editor-fold>
    }

    private function _preview(Request $request,CustomerSupplier $customerSupplier){
        /** @noinspection PhpUndefinedClassInspection */
        $data = ReviewLog::query()->updateOrInsert(
            [
                'model'=>'customer_suppliers',
                'foreign_key'=>$customerSupplier->id,
                'role_id'=>$request->get('role_id'),
                'status'=>0
            ],
            [
                'user_id'=>$request->get('user_id'),
                'name'=>Config::get('constants.CUSTOMER_SUPPLIER_REVIEW')[$request->get('role_id')],
                'suggestion'=>$request->input('suggestion'),
                'status'=>1,
            ]);
        return $data;
    }

    private function _process_location(Request $request){
        $role_id = $request->get('role_id');

        #当前角色，是否可提交审批
        /** @noinspection PhpUndefinedClassInspection */
        $role_ids = array_keys(Config::get('constants.REVIEW'));
        //登录角色所处的审批流程位置
        $process_location = array_search($role_id,$role_ids);
        return $process_location;
    }
    /**
     * 审核02105
     *
     * @queryParam customerSupplier required 客户供应商id
     * @queryParam status required 审核 -1-审核不通过、1-审核通过
     * @response {
     * }
     * @param Request $request
     * @param CustomerSupplier $customerSupplier
     * @return array
     * @throws \Exception
     */
    function review(Request $request,CustomerSupplier $customerSupplier){
        $process_location = $this->_process_location($request);

        if($process_location === 0){
            throw new \Exception("你没有审批权限");
        }elseif ($process_location === 1){
            if ($customerSupplier->process0_status === "1"){
                $customerSupplier->process1_status = $request->input('status');
                $customerSupplier->process1_time    = \date("Y-m-d H:i:s");
                $customerSupplier->process1_user_id = $request->get('user_id');
            }else{
                throw new \Exception("作为商务会签人的您，无法有效审核");
            }
        }else{
            throw new \Exception("客户供应商审批错误");
        }

        if($request->input('status') == -1){
            $customerSupplier->process0_status  = -1;
            $customerSupplier->process0_time    = null;
            $customerSupplier->process1_status  = 0;
            $customerSupplier->process1_user_id = null;
            $customerSupplier->process1_time    = null;
        }

        $customerSupplier->save();

        $this->_review($request,$customerSupplier,$process_location);

        return [];
    }

    private function _review(Request $request,CustomerSupplier $customerSupplier,$process_location){
        /** @noinspection PhpUndefinedClassInspection */
        $role_ids = array_keys(Config::get('constants.CUSTOMER_SUPPLIER_REVIEW'));
        $role_id = $role_ids[$process_location];
        /** @noinspection PhpUndefinedClassInspection */
        $data = ReviewLog::query()->updateOrInsert(
            [
                'model'=>'customer_suppliers',
                'foreign_key'=>$customerSupplier->id,
                'role_id'=>$role_id,
                'status'=>0
            ],
            [
                'user_id'=>$request->get('user_id'),
                'name'=>Config::get('constants.CUSTOMER_SUPPLIER_REVIEW')[$request->get('role_id')],
                'status'=>$request->input('status'),
                'suggestion'=>$request->input('suggestion'),
            ]);
        return $data;
    }

    /**
     * 显示02106
     * @queryParams customerSupplier required 客户供应商id
     * @response {
     *  "id":4,
     *  "name":"公司/个人全称",
     *  "name_abbreviation": "公司/个人简称",
     *  "name_code":"公司/个人助记码",
     *  "tax_identification_number":"公司纳税人识别号",
     *  "contact":"联系人",
     *  "id_card_number":"身份证号",
     *  "tel_area_code":"电话区号",
     *  "tel":"电话",
     *  "mobile":"手机号",
     *  "cities":"城市自增id",
     *  "address":"地址",
     *  "email":"email",
     *  "logistics_role":"物流角色id，数字用英文逗号分割",
     *  "currency":[{
     *       "english":"CNY",
     *       "chinese":"人民币"
     * },{
     *      "english":"USB",
     *      "chinese":"美元"
     * }
     * ],
     *  "is_customer":"0-不是客户 1-是客户",
     *  "is_supplier": "0-不是供应商 1-是供应商",
     *  "is_invoice":"0-不是结算单位 1-是结算单位",
     *  "bank_name":"银行名称",
     *  "bank_account":"银行账号",
     *  "pay_max_time":[{
     *      "key":15,
     *      "item":"15天内付款",
     *      "is_selected":1
     * },{
     *      "key":30,
     *      "item":"30天内付款",
     *      "is_selected":0
     * },{
     *      "key":45,
     *      "item":"45天内付款",
     *      "is_selected":1
     * },{
     *      "key":60,
     *      "item":"50天内付款",
     *      "is_selected":0
     * }],
     *  "receive_max_time":[{
     *      "key":15,
     *      "item":"15天内收款",
     *      "is_selected":1
     * },{
     *      "key":30,
     *      "item":"30天内收款",
     *      "is_selected":0
     * },{
     *      "key":45,
     *      "item":"45天内收款",
     *      "is_selected":1
     * },{
     *      "key":60,
     *      "item":"50天内收款",
     *      "is_selected":0
     * }],
     *  "credit_max_money":"信控金额",
     *  "credit_max_time":"信控宽限天数",
     *  "created_users_name":"创建人",
     *  "crated_at":"创建时间",
     *  "updated_users_name":"修改人",
     *  "updated_updated_at":"修改时间",
     *  "reviewed_users_name":"审核人",
     *  "reviewed_updated_at":"审核时间"
     * }
     *
     * @param CustomerSupplier $customerSupplier
     * @return CustomerSupplier
     */
    public function show(CustomerSupplier $customerSupplier)
    {
        $logistics_role_refactor = collect(\Config::get('constants.LOGISTICS_ROLE'))->map(function($item,$key)use($customerSupplier){
            $data = [];
            $data['key'] = $key;
            $data['item'] = $item;
            $data['is_selected'] = $key == $customerSupplier->logistics_role ? 1 : 0;
            return $data;
        })->toArray();
        $customerSupplier->logistics_role = array_values($logistics_role_refactor);

        $receive_max_time_refactor = collect(\Config::get('constants.RECEIVE_MAX_TIME'))->map(function($item,$key)use($customerSupplier){
            $data = [];
            $data['key'] = $key;
            $data['item'] = $item;
            $data['is_selected'] = $key == $customerSupplier->receive_max_time ? 1 : 0;
            return $data;
        })->toArray();
        $customerSupplier->receive_max_time = array_values($receive_max_time_refactor);

        $pay_max_time_refactor = collect(\Config::get('constants.PAY_MAX_TIME'))->map(function($item,$key)use($customerSupplier){
            $data = [];
            $data['key'] = $key;
            $data['item'] = $item;
            $data['is_selected'] = $key == $customerSupplier->pay_max_time ? 1 : 0;
            return $data;
        })->toArray();
        $customerSupplier->pay_max_time = array_values($pay_max_time_refactor);

        $currency_refactor = collect(\Config::get('constants.CURRENCY'))->map(function($item,$key)use($customerSupplier){
            $data = [];
            $data['key'] = $key;
            $data['item'] = $item;
            $data['is_selected'] = $key == $customerSupplier->currency ? 1 : 0;
            return $data;
        })->toArray();
        $customerSupplier->currency = array_values($currency_refactor);

        return $customerSupplier;
    }

    /**
     * 更新02107
     *
     * @bodyParam customerSupplier int required 客户供应商id
     * @bodyParam name string required 公司/个人全称
     * @bodyParam name_abbreviation string required 公司/个人简称
     * @bodyParam name_code required 公司/个人助记码
     * @bodyParam tax_identification_number string required 公司纳税人识别号
     * @bodyParam contact string 联系人
     * @bodyParam id_card_number string 身份证号
     * @bodyParam tel_area_code string 电话区号
     * @bodyParam tel string 电话
     * @bodyParam mobile string 手机号
     * @bodyParam cities int 城市自增id
     * @bodyParam address string 地址
     * @bodyParam email string email
     * @bodyParam logistics_role string 物流角色，数字用英文逗号分割
     * @bodyParam currency string 货币单位，CNY或者USB
     * @bodyParam is_customer int 0-不是客户 1-是客户
     * @bodyParam is_supplier int 0-不是供应商 1-是供应商
     * @bodyParam is_invoice int 0-不是结算单位 1-是结算单位
     * @bodyParam bank_name string 银行名称
     * @bodyParam bank_account string 银行账号
     * @bodyParam pay_max_time int 最多多少天内付款，值为15,30,45,60
     * @bodyParam receive_max_time int 最多多少天内收款，值为15,30,45,60
     * @bodyParam credit_max_money int 信控金额
     * @bodyParam credit_max_time int 信控宽限天数
     * @response {
     * }
     * @param Request $request
     * @param CustomerSupplier $customerSupplier
     * @return array
     */
    public function update(Request $request, CustomerSupplier $customerSupplier)
    {
        $request->has('name') && $request->input('name') && $customerSupplier->name = $request->input('name');
        $request->has('name_abbreviation') && $request->input('name_abbreviation') && $customerSupplier->name_abbreviation = $request->input('name_abbreviation');
        $request->has('name_code') && $request->input('name_code') && $customerSupplier->name_code = $request->input('name_code');
        $request->has('tax_identification_number') && $request->input('tax_identification') && $customerSupplier->tax_identification_number = $request->input('tax_identification_number');
        $request->has('contact') && $request->input('contact') && $customerSupplier->contact = $request->input('contact');
        $request->has('id_card_number') && $request->input('id_card_number') && $customerSupplier->id_card_number = $request->input('id_card_number');
        $request->has('tel_area_code') && $request->input('tel_area_code') && $customerSupplier->tel_area_code = $request->input('tel_area_code');
        $request->has('tel') && $request->input('tel') && $customerSupplier->tel = $request->input('tel');
        $request->has('mobile') && $request->input('mobile') && $customerSupplier->mobile = $request->input('mobile');
        $request->has('city_id') && $request->input('city_id') && $customerSupplier->city_id = $request->input('city_id');
        $request->has('address') && $request->input('address') && $customerSupplier->address = $request->input('address');
        $request->has('email') && $request->input('email') && $customerSupplier->email = $request->input('email');
        $request->has('logistics_role') && $request->input('logistics_role') && $customerSupplier->logistics_role = $request->input('logistics_role');
        $request->has('currency') && $request->input('currency') && $customerSupplier->currency = $request->input('currency');
        $request->has('is_customer') && $request->input('is_customer') && $customerSupplier->is_customer = $request->input('is_customer');
        $request->has('is_supplier') && $request->input('is_supplier') && $customerSupplier->is_supplier = $request->input('is_supplier');
        $request->has('is_invoice') && $request->input('is_invoice') && $customerSupplier->is_invoice = $request->input('is_invoice');
        $request->has('bank_name') && $request->input('bank_name') && $customerSupplier->bank_name = $request->input('bank_name');
        $request->has('bank_account') && $request->input('bank_account') && $customerSupplier->bank_account = $request->input('bank_account');
        $request->has('pay_max_time') && $request->input('pay_max_time') && $customerSupplier->pay_max_time = $request->input('pay_max_time');
        $request->has('receive_max_time') && $request->input('receive_max_time') && $customerSupplier->receive_max_time = $request->input('receive_max_time');
        $request->has('credit_max_money') && $request->input('credit_max_money') && $customerSupplier->credit_max_money = $request->input('credit_max_money');
        $request->has('credit_max_time') && $request->input('credit_max_time') && $customerSupplier->credit_max_time = $request->input('credit_max_time');
        $request->has('created_user_id') && $request->input('created_user_id') && $customerSupplier->created_user_id = $request->input('created_user_id');
        $request->has('created_user_name') && $request->input('created_user_name') && $customerSupplier->created_user_name = $request->input('created_user_name');
        $request->has('created_created_at') && $request->input('created_created_at') && $customerSupplier->created_created_at = $request->input('created_created_at');
        $request->has('updated_user_id') && $request->input('updated_user_id') && $customerSupplier->updated_user_id = $request->input('updated_user_id');
        $request->has('updated_user_name') && $request->input('updated_user_name') && $customerSupplier->updated_user_name = $request->input('updated_user_name');
        $request->has('updated_updated_at') && $request->input('updated_updated_at') && $customerSupplier->updated_updated_at = $request->input('updated_updated_at');
        $request->has('reviewed_user_id') && $request->input('reviewed_user_id') && $customerSupplier->reviewed_user_id = $request->input('reviewed_user_id');
        $request->has('reviewed_user_name') && $request->input('reviewed_user_name') && $customerSupplier->reviewed_user_name = $request->input('reviewed_user_name');
        $request->has('reviewed_updated_at') && $request->input('reviewed_updated_at') && $customerSupplier->reviewed_updated_at = $request->input('reviewed_updated_at');
        $request->has('status') && $request->input('status') && $customerSupplier->status = $request->input('status');
        $request->has('is_review') && $request->input('is_review') && $customerSupplier->is_review = $request->input('is_review');
        $customerSupplier->save();
        return [];
    }

    /**
     * 删除02108
     * @queryParam customerSupplier required 客户供应商id
     * @response {
     * }
     * @param CustomerSupplier $customerSupplier
     * @return array
     */
    public function destroy(CustomerSupplier $customerSupplier)
    {
        CustomerSupplier::destroy($customerSupplier->id);
        return [];
    }

    /**
     * 物流角色列表02109
     *
     * @response {
     *  "1":"委托人",
     *  "2": "船公司",
     *  "3": "订舱公司",
     *  "4": "换单公司",
     *  "5": "货代公司",
     *  "6": "车队",
     *  "7": "保险公司",
     *  "8": "仓储公司",
     *  "9": "铁路公司",
     *  "10": "开证公司",
     *  "11": "提箱公司",
     *  "12": "还箱公司",
     *  "13": "检测公司",
     *  "14": "消毒公司",
     *  "15": "蒸熏公司",
     *  "16": "理货公司",
     *  "17": "装卸公司",
     *  "18": "其他"
     * }
     *
     * @return mixed
     */
    public function logisticsRole(){
        /** @noinspection PhpUndefinedClassInspection */
        return Config::get('constants.LOGISTICS_ROLE');
    }

}
