<?php

namespace App\Http\Controllers;

use App\ClearCompany;
use App\Contract;
use App\ContractCustomerSupplier;
use App\ContractData;
use App\CustomerSupplier;
use App\CustomerSupplierBusinessData;
use App\ReviewLog;
use App\RoleReview;
use App\User;
use Illuminate\Http\Request;
use /** @noinspection PhpUndefinedClassInspection */
    Illuminate\Support\Facades\Config;
use /** @noinspection PhpUndefinedClassInspection */
    Illuminate\Support\Facades\DB;
use /** @noinspection PhpUndefinedClassInspection */
    Illuminate\Support\Facades\Storage;

/**
 * @group 合同审批管理022
 */
class ContractController extends Controller
{
    /**
     * 列表02201
     *
     * @queryParam page int第几页，默认为1 Example:1
     * @queryParam search 模糊查询
     * @queryParam status int 办理状态0:未办理，1:已办理 Example:1
     * @queryParam result 办理结果-1:退签，1:同意
     * @queryParam clear_company_id 结算公司id
     * @queryParam segment_business_id 业务板块id
     * @queryParam master_business_id 主业务板块id
     * @queryParam process0_user_id 申请人id
     * @queryParam process0_begin_time 申请开始时间
     * @queryParam process0_end_time 申请结束时间
     * @queryParam process1_user_id 商务会签人id
     * @queryParam process1_begin_time 商务会签开始时间
     * @queryParam process1_end_time 商务会签结束时间
     * @queryParam process2_user_id 业务会签人id
     * @queryParam process2_begin_time 业务开始时间
     * @queryParam process2_end_time 业务结束时间
     * @queryParam process3_user_id 审批人id
     * @queryParam process3_begin_time 审批开始时间
     * @queryParam process3_end_time 审批结束时间
     * @queryParam process4_user_id 归档人
     * @queryParam process4_begin_time 归档开始时间
     * @queryParam process4_end_time 归档结束时间
     * @queryParam per_page 每页数，默认为10
     * @response {
     * "data":[{
     *  "id": 4,
     *  "result": "办理结果-1-退签，0-草稿(使用--表示)，1-同意、",
     *  "status": "办理状态0-未办理，1-已办理",
     *  "sn": "合同编号",
     *  "inner_sn": "合同序号",
     *  "part_a_customer_suppliers": "甲方",
     *  "part_b_customer_suppliers": "乙方",
     *  "part_c_customer_suppliers": "丙方",
     *  "segment_businesses": "业务板块",
     *  "master_businesses": "主业务类型",
     *  "slaver_businesses": "子业务类型",
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
     * @response 404 {
     *  "message": "No query results"
     * }
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Pagination\AbstractPaginator|mixed
     */
    public function index(Request $request)
    {
        //fixme-benjamin
        /** @var \Illuminate\Pagination\AbstractPaginator $list */
        /** @noinspection PhpParamsInspection */
        $list = Contract::query()->when($request->has('status'),function ($q)use($request){
            /** @type \Illuminate\Database\Eloquent\Builder $q */
            $process_status = 'process'.$this->_process_location($request->get('role_id'))."_status";
            if($request->input('status') === '0'){
                $q->whereIn($process_status,[null,0]);
            }elseif ($request->input('status') === '1'){
                $q->whereIn($process_status,[-1,1]);
            }else{
                throw new \Exception('status参数异常');
            }
        })
            ->when($request->has('clear_company_id'),function ($q)use($request){
            /** @type \Illuminate\Database\Eloquent\Builder $q */
            $q->where('clear_company_id',$request->input('clear_company_id'));
        })->when($request->has('result'),function ($q)use($request){
            //办理结果 -1退签1同意
            /** @type \Illuminate\Database\Eloquent\Builder $q */
            $process_status = 'process'.$this->_process_location($request->get('role_id'))."_status";
            $q->where($process_status,$request->input('result'));
        })->when($request->has('process0_begin_time'),function ($q)use($request){
            //申请开始时间
            /** @type \Illuminate\Database\Eloquent\Builder $q */
            $q->where('process0_time','>=',$request->input('process0_begin_time'));
        })->when($request->has('process0_end_time'),function ($q)use($request){
            //申请结束时间
            /** @type \Illuminate\Database\Eloquent\Builder $q */
            $q->where('process0_time','<=',$request->input('process0_end_time'));
        })->when($request->has('process1_user_id'),function ($q)use($request){
            //申请人
            /** @type \Illuminate\Database\Eloquent\Builder $q */
            $q->where('process0_user_id',$request->input('process0_user_id'));
        })->when($request->has('process1_begin_time'),function ($q)use($request){
            //商务会签开始时间
            /** @type \Illuminate\Database\Eloquent\Builder $q */
            $q->where('process1_time','>=',$request->input('process1_begin_time'));
        })->when($request->has('process1_end_time'),function ($q)use($request){
            //商务会签结束时间
            /** @type \Illuminate\Database\Eloquent\Builder $q */
            $q->where('process1_time','<=',$request->input('process1_end_time'));
        })->when($request->has('process1_user_id'),function ($q)use($request){
            //商务会签人
            /** @type \Illuminate\Database\Eloquent\Builder $q */
            $q->where('process1_user_id',$request->input('process1_user_id'));
        })->when($request->has('process2_begin_time'),function ($q)use($request){
            //业务会签开始时间
            /** @type \Illuminate\Database\Eloquent\Builder $q */
            $q->where('process2_time','>=',$request->input('process2_begin_time'));
        })->when($request->has('process2_end_time'),function ($q)use($request){
            //业务会签结束时间
            /** @type \Illuminate\Database\Eloquent\Builder $q */
            $q->where('process2_time','<=',$request->input('process2_end_time'));
        })->when($request->has('process2_user_id'),function ($q)use($request){
            //业务会签人
            /** @type \Illuminate\Database\Eloquent\Builder $q */
            $q->where('process2_user_id',$request->input('process2_user_id'));
        })->when($request->has('process3_begin_time'),function ($q)use($request){
            //审批人开始时间
            /** @type \Illuminate\Database\Eloquent\Builder $q */
            $q->where('process3_time','>=',$request->input('process3_begin_time'));
        })->when($request->has('process3_end_time'),function ($q)use($request){
            //审批人结束时间
            /** @type \Illuminate\Database\Eloquent\Builder $q */
            $q->where('process3_time','<=',$request->input('process3_end_time'));
        })->when($request->has('process3_user_id'),function ($q)use($request){
            //审批人
            /** @type \Illuminate\Database\Eloquent\Builder $q */
            $q->where('process3_user_id',$request->input('process3_user_id'));
        })->when($request->has('process4_begin_time'),function ($q)use($request){
            //归档人开始时间
            /** @type \Illuminate\Database\Eloquent\Builder $q */
            $q->where('process4_time','>=',$request->input('process4_begin_time'));
        })->when($request->has('process4_end_time'),function ($q)use($request){
            //归档人结束时间
            /** @type \Illuminate\Database\Eloquent\Builder $q */
            $q->where('process4_time','<=',$request->input('process4_end_time'));
        })->when($request->has('process4_user_id'),function ($q)use($request){
            //归档人
            /** @type \Illuminate\Database\Eloquent\Builder $q */
            $q->where('process4_user_id',$request->input('process4_user_id'));
        })->with([
            'part_a_customer_suppliers',
            'part_b_customer_suppliers',
            'part_c_customer_suppliers',
            'contract_data'=>function($q)use($request){
            /** @type \Illuminate\Database\Eloquent\Builder $q */
            $q->with(['segment_businesses'=>function($q)use($request){
                /** @type \Illuminate\Database\Eloquent\Builder $q */
                $q->when($request->has('segment_business_id'),function ($q)use($request){
                    /** @type \Illuminate\Database\Eloquent\Builder $q */
                    $q->where('segment_business_id',$request->input('segment_business_id'));
                });
            },'master_businesses'=>function($q)use($request){
                /** @type \Illuminate\Database\Eloquent\Builder $q */
                $q->when($request->has('master_business_id'),function ($q)use($request){
                    /** @type \Illuminate\Database\Eloquent\Builder $q */
                    $q->where('master_business_id',$request->input('master_business_id'));
                });
            },'slaver_businesses'=>function($q)use($request){
                /** @type \Illuminate\Database\Eloquent\Builder $q */
                $q->when($request->has('slaver_business_id'),function ($q)use($request){
                    /** @type \Illuminate\Database\Eloquent\Builder $q */
                    $q->where('slaver_business_id',$request->input('slaver_business_id'));
                });
            }]);
        }])->leftJoin('customer_suppliers as part_a_customer_suppliers','contracts.part_a_customer_supplier_id','=','part_a_customer_suppliers.id')
            ->leftJoin('customer_suppliers as part_b_customer_suppliers','contracts.part_b_customer_supplier_id','=','part_b_customer_suppliers.id')
            ->leftJoin('customer_suppliers as part_c_customer_suppliers','contracts.part_c_customer_supplier_id','=','part_c_customer_suppliers.id')
            ->when($request->has('search'),function ($q)use($request){
                /** @type \Illuminate\Database\Eloquent\Builder $q */
                $q->where('part_a_customer_suppliers.name','like',"%".$request->input('search')."%")
                  ->orWhere('part_c_customer_suppliers.name','like',"%".$request->input('search')."%")
                  ->orWhere('part_b_customer_suppliers.name','like',"%".$request->input('search')."%");
            })
            ->orderBy("contracts.updated_at","desc")->paginate($request->get('per_page',10));;
//            return $list;
//        $list = Contract::query()->with([
////                                            'part_a_customer_suppliers',
////                                            'part_b_customer_suppliers',
////                                            'part_c_customer_suppliers',
//                                            'role_reviews'=>function($q)use($request){
//                                                    /** @type \Illuminate\Database\Eloquent\Builder $q */
//                                                    $q->with(['users'])
//                                                        ->where('model','contract')
//                                                        ->where('role_id',$request->get('role_id'))
//                                                        ->orderBy("id","desc")
//                                                        ->when($request->has('status'),function ($q)use($request){
//                                                            /** @type \Illuminate\Database\Eloquent\Builder $q */
//                                                            if($request->input('status') === '0'){
//                                                                $q->whereIn('status',[null,0]);
//                                                            }elseif ($request->input('status') === '1'){
//                                                                $q->where('status',1);
//                                                            }else{
//                                                                throw new \Exception('status参数异常');
//                                                            }
//                                                        })->when($request->has('result'),function ($q)use($request){
//                                                            //办理结果 -1退签1同意
//                                                            /** @type \Illuminate\Database\Eloquent\Builder $q */
//                                                            $q->where('status',$request->input('result'));
//                                                        })->when($request->has('process1_begin_time'),function ($q)use($request){
//                                                            //申请开始时间
//                                                            /** @type \Illuminate\Database\Eloquent\Builder $q */
//                                                            /** @noinspection PhpUndefinedClassInspection */
//                                                            $q->whereNotNull('status')
//                                                                ->where('role_id',array_keys(Config::get('constants.REVIEW'))[0])
//                                                                ->where('updated_at','>=',$request->input('process1_begin_time'));
//                                                        })->when($request->has('process1_end_time'),function ($q)use($request){
//                                                            //申请结束时间
//                                                            /** @type \Illuminate\Database\Eloquent\Builder $q */
//                                                            /** @noinspection PhpUndefinedClassInspection */
//                                                            $q->whereNotNull('status')
//                                                                ->where('role_id',array_keys(Config::get('constants.REVIEW'))[0])
//                                                                ->where('updated_at','<=',$request->input('process1_end_time'));
//                                                        })->when($request->has('process1_user_id'),function ($q)use($request){
//                                                            //申请人
//                                                            /** @type \Illuminate\Database\Eloquent\Builder $q */
//                                                            /** @noinspection PhpUndefinedClassInspection */
//                                                            $q->where('role_id',array_keys(Config::get('constants.REVIEW'))[0])
//                                                                ->where('user_id',$request->input('process1_user_id'));
//                                                        })->when($request->has('process2_begin_time'),function ($q)use($request){
//                                                            //商务会签开始时间
//                                                            /** @type \Illuminate\Database\Eloquent\Builder $q */
//                                                            /** @noinspection PhpUndefinedClassInspection */
//                                                            $q->whereNotNull('status')
//                                                                ->where('role_id',array_keys(Config::get('constants.REVIEW'))[1])
//                                                                ->where('updated_at','>=',$request->input('process2_begin_time'));
//                                                        })->when($request->has('process2_end_time'),function ($q)use($request){
//                                                            //商务会签结束时间
//                                                            /** @type \Illuminate\Database\Eloquent\Builder $q */
//                                                            /** @noinspection PhpUndefinedClassInspection */
//                                                            $q->whereNotNull('status')
//                                                                ->where('role_id',array_keys(Config::get('constants.REVIEW'))[1])
//                                                                ->where('updated_at','<=',$request->input('process2_end_time'));
//                                                        })->when($request->has('process2_user_id'),function ($q)use($request){
//                                                            //商务会签人
//                                                            /** @type \Illuminate\Database\Eloquent\Builder $q */
//                                                            /** @noinspection PhpUndefinedClassInspection */
//                                                            $q->where('role_id',array_keys(Config::get('constants.REVIEW'))[1])
//                                                                ->where('user_id',$request->input('process2_user_id'));
//                                                        });
//                                            },
//                                            'contract_data'=>function($q)use($request){
//                                                /** @type \Illuminate\Database\Eloquent\Builder $q */
//                                                $q->with(['segment_businesses'=>function($q)use($request){
//                                                    /** @type \Illuminate\Database\Eloquent\Builder $q */
//                                                    $q->when($request->has('segment_business_id'),function ($q)use($request){
//                                                        /** @type \Illuminate\Database\Eloquent\Builder $q */
//                                                        $q->where('segment_business_id',$request->input('segment_business_id'));
//                                                    });
//                                                },'master_businesses'=>function($q)use($request){
//                                                    /** @type \Illuminate\Database\Eloquent\Builder $q */
//                                                    $q->when($request->has('master_business_id'),function ($q)use($request){
//                                                        /** @type \Illuminate\Database\Eloquent\Builder $q */
//                                                        $q->where('master_business_id',$request->input('master_business_id'));
//                                                    });
//                                                },'slaver_businesses'=>function($q)use($request){
//                                                    /** @type \Illuminate\Database\Eloquent\Builder $q */
//                                                    $q->when($request->has('slaver_business_id'),function ($q)use($request){
//                                                        /** @type \Illuminate\Database\Eloquent\Builder $q */
//                                                        $q->where('slaver_business_id',$request->input('slaver_business_id'));
//                                                    });
//                                                }]);
//                                            }
//            ])
//            ->when($request->has('clear_company_id'),function ($q)use($request){
//                /** @type \Illuminate\Database\Eloquent\Builder $q */
//                $q->when('clear_company_id',$request->input('clear_company_id'));
//            })
//            ->orderBy("updated_at","desc")->paginate($request->get('per_page',10));
//        return $list;
//        $anonymousResourceCollection = \App\Http\Resources\Contract::collection($list);
//        return $anonymousResourceCollection;
        /** @var \Illuminate\Pagination\AbstractPaginator $list */
        $collection = $list->setCollection($list->getCollection()->map(function ($list)use($request){
            //fixme-benjamin 一个人多个权限角色，仅仅取第一个
//            $status = collect(data_get($list,'role_reviews'))->map(function ($item/*,$key*/){
//               return $item->status;
//            })->first();
            $process_status = 'process'.$this->_process_location($request->get('role_id'))."_status";
            $data = [
                'id'=>$list->id,
                'result'=>intval(data_get($list,$process_status)),//data_get($list,'role_reviews'),
                'status'=>data_get($list,$process_status) ? 1 : 0,
                'sn'=>data_get($list,'sn'),
                'inner_sn'=>data_get($list,'inner_sn'),
                'part_a_customer_suppliers'=>data_get($list,'part_a_customer_suppliers.name'),
                'part_b_customer_suppliers'=>data_get($list,'part_b_customer_suppliers.name'),
                'part_c_customer_suppliers'=>data_get($list,'part_c_customer_suppliers.name'),
                'segment_businesses'=>collect($list->contract_data)->map(function($item){
                    return $item->segment_businesses->name;
                })->join(","),//($list,'contract_data.segment_businesses'),
                'master_businesses'=>collect($list->contract_data)->map(function($item){
                    return $item->master_businesses->name;
                })->join(","),
                'slaver_businesses'=>collect($list->contract_data)->map(function($item){
                    return $item->slaver_businesses->name;
                })->join(","),
            ];
            return $data;
        }));

        return $collection;
//        $anonymousResourceCollection = \App\Http\Resources\Business::collection($business);
//        return $contract;
//        return $anonymousResourceCollection;
    }

