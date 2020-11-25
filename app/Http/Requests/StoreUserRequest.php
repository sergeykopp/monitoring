<?php

namespace Kopp\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
        return [
        ];
    }

    // Сообщения ошибок валидации
    public function messages()
    {
        return [
        ];
    }
}
