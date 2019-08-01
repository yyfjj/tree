<?php

namespace App\Http\Controllers;

use App\InvoiceType;
use Illuminate\Http\Request;

/**
 * @group 开票类型027
 */
class InvoiceTypeController extends Controller
{
    /**
     * 列表02701
     *
     * @queryParam page int第几页，默认为1
     * @queryParam per_page int每页数，默认为10
     * @response {
     * "data":[{
     *  "id": 4,
     *  "direction": "in:出,out:入",
     *  "name": "开票类型",
     *  "tax_rate": "税率",
     *  "user_name": "录入人",
     *  "created_at": "创立时间",
     *  "updated_at": "更新时间"
     * }],
     *  "current_page": 1,
     *  "first_page_url": "http://host/api/v1/invoiceTypes?page=1",
     *  "from": 1,
     *  "last_page": 5,
     *  "last_page_url": "http://host/api/v1/invoiceTypes?page=5",
     *  "next_page_url": "http://host/api/v1/invoiceTypes?page=2",
     *  "path": "http://host/api/v1/invoiceTypes",
     *  "per_page": 10,
     *  "prev_page_url": null,
     *  "to": 10,
     *  "total": 50
     * }
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $list = InvoiceType::query()->with(['users'])->orderBy("updated_at","desc")->paginate($request->get('per_page',10));
//        return $list;
        /** @var \Illuminate\Pagination\AbstractPaginator $list */
        $collection = $list->setCollection($list->getCollection()->map(function ($list){
            /** @var InvoiceType $list */
            $data = [
                'id'=>$list->id,
                'direction'=>data_get($list,'direction',null),
                'name' =>data_get($list,'name',null),
                'tax_rate' => data_get($list,'tax_rate',null),
                'user_name' => data_get($list,'users.name'),
                'created_at' => (string)data_get($list,'created_at'),
                'updated_at' => (string)data_get($list,'updated_at'),
            ];
            return $data;
        }));
        return $collection;
    }

    /**
     * 插入02702
     *
     * @bodyParam direction int required in:收,out:付 收付标志
     * @bodyParam name string required 开票类型
     * @bodyParam tax_rate float required 税率
     * @response {
     * }
     * @param Request $request
     * @return array
     */
    public function store(Request $request)
    {
        $invoiceType = new InvoiceType();
        $invoiceType->name = $request->input('name');
        $invoiceType->direction = $request->input('direction');
        $invoiceType->tax_rate = $request->input('tax_rate');
        $invoiceType->user_id = $request->get('user_id');
        $invoiceType->save();

        return [];
    }

    /**
     * 显示02703
     * @queryParam invoiceType required int开票类型id
     * @response {
     *  "id": 4,
     *  "direction": "in:出,out:入",
     *  "name":"开票类型",
     *  "tax_rate":"税率",
     *  "uesrs":{
     *    "id":"录入人id",
     *    "name":"录入人"
     *  },
     *  "created_at": "生成时间",
     *  "updated_at": "修改时间"
     * }
     * @param InvoiceType $invoiceType
     * @return InvoiceType|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|null|object
     */
    public function show(InvoiceType $invoiceType)
    {
        $model = InvoiceType::query()->where('id', $invoiceType->id)->with(['users'])->first();
        return $model;
    }

    /**
     * 更新02704
     *
     * @queryParam invoiceType required int开票类型id
     * @queryParam direction int in:收,out:付 收付标志
     * @queryParam name string 开票类型
     * @queryParam tax_rate float税率
     * @response 200{
     * }
     * @param Request $request
     * @param InvoiceType $invoiceType
     * @return array
     */
    public function update(Request $request, InvoiceType $invoiceType)
    {
        $request->has('direction') && $request->input('direction') && $invoiceType->direction = $request->input('direction');
        $request->has('name') && $request->input('name') && $invoiceType->name = $request->input('name');
        $request->has('tax_rate') && $request->input('tax_rate') && $invoiceType->tax_rate = $request->input('tax_rate');
        $invoiceType->user_id = $request->get('user_id');
        $invoiceType->save();

        return [];
    }

    /**
     * 删除02705
     *
     * @queryParam invoiceType required int开票类型id
     * @response 200{
     * }
     * @param InvoiceType $invoiceType
     * @return array
     */
    public function destroy(InvoiceType $invoiceType)
    {
        InvoiceType::destroy($invoiceType->id);

        return [];
    }
}
