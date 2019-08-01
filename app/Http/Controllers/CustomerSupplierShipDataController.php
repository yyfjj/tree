<?php

namespace App\Http\Controllers;

use App\CustomerSupplier;
use App\CustomerSupplierShipData;
use App\CustomerSupplierShipDataData;
use Illuminate\Http\Request;
use /** @noinspection PhpUndefinedClassInspection */
    Illuminate\Support\Facades\DB;

/**
 * @group 船公司029
 */
class CustomerSupplierShipDataController extends Controller
{
    /**
     * 列表02901
     *
     * @queryParam page int 第几页，默认第一页
     * @queryParam per_page int 每页记录数，默认是10
     * @queryParam search string 模糊搜索
     * @queryParam status int 状态0:禁用，1:启用
     * @queryParam company_id int 船公司id
     * @queryParam ship_id int 航名id
     * @queryParam route_id int 航线id
     * @queryParam user_id int 操作人id
     * @queryParam segment_business_id int 业务板块id
     * @queryParam master_business_id int 主业务id
     * @queryParam slaver_business_id int 子业务id
     * @response {
     * "data":[{
     *  "customer_supplier_id": "船公司id",
     *  "customer_supplier_name": "船公司名称",
     *  "parent_id":"船名id",
     *  "parent_name":"船名",
     *  "parent_status":"船状态0:禁止,1:启用",
     *  "id":"船名id",
     *  "name":"船名",
     *  "status":"船状态0:禁止,1:启用",
     *  "user_name":"操作人",
     *  "time":"操作日期"
     * }],
     *  "current_page": 1,
     *  "first_page_url": "http://host/api/v1/shipCompanies?page=1",
     *  "from": 1,
     *  "last_page": 5,
     *  "last_page_url": "http://host/api/v1/shipCompanies?page=5",
     *  "next_page_url": "http://host/api/v1/shipCompanies?page=2",
     *  "path": "http://host/api/v1/shipCompanies",
     *  "per_page": 10,
     *  "prev_page_url": null,
     *  "to": 10,
     *  "total": 50
     * }
     * @param Request $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Pagination\AbstractPaginator
     * @throws \Exception
     */
    public function index(Request $request)
    {
        $key = array_search('船公司',\Config::get('constants.LOGISTICS_ROLE'));
        if($key === false){
            throw new \Exception('请在constants.php文件内定义船公司常量');
        }

        $list = CustomerSupplier::query()
            ->select([
                "customer_suppliers.id as customer_supplier_id",
                "customer_suppliers.name as customer_supplier_name",
                "parent.id as parent_id",
                "parent.name as parent_name",
                "parent.status as parent_status",
                "parent.updated_at as parent_updated_at",
                "son.id as id",
                "son.name as name",
                "son.status as status",
                "son.updated_at as son_updated_at",
                "data.segment_business_id as segment_business_id",
                "data.master_business_id as master_business_id",
                "data.slaver_business_id as slaver_business_id",
                "parent.user_id as ship_user_id",
                "son.user_id as route_user_id",
            ])
            ->leftJoinSub("select * from customer_supplier_ship_data where parent_id=0","parent",'customer_suppliers.id','=','parent.customer_supplier_id')
            ->leftJoin('customer_supplier_ship_data as son','parent.id','=','son.parent_id')
            ->leftJoin('customer_supplier_ship_data_data as data','son.id','=','data.customer_supplier_ship_data_id')
            ->with(['segment_business','master_business','slaver_business','ship_user','route_user'])
            ->whereRaw("find_in_set({$key},logistics_role)")
            ->when($request->input('search'),function ($q)use($request){
                /** @type \Illuminate\Database\Eloquent\Builder $q */
                $q->where("customer_suppliers.name","like","%{$request->input('search')}%");
            })
            ->when($request->has('status') && $request->input('status') !== '',function($q)use($request){
                /** @type \Illuminate\Database\Eloquent\Builder $q */
                $status = $request->input('status');
                $q->whereRaw("((parent.status={$status} and son.status is null) or (son.status={$status}))");
            })
            //船公司
            ->when($request->has('company_id') && $request->input('company_id'),function ($q)use($request){
                /** @type \Illuminate\Database\Eloquent\Builder $q */
                $q->where('customer_suppliers.id',$request->input('company_id'));
            })
            //船
            ->when($request->has('ship_id') && $request->input('ship_id'),function($q)use($request){
                /** @type \Illuminate\Database\Eloquent\Builder $q */
                $q->where('parent.id',$request->input('ship_id'));
            })
            //航线
            ->when($request->has('route_id') && $request->input('route_id'),function($q)use($request){
                /** @type \Illuminate\Database\Eloquent\Builder $q */
                $q->where('son.id',$request->input('route_id'));
            })
            //操作人
            ->when($request->has('user_id') && $request->input('user_id'),function($q)use($request){
                /** @type \Illuminate\Database\Eloquent\Builder $q */
                $user_id = $request->input('user_id');
                $q->whereRaw("((parent.user_id={$user_id} and son.user_id is null) or (son.user_id={$user_id}))");
            })
            //业务板块
            ->when($request->input('segment_business_id'),function ($q)use($request){
                /** @type \Illuminate\Database\Eloquent\Builder $q */
                $q->where("data.segment_business_id",$request->input('segment_business_id'));
            })
            //主业务板块
            ->when($request->input('master_business_id'),function ($q)use($request){
                /** @type \Illuminate\Database\Eloquent\Builder $q */
                $q->where("data.master_business_id",$request->input('master_business_id'));
            })
            //子业务板块
            ->when($request->input('slaver_business_id'),function ($q)use($request){
                /** @type \Illuminate\Database\Eloquent\Builder $q */
                $q->where("data.slaver_business_id",$request->input('slaver_business_id'));
            })
            ->orderBy("customer_suppliers.id","desc")
            ->paginate($request->get('per_page',10));
        /** @var \Illuminate\Pagination\AbstractPaginator $list */
        $list = $list->setCollection($list->getCollection()->map(function ($item){
            $data = [];
            $data['customer_supplier_id'] = data_get($item,'customer_supplier_id');
            $data['customer_supplier_name'] = data_get($item,'customer_supplier_name');
            $data['parent_id'] = data_get($item,'parent_id');
            $data['parent_name'] = data_get($item,'parent_name');
            $data['parent_status'] = data_get($item,'parent_status');
            $data['id'] = data_get($item,'id');
            $data['name'] = data_get($item,'name');
            $data['status'] = data_get($item,'status');
            $data['user_name'] = data_get($item,'route_user.name',data_get($item,'ship_user.name'));
            $data['time'] = data_get($item,'son_updated_at',data_get($item,'parent_updated_at'));
            return $data;
        }));

        return $list;
    }

