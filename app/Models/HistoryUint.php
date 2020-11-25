<?php

namespace Kopp\Models;

use Illuminate\Database\Eloquent\Model;

class HistoryUint extends Model
{
    protected $connection = 'zabbixdb';
    protected $table = 'history_uint';
    public $timestamps = false; // Не использовать поля created_at и updated_at
}
