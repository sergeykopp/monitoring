<?php

namespace Kopp\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';
    public $timestamps = false; // Не использовать поля created_at и updated_at

    // Связь с таблицей users через таблицу role_user
    public function users()
    {
        return $this->belongsToMany('Kopp\Models\User', 'role_user', 'id_role', 'id_user');
    }
}
