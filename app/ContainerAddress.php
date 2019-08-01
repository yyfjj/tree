<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
/**
 * This is the model class for table "container_addresses".
 *
 * @property string $id
 * @property string $address 装箱地点
 * @property int $is_up 是否装箱地点
 * @property int $is_down 是否送箱地点
 * @property int $status
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 * @property array $container_address_data
 * @property object $segment_businesses
 * @property object $master_businesses
 * @property object $slaver_businesses
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ContainerAddress newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ContainerAddress newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ContainerAddress query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ContainerAddress whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ContainerAddress whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ContainerAddress whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ContainerAddress whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ContainerAddress whereIsDown($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ContainerAddress whereIsUp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ContainerAddress whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ContainerAddress whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ContainerAddress extends Model
{
    function container_address_data(){
        return $this->hasMany('\App\ContainerAddressData','container_addresses_id','id');
    }
}
