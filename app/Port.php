<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * This is the model class for table "ports".
 *
 * @property string $id
 * @property string $name
 * @property string $address
 * @property int $status
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Port newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Port newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Port onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Port query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Port whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Port whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Port whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Port whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Port whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Port whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Port whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Port withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Port withoutTrashed()
 * @mixin \Eloquent
 */
class Port extends Model
{
    use SoftDeletes;
    protected $attributes = [
        'status'=>1,
    ];
}
