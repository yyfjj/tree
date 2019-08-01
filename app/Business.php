<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * This is the model class for table "businesses".
 *
 * @property string $id
 * @property string $name 名字
 * @property string $parent_id
 * @property int $status
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Business[] $master_businesses
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Business[] $slaver_businesses
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Business newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Business newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Business onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Business query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Business whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Business whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Business whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Business whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Business whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Business whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Business whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Business withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Business withoutTrashed()
 * @mixin \Eloquent
 */
class Business extends Model
{
    //
    use SoftDeletes;
    protected $attributes = [
        'status'=>1,
        'parent_id'=>0,
    ];

    function master_businesses(){
        return $this->hasMany('\App\Business','parent_id','id');
    }

    function slaver_businesses(){
        return $this->hasMany('\App\Business','parent_id','id');
    }
}
