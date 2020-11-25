<?php

namespace Kopp\Drivers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class LogDriver
{
	// Логирование IP-адресов и URI запросов
	public static function requestLog($remoteAddr, $httpHost, $requestUri, $parameters)
	{
		$appendString = '[' . strftime('%d.%m.%Y %H:%M') . '] - IP: ' . $remoteAddr . ";\tURI: " . $httpHost . $requestUri . ';';
		if(!empty($parameters)) {
            $new_arr = [];
            // Исключение из массива пустых значений и токена
            foreach($parameters as $key=>$value){
                if('' == $value or '_token' == $key){
		            continue;
                }
                $new_arr[$key] = $value;
            }
            $quantity = count($new_arr);
            $currentIndex = 1;
            $appendString .= "\tPARAMETERS: ";
		    foreach($new_arr as $key=>$value){
		        if(is_array($value)){
                    $appendString .= $key . ': ' . json_encode($value);
                } else {
                    $appendString .= $key . ': ' . $value;
                }
		        if($currentIndex == $quantity) {
                    $appendString .= ';';
                } else{
                    $appendString .= ', ';
                }
                $currentIndex++;
            }
        }
        $appendString .= "\r\n";
        try {
            File::append(config('settings.request_log'), $appendString);
        } catch (\Exception $e) {

        }
	}
	
    // Логирование при сохранении проблем
    public static function storeTrouble ($subject, $trouble)
    {
        $subject .= ' пользователем ' . Auth::user()->name;
        if(null != $trouble->directorate) {
            $office = $trouble->directorate->name . ' дирекция';
            if (null != $trouble->filial) {
                $office .=  '|' . $trouble->filial->name . ' филиал';
            }
            if (null != $trouble->city) {
                $office .= '|г. ' . $trouble->city->name;
            }
            if (null != $trouble->office) {
                $office .= '|' . $trouble->office->name;
                $office .= '|' . $trouble->office->address;
            }
        } else {
            $office = 'Все дирекции';
        }
        $words = explode(' ', $trouble->user->name);
        $firstLetter = mb_substr($words[1],0,1,"UTF-8");
        $lastLetter = mb_substr($words[2],0,1,"UTF-8");
        $appendString = '[' . strftime('%d.%m.%Y %H:%M') .", $subject] - \r\n"
            . '    Дирекция|Филиал|Город|Подразделение : ' . $office . "\r\n"
            . '    Время события (МСК) : ' . $trouble->started_at . "\r\n"
            . '    Источник события : ' . $trouble->source->name . "\r\n"
            . '    Описание : ' . str_replace("\r\n", "\r\n        ", $trouble->description) . "\r\n"
            . '    Решение : ' . str_replace("\r\n", "\r\n        ", $trouble->action) . "\r\n"
            . '    Заявка в ОТРС : ' . $trouble->incident . "\r\n"
            . '    Время завершения (МСК) : ' . $trouble->finished_at . "\r\n"
            . '    Сервис : ' . $trouble->service->name . "\r\n"
            . '    Приоритет события : ' . $trouble->status->name . "\r\n"
            . '    Дежурный : ' . $words[0] . ' ' . $firstLetter . '.' . $lastLetter . '.' . "\r\n";
        try {
            File::append(config('settings.monitoring_log'), $appendString);
        } catch (\Exception $e) {

        }
    }
	
	// Удаление старых логов о проблемах
    public static function deleteOldTroubles ()
	{
		$deleteToDate = strftime('%d.%m.%Y', time() - config('settings.periodStoreInformation') * 24 * 60 * 60 + 24 * 60 * 60);
		$file = File::get(config('settings.monitoring_log'));
		$pos = strpos($file, '[' . $deleteToDate);
		if(0 != $pos){
			$newContent = substr($file, $pos);
			File::put(config('settings.monitoring_log'), $newContent);
		}
	}

    // Логирование при сохранении подразделений
    public static function storeOffice ($subject, $office)
    {
        $subject .= ' пользователем ' . Auth::user()->name;
        $appendString = '[' . strftime('%d.%m.%Y %H:%M') .", $subject] - \r\n"
            . '    Город : ' . $office->city->name . "\r\n"
            . '    Наименование : ' . $office->name . "\r\n"
            . '    Адрес : ' . $office->address . "\r\n"
            . '    Заметки : ' . str_replace("\r\n", "\r\n        ", $office->notes) . "\r\n";
        try {
            File::append(config('settings.offices_log'), $appendString);
        } catch (\Exception $e) {

        }
    }

