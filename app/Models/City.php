<?php

namespace Kopp\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $table = 'cities';
    public $timestamps = false; // Не использовать поля created_at и updated_at

    public function filial()
    {
        return $this->belongsTo('Kopp\Models\Filial', 'id_filial', 'id');
    }

    public function offices()
    {
        return $this->hasMany('Kopp\Models\Office', 'id_city', 'id')->orderBy('name');
    }

    public function troubles()
    {
        return $this->hasMany('Kopp\Models\Trouble', 'id_city', 'id');
    }

    // Изменение name перед записью в БД
    public function setNameAttribute($value)
    {
        $value = preg_replace('/\t/u', ' ', $value); // Замена табуляции на пробел
        $value = preg_replace('/([" \'])\1+/u', '$1', $value); // Удаление повторяющихся пробелов и кавычек
        $value = trim($value);
        $value = preg_replace('/([\.,])([а-яёй0-9])/u', '$1 $2', $value); // Добавление пробела между (точкой, запятой) и (буквой, цифрой)
        $this->attributes['name'] = preg_replace('/\r\n/u', '', $value); // Удаление переводов строк
    }
}
