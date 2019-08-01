<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Route
 *
 * @property int $id
 * @property string $name
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Route newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Route newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Route onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Route query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Route whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Route whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Route whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Route whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Route whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Route whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Route withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Route withoutTrashed()
 * @mixin \Eloquent
 */
class Route extends Model
{
    use SoftDeletes;
    protected $attributes = [
    'status'=>1,
    ];
}
