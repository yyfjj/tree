<?php

namespace App\Http\Controllers;

use App\City;
use Illuminate\Http\Request;

/**
 * @group 行政区023
 */
class CityController extends Controller
{
    /**
     * 行政区 02301
     * @queryParam city required 父节点，例如为0时，获取所有国家,然后以获取到的id为参数，会获取到该国家下的省份
     * @response{
     *  "id":1,
     *  "parent_id":0,
     *  "name":"中国"
     * }
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function show($parent_id)
    {
        $city = City::query()->where("parent_id",$parent_id)->get();
        return $city;
    }

}
