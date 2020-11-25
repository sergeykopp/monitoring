<?php

namespace Kopp\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGroupServicesRequest extends FormRequest
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
        if ($this->request->has('delete')){
            return [];
        } else {
            return [
                'name' => 'required|unique:groups_services,name',
            ];

        }
    }

    // Сообщения ошибок валидации
    public function messages()
    {
        return [
            'name.required' => 'Поле НАИМЕНОВАНИЕ обязательно к заполнению',
            'name.unique' => 'Такая ГРУППА СЕРВИСОВ уже есть в базе',
        ];
    }
}