    /**
     * 插入/更新航名/航次02902
     *
     * parent_name和parent_id必须传一个,传parent_name表示新增，传parent_id表示修改
     * name和id必须传一个,传name表示新增，传id表示修改
     * @jsonParam customer_supplier_id int required 客户供应商id
     * @jsonParam parent_name string 船名称
     * @jsonParam parent_id int 船id
     * @jsonParam parent_status int 船0:禁用,1:启用(默认)
     * @jsonParam name string 航线名称
     * @jsonParam id int 航线id
     * @jsonParam status int 航线0:禁用,1:启用(默认).Example:1
     *
     * @param Request $request
     * @return array
     */
    public function updateOrInsert(Request $request)
    {
        $ship = function ($input,$user_id){
            if(isset($input['parent_id'])){
                $c = CustomerSupplierShipData::find($input['parent_id']);
            }else{
                $c = new CustomerSupplierShipData();
            }
            $c->user_id = $user_id;
            $c->parent_id = 0;
            $c->customer_supplier_id = $input['customer_supplier_id'];
            $c->name = $input['parent_name'];
            $c->save();
            return $c->id;
        };

        $route = function ($request,$parent_id,$user_id){
            $data = [];
            $data['parent_id'] = $parent_id;
            if(isset($request['id'])){
                $c = CustomerSupplierShipData::find($request['id']);
            }else{
                $c = new CustomerSupplierShipData();
            }
            $c->parent_id = $parent_id;
            $c->user_id = $user_id;
            $c->customer_supplier_id = $request['customer_supplier_id'];
            $c->name = $request['name'];
            $c->save();
            return $c->id;
        };

        /** @noinspection PhpUndefinedClassInspection */
        DB::transaction(function () use($ship,$route,$request){
            $data = $request->getContent();
            $data = json_decode($data,true);
            collect($data)->map(function($item)use($request,$ship,$route){
                $parent_id = $ship($item,$request->get('user_id'));
                $route($item,$parent_id,$request->get('user_id'));
            });
        });

        return [];
    }

