<?php

namespace App\Http\Controllers;

use App\Route;
use Illuminate\Http\Request;
/**
 * @group 航线005
 */
class RouteController extends Controller
{
    /**
     * 列表00501
     *
     * @queryParam page 第几页，默认第一页
     * @queryParam per_page 每页记录数，默认是10
     * @response {
     * "data":[{
     *  "id": 4,
     *  "name": "名称",
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
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $business = Route::query()->orderBy("id","asc")->paginate($request->get('per_page',10));
        return \App\Http\Resources\ShipCompany::collection($business);
    }

    /**
     * 插入00502
     *
     * @bodyParam name string required 航线名称
     * @response {
     *  "id": 4,
     *  "name": "名称",
     *  "created_at": "生成时间",
     *  "updated_at": "修改时间",
     *  "status":"0-禁止1-启用"
     * }
     * @param Request $request
     * @return Route
     */
    public function store(Request $request)
    {
        $route = new Route();
        $route->name = $request->input('name');
        $route->save();
        return $route;
    }

    /**
     * 详情00503
     *
     * @urlParam route 航线id
     * @response {
     *  "id": 4,
     *  "name": "航线名称",
     *  "status":0,
     *  "created_at": "生成时间",
     *  "updated_at": "修改时间"
     * }
     * @param Route $route
     * @return Route
     */
    public function show(Route $route)
    {
        return $route;
    }

    /**
     * 更新00504
     * @urlParam route required 航线id
     * @queryParam name  航线名称
     * @queryParam status 航线状态0-禁止1-启用
     * @response {
     * }
     * @param Request $request
     * @param Route $route
     * @return mixed
     */
    public function update(Request $request, Route $route)
    {
        $request->has('name') && $request->input('name') && $route->name = $request->input('name');
        $request->has('status') && $request->input('status') && $route->status = $request->input('status');
        $route->save();
        return [];
    }
    /**
     * 删除00505
     * @urlParam route required 航线id
     * @response {
     * }
     * @param Route $route
     * @return array
     */
    public function destroy(Route $route)
    {
        Route::destroy($route->id);
        return [];
    }
}
