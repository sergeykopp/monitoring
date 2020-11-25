<?php

namespace Kopp\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOfficeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    // Подготовка перед валидацией
    public function prepareForValidation()
    {

    }

    // Правила валидации
    public function rules()
    {
        if ($this->request->has('delete')) {
            return [];
        }
        return [
            'id_city' => 'exists:cities,id',
            'name' => 'required',
            'address' => 'required',
        ];
    }

    // Сообщения ошибок валидации
    public function messages()
    {
        return [
            'id_city.exists' => 'Такого ГОРОДА нет в базе',
            'name.required' => 'Поле НАИМЕНОВАНИЕ обязательно к заполнению',
            'address.required' => 'Поле АДРЕС обязательно к заполнению',
        ];
    }
}
