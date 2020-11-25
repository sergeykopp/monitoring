<?php

namespace Kopp\Models;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    protected $connection = 'zabbixdb';
    protected $table = 'history';
    public $timestamps = false; // Не использовать поля created_at и updated_at
}