    // Логирование при сохранении городов
    public static function storeCity ($subject, $city)
    {
        $subject .= ' пользователем ' . Auth::user()->name;
        $appendString = '[' . strftime('%d.%m.%Y %H:%M') .", $subject] - \r\n"
            . '    Филиал : ' . $city->filial->name . "\r\n"
            . '    Наименование : ' . $city->name . "\r\n";
        try {
            File::append(config('settings.cities_log'), $appendString);
        } catch (\Exception $e) {

        }
    }

    // Логирование при сохранении филиалов
    public static function storeFilial ($subject, $filial)
    {
        $subject .= ' пользователем ' . Auth::user()->name;
        $appendString = '[' . strftime('%d.%m.%Y %H:%M') .", $subject] - \r\n"
            . '    Дирекция : ' . $filial->directorate->name . "\r\n"
            . '    Наименование : ' . $filial->name . "\r\n";
        try {
            File::append(config('settings.filials_log'), $appendString);
        } catch (\Exception $e) {

        }
    }

    // Логирование при сохранении дирекций
    public static function storeDirectorate ($subject, $directorate)
    {
        $subject .= ' пользователем ' . Auth::user()->name;
        $appendString = '[' . strftime('%d.%m.%Y %H:%M') .", $subject] - \r\n"
            . '    Наименование : ' . $directorate->name . "\r\n";
        try {
            File::append(config('settings.directorates_log'), $appendString);
        } catch (\Exception $e) {

        }
    }

    // Логирование при сохранении пользователей
    public static function storeUser ($subject, $user)
    {
        $subject .= ' пользователем ' . Auth::user()->name;
        $roles = '';
        foreach($user->roles as $role){
            $roles .= $role->name . ', ';
        }
        $appendString = '[' . strftime('%d.%m.%Y %H:%M') .", $subject] - \r\n"
            . '    ФИО : ' . $user->name . "\r\n"
            . '    Имя пользователя : ' . $user->login . "\r\n"
            . '    Почтовый адрес : ' . $user->email . "\r\n"
            . '    Роли : ' . $roles . "\r\n";
        try {
            File::append(config('settings.users_log'), $appendString);
        } catch (\Exception $e) {

        }
    }

    // Логирование при сохранении источников событий
    public static function storeSource ($subject, $source)
    {
        $subject .= ' пользователем ' . Auth::user()->name;
        $appendString = '[' . strftime('%d.%m.%Y %H:%M') .", $subject] - \r\n"
            . '    Наименование : ' . $source->name . "\r\n";
        try {
            File::append(config('settings.sources_log'), $appendString);
        } catch (\Exception $e) {

        }
    }

    // Логирование при сохранении группы сервисов
    public static function storeGroupServices ($subject, $groupServices)
    {
        $subject .= ' пользователем ' . Auth::user()->name;
        $appendString = '[' . strftime('%d.%m.%Y %H:%M') .", $subject] - \r\n"
            . '    Наименование : ' . $groupServices->name . "\r\n";
        try {
            File::append(config('settings.sources_log'), $appendString);
        } catch (\Exception $e) {

        }
    }

    // Логирование при сохранении сервиса
    public static function storeService ($subject, $service)
    {
        $subject .= ' пользователем ' . Auth::user()->name;
        $appendString = '[' . strftime('%d.%m.%Y %H:%M') .", $subject] - \r\n"
            . '    Наименование : ' . $service->name . "\r\n";
        try {
            File::append(config('settings.sources_log'), $appendString);
        } catch (\Exception $e) {

        }
    }

    // Логирование ошибок
    public static function error($message)
    {
        $message = '[' . strftime('%d.%m.%Y %H:%M') . "] - \r\n" . $message;
        try {
            File::append(config('settings.error_log'), $message);
        } catch (\Exception $e) {

        }
    }

    // Логирование для поиска причин непонятных действий
    public static function findBugs($now)
    {
        try {
            if('00' == strftime('%S', $now)) {
                File::append(config('settings.findBugs_log'), strftime('%d.%m.%Y %H:%M:%S', $now - 60) . " - поправочка\r\n");
            }
            File::append(config('settings.findBugs_log'), strftime('%d.%m.%Y %H:%M:%S', $now) . "\r\n");
        } catch (\Exception $e) {

        }
    }
	
	// Тест шедулера
    public static function shedulerTest($startProcess, $comment)
    {
        try {
			File::append(config('settings.sheduler_log'), '[' . strftime('%d.%m.%Y %H:%M:%S', $startProcess) . '] - ' . $comment . ' (' . (time() - $startProcess) . ' сек.)' . "\r\n");
        } catch (\Exception $e) {

        }
    }
}