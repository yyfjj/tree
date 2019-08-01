<?php

namespace App\Http\Controllers;

use App\ChargeItem;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * @group 费用科目019
 */
class ChargeItemController extends Controller
{
    /**
     * 列表01901
     *
     * @queryParam page 第几页，默认为1
     * @queryParam per_page 每页数，默认为10
     * @queryParam search 搜索
     * @response {
     * "data":[{
     *  "id": 4,
     *  "segment_name": "业务板块名称",
     *  "master_name": "主业务类型名称",
     *  "slaver_name": "子业务类型名称",
     *  "charge_items_name": "费用科目名称",
     *  "charge_items_code": "费用科目代码",
     *  "invoice_types_name": "开票类型",
     *  "invoice_types_tax": "开票费率",
     *  "is_tax_free":"0-不免税1-免税"
     * }],
     *  "current_page": 1,
     *  "first_page_url": "http://demu.tao3w.com/api/v1/chargeItemTaxRates?page=1",
     *  "from": 1,
     *  "last_page": 5,
     *  "last_page_url": "http://demu.tao3w.com/api/v1/chargeItemTaxRates?page=5",
     *  "next_page_url": "http://demu.tao3w.com/api/v1/chargeItemTaxRates?page=2",
     *  "path": "http://demu.tao3w.com/api/v1/chargeItemTaxRates",
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
        $list = ChargeItem::query()->orderBy("updated_at", "desc")
                                                    ->paginate($request->get('per_page', 10));
        /** @var \Illuminate\Pagination\AbstractPaginator $list */
        $result = $list->setCollection($list->getCollection()->map(function($list){
            /** @var ChargeItem $list */
            $data = [
                'id'=>$list->id,
                'code'=>$list->code,
                'name'=>$list->name,
            ];
            return $data;
        }));
        return $result;
    }

    /**
     * 插入01902
     * @bodyParam code string required 费用科目代码
     * @bodyParam name string required 费用科目名称
     * @response 200{
     * }
     * @param Request $request
     * @return array
     */
    public function store(Request $request)
    {
        $chargeItem = new ChargeItem();
        $chargeItem->code = $request->input('code');
        $chargeItem->name = $request->input('name');
        $chargeItem->save();
        return [];
    }

    /**
     * 详情01903
     * @queryParam chargeItem required 费用科目id
     * @response {
     *   "code":"费用科目代码",
     *   "name":"费用科目名称"
     * }
     * @param ChargeItem $chargeItem
     * @return ChargeItem
     */
    public function show(ChargeItem $chargeItem)
    {
        return $chargeItem;
    }

    /**
     * 更新01904
     * @queryParam code required 费用科目代码
     * @queryParam name required 费用科目名称
     * @response {
     * }
     * @param Request $request
     * @param ChargeItem $chargeItem
     * @return array
     */
    public function update(Request $request, ChargeItem $chargeItem)
    {
        $chargeItem->code = $request->input('code');
        $chargeItem->name = $request->input('name');
        $chargeItem->save();
        return [];
    }

    /**
     * 删除01905
     *
     * @queryParam chargeItem required 费用科目id
     * @response {
     * }
     * @param ChargeItem $chargeItem
     * @return array
     */
    public function destroy(ChargeItem $chargeItem)
    {
        ChargeItem::destroy($chargeItem->id);
        return [];
    }
}
