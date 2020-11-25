<?php

namespace Kopp\Models;

use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    protected $connection = 'zabbixdb';
    protected $table = 'actions';
    protected $primaryKey = 'actionid';
    public $timestamps = false; // Не использовать поля created_at и updated_at
}
