<?php

namespace Kopp\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $connection = 'zabbixdb';
    protected $table = 'events';
    protected $primaryKey = 'eventid';
    public $timestamps = false; // Не использовать поля created_at и updated_at

    public function trigger()
    {
        return $this->hasOne('Kopp\Models\Trigger', 'triggerid', 'objectid');
    }
}
