<?php

namespace Kopp\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTroubleRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    // Подготовка перед валидацией
    public function prepareForValidation()
    {
        // Преобразование incident
        if ($this->request->has('incident')) {
            // Удаление в инциденте всего, кроме цифр
            $this->request->set('incident', preg_replace('/\D/u', '', $this->request->get('incident')));
        }
        // Преобразование started_at
        if ($this->request->has('started_at')) {
            // Удаление лишних пробелов
            $this->request->set('started_at', trim($this->request->get('started_at')));
            $this->request->set('started_at', preg_replace('/ +/u', ' ', $this->request->get('started_at')));
            // Из XX/XX/XX XX:XX в XX.XX.XXXX XX:XX
            if (preg_match("/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2}) ([0-9]{1,2})\:([0-9]{1,2})/u", $this->request->get('started_at'), $regs)
                or preg_match("/([0-9]{1,2})\.([0-9]{1,2})\.([0-9]{2,4}) ([0-9]{1,2})\:([0-9]{1,2})/u", $this->request->get('started_at'), $regs)) {
                if (2 > mb_strlen($regs[1], "utf-8")) {
                    $regs[1] = '0' . $regs[1];
                }
                if (2 > mb_strlen($regs[2], "utf-8")) {
                    $regs[2] = '0' . $regs[2];
                }
                if (4 != mb_strlen($regs[3], "utf-8")) {
                    $regs[3] = '20' . $regs[3];
                }
                if (2 > mb_strlen($regs[4], "utf-8")) {
                    $regs[4] = '0' . $regs[4];
                }
                if (2 > mb_strlen($regs[5], "utf-8")) {
                    $regs[5] = '0' . $regs[5];
                }
                $this->request->set('started_at', "$regs[1].$regs[2].$regs[3] $regs[4]:$regs[5]");
            }
        }
        // Преобразование finished_at
        if ($this->request->has('finished_at')) {
            // Удаление лишних пробелов
            $this->request->set('finished_at', trim($this->request->get('finished_at')));
            $this->request->set('finished_at', preg_replace('/ +/u', ' ', $this->request->get('finished_at')));
            // Из XX/XX/XX XX:XX в XX.XX.XXXX XX:XX
            if (preg_match("/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2}) ([0-9]{1,2})\:([0-9]{1,2})/u", $this->request->get('finished_at'), $regs)
                or preg_match("/([0-9]{1,2})\.([0-9]{1,2})\.([0-9]{2,4}) ([0-9]{1,2})\:([0-9]{1,2})/u", $this->request->get('finished_at'), $regs)) {
                if (2 > mb_strlen($regs[1], "utf-8")) {
                    $regs[1] = '0' . $regs[1];
                }
                if (2 > mb_strlen($regs[2], "utf-8")) {
                    $regs[2] = '0' . $regs[2];
                }
                if (4 != mb_strlen($regs[3], "utf-8")) {
                    $regs[3] = '20' . $regs[3];
                }
                if (2 > mb_strlen($regs[4], "utf-8")) {
                    $regs[4] = '0' . $regs[4];
                }
                if (2 > mb_strlen($regs[5], "utf-8")) {
                    $regs[5] = '0' . $regs[5];
                }
                $this->request->set('finished_at', "$regs[1].$regs[2].$regs[3] $regs[4]:$regs[5]");
            }
        }
    }

    // Правила валидации
    public function rules()
    {
        if ($this->request->has('delete')) {
            return [];
        } else {
            return [
                'id_directorate' => 'exists:directorates,id|nullable',
                'id_filial' => 'exists:filials,id|nullable',
                'id_city' => 'exists:cities,id|nullable',
                'id_office' => 'exists:offices,id|nullable',
                'started_at' => 'required|date_format:d.m.Y H:i|before:tomorrow',
                'id_source' => 'required|min:1|exists:sources,id',
                'description' => 'required',
                'incident' => 'digits_between:5,10',
                'finished_at' => 'date_format:d.m.Y H:i|after:started_at',
                'id_service' => 'required|min:1|exists:services,id',
                'id_status' => 'required|min:1|exists:statuses,id',
                'id_cause' => 'exists:causes,id|required_with:risk|nullable',
            ];
        }
    }

    // Сообщения ошибок валидации
    public function messages()
    {
        return [
            'id_directorate.exists' => 'Такой ДИРЕКЦИИ нет в базе',
            'id_filial.exists' => 'Такого ФИЛИАЛА нет в базе',
            'id_city.exists' => 'Такого ГОРОДА нет в базе',
            'id_office.exists' => 'Такого ПОДРАЗДЕЛЕНИЯ нет в базе',
            'started_at.required' => 'Поле ВРЕМЯ СОБЫТИЯ обязательно к заполнению',
            'started_at.date_format' => 'Поле ВРЕМЯ СОБЫТИЯ не соответствует шаблону XX.XX.XXXX XX:XX',
            'started_at.before' => 'Значение поля ВРЕМЯ СОБЫТИЯ не должно быть в будущем',
            'id_source.required' => 'Поле ИСТОЧНИК СОБЫТИЯ обязательно к заполнению',
            'id_source.exists' => 'Такого ИСТОЧНИКА СОБЫТИЯ нет в базе',
            'description.required' => 'Поле ОПИСАНИЕ обязательно к заполнению',
            'incident.digits_between' => 'Поле ИНЦИДЕНТ должно содержать от 5 до 10 цифр',
            'finished_at.date_format' => 'Поле ВРЕМЯ ЗАВЕРШЕНИЯ не соответствует шаблону XX.XX.XXXX XX:XX',
            'finished_at.after' => 'Значение поля ВРЕМЯ ЗАВЕРШЕНИЯ должно быть позже значения поля ВРЕМЯ СОБЫТИЯ',
            'id_service.required' => 'Поле СЕРВИС обязательно к заполнению',
            'id_service.exists' => 'Такого СЕРВИСА нет в базе',
            'id_status.required' => 'Поле ПРИОРИТЕТ СОБЫТИЯ обязательно к заполнению',
            'id_status.exists' => 'Такого ПРИОРИТЕТА СОБЫТИЯ нет в базе',
            'id_cause.exists' => 'Такой ПРИЧИНЫ нет в базе',
            'id_cause.required_with' => 'Проблема помечена как ТЕХНИЧЕСКИЙ РИСК, необходимо выбрать причину',
        ];
    }
}
