<?php

namespace Kopp\Models;

use Illuminate\Database\Eloquent\Model;

class Host extends Model
{
    protected $connection = 'zabbixdb';
    protected $table = 'hosts';
    protected $primaryKey = 'hostid';
    public $timestamps = false; // Не использовать поля created_at и updated_at

    public function items()
    {
        return $this->hasMany('Kopp\Models\Item', 'hostid', 'hostid')->orderBy('name');
    }

    public function interfaces()
    {
        return $this->belongsTo('Kopp\Models\Interfaces', 'hostid', 'hostid');
    }
}
