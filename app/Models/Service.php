<?php

namespace Kopp\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $table = 'services';
    public $timestamps = false; // Не использовать поля created_at и updated_at

    public function groupServices()
    {
        return $this->belongsTo('Kopp\Models\GroupServices', 'id_group_services', 'id');
    }

    public function troubles()
    {
        return $this->hasMany('Kopp\Models\Trouble', 'id_service', 'id');
    }
}