    /**
     * 删除船、航线02903
     * @urlParam customerSupplierShipData required 船id\航次id,多个用英文逗号分隔
     * @response {
     * }
     * @param CustomerSupplierShipData $customerSupplierShipData
     * @return array
     */
    public function destroy($customerSupplierShipData)
    {
        $ids = explode(",", $customerSupplierShipData);
        /** @noinspection PhpUndefinedClassInspection */
        DB::transaction(function () use($ids){
            CustomerSupplierShipData::destroy($ids);
            CustomerSupplierShipDataData::query()->whereIn('customer_supplier_ship_data_id',$ids)->delete();
        });

        return [];
    }

    /**
     * 插入/更新业务板块关联信息02904
     *
     * @jsonParam customer_supplier_ship_data_data_id int required 航次和业务板块关联表id
     * @jsonParam customer_supplier_ship_data_id int 船公司船名航次与业务板块类型关系id
     * @jsonParam segment_business_id int 业务板块id
     * @jsonParam master_business_id int 主业务id
     * @jsonParam slaver_business_id int 子业务id
     *
     * @param Request $request
     * @return array
     */
    public function businessUpdateOrInsert(Request $request){

        $updateOrInsert = function ($input,$user_id){
            //更新
            if(isset($input['customer_supplier_ship_data_data_id'])){
                $c = CustomerSupplierShipDataData::find($input['customer_supplier_ship_data_data_id']);
            }else{//插入
                $c = new CustomerSupplierShipDataData();
            }

            $c->user_id = $user_id;
            $c->customer_supplier_ship_data_id = $input['customer_supplier_ship_data_id'];
            isset($input['segment_business_id']) && $c->segment_business_id = $input['segment_business_id'];
            isset($input['master_business_id']) && $c->master_business_id = $input['master_business_id'];
            isset($input['slaver_business_id']) && $c->slaver_business_id = $input['slaver_business_id'];
            $c->save();
            return $c;
        };

        /** @noinspection PhpUndefinedClassInspection */
        DB::transaction(function () use($updateOrInsert,$request){
            $data = $request->getContent();
            $data = json_decode($data,true);
            collect($data)->map(function($item)use($request,$updateOrInsert){
                $updateOrInsert($item,$request->get('user_id'));
            });
        });

        return [];
    }

    /**
     * 删除航线下的业务板块02905
     * @urlParam customerSupplierShipData required 船id\航次id,多个用英文逗号分割
     * @response {
     * }
     * @param string $customerSupplierShipDataData
     * @return array
     */
    public function businessDestroy(string $customerSupplierShipDataData)
    {
        $ids = explode(",", $customerSupplierShipDataData);
        CustomerSupplierShipDataData::destroy($ids);
        return [];
    }

    /**
     * 业务板块关联信息02906
     * @urlParam customerSupplierShipData required 航次id
     * @response {
     *  "data":[
     *      {
     *          "segment_business_id": "业务板块id",
     *          "segment_business_name": "业务板块名称",
     *          "master_business_id": "主业务板块id",
     *          "master_business_name": "主业务板块名称",
     *          "slaver_business_id": "子业务板块id",
     *          "slaver_business_name": "子业务板块名称",
     *          "user_name": "操作人",
     *          "updated_at": "2019-07-17 15:37:11"
     *      }
     *  ]
     * }
     * @param CustomerSupplierShipData $customerSupplierShipData
     * @return CustomerSupplierShipDataData[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    function businessShow(CustomerSupplierShipData $customerSupplierShipData){
        $list = CustomerSupplierShipDataData::query()->with(['users','segment_businesses','master_businesses','slaver_businesses'])
            ->where('customer_supplier_ship_data_id',$customerSupplierShipData->id)
            ->get()->map(function ($item){
                $data=[];
                $data['segment_business_id'] = data_get($item,'segment_businesses.id');
                $data['segment_business_name'] = data_get($item,'segment_businesses.name');
                $data['master_business_id'] = data_get($item,'master_businesses.id');
                $data['master_business_name'] = data_get($item,'master_businesses.name');
                $data['slaver_business_id'] = data_get($item,'slaver_businesses.id');
                $data['slaver_business_name'] = data_get($item,'slaver_businesses.name');
                $data['user_name'] = data_get($item,'users.name');
                $data['updated_at'] = (string)data_get($item,'updated_at');
                return $data;
            });

        return $list;
    }
}