    /**
     * 插入合同草拟02210
     *
     * parent_name和parent_id必须传一个,传parent_name表示新增，传parent_id表示修改
     * name和id必须传一个,传name表示新增，传id表示修改
     * @bodyParam name string required 合同名称
     * @bodyParam sn_alias string 对方合同号
     * @bodyParam type string 合同类型 Example:customer
     * @bodyParam clear_company_id int 结算公司id
     * @bodyParam attachment string 合同附件
     * @bodyParam customer_supplier_id[] int 客户供应商id
     * @bodyParam is_invoice[] int 是否结算单位0:否,1:是 Example:1
     *
     * @param Request $request
     */
    public function store_0(Request $request){
        /** @noinspection PhpUndefinedClassInspection */
        DB::transaction(function () use($request){
            //<editor-fold desc="草拟合同">
            $contract = new Contract();
            $contract->name =  $request->input('name');
            $contract->sn_alias =  $request->input('sn_alias');
            $contract->type = $request->input('type');
            $contract->clear_company_id = $request->input('clear_company_id');
            $contract->process0_user_id = $request->get('user_id');
            if($request->has('attachment')){
                $fileCharater = $request->file('attachment');
                if ($fileCharater->isValid()) {
                    //获取文件的扩展名
                    $ext = $fileCharater->getClientOriginalExtension();
                    //获取文件的绝对路径
                    $path = $fileCharater->getRealPath();
                    //定义文件名
                    $filename = date('Y-m-d-h-i-s').'-'.rand(1000,9999).'.'.$ext;
                    //存储文件。disk里面的public。总的来说，就是调用disk模块里的public配置
                    /** @noinspection PhpUndefinedClassInspection */
                    Storage::disk('public')->put($filename, file_get_contents($path));
                    $contract->attachment = $filename;
                }
            }
            $contract->save();
            //</editor-fold>

            //<editor-fold desc="关联合同和客户供应商">
            collect($request->input('customer_supplier_id'))->map(function($item,$key)use($contract,$request){
                $contract->contract_customer_suppliers()->attach($item,['is_invoice'=>$request->input('is_invoice')[$key]]);
            });
            //</editor-fold>

            //<editor-fold desc="初始化审批流程">
            /** @noinspection PhpUndefinedClassInspection */
            collect(Config::get('constants.REVIEW'))->map(function($item, $key)use($request,$contract){
                $reviewLog = new ReviewLog();
                $reviewLog->model = 'contracts';
                $reviewLog->foreign_key = $contract->id;
                $reviewLog->name = $item;
//            $reviewLog->user_id = $request->get('user_id);
                $reviewLog->role_id = $key;
//            $reviewLog->status = 0;
                $reviewLog->save();
            }
            );
            //</editor-fold>
        });
        return [];
    }

