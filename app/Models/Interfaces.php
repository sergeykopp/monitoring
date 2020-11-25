<?php

namespace Kopp\Models;

use Illuminate\Database\Eloquent\Model;

class Interfaces extends Model
{
    protected $connection = 'zabbixdb';
    protected $table = 'interface';
    protected $primaryKey = 'interfaceid';
    public $timestamps = false; // Не использовать поля created_at и updated_at
}
