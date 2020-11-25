<?php

namespace Kopp\Models;

use Illuminate\Database\Eloquent\Model;

class Functions extends Model
{
    protected $connection = 'zabbixdb';
    protected $table = 'functions';
    protected $primaryKey = 'functionid';
    public $timestamps = false; // Не использовать поля created_at и updated_at

    public function item()
    {
        return $this->hasOne('Kopp\Models\Item', 'itemid', 'itemid');
    }

    public function trigger()
    {
        return $this->hasOne('Kopp\Models\Trigger', 'triggerid', 'triggerid');
    }
}
