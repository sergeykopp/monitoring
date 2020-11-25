<?php

namespace Kopp\Models;

use Illuminate\Database\Eloquent\Model;

class Cause extends Model
{
    protected $table = 'causes';
    public $timestamps = false; // Не использовать поля created_at и updated_at

    public function troubles()
    {
        return $this->hasMany('Kopp\Models\Trouble', 'id_cause', 'id');
    }
}