    /**
     * 插入商务会签02211
     *
     * @urlParam contract int required 合同id
     * @bodyParam sn string required 合同编号
     * @bodyParam begin_time string required 合同生效开始日 Example:2020-01-01
     * @bodyParam end_time string required 合同生效结束日 Example:2020-12-12
     * @bodyParam credit_time_type int required 信控基准日1:业务日期,2:开票日期,3:到港日期 Example:1
     * @bodyParam credit_delay_type int 延迟类型1:延迟月份,2:延后自然日数,3:延后工作日数 Example:1
     * @bodyParam credit_delay_data int 延后月份:延迟类型为1,1:次月,2:次月月底,3:次次月,4:次次月底,5:次次次月,6:次次次月底;延迟类型为2:表示延后自然日数;延迟类型为3:表示延后工作日数
     * @bodyParam credit_delay_data_data int 延后月份结算日:当是次月、次次月、次次次月才有具体数据天
     * @bodyParam segment_business_id[] int required 业务板块id
     * @bodyParam master_business_id[] int required 主业务板块id
     * @bodyParam slaver_business_id[] int required 子业务板块id
     * @bodyParam charge_rule_id[] int required 价格协议id,如果无价格协议，该参数必须传递一个空字符串
     *
     * @param Request $request
     */
    public function store_1(Request $request,Contract $contract){
        if($contract->process0_status != 1){
            throw new \Exception('合同草拟提交后方可商务会签操作');
        }

        if($contract->process1_status == 1)

        $contract->sn = $request->input('sn');
        $contract->begin_time = $request->input('begin_time');
        $contract->end_time   = $request->input('end_time');
        $contract->credit_time_type =$request->input('credit_time_type');
        $contract->credit_delay_type = $request->input('credit_delay_type');
        $contract->credit_delay_data = $request->input('creadit_delay_data');
        $contract->credit_delay_data_data = $request->input('credit_delay_data_data');
        $contract->process1_user_id = $request->get('user_id');
        $contract->process1_time    = date('Y-m-d H:i:s');
        $contract->save();

        collect($request->input('segment_business_id'))->map(function ($item,$key)use($contract,$request){
            $contract_data = new ContractData();
            $contract_data->contract_id         = $contract->id;
            $contract_data->segment_business_id = $item;
            $contract_data->master_business_id  = $request->input('master_business_id')[$key];
            $contract_data->slaver_business_id  = $request->input('slaver_business_id')[$key];
            $contract_data->charge_rule_id      = 1;//fixme-benjamin
            $contract_data->save();
        });

        //<editor-fold desc="客户供应商">
        $customer_supplier_id = collect(data_get(Contract::query()->with(['contract_customer_suppliers'])->where("id",$contract->id)->first(),"contract_customer_suppliers"))->map(function ($item){
            return $item['id'];
        })->map(function($item)use($request){
            collect($request->input('segment_business_id'))->map(function ($v,$k)use($item,$request){
                CustomerSupplierBusinessData::updateOrInsert(
                    [
                        'customer_supplier_id'=>$item,
                        'segment_business_id'=>$request->input('segment_business_id')[$k],
                        'master_business_id'=>$request->input('master_business_id')[$k],
                        'slaver_business_id'=>$request->input('slaver_business_id')[$k],
                        'charge_rule_id'=>$request->input('charge_rule_id')[$k],
                    ],
                    [
                        'is_lock'=>1
                    ]
                );
            });
        });
        return [];
        //</editor-fold>
    }
    /**
     * 详情02203
     * @urlParam contract required 合同自增id
     * @response{
     *  "time_line":{
     *      "font":[
     *          "合同草拟","商务会签","业务会签","领导审批","合同归档"
     *      ],
     *      "time":[
     *          "2019-08-02 10:03:03",null,null,null,"null表示未到该步骤"
     *      ]
     * },
     *  "id":"合同自增id",
     *  "name":"合同名称",
     *  "sn_alias":"对方合同编号",
     *  "type":"合同类型customer:客户合同,supplier:供应商合同",
     *  "attachment":"合同附件",
     *  "customer_suppliers":[
     *      {
     *          "id":"客户/供应商id",
     *          "is_invoice":"是否结算单位0:否,1:是"
     *      }
     *  ],
     *  "clear_company_id":2,
     *  "process0_user_name":"申请人",
     *  "process0_time":"申请时间",
     *  "sn":"合同编号",
     *  "begin_time":"合同生效日",
     *  "end_time":"合同失效日",
     *  "credit_time_type":"信控基准日1:业务日期,2:开票日期,3:到港日期",
     *  "credit_delay_type":"延迟类型1:延迟月份,2:延后自然日数,3:延后工作日数",
     *  "credit_delay_data":"延后月份:延迟类型为1,1:次月,2:次月月底,3:次次月,4:次次月底,5:次次次月,6:次次次月底;延迟类型为2:表示延后自然日数;延迟类型为3:表示延后工作日数",
     *  "credit_delay_data_data":"延后月份结算日:当是次月、次次月、次次次月才有具体数据天",
     *  "process1_user_name":"商务会签操作人",
     *  "process1_time":"商务会签操作时间",
     *  "contract_data":[
     *      {
     *          "segment_business_id":"业务板块id",
     *          "master_business_id":"主业务类型id",
     *          "slaver_business_id":"子业务类型id",
     *          "charge_rule_id":"价格协议id"
     *      }
     * ]
     *  "review_logs":[{
     *      "process_name":"步骤名称",
     *      "depart_name":"部门名称",
     *      "process_users_name":"办理人",
     *      "process_status":"已办理/未办理",
     *      "process_result":"申请/同意/归档",
     *      "process_suggestion":"办理意见",
     *      "process_time":"办理时间"
     * }]
     * }
     * @param Contract $contract
     * @return array
     */
    public function show(Contract $contract)
    {
        $contract = Contract::query()->with(['contract_data'=>function($q){
            /** @type \Illuminate\Database\Eloquent\Builder $q */
            $q->with(['segment_businesses','master_businesses','slaver_businesses'/*,'charge_rules'*/]);
        },'contract_customer_suppliers',
            'review_logs'=>function($q){
                /** @type \Illuminate\Database\Eloquent\Builder $q */
                $q->with(['users','roles']);
            }])
                                    ->where('id',$contract->id)->first();
        $return = function($contract){
            /** @var Contract $contract */
            $return = [];
            $return['time_line'] = [
                'font'=>array_values(Config::get('constants.REVIEW')),
                'time'=>[$contract->process0_time,$contract->process1_time,$contract->process2_time,$contract->process3_time,$contract->process4_time]
            ];
            $return['id'] = $contract->id;
            $return['name'] = $contract->name;
            $return['sn_alias'] = $contract->sn_alias;
            $return['type'] = $contract->type;
            $return['attachment'] = $contract->attachment;
            $return['customer_suppliers'] = collect($contract->contract_customer_suppliers)->map(function ($item,$key){
                /** @var CustomerSupplier $item */
                return ['id'=>$item->id,'is_invoice'=>$item->is_invoice];
            });
            $return['clear_company_id'] = $contract->clear_company_id;
            $return['process0_user_name'] = User::find($contract->process0_user_id)->getAttributeValue('name');
            $return['process0_time'] = $contract->process0_time;
            $return['sn'] = $contract->sn;
            $return['begin_time'] = $contract->begin_time;
            $return['end_time'] = $contract->end_time;
            $return['credit_delay_type'] = $contract->credit_delay_type;
            $return['credit_time_type']  = $contract->credit_time_type;
            $return['credit_delay_data'] = $contract->credit_delay_data;
            $return['credit_delay_data_data'] = $contract->credit_delay_data_data;
            $return['process1_user_name'] = User::find($contract->process1_user_id)->getAttributeValue('name');
            $return['process1_time'] = $contract->process1_time;
            $return['contract_data'] = collect($contract->contract_data)->map(function ($item,$key){
                $data = [];
                /** @var ContractData $item */
                $data['segment_business_id'] = $item->segment_business_id;
                $data['master_business_id'] = $item->master_business_id;
                $data['slaver_business_id'] = $item->slaver_business_id;
                $data['slaver_business_id'] = $item->slaver_business_id;
                $data['charge_rule_id'] = $item->charge_rule_id;
                return $data;
            });
            $return['review_logs'] = collect($contract->review_logs)->map(function($item){
                /** @var ReviewLog $item */
                $data = [];
//            $data['name'] = data_get($item,"name");
                $data['process_users_name'] = data_get($item,"users.name");
                $data['depart_name'] = data_get($item,"roles.name");
//            $data['roles_name'] = data_get($item,"roles.name");
                $data['process_name'] = Config::get("constants.REVIEW")[data_get($item,'roles.id')];
                $data['process_status'] = data_get($item,"status") == 0 ? "未办理" : "已办理";
                if(data_get($item,"status") == -1){
                    $data['process_result'] = "退回";
                }elseif(data_get($item,'status') == 0){
                    $data['process_result'] = '未办理';
                }else{
                    $process_location = $this->_process_location(data_get($item, 'role_id'));
                    if($process_location == 0){
                        $data['process_result'] = "提交";
                    }elseif ($process_location == 4){
                        $data['process_result'] = '归档';
                    }else{
                        $data['process_result'] = '同意';
                    }
                }
                $data['process_suggestion'] = data_get($item,"suggestion");
                $data['process_time']       = (string)data_get($item,"updated_at");
                return $data;
            });
            return $return;
        };
        return $return($contract);
        $return = function($contract){
            /** @var Contract $contract */
            $return = [];
            $return['id'] = $contract->id;
            $return['sn'] = $contract->sn;
            $return['customer_sn'] = $contract->customer_sn;
            $return['name'] = $contract->name;
            /** @noinspection PhpUndefinedClassInspection */
            $return['type'] = collect(Config::get('constants.CONTRACT_TYPE'))->map(function($item, $key)use($contract){
                /** @var Contract $data */
                $data = [];
                $data['key'] = $key;
                $data['item'] = $item;
                $data['is_selected'] = $contract->type == $item ? 1 : 0;
                return $data;
            })->values();
            $return['clear_companies'] = collect(ClearCompany::query()->select(['id','name'])->get())->map(function ($item) use($contract){
                /** @var Contract $contract */
                /** @var ClearCompany $item */
                $data = [];
                $data['key'] = $item->id;
                $data['item'] = $item->name;
                $data['is_selected'] = $item->id == $contract->clear_company_id ? 1 : 0;
                return $data;
            });
            $value = CustomerSupplier::query()->select(['id', 'name'])->get();
            $return['part_a_customer_suppliers'] = collect($value)->map(function ($item) use($contract){
                /** @var Contract $contract */
                /** @var ClearCompany $item */
                $data = [];
                $data['key'] = $item->id;
                $data['item'] = $item->name;
                $data['is_selected'] = $item->id == $contract->part_a_customer_supplier_id ? 1 : 0;
                return $data;
            });
            $return['part_b_customer_suppliers'] = collect($value)->map(function ($item) use($contract){
                /** @var Contract $contract */
                /** @var ClearCompany $item */
                $data = [];
                $data['key'] = $item->id;
                $data['item'] = $item->name;
                $data['is_selected'] = $item->id == $contract->part_b_customer_supplier_id ? 1 : 0;
                return $data;
            });
            $return['part_c_customer_suppliers'] = collect($value)->map(function ($item) use($contract){
                /** @var Contract $contract */
                /** @var ClearCompany $item */
                $data = [];
                $data['key'] = $item->id;
                $data['item'] = $item->name;
                $data['is_selected'] = $item->id == $contract->part_c_customer_supplier_id ? 1 : 0;
                return $data;
            });
            /** @noinspection PhpUndefinedClassInspection */
            $return['from'] = collect(Config::get('constants.CONTRACT_FROM'))->map(function ($item, $key) use($contract){
                /** @var Contract $contract */
                $data = [];
                $data['key'] = $key;
                $data['item'] = $item;
                $data['is_selected'] = $key == $contract->from ? 1 : 0;
                return $data;
            })->values();
            $return['contract_data'] = collect($contract->contract_data)->map(function($item){
                $data=[];
                $data['segment_businesses_id'] = data_get($item,'segment_businesses.id',null);
                $data['segment_businesses_name'] = data_get($item,'segment_businesses.name',null);
                $data['master_businesses_id'] = data_get($item,'master_businesses.id',null);
                $data['master_businesses_name'] = data_get($item,'master_businesses.name',null);
                $data['slaver_businesses_id'] = data_get($item,'slaver_businesses.id',null);
                $data['slaver_businesses_name'] = data_get($item,'slaver_businesses.name',null);
                $data['charge_rules_id'] = null;
                $data['charge_rules_name'] = null;//fixme-benjamin 收费规则
                return $data;
            });
            $return['attachment'] = $contract->attachment;
            $return['review_logs'] = collect($contract->review_logs)->map(function($item){
                /** @var ReviewLog $item */
                $data = [];
                $data['name'] = $item->name;
                $data['roles_name'] = data_get($item,"roles.name",null);
                $data['users_name'] = data_get($item,"users.name",null);
                $data['status']     = $item->status;
                $data['suggestion'] = $item->suggestion;
                $data['updated_at'] = (string)$item->updated_at;
                return $data;
            });
            return $return;
        };
        return $return($contract);
    }

