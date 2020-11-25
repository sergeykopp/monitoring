<?php

namespace Kopp\Models;

use Illuminate\Database\Eloquent\Model;

class Trigger extends Model
{
    protected $connection = 'zabbixdb';
    protected $table = 'triggers';
    protected $primaryKey = 'triggerid';
    public $timestamps = false; // Не использовать поля created_at и updated_at

    public function events()
    {
        return $this->hasMany('Kopp\Models\Event', 'objectid', 'triggerid');
    }

    public function functions()
    {
        return $this->hasOne('Kopp\Models\Functions', 'triggerid', 'triggerid');
    }
}
