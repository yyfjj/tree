<?php

namespace App\Http\Controllers;

use App\ClearCompany;
use Illuminate\Http\Request;
/**
 * @group 结算公司003
 */
class ClearCompanyController extends Controller
{
    /**
     * 列表00301
     *
     * @queryParam page int 第几页，默认第一页 Example: 1
     * @queryParam per_page int 每页记录数，默认是10 Example: 10
     * @queryParam search string 模糊搜索
     * @queryParam status int 0:禁用,1:启用. Example: 1
     * @queryParam user_id int 操作人
     * @response {
     * "data":[{
     *  "id": 4,
     *  "name": "结算公司名称",
     *  "status":"0-禁止1-启用",
     *  "created_at": "生成时间",
     *  "updated_at": "修改时间"
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
     * @param Request $request
     * @return \Illuminate\Pagination\AbstractPaginator
     */
    public function index(Request $request)
    {
        $list = ClearCompany::query()->with(['users'])->when($request->has('search'),function ($q)use($request){
            /** @type \Illuminate\Database\Eloquent\Builder $q */
            $q->where('name','like',"%{$request->input('search')}%");
        })->when($request->has('status') && $request->input('status') !== '',function ($q)use ($request){
            /** @type \Illuminate\Database\Eloquent\Builder $q */
            $q->where('status',$request->input('status'));
        })->when($request->has('user_id'),function ($q)use($request){
            /** @type \Illuminate\Database\Eloquent\Builder $q */
            $q->where('user_id',$request->input('user_id'));
        })
            ->orderBy("updated_at","desc")
            ->paginate($request->get('per_page',10));

        /** @var \Illuminate\Pagination\AbstractPaginator $list */
        $collection = $list->setCollection($list->getCollection()->map(function ($list){
            /** @var ClearCompany $list */
            $data = [
                'id'=>data_get($list,'id'),
                'name'=>data_get($list,'name'),
                'status' =>data_get($list,'status'),
                'user_name'=>data_get($list,'users.name'),
                'created_at'=>(string)data_get($list,'created_at',null),
                'updated_at'=>(string)data_get($list,'updated_at',null),
            ];
            return $data;
        }));
        return $collection;
    }

    /**
     * 插入00302
     *
     * @bodyParam name string required 结算公司名称 Example:结算公司名称
     * @response {
     * }
     * @response 404 {
     *  "message": "No query results"
     * }
     * @param Request $request
     * @return array
     */
    public function store(Request $request)
    {
        $clearCompany = new ClearCompany();
        $clearCompany->name = $request->input('name');
        $clearCompany->user_id = $request->get('user_id');
        $clearCompany->save();
        return [];

    }

    /**
     * 详情00303
     *
     * @urlParam id 结算公司id
     * @response {
     *  "id": 4,
     *  "name": "结算公司名称",
     *  "status": "结算公司状态",
     *  "created_at": "生成时间",
     *  "updated_at": "修改时间"
     * }
     * @response 404 {
     *  "message": "No query results"
     * }
     * @param ClearCompany $clearCompany
     * @return ClearCompany
     */
    public function show(ClearCompany $clearCompany)
    {
        return $clearCompany;
//        return \App\Http\Resources\ClearCompany::collection($clearCompany->toArray());
    }

    /**
     * 更新00304
     *
     * @urlParam clearCompany int 结算公司id
     * @queryParam name string 结算公司名称
     * @queryParam status int 结算公司状态 -1-删除0-禁止1-启用 Example:1
     * @response {
     * }
     * @response 404 {
     *  "message": "No query results"
     * }
     * @param Request $request
     * @param ClearCompany $clearCompany
     * @return array
     */
    public function update(Request $request, ClearCompany $clearCompany)
    {
        $request->has('name') && $request->input('name') && $clearCompany->name = $request->input('name');
        $request->has('status') && $request->input('size') && $clearCompany->status = $request->input('size');
        $clearCompany->user_id = $request->get('user_id');
        $clearCompany->save();
        return [];
    }

    /**
     * 删除00305
     *
     * @urlParam clearCompany required 结算公司id
     * @response {
     *  "id": 4,
     *  "name": "结算公司名称",
     *  "created_at": "生成时间",
     *  "updated_at": "修改时间"
     * }
     * @response 404 {
     *  "message": "No query results"
     * }
     * @param Request $request
     * @param ClearCompany $clearCompany
     * @return array
     */
    public function destroy(Request $request,ClearCompany $clearCompany)
    {
        $clearCompany->user_id = $request->get('user_id');
        $clearCompany->save();
        ClearCompany::destroy($clearCompany->id);
        return $clearCompany->toArray();
    }
}
