<?php

namespace Kopp\Models;

use Illuminate\Database\Eloquent\Model;

class GroupServices extends Model
{
    protected $table = 'groups_services';
    public $timestamps = false; // Не использовать поля created_at и updated_at

    public function services()
    {
        return $this->hasMany('Kopp\Models\Service', 'id_group_service', 'id');
    }
}
