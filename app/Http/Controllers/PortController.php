<?php

namespace App\Http\Controllers;

use App\Port;
use Illuminate\Http\Request;

/**
 * @group 港口004
 */
class PortController extends Controller
{
    /**
     * 列表00401
     *
     * @queryParam page 第几页，默认第一页
     * @queryParam per_page 每页记录数，默认是10
     * @response {
     * "data":[{
     *  "id": 4,
     *  "name": "港口名称",
     *  "address": "港口地址",
     *  "status":"0-禁止1-启用",
     *  "created_at": "生成时间",
     *  "updated_at": "修改时间"
     * }],
     *  "current_page": 1,
     *  "first_page_url": "http://host/api/v1/contracts?page=1",
     *  "from": 1,
     *  "last_page": 5,
     *  "last_page_url": "http://host/api/v1/contracts?page=5",
     *  "next_page_url": "http://host/api/v1/contracts?page=2",
     *  "path": "http://host/api/v1/contracts",
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
        $list = Port::query()->where("id",">",1)->orderBy("updated_at","desc")->paginate($request->get('per_page',10));
        /** @var \Illuminate\Pagination\AbstractPaginator $list */
        $collection = $list->setCollection($list->getCollection()->map(function ($list){
            $data = [
                'id'=>$list->name,
                'status'=>$list->status ?? '',
                'address' =>$list->address ?? '',
                'created_at' => (string)data_get($list,'created_at',null),
                'updated_at' => (string)data_get($list,'updated_at',null),

            ];
            return $data;
        }));

        return $collection;
    }

    /**
     * 插入00402
     *
     * @bodyParam name string required 港口名称
     * @bodyParam address string required 港口地址
     * @response {
     *  "id": 4,
     *  "name": "港口名称",
     *  "address": "港口地址",
     *  "created_at": "生成时间",
     *  "updated_at": "修改时间",
     *  "status":"0-禁止1-启用"
     * }
     * @response 404 {
     *  "message": "No query results"
     * }
     * @param Request $request
     * @return Port
     */
    public function store(Request $request)
    {
        $port = new Port();
        $port->name = $request->input('name');
        $port->address = $request->input('address');
        $port->save();
        return $port;
    }

    /**
     * 详情00403
     *
     * @queryParam port 港口id
     * @response {
     *  "id": 4,
     *  "name": "港口名称",
     *  "address":"港口地址",
     *  "status":0,
     *  "created_at": "生成时间",
     *  "updated_at": "修改时间"
     * }
     * @response 404 {
     *  "message": "No query results"
     * }
     * @param Port $port
     * @return Port
     */
    public function show(Port $port)
    {
        return $port;
    }

    /**
     * 更新00404
     * @queryParam port required 港口id
     * @queryParam name  港口名称
     * @queryParam address 业务名称
     * @queryParam status 港口状态0-禁止1-启用
     * @response {
     * "id":1,
     * "name":"业务名称",
     * "parent_id":0,
     * "created_at":"2019-06-12 07:48:10",
     * "updated_at":"2019-06-12 07:48:10"
     * }
     * @param Request $request
     * @param Port $port
     * @return Port
     */
    public function update(Request $request, Port $port)
    {
        $port->id = $request->input('id');
        if($request->input('name') !== null){
            $port->name = $request->input('name');
        }
        if($request->input('address') !== null){
            $port->address = $request->input('address');
        }
        if($request->input('status') !== null){
            $port->status = $request->input('status');
        }
        $port->save();
        return $port;
    }

    /**
     * 删除00405
     * @queryParam port required 港口id
     * @response {
     * "id":1,
     * "name":"业务名称",
     * "parent_id":0,
     * "created_at":"2019-06-12 07:48:10",
     * "updated_at":"2019-06-12 07:48:10"
     * }
     * @param Port $port
     * @return Port
     */
    public function destroy(Port $port)
    {
        Port::destroy($port->id);
        return $port->refresh();
    }
}
