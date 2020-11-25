<?php

namespace Kopp\Models;

use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    protected $table = 'offices';
    public $timestamps = false; // Не использовать поля created_at и updated_at

    public function city()
    {
        return $this->belongsTo('Kopp\Models\City', 'id_city', 'id');
    }

    public function troubles()
    {
        return $this->hasMany('Kopp\Models\Trouble', 'id_office', 'id');
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

    // Изменение address перед записью в БД
    public function setAddressAttribute($value)
    {
        $value = preg_replace('/\t/u', ' ', $value); // Замена табуляции на пробел
        $value = preg_replace('/([" \'])\1+/u', '$1', $value); // Удаление повторяющихся пробелов и кавычек
        $value = trim($value);
        $value = preg_replace('/([\.,])([а-яёй0-9])/u', '$1 $2', $value); // Добавление пробела между (точкой, запятой) и (буквой, цифрой)
        $this->attributes['address'] = preg_replace('/\r\n/u', '', $value); // Удаление переводов строк
    }

    // Изменение notes перед записью в БД
    public function setNotesAttribute($value)
    {
        $value = preg_replace('/\t/u', ' ', $value); // Замена табуляции на пробел
        $value = preg_replace('/([" ]|\r\n)\1+/u', '$1', $value); // Удаление повторяющихся пробелов, кавычек и переводов строк
        $value = trim($value);
        $value = preg_replace('/(\.)([а-яёй])/u', '$1 $2', $value); // Добавление пробела между точкой и буквой
        $this->attributes['notes'] = preg_replace('/(^\r\n)|(\r\n\s*$)/u', '', $value); // Удаление переводов строк в начале и в конце текста
    }
}
