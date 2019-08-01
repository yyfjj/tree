<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
/**
 * This is the model class for table "charge_items".
 *
 * @property string $id
 * @property string $code 费用科目代码
 * @property string $name 费用科目名称
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ChargeItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ChargeItem newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\ChargeItem onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ChargeItem query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ChargeItem whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ChargeItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ChargeItem whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ChargeItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ChargeItem whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ChargeItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ChargeItem withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\ChargeItem withoutTrashed()
 * @mixin \Eloquent
 */
class ChargeItem extends Model
{
    use SoftDeletes;

}
