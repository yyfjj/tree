<?php

namespace App\Http\Controllers;

use App\ContainerType;
use Illuminate\Http\Request;

/**
 * @group 箱型对应关系024
 * Class ContainerTypeController
 * @package App\Http\Controllers
 */
class ContainerTypeController extends Controller
{
    /**
     * 列表02401
     *
     * @queryParam page 第几页，默认第一页
     * @queryParam per_page 每页记录数，默认是10
     * @response {
     * "data":[{
     *  "id": 4,
     *  "name": "箱型名称",
     *  "size": "箱型尺寸",
     *  "created_at": "生成时间",
     *  "updated_at": "修改时间"
     * }],
     *  "current_page": 1,
     *  "first_page_url": "http://host/api/v1/containerTypes?page=1",
     *  "from": 1,
     *  "last_page": 5,
     *  "last_page_url": "http://host/api/v1/containerTypes?page=5",
     *  "next_page_url": "http://host/api/v1/containerTypes?page=2",
     *  "path": "http://host/api/v1/containerTypes",
     *  "per_page": 10,
     *  "prev_page_url": null,
     *  "to": 10,
     *  "total": 50
     * }
     * @response 404 {
     *  "message": "No query results"
     * }
     * @param Request $request
     * @return \Illuminate\Pagination\AbstractPaginator
     */
    public function index(Request $request)
    {
        $list = ContainerType::query()->orderBy("updated_at","desc")->paginate($request->get('per_page',10));
        /** @var \Illuminate\Pagination\AbstractPaginator $list */
        $collection = $list->setCollection($list->getCollection()->map(function ($list){
            /** @var ContainerType $list */
            $data = [
                'id'=>$list->id,
                'name'=>$list->name ?? null,
                'size' =>$list->size ?? null,
                'created_at'=>(string)data_get($list,'created_at',null),
                'updated_at'=>(string)data_get($list,'updated_at',null),
            ];
            return $data;
        }));
        return $collection;
    }

    /**
     * 插入02402
     *
     * @bodyParam name string required 箱型名称
     * @bodyParam size string required 箱型尺寸
     * @response {
     * }
     * @param Request $request
     * @return array
     */
    public function store(Request $request)
    {
        $containerType = new ContainerType();
        $containerType->name = $request->input('name');
        $containerType->size = $request->input('size');
        $containerType->save();
        return [];
    }

    /**
     * 详情02403
     *
     * @queryParam containerType required 箱型id
     * @response {
     *  "id": 4,
     *  "name": "箱型名称",
     *  "size": "箱型尺寸",
     *  "created_at": "生成时间",
     *  "updated_at": "修改时间"
     * }
     * @param ContainerType $containerType
     * @return ContainerType
     */
    public function show(ContainerType $containerType)
    {
        return $containerType;
    }

    /**
     * 更新02404
     * @queryParam containerType required 箱型id
     * @queryParam name 箱型名称
     * @queryParam size 箱型尺寸
     * @response {
     * }
     * @param Request $request
     * @param ContainerType $containerType
     * @return array
     */
    public function update(Request $request, ContainerType $containerType)
    {
        $request->has('name') && $request->input('name') && $containerType->name = $request->input('name');
        $request->has('size') && $request->input('size') && $containerType->size = $request->input('size');
        $containerType->save();
        return [];
    }

    /**
     * 删除00705
     * @queryParam containerType required 箱型id
     * @response {
     * }
     * @param ContainerType $containerType
     * @return array
     */
    public function destroy(ContainerType $containerType)
    {
        ContainerType::destroy($containerType->id);
        return [];
    }
}
