<?php

namespace App\Http\Controllers;

use App\Http\Resources\CompanyOrganize;
use Illuminate\Http\Request;

class TestsController extends BaseController
{

    //
    function show(Request $request){
        $user = $request->user();
        return $user;
        $t = $this->user();
        var_dump($t);
        return "++++++";
        die("++");
//        \Validator::make($_REQUEST, [
//            'id'=>['required|unique:users,username']
//        ], '这个错误');

        $orFail = \App\CompanyOrganize::findOrFail($id);
//        return $this->response->array($orFail);
        throw new \Exception("===");

    }
}
