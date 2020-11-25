<?php

namespace Kopp\Models;

use Illuminate\Database\Eloquent\Model;

class Directorate extends Model
{
    protected $table = 'directorates';
    public $timestamps = false; // Не использовать поля created_at и updated_at

    public function filials()
    {
        return $this->hasMany('Kopp\Models\Filial', 'id_directorate', 'id')->orderBy('name');
    }

    public function troubles()
    {
        return $this->hasMany('Kopp\Models\Trouble', 'id_directorate', 'id');
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
