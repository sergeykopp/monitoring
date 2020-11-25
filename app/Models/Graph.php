<?php

namespace Kopp\Models;

use Illuminate\Database\Eloquent\Model;

class Graph extends Model
{
    protected $connection = 'zabbixdb';
    protected $table = 'graphs';
    protected $primaryKey = 'graphid';
    public $timestamps = false; // Не использовать поля created_at и updated_at

    // Связь с таблицей items через таблицу graphs_items
    public function items()
    {
        return $this->belongsToMany('Kopp\Models\Item', 'graphs_items', 'graphid', 'itemid');
    }
}