    /**
     * 更新合同草拟
     * @urlParam id int required 合同id
     * @bodyParam name string 合同名称
     * @bodyParam type string 合同类型 Example:supplier
     * @bodyParam attachment string 附件
     * @bodyParam customer_supplier_id[] int 客户供应商id
     * @bodyParam is_invoice[] int 是否结算单位 Example:1
     * @bodyParam clear_company_id int 结算公司id
     * @param Request $request
     * @param Contract $contract
     * @response {
     * }
     */
    public function update_0(Request $request,Contract $contract){
        /** @noinspection PhpUndefinedClassInspection */
        DB::transaction(function () use($request,$contract) {
            //<editor-fold desc="修改contract表">
            $request->has('name') && $request->input('name') && $contract->name = $request->input('name');
            $request->has('sn_alias') && $request->input('sn_alias') && $contract->sn_alias = $request->input('sn_alias');
            $request->has('type') && $request->input('type') && $contract->type = $request->input('type');
            $request->has('clear_company_id') && $request->input('clear_company_id') && $contract->clear_company_id = $request->input('clear_company_id');
            if ($request->has('attachment')) {
                $fileCharater = $request->file('attachment');
                if ($fileCharater && $fileCharater->isValid()) {
                    //获取文件的扩展名
                    $ext = $fileCharater->getClientOriginalExtension();
                    //获取文件的绝对路径
                    $path = $fileCharater->getRealPath();
                    //定义文件名
                    $filename = date('Y-m-d-h-i-s') . '-' . rand(1000, 9999) . '.' . $ext;
                    //存储文件。disk里面的public。总的来说，就是调用disk模块里的public配置
                    /** @noinspection PhpUndefinedClassInspection */
                    Storage::disk('public')->put($filename, file_get_contents($path));
                    $contract->attachment = $filename;
                }
            }
            $contract->save();
            //</editor-fold>
            //<editor-fold desc="更新contract_customer_supplier表">
            $request->has('customer_supplier_id') &&
            $request->input('customer_supplier_id') &&
                $contract->contract_customer_suppliers()->sync(
                    collect(array_flip($request->input(['customer_supplier_id'])))->map(function($item,$key)use($request){
                        return ['is_invoice'=>$request->input('is_invoice')[$item]];
                    })->toArray()
                );
            //</editor-fold>
        });
        return [];
    }

