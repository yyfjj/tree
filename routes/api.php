<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});
//Route::group([],function(){
//    Route::get('company_organize.index',"CompanyOrganizeController@index");
//    Route::get('company_organize.show/{id}',"CompanyOrganizeController@show");
//    Route::post("company_organize.store","CompanyOrganizeController@store");
//    Route::put("company_organize.update/{id}","CompanyOrganizeController@update");
//    Route::delete("company_organize.destory/{id}","CompanyOrganizeController@destroy");
//});
//Route::group([],function(){
//    Route::get('clear_company.index',"ClearCompanyController@index");
//    Route::get('clear_company.show/{id}',"ClearCompanyController@show");
//    Route::post("clear_company.store","ClearCompanyController@store");
//    Route::put("clear_company.update/{id}","ClearCompanyController@update");
//    Route::delete("clear_company.destory/{id}","ClearCompanyController@destroy");
//});

//Route::group([],function(){
//    Route::get('business.index',"BusinessController@index");
//    Route::get('business.show/{id}',"BusinessController@show");
//    Route::post("business.store","BusinessController@store");
//    Route::put("business.update/{id}","BusinessController@update");
//    Route::delete("business.destory/{id}","BusinessController@destroy");
//});



Route::get("post","API\PostAPIController@index")->middleware(\App\Http\Middleware\Role::class);
Route::group(['prefix'=>'v1','middleware'=>['auth:api','role']],function (){
    Route::apiResources([
        'apiTokens'=>'ApiTokenController',
        'registers'=>'Auth\RegisterController',
        'businesses'=>'BusinessController',
        'photos'=>'PhotoController',
        'ports'=>'PortController',
        'companyOrganizes'=>'CompanyOrganizeController',
        'clearCompanies'=>'ClearCompanyController',
        'shipCompanies'=>'ShipCompanyController',//不要
        'routes'=>'RouteController',
        'switchBillCompanies'=>'SwitchBillCompanyController',//换单公司不要
        'contracts'=>'ContractController',
        'chargeItemTaxRates'=>'ChargeItemTaxRateController',
        'chargeItems'=>'ChargeItemController',
        'customerSuppliers'=>"CustomerSupplierController",
        'cities'=>'CityController',
        'containerTypes'=>'ContainerTypeController',
        'containerAddresses'=>'ContainerAddressController',
        'freightForwarders'=>'FreightForwarderController',
        'invoiceTypes'=>'InvoiceTypeController',
        'ships'=>'ShipController',
    ]);
    Route::get('businesses/list/result',"BusinessController@listResult");
    Route::get('customerSuppliers/logistics/role', 'CustomerSupplierController@logisticsRole');
    Route::post("customerSuppliers/copy/{customerSupplier}",'CustomerSupplierController@copy');
    Route::put("customerSuppliers/preview/{customerSupplier}",'CustomerSupplierController@preview');
    Route::put("customerSuppliers/review/{customerSupplier}",'CustomerSupplierController@review');
//    Route::post('contracts/copy/{contract}','ContractController@copy');
    Route::put("contracts/preview/{contract}",'ContractController@preview');
    Route::put("contracts/review/{contract}",'ContractController@review');
    Route::put("contracts/filing/{contract}",'ContractController@filing');
    Route::post("contracts/store_0",'ContractController@store_0');
    Route::post("contracts/store_1/{contract}",'ContractController@store_1');
    Route::post("contracts/update_0/{contract}",'ContractController@update_0');
    Route::post("contracts/update_1/{contract}",'ContractController@update_1');
    Route::post("contracts/submit/{contract}",'ContractController@submit');
    Route::get("contracts/review/list/{contract}",'ContractController@review_list');
    Route::get("customerSupplierShipData","CustomerSupplierShipDataController@index");
    Route::get("customerSupplierShipData/data/{customerSupplierShipData}","CustomerSupplierShipDataController@businessShow");
    Route::post("customerSupplierShipData","CustomerSupplierShipDataController@updateOrInsert");
    Route::post("customerSupplierShipData/business","CustomerSupplierShipDataController@businessUpdateOrInsert");
    Route::delete("customerSupplierShipData/{customerSupplierShipData}","CustomerSupplierShipDataController@destroy");
    Route::delete("customerSupplierShipData/data/{customerSupplierShipDataData}","CustomerSupplierShipDataController@businessDestroy");
//    Route::post('contract',);
});
Route::post('v1/logout/{user}','ApiTokenController@logout');
Route::post('v1/register','ApiTokenController@store');
Route::post('v1/login','ApiTokenController@login');
Route::post('v1/logout','ApiTokenController@logout')->middleware('auth:api');
Route::get("v1/test1/{id}","TestsController@show");//->middleware('auth:api');

//Route::resource('photos','PhotoController')->parameters([
//   'photos'=>'admin_user',
//]);SwaggervelServiceProvider
//Route::post("v1/test","Auth\RegisterController@test");
//Route::get("v1/showAge/{id}","Auth\RegisterController@showAge");
//Route::get("v1/benjamin","benjaminController@age");
//Route::prefix("v1")->get("xxx",'PortController@index');

//Route::get('users/{id}', 'CompanyOrganizeController@show');
//Route::get('userss/{xx}', function (App\CompanyOrganize $ad) {
//    die($ad->name);
//});


$api = app('Dingo\Api\Routing\Router');
$api->version('v1', function ($api) {
    $api->group(['namespace' => 'App\Http\Controllers'], function ($api) {
//namespace声明路由组的命名空间，因为上面设置了"prefix"=>"api",所以以下路由都要加一个api前缀，比如请求/api/users_list才能访问到用户列表接口
        $api->group([], function ($api) {
            #管理员可用接口
            #用户列表api
//            $api->get('/users_list','AdminApiController@usersList');
            #添加用户api
//            $api->post('/add_user','AdminApiController@addUser');
            #编辑用户api
//            $api->post('/edit_user','AdminApiController@editUser');
            #删除用户api
//            $api->post('/del_user','AdminApiController@delUser');
            #上传头像api
//            $api->post('/upload_avatar','UserApiController@uploadAvatar');
            $api->get("test/{ClearCompany}","ClearCompanyController@show");

        });

    });
});