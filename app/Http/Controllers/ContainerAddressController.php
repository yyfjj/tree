<?php

namespace App\Http\Controllers;

use App\ContainerAddress;
use App\ContainerAddressData;
use Illuminate\Http\Request;

/**
 * @group 装箱送箱地点025
 */
class ContainerAddressController extends Controller
{
    /**
     * 列表02501
     *
     * @queryParam page 第几页，默认第一页
     * @queryParam per_page 每页记录数，默认是10
     * @response {
     * "data":[{
     *  "id": 4,
     *  "address": "地址",
     *  "is_up": "是否装箱地点0:否，1:是",
     *  "is_down": "是否送箱地点0:否，1:是",
     *  "status": "0:禁用，1:启用",
     *  "container_address_data":{
     *      "segment_businesses_id":"业务板块id",
     *      "segment_businesses_name":"业务板块名称",
     *      "master_businesses_id":"主业务id",
     *      "master_businesses_name":"主业务名称",
     *      "slaver_businesses_id":"子业务id",
     *      "slaver_businesses_name":"子业务名称"
     * }
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
     * @response 404 {
     *  "message": "No query results"
     * }
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $list = ContainerAddress::query()->with(['container_address_data'=>function($q){
            /** @type \Illuminate\Database\Eloquent\Builder $q */
            $q->with(['segment_businesses','master_businesses','slaver_businesses']);
        }])->orderBy("updated_at","desc")->paginate($request->get('per_page',10));
        /** @var \Illuminate\Pagination\AbstractPaginator $list */
        $collection = $list->setCollection($list->getCollection()->map(function ($list){
            /** @var ContainerAddress $list */
            $container_address_data = collect($list->container_address_data)->map(function($item,$key){
                $data['segment_businesses_id'] = $item->segment_businesses->id;
                $data['segment_businesses_name'] = $item->segment_businesses->name;
                $data['master_businesses_id'] = $item->master_businesses->id;
                $data['master_businesses_name'] = $item->master_businesses->name;
                $data['slaver_businesses_id'] = $item->slaver_businesses->id;
                $data['slaver_businesses_name'] = $item->slaver_businesses->name;
                return $data;
            });

            $data = [
                'id'=>$list->id,
                'status'=>$list->status ?? '',
                'address' =>$list->address ?? '',
                'is_up' => $list->is_up ?? '',
                'is_down' => $list->is_down ?? '',
                'container_address_data'=>$container_address_data,

            ];
            return $data;
        }));

        return $collection;
    }

    /**
     * 新增02502
     * @bodyParam address string required 地址
     * @bodyParam is_up bool required 是否装箱地点0:是,1:否
     * @bodyParam is_down bool required 是否送箱地点0:是,1:否
     * @bodyParam segment_businesses_id int required 业务板块id，多个用数组方式
     * @bodyParam master_businesses_id int required 主业务类型id，多个用数组方式
     * @bodyParam slaver_businesses_id int required 子业务类型id，多个用数组方式
     * @response {
     * }
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        \DB::transaction(function () use($request){
            $containerAddress = new ContainerAddress();
            $request->has('address') && $request->input('address') && $containerAddress->address = $request->input('address');
            $request->has('is_up') && $request->input('is_up') && $containerAddress->is_up = $request->input('is_up');
            $request->has('is_down') && $request->input('is_down') && $containerAddress->is_down = $request->input('is_down');
            $containerAddress->save();

            collect($request->input('segment_businesses_id'))->map(function($item,$key)use($request,$containerAddress){
                $contractData = new ContainerAddressData();
                $contractData->container_addresses_id = $containerAddress->id;
                $contractData->segment_businesses_id = $item;
                $contractData->master_businesses_id  = $request->input('master_businesses_id')[$key];
                $contractData->slaver_businesses_id  = $request->input('slaver_businesses_id')[$key];
                $contractData->save();
            });
        });

        return [];
    }

    /**
     * 详情02503
     *
     * @queryParam containerAddress required 装箱送箱地点id
     * @response {
     *  "id": 4,
     *  "address": "地址",
     *  "is_up": "是否装箱地址0:否,1:是",
     *  "is_down": "是否送箱地址0:否,1:是",
     *  "status": "0:禁用,1:启用",
     *  "container_address_data":[{
     *      "segment_businesses_id":"业务板块id",
     *      "segment_businesses_name":"业务板块名称",
     *      "master_businesses_id":"主业务板块id",
     *      "master_businesses_name":"主业务板块名称",
     *      "slaver_businesses_id":"子业务板块id",
     *      "slaver_businesses_name":"子业务板块名称"
     * }]
     * }
     * @param ContainerType $containerType
     * @return ContainerType
     */
    public function show(ContainerAddress $containerAddress)
    {
        $data = ContainerAddress::query()->with(['container_address_data'=>function($q){
            /** @type \Illuminate\Database\Eloquent\Builder $q */
            $q->with(['segment_businesses','master_businesses','slaver_businesses']);
        }])->where("id",$containerAddress->id)->first();

        $return = function($data){
            $return = [];
            /** @var ContainerAddress $data */
            $return['id'] = $data->id;
            $return['address'] = $data->address;
            $return['is_up']   = $data->is_up;
            $return['is_down']   = $data->is_down;
            $return['status']   = $data->status;
            $return['container_address_data'] = collect($data->container_address_data)->map(function($item,$key){
                /** @var ContainerAddress $item */
                $data=[];
                $data['segment_businesses_id'] = data_get($item,'segment_businesses.id',null);
                $data['segment_businesses_name'] = data_get($item,'segment_businesses.name',null);
                $data['master_businesses_id'] = data_get($item,'master_businesses.id',null);
                $data['master_businesses_name'] = data_get($item,'master_businesses.name',null);
                $data['slaver_businesses_id'] = data_get($item,'slaver_businesses.id',null);
                $data['slaver_businesses_name'] = data_get($item,'slaver_businesses.name',null);
                return $data;
            });
            return $return;
        };
        return $return($data);
    }

    /**
     * 更新02504
     * @queryParam containerType required 装箱送箱地点id
     * @queryParam address 地址
     * @queryParam is_up 是否装箱地点0:否，1:是
     * @queryParam is_up 是否送箱地点0:否，1:是
     * @bodyParam segment_businesses_id int required 业务板块id，多个用数组方式
     * @bodyParam master_businesses_id int required 主业务类型id，多个用数组方式
     * @bodyParam slaver_businesses_id int required 子业务类型id，多个用数组方式
     * @response {
     * }
     * @param Request $request
     * @param ContainerAddress $containerAddress
     * @return array
     * @throws \Throwable
     */
    public function update(Request $request, ContainerAddress $containerAddress)
    {
        \DB::transaction(function () use($request,$containerAddress) {
            $containerAddress->id = $request->input('id');
            $request->has('address') && $request->input('address') && $containerAddress->address = $request->input('address');
            $request->has('is_up') && $request->input('is_up') && $containerAddress->is_up = $request->input('is_up');
            $request->has('is_down') && $request->input('is_down') && $containerAddress->is_down = $request->input('is_down');
            $request->has('status') && $request->input('status') && $containerAddress->status = $request->input('status');
            $containerAddress->save();

            if ($request->has('slaver_businesses_id') && $request->input('slaver_businesses_id')) {
                ContainerAddressData::query()->where("container_addresses_id", $containerAddress->id)->delete();
                collect($request->input('segment_businesses_id'))->map(function ($item, $key) use ($request, $containerAddress) {
                    $contractData = new ContainerAddressData();
                    $contractData->container_addresses_id = $containerAddress->id;
                    $contractData->segment_businesses_id = $item;
                    $contractData->master_businesses_id = $request->input('master_businesses_id')[$key];
                    $contractData->slaver_businesses_id = $request->input('slaver_businesses_id')[$key];
                    $contractData->save();
                });
            }
        });
            return [];
    }

    /**
     * 删除02505
     * @queryParam containerAddress required 装箱送箱地点id
     * @response {
     * }
     * @param ContainerAddress $containerAddress
     * @return array
     */
    public function destroy(ContainerAddress $containerAddress)
    {
        ContainerAddress::destroy($containerAddress->id);
        ContainerAddressData::query()->where("container_addresses_id",$containerAddress->id)->delete();
        return [];
    }
}