    /**
     * 更新商务会签02204
     *
     * @urlParam contract int required 合同id
     * @bodyParam sn string required 合同编号
     * @bodyParam begin_time string required 合同生效开始日 Example:2020-01-01
     * @bodyParam end_time string required 合同生效结束日 Example:2020-12-12
     * @bodyParam credit_time_type int required 信控基准日1:业务日期,2:开票日期,3:到港日期 Example:1
     * @bodyParam credit_delay_type int 延迟类型1:延迟月份,2:延后自然日数,3:延后工作日数 Example:1
     * @bodyParam credit_delay_data int 延后月份:延迟类型为1,1:次月,2:次月月底,3:次次月,4:次次月底,5:次次次月,6:次次次月底;延迟类型为2:表示延后自然日数;延迟类型为3:表示延后工作日数
     * @bodyParam credit_delay_data_data int 延后月份结算日:当是次月、次次月、次次次月才有具体数据天
     * @bodyParam segment_business_id[] int required 业务板块id
     * @bodyParam master_business_id[] int required 主业务板块id
     * @bodyParam slaver_business_id[] int required 子业务板块id
     * @bodyParam charge_rule_id[] int required 价格协议id,如果无价格协议，该参数必须传递一个空字符串
     *
     * @response {
     * }
     */
    public function update_1(Request $request, Contract $contract)
    {
        return $this->store_1($request,$contract);
//        return [];
    }

    /**
     * 更新02204
     *
     * @queryParam id required 合同id
     * @queryParam sn 合同编号
     * @queryParam customer_sn 对方合同编号
     * @queryParam name 合同名称
     * @queryParam type 合同类型customer：客户合同，supplier：供应商合同
     * @queryParam clear_companies_id 结算公司id
     * @queryParam part_a_customer_supplier_id 合同甲方
     * @queryParam part_b_customer_supplier_id 合同乙方
     * @queryParam part_c_customer_supplier_id 合同丙方
     * @queryParam from 揽货性质，company:公司揽货、person:销售揽货
     * @queryParam is_invoice 是否结算客户0:否，1:是
     * @bodyParam attachment string 合同附件
     * @queryParam segment_business_id 业务板块id，多个用数组方式
     * @queryParam master_business_id 主业务类型id，多个用数组方式
     * @queryParam slaver_business_id 子业务类型id，多个用数组方式
     * @queryParam $charge_rule_id 价格协议号id，多个用数组方式
     * @param Request $request
     * @param Contract $contract
     * @return array
     */
    public function update(Request $request, Contract $contract)
    {
        /** @noinspection PhpUndefinedClassInspection */
        DB::transaction(function () use($request,$contract){
            $request->has('sn') && $request->input('sn') && $contract->sn = $request->input('sn');
            $request->has('customer_sn') && $request->input('customer_sn') && $contract->customer_sn = $request->input('customer_sn');
            $request->has('name') && $request->input('name') && $contract->name = $request->input('name');
            $request->has('type') && $request->input('type') && $contract->type = $request->input('type');
            $request->has('clear_company_id') && $request->input('clear_company_id') && $contract->clear_company_id = $request->input('clear_company_id');
            $request->has('part_a_customer_supplier_id') && $request->input('part_a_customer_supplier_id') && $contract->part_a_customer_supplier_id = $request->input('part_a_customer_supplier_id');
            $request->has('part_b_customer_supplier_id') && $request->input('part_b_customer_supplier_id') && $contract->part_b_customer_supplier_id = $request->input('part_b_customer_supplier_id');
            $request->has('part_c_customer_supplier_id') && $request->input('part_c_customer_supplier_id') && $contract->part_c_customer_supplier_id = $request->input('part_c_customer_supplier_id');
            $request->has('from') && $request->input('from') && $contract->from = $request->input('from');
            $request->has('is_invoice') && $request->input('is_invoice') && $contract->is_invoice = $request->input('is_invoice');

            if($request->has('attachment')){
                $fileCharater = $request->file('attachment');
                if ($fileCharater->isValid()) {
                    //获取文件的扩展名
                    $ext = $fileCharater->getClientOriginalExtension();
                    //获取文件的绝对路径
                    $path = $fileCharater->getRealPath();
                    //定义文件名
                    $filename = date('Y-m-d-h-i-s').'-'.rand(1000,9999).'.'.$ext;
                    //存储文件。disk里面的public。总的来说，就是调用disk模块里的public配置
                    /** @noinspection PhpUndefinedClassInspection */
                    Storage::disk('public')->put($filename, file_get_contents($path));
                    $contract->attachment = $filename;
                }
            }

            $contract->save();

            ContractData::query()->where("contracts_id",$contract->id)->delete();
            collect($request->input('segment_businesses_id'))->map(function($item,$key)use($request,$contract){
                $contractData = new ContractData();
                $contractData->contract_id = $contract->id;
                $contractData->segment_business_id = $item;
                $contractData->master_business_id  = $request->input('master_business_id')[$key];
                $contractData->slaver_business_id  = $request->input('slaver_business_id')[$key];
                $contractData->charge_rule_id  = $request->input('charge_rule_id')[$key];
                $contractData->save();
            });
        });

        return [];
    }

//    public function upload(Request $request){
//        $fileCharater = $request->file('source');
//        //获取文件的扩展名
//        $ext = $fileCharater->getClientOriginalExtension();
//
//        //获取文件的绝对路径
//        $path = $fileCharater->getRealPath();
//
//        //定义文件名
//        $filename = date('Y-m-d-h-i-s').'-'.rand(1000,9999).'.'.$ext;
//
//        //存储文件。disk里面的public。总的来说，就是调用disk模块里的public配置
//        \Storage::disk('public')->put($filename, file_get_contents($path));
//        return $request->input('name');
//    }

    /**
     * 删除02205
     * @urlParam contract required 合同id
     * @response {
     * }
     * @param Contract $contract
     * @return array
     */
    public function destroy(Contract $contract)
    {
        if($contract->process0_status != 0){
            throw new \Exception("审批中的合同，无法删除！");
        }
        /** @noinspection PhpUndefinedClassInspection */
        DB::transaction(function () use($contract){
            Contract::destroy($contract->id);
//            ContractData::query()->where("contracts_id",$contract->id)->delete();
            RoleReview::query()->where("model","contract")->where('foreign_key',$contract->id)->delete();
        });
        Storage::disk('public')->delete($contract->attachment);

        return [];
    }

    /**
     * 提交02210
     *
     * @urlParam contract int required 合同id
     * @bodyParam status int required 审核 -1-审核不通过、1-审核通过 Example:1
     * @bodyParam inner_sn string 内部管理合同该编号(只有最后一步归档才需要)
     * @bodyParam suggestion 建议
     * @response {
     * }
     * @param Request $request
     * @param Contract $contract
     * @return array
     * @throws \Exception
     */
    function submit(Request $request,Contract $contract){
        //<editor-fold desc="审批权限校验">
        $process_location = $this->_process_location($request->get('role_id'));
        if($process_location === false){
            throw new \Exception('您没有提交权限');
        }
        if($process_location == 4 && $request->input('status') == -1){
            throw new \Exception('综合管理部没有退回权限');
        }
        //</editor-fold>
        //<editor-fold desc="审批通过">
        if($process_location === 0 && $contract->process0_status === 0){
            $contract->process0_status = $request->input('status');
            $contract->process0_time   = date("Y-m-d H:i:s");
            $contract->process0_user_id= $request->get('user_id');
        }elseif ($process_location === 1 && $contract->process0_status === 1 && $contract->process1_status === 0){
            $contract->process1_status  = $request->input('status');
            $contract->process1_time    = date("Y-m-d H:i:s");
            $contract->process1_user_id = $request->get('user_id');
        }elseif($process_location === 2 && $contract->process1_status === 1 && $contract->process2_status === 0){
            $contract->process2_status  = $request->input('status');
            $contract->process2_user_id = $request->get('user_id');
            $contract->process2_time    = date("Y-m-d H:i:s");
        }elseif($process_location === 3 && $contract->process2_status === 1 && $contract->process3_status === 0){
            $contract->process3_status  = $request->input('status');
            $contract->process3_user_id = $request->get('user_id');
            $contract->process3_time    = date('Y-m-d H:i:s');
        }elseif($process_location === 4 && $contract->process3_status === 1 && $contract->process4_status == 0){
            $contract->process4_status  = $request->input('status');
            $contract->process4_user_id = $request->get('user_id');
            $contract->process4_time    = date("Y-m-d H:i:s");
            $contract->sn_inner         = $request->input('sn_inner');
        }else{
            throw new \Exception("合同审核错误");
        }
        $contract->save();
        //</editor-fold>
        //<editor-fold desc="审批不通过">
        if($request->input('status') == -1){
            $contract->process0_status  = 0;
            $contract->process0_time    = null;
            $contract->process1_status  = 0;
            $contract->process1_user_id = null;
            $contract->process1_time    = null;
            $contract->process2_status  = 0;
            $contract->process2_user_id = null;
            $contract->process2_time    = null;
            $contract->process3_status  = 0;
            $contract->process3_user_id = null;
            $contract->process3_time    = null;
            $contract->process4_status  = 0;
            $contract->process4_user_id = null;
            $contract->process4_time    = null;
            $contract->save();
            return [];
        }
        //</editor-fold>
        //<editor-fold desc="写入审批记录">
        $this->_review($request,$contract,$process_location);
        //</editor-fold>
        return [];
    }

    private function _preview(Request $request,Contract $contract){
        /** @noinspection PhpUndefinedClassInspection */
        $data = ReviewLog::query()->updateOrInsert(
            [
                'model'=>'contracts',
                'foreign_key'=>$contract->id,
                'role_id'=>$request->get('role_id'),
                'status'=>0
            ],
            [
                'user_id'=>$request->get('user_id'),
                'name'=>Config::get('constants.REVIEW')[$request->get('role_id')],
                'suggestion'=>$request->input('suggestion'),
                'status'=>1,
            ]);
        return $data;
    }

    //角色所处流程位置
    private function _process_location($role_id){
//        $role_id = $request->get('role_id');

        #当前角色，是否可提交审批
        /** @noinspection PhpUndefinedClassInspection */
        $role_ids = array_keys(Config::get('constants.REVIEW'));
        //登录角色所处的审批流程位置
        $process_location = array_search($role_id,$role_ids);
        return $process_location;
    }

    /**
     * 提交审核02207
     * 只有发起者有这个动作
     * @queryParam contract required 合同id
     * @response {
     * }
     * @param Request $request
     * @param Contract $contract
     * @return array
     * @throws \Exception
     */
    function preview(Request $request,Contract $contract){
        $role_id = $request->get('role_id');
        $user_id = $request->get('user_id');

        #当前角色，是否可提交审批
        /** @noinspection PhpUndefinedClassInspection */
        $role_ids = array_keys(Config::get('constants.REVIEW'));
        //登录角色所处的审批流程位置
        $process_location = array_search($role_id,$role_ids);

        if($process_location > 0){
            throw new \Exception("只有销售部业务员才能发起提交审核");
        }

        //<editor-fold desc="登录角色提交当前审批">
        //当前位置状态
//        $current_process_status_field = 'process'.$process_location.'_status';
        $can = [0,-1];
        if($process_location === 0){
            if(in_array($contract->process0_status, $can) ){
                $contract->process0_status = 1;
                $contract->process0_time   = date('Y-m-d H:i:s');
                $contract->process0_user_id= $user_id;
//                return [];
            }else{
                throw new \Exception("做为申请人的您，不可提交审批");
            }
//        }elseif ($process_location === 1 ){
//            if($contract->process0_status === "1" && $contract->process1_status == 0){
//                $contract->process1_status = 1;
//                $contract->process1_time   = date('Y-m-d H:i:s');
//                $contract->process1_user_id= $user_id;
//            }else{
//                throw new \Exception("作为商务会签人的您，不可提交审批");
//            }
//        }elseif ($process_location === 2 ){
//            if($contract->process1_status === "1" && $contract->process2_status == 0){
//                $contract->process2_status = 1;
//                $contract->process2_time   = date('Y-m-d H:i:s');
//                $contract->process2_user_id= $user_id;
//            }else{
//                throw new \Exception("作为业务会签人的您，不可提交审批");
//            }
//        }elseif ($process_location === 3 ){
//            if($contract->process2_status === "1" && $contract->process3_status == 0){
//                $contract->process3_status = 1;
//                $contract->process3_time   = date('Y-m-d H:i:s');
//                $contract->process3_user_id= $user_id;
//            }else{
//                throw new \Exception("作为审批人的您，已经提交审批，不可重复提交");
//            }
//        }elseif ($process_location === 4 ){
//            if($contract->process3_status === "1" && $contract->process4_status == 0){
//                $contract->process4_status = 1;
//                $contract->process4_time   = date('Y-m-d H:i:s');
//                $contract->process4_user_id= $user_id;
//            }else{
//                throw new \Exception("作为审批人的您，已经提交审批，不可重复提交");
//            }
//        }else{
//            throw new \Exception("合同提交审核错误");
        }
        $contract->save();

        $this->_preview($request,$contract);

        return [];
        //</editor-fold>
    }

    private function _review(Request $request,Contract $contract,$process_location){
        /** @noinspection PhpUndefinedClassInspection */
        $role_ids = array_keys(Config::get('constants.REVIEW'));
        $role_id = $role_ids[$process_location];
        /** @noinspection PhpUndefinedClassInspection */
        $data = ReviewLog::query()->updateOrInsert(
            [
                'model'=>'contracts',
                'foreign_key'=>$contract->id,
                'role_id'=>$role_id,
                'status'=>0
            ],
            [
                'user_id'=>$request->get('user_id'),
                'name'=>Config::get('constants.REVIEW')[$request->get('role_id')],
                'status'=>$request->input('status'),
                'suggestion'=>$request->input('suggestion'),
                'created_at'=>date("Y-m-d H:i:s"),
                'updated_at'=>date("Y-m-d H:i:s"),
            ]);
        return $data;
    }

    /**
     * 审核02208
     *
     * @queryParam contract required 合同id
     * @queryParam status required 审核 -1-审核不通过、1-审核通过
     * @queryParam inner_sn string 内部管理合同该编号(只有最后一步归档才需要)
     * @queryParam suggestion 建议
     * @response {
     * }
     * @param Request $request
     * @param Contract $contract
     * @return array
     * @throws \Exception
     */
    function review(Request $request,Contract $contract){
        $process_location = $this->_process_location($request->get('role_id'));

        if($process_location === 0){
            throw new \Exception("你没有审批权限");
        }elseif ($process_location === 1){
            if ($contract->process0_status === "1"){
                $contract->process1_status = $request->input('status');
                $contract->process1_time    = \date("Y-m-d H:i:s");
                $contract->process1_user_id = $request->get('user_id');
            }else{
                throw new \Exception("作为商务会签人的您，无法有效审核");
            }
        }elseif($process_location === 2){
            if ($contract->process1_status === "1"){
                $contract->process2_status = $request->input('status');
                $contract->process2_user_id = $request->get('user_id');
                $contract->process2_time    = \date("Y-m-d H:i:s");
            }else{
                throw new \Exception("作为业务会签人的您，无法有效审核");
            }
        }elseif($process_location === 3){
            if ($contract->process2_status === "1"){
                $contract->process3_status  = $request->input('status');
                $contract->process3_user_id = $request->get('user_id');
                $contract->process3_time    = \date("Y-m-d H:i:s");
            }else{
                throw new \Exception("作为总经理会签人的您，无法有效审核");
            }
        }
//        elseif($process_location === 4){
//            if ($contract->process3_status === "1"){
//                $contract->process4_status  = $request->input('status');
//                $contract->process4_user_id = $request->get('user_id');
//                $contract->process4_time    = \date("Y-m-d H:i:s");
//                $contract->inner_sn         = $request->input('inner_sn');
//            }else{
//                throw new \Exception("作为归档人的您，无法有效审核");
//            }
//        }
        else{
            throw new \Exception("合同审核错误");
        }

        if($request->input('status') == -1){
            $contract->process0_status  = -1;
            $contract->process0_time    = null;
            $contract->process1_status  = 0;
            $contract->process1_user_id = null;
            $contract->process1_time    = null;
            $contract->process2_status  = 0;
            $contract->process2_user_id = null;
            $contract->process2_time    = null;
            $contract->process3_status  = 0;
            $contract->process3_user_id = null;
            $contract->process3_time    = null;
            $contract->process4_status  = 0;
            $contract->process4_user_id = null;
            $contract->process4_time    = null;
        }

        $contract->save();

        $this->_review($request,$contract,$process_location);

        return [];
    }

    /**
     * 归档02209
     * @queryParam contract required int 合同id
     * @queryParam inner_sn required string 内部管理合同该编号
     * @response {
     * }
     * @param Request $request
     * @param Contract $contract
     * @return array
     * @throws \Exception
     */
    function filing(Request $request,Contract $contract){
        /** @noinspection PhpUndefinedClassInspection */
        DB::transaction(function()use($request,$contract){
            $process_location = $this->_process_location($request->get('role_id'));
            /** @noinspection PhpUndefinedClassInspection */
            #去除审批流程最后一个角色role_id
            $array = array_keys(Config::get('constants.REVIEW'));
            $last_role_id= array_pop($array);
            if($last_role_id != $request->get('role_id')){
                throw new \Exception('您没有归档权限');
            }

            if($contract->process3_status != 1){
                throw new \Exception('请做完审批流程，方可归档');
            }
            $contract->process4_status = 1;
            $contract->process4_time = \date('Y-m-d H:i:s');
            $contract->process4_user_id = $request->get('user_id');
            $contract->inner_sn = $request->input('inner_sn');
            $contract->save();

            /** @noinspection PhpUndefinedClassInspection */
            $role_ids = array_keys(Config::get('constants.REVIEW'));
            $role_id = $role_ids[$process_location];
            /** @noinspection PhpUndefinedClassInspection */
            ReviewLog::query()->updateOrInsert(
                [
                    'model'=>'contracts',
                    'foreign_key'=>$contract->id,
                    'role_id'=>$role_id,
                    'status'=>0
                ],
                [
                    'user_id'=>$request->get('user_id'),
                    'name'=>Config::get('constants.REVIEW')[$request->get('role_id')],
                    'status'=>1,
                    'suggestion'=>$request->input('suggestion',''),
                ]);
        });


        return [];
    }

    /**
     * 审批日志02211
     * @response {
     *   [
     *      "id":7,
     *      "process_name":"步骤",
     *      "process_users_name":"办理人",
     *      "depart_name":"部门名称",
     *      "process_status":"办理状态",
     *      "process_result":"办理结果",
     *      "process_suggestion":"办理意见",
     *      "process_time":"办理时间"
     *   ]
     * }
     * @param Request $request
     * @param Contract $contract
     * @return array
     * @throws \Exception
     */
    function review_list(Request $request,Contract $contract){
        $r = ReviewLog::with(['users','roles'])->where("model","contracts")->where("foreign_key",$contract->id)->orderBy("id","asc")->get()->map(function($item,$key){
            $data = [];
//            $data['name'] = data_get($item,"name");
            $data['process_users_name'] = data_get($item,"users.name");
            $data['depart_name'] = data_get($item,"roles.name");
//            $data['roles_name'] = data_get($item,"roles.name");
            $data['process_name'] = Config::get("constants.REVIEW")[data_get($item,'roles.id')];
            $data['process_status'] = data_get($item,"status") == 0 ? "未办理" : "已办理";
            if(data_get($item,"status") == -1){
                $data['process_result'] = "退回";
            }elseif(data_get($item,'status') == 0){
                $data['process_result'] = '未办理';
            }else{
                $process_location = $this->_process_location(data_get($item, 'role_id'));
                if($process_location == 0){
                    $data['process_result'] = "提交";
                }elseif ($process_location == 4){
                    $data['process_result'] = '归档';
                }else{
                    $data['process_result'] = '同意';
                }
            }
            $data['process_suggestion'] = data_get($item,"suggestion");
            $data['process_time']       = (string)data_get($item,"updated_at");
            return $data;
        });
        return $r;
    }
}
