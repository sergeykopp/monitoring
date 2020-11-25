<?php

namespace Kopp\Drivers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Kopp\Models\Role;

class MailDriver
{
    // Отправка писем при сохранении проблем
    public static function storeTrouble($subject, $trouble)
    {
        $subject .= ' пользователем ' . Auth::user()->name;
        // Отправлять сообщения только пользователям с ролью администратор
        $administrators = Role::where('name','Administrator')->first()->users;
        $mailTo = [];
        foreach ($administrators as $administrator) {
            $mailTo[] = $administrator->email;
        }
		// Экранирование тегов в description и action
		$trouble->description = htmlspecialchars($trouble->description);
		$trouble->action = htmlspecialchars($trouble->action);
        // Замена \r\n на <br /> в description и action
        $trouble->description = str_replace("\r\n", "<br />", $trouble->description);
        $trouble->action = str_replace("\r\n", "<br />", $trouble->action);
        $data = [
            'trouble' => $trouble,
        ];
        try {
            Mail::send('emails.storeTrouble', $data, function ($message) use ($subject, $mailTo) {
                //$message->from('monitoringPortal@binbank.ru', 'Портал мониторинга'); // Адрес отправителя и его псевдоним
                //$message->sender('sergeykopp@binbank.ru', 'Сергей'); // Сергей <sergeykopp@binbank.ru>; от имени; Портал мониторинга <monitoringPortal@binbank.ru>
                $message->to($mailTo); // Кому
                //$message->to(['kopp2@binbank.ru', 'aanikiforov@binbank.ru']); // Кому (несколько)
                //$message->cc('kopp2@binbank.ru'); // Копия
                //$message->bcc('kopp2@binbank.ru'); // Скрытая копия
                $message->subject($subject); // Тема
            });
        } catch (\Exception $e) {
            $message = '    Ошибка при попытке отправки сообщения : ' . $subject . "\r\n";
            $message .= '    $e->getMessage() : ' . $e->getMessage() . "\r\n";
            LogDriver::error($message);
        }
    }

    // Отправка писем при сохранении подразделений
    public static function storeOffice($subject, $office)
    {
        $subject .= ' пользователем ' . Auth::user()->name;
        // Отправлять сообщения только пользователям с ролью администратор
        $administrators = Role::where('name','Administrator')->first()->users;
        $mailTo = [];
        foreach ($administrators as $administrator) {
            $mailTo[] = $administrator->email;
        }
        // Экранирование тегов в notes
        $office->notes = htmlspecialchars($office->notes);
        // Замена \r\n на <br /> в notes
        $office->notes = str_replace("\r\n", "<br />", $office->notes);
        $data = [
            'office' => $office,
        ];
        try {
            Mail::send('emails.storeOffice', $data, function ($message) use ($subject, $mailTo) {
                //$message->from('monitoringPortal@binbank.ru', 'Портал мониторинга'); // Адрес отправителя и его псевдоним
                //$message->sender('sergeykopp@binbank.ru', 'Сергей'); // Сергей <sergeykopp@binbank.ru>; от имени; Портал мониторинга <monitoringPortal@binbank.ru>
                $message->to($mailTo); // Кому
                //$message->to(['kopp2@binbank.ru', 'aanikiforov@binbank.ru']); // Кому (несколько)
                //$message->cc('kopp2@binbank.ru'); // Копия
                //$message->bcc('kopp2@binbank.ru'); // Скрытая копия
                $message->subject($subject); // Тема
            });
        } catch (\Exception $e) {
            $message = '    Ошибка при попытке отправки сообщения : ' . $subject . "\r\n";
            $message .= '    $e->getMessage() : ' . $e->getMessage() . "\r\n";
            LogDriver::error($message);
        }
    }

    // Отправка писем при сохранении города
    public static function storeCity($subject, $city)
    {
        $subject .= ' пользователем ' . Auth::user()->name;
        // Отправлять сообщения только пользователям с ролью администратор
        $administrators = Role::where('name','Administrator')->first()->users;
        $mailTo = [];
        foreach ($administrators as $administrator) {
            $mailTo[] = $administrator->email;
        }
        $data = [
            'city' => $city,
        ];
        try {
            Mail::send('emails.storeCity', $data, function ($message) use ($subject, $mailTo) {
                //$message->from('monitoringPortal@binbank.ru', 'Портал мониторинга'); // Адрес отправителя и его псевдоним
                //$message->sender('sergeykopp@binbank.ru', 'Сергей'); // Сергей <sergeykopp@binbank.ru>; от имени; Портал мониторинга <monitoringPortal@binbank.ru>
                $message->to($mailTo); // Кому
                //$message->to(['kopp2@binbank.ru', 'aanikiforov@binbank.ru']); // Кому (несколько)
                //$message->cc('kopp2@binbank.ru'); // Копия
                //$message->bcc('kopp2@binbank.ru'); // Скрытая копия
                $message->subject($subject); // Тема
            });
        } catch (\Exception $e) {
            $message = '    Ошибка при попытке отправки сообщения : ' . $subject . "\r\n";
            $message .= '    $e->getMessage() : ' . $e->getMessage() . "\r\n";
            LogDriver::error($message);
        }
    }

    // Отправка писем при сохранении филиала
    public static function storeFilial($subject, $filial)
    {
        $subject .= ' пользователем ' . Auth::user()->name;
        // Отправлять сообщения только пользователям с ролью администратор
        $administrators = Role::where('name','Administrator')->first()->users;
        $mailTo = [];
        foreach ($administrators as $administrator) {
            $mailTo[] = $administrator->email;
        }
        $data = [
            'filial' => $filial,
        ];
        try {
            Mail::send('emails.storeFilial', $data, function ($message) use ($subject, $mailTo) {
                //$message->from('monitoringPortal@binbank.ru', 'Портал мониторинга'); // Адрес отправителя и его псевдоним
                //$message->sender('sergeykopp@binbank.ru', 'Сергей'); // Сергей <sergeykopp@binbank.ru>; от имени; Портал мониторинга <monitoringPortal@binbank.ru>
                $message->to($mailTo); // Кому
                //$message->to(['kopp2@binbank.ru', 'aanikiforov@binbank.ru']); // Кому (несколько)
                //$message->cc('kopp2@binbank.ru'); // Копия
                //$message->bcc('kopp2@binbank.ru'); // Скрытая копия
                $message->subject($subject); // Тема
            });
        } catch (\Exception $e) {
            $message = '    Ошибка при попытке отправки сообщения : ' . $subject . "\r\n";
            $message .= '    $e->getMessage() : ' . $e->getMessage() . "\r\n";
            LogDriver::error($message);
        }
    }

    // Отправка писем при сохранении дирекции
    public static function storeDirectorate($subject, $directorate)
    {
        $subject .= ' пользователем ' . Auth::user()->name;
        // Отправлять сообщения только пользователям с ролью администратор
        $administrators = Role::where('name','Administrator')->first()->users;
        $mailTo = [];
        foreach ($administrators as $administrator) {
            $mailTo[] = $administrator->email;
        }
        $data = [
            'directorate' => $directorate,
        ];
        try {
            Mail::send('emails.storeDirectorate', $data, function ($message) use ($subject, $mailTo) {
                //$message->from('monitoringPortal@binbank.ru', 'Портал мониторинга'); // Адрес отправителя и его псевдоним
                //$message->sender('sergeykopp@binbank.ru', 'Сергей'); // Сергей <sergeykopp@binbank.ru>; от имени; Портал мониторинга <monitoringPortal@binbank.ru>
                $message->to($mailTo); // Кому
                //$message->to(['kopp2@binbank.ru', 'aanikiforov@binbank.ru']); // Кому (несколько)
                //$message->cc('kopp2@binbank.ru'); // Копия
                //$message->bcc('kopp2@binbank.ru'); // Скрытая копия
                $message->subject($subject); // Тема
            });
        } catch (\Exception $e) {
            $message = '    Ошибка при попытке отправки сообщения : ' . $subject . "\r\n";
            $message .= '    $e->getMessage() : ' . $e->getMessage() . "\r\n";
            LogDriver::error($message);
        }
    }

    // Отправка писем при сохранении пользователей
    public static function storeUser($subject, $user)
    {
        $subject .= ' пользователем ' . Auth::user()->name;
        // Отправлять сообщения только пользователям с ролью администратор
        $administrators = Role::where('name','Administrator')->first()->users;
        $mailTo = [];
        foreach ($administrators as $administrator) {
            $mailTo[] = $administrator->email;
        }
        $data = [
            'user' => $user,
        ];
        try {
            Mail::send('emails.storeUser', $data, function ($message) use ($subject, $mailTo) {
                //$message->from('monitoringPortal@binbank.ru', 'Портал мониторинга'); // Адрес отправителя и его псевдоним
                //$message->sender('sergeykopp@binbank.ru', 'Сергей'); // Сергей <sergeykopp@binbank.ru>; от имени; Портал мониторинга <monitoringPortal@binbank.ru>
                $message->to($mailTo); // Кому
                //$message->to(['kopp2@binbank.ru', 'aanikiforov@binbank.ru']); // Кому (несколько)
                //$message->cc('kopp2@binbank.ru'); // Копия
                //$message->bcc('kopp2@binbank.ru'); // Скрытая копия
                $message->subject($subject); // Тема
            });
        } catch (\Exception $e) {
            $message = '    Ошибка при попытке отправки сообщения : ' . $subject . "\r\n";
            $message .= '    $e->getMessage() : ' . $e->getMessage() . "\r\n";
            LogDriver::error($message);
        }
    }

    // Отправка писем при сохранении источника событий
    public static function storeSource($subject, $source)
    {
        $subject .= ' пользователем ' . Auth::user()->name;
        // Отправлять сообщения только пользователям с ролью администратор
        $administrators = Role::where('name','Administrator')->first()->users;
        $mailTo = [];
        foreach ($administrators as $administrator) {
            $mailTo[] = $administrator->email;
        }
        $data = [
            'source' => $source,
        ];
        try {
            Mail::send('emails.storeSource', $data, function ($message) use ($subject, $mailTo) {
                //$message->from('monitoringPortal@binbank.ru', 'Портал мониторинга'); // Адрес отправителя и его псевдоним
                //$message->sender('sergeykopp@binbank.ru', 'Сергей'); // Сергей <sergeykopp@binbank.ru>; от имени; Портал мониторинга <monitoringPortal@binbank.ru>
                $message->to($mailTo); // Кому
                //$message->to(['kopp2@binbank.ru', 'aanikiforov@binbank.ru']); // Кому (несколько)
                //$message->cc('kopp2@binbank.ru'); // Копия
                //$message->bcc('kopp2@binbank.ru'); // Скрытая копия
                $message->subject($subject); // Тема
            });
        } catch (\Exception $e) {
            $message = '    Ошибка при попытке отправки сообщения : ' . $subject . "\r\n";
            $message .= '    $e->getMessage() : ' . $e->getMessage() . "\r\n";
            LogDriver::error($message);
        }
    }

    // Отправка писем при сохранении группы сервисов
    public static function storeGroupServices($subject, $groupServices)
    {
        $subject .= ' пользователем ' . Auth::user()->name;
        // Отправлять сообщения только пользователям с ролью администратор
        $administrators = Role::where('name','Administrator')->first()->users;
        $mailTo = [];
        foreach ($administrators as $administrator) {
            $mailTo[] = $administrator->email;
        }
        $data = [
            'groupServices' => $groupServices,
        ];
        try {
            Mail::send('emails.storeGroupServices', $data, function ($message) use ($subject, $mailTo) {
                //$message->from('monitoringPortal@binbank.ru', 'Портал мониторинга'); // Адрес отправителя и его псевдоним
                //$message->sender('sergeykopp@binbank.ru', 'Сергей'); // Сергей <sergeykopp@binbank.ru>; от имени; Портал мониторинга <monitoringPortal@binbank.ru>
                $message->to($mailTo); // Кому
                //$message->to(['kopp2@binbank.ru', 'aanikiforov@binbank.ru']); // Кому (несколько)
                //$message->cc('kopp2@binbank.ru'); // Копия
                //$message->bcc('kopp2@binbank.ru'); // Скрытая копия
                $message->subject($subject); // Тема
            });
        } catch (\Exception $e) {
            $message = '    Ошибка при попытке отправки сообщения : ' . $subject . "\r\n";
            $message .= '    $e->getMessage() : ' . $e->getMessage() . "\r\n";
            LogDriver::error($message);
        }
    }

    // Отправка писем при сохранении сервиса
    public static function storeService($subject, $service)
    {
        $subject .= ' пользователем ' . Auth::user()->name;
        // Отправлять сообщения только пользователям с ролью администратор
        $administrators = Role::where('name','Administrator')->first()->users;
        $mailTo = [];
        foreach ($administrators as $administrator) {
            $mailTo[] = $administrator->email;
        }
        $data = [
            'service' => $service,
        ];
        try {
            Mail::send('emails.storeService', $data, function ($message) use ($subject, $mailTo) {
                //$message->from('monitoringPortal@binbank.ru', 'Портал мониторинга'); // Адрес отправителя и его псевдоним
                //$message->sender('sergeykopp@binbank.ru', 'Сергей'); // Сергей <sergeykopp@binbank.ru>; от имени; Портал мониторинга <monitoringPortal@binbank.ru>
                $message->to($mailTo); // Кому
                //$message->to(['kopp2@binbank.ru', 'aanikiforov@binbank.ru']); // Кому (несколько)
                //$message->cc('kopp2@binbank.ru'); // Копия
                //$message->bcc('kopp2@binbank.ru'); // Скрытая копия
                $message->subject($subject); // Тема
            });
        } catch (\Exception $e) {
            $message = '    Ошибка при попытке отправки сообщения : ' . $subject . "\r\n";
            $message .= '    $e->getMessage() : ' . $e->getMessage() . "\r\n";
            LogDriver::error($message);
        }
    }

    // Отправка писем по истечении 12 часов после регистрации проблем по каналам связи
    public static function channel12Hours($trouble)
    {
        $subject = 'Требуется эскалировать проблему http://monitoring/edit/' . $trouble->id . ' посредством OTRS на группу «Телеком. Каналы связи»';
        // Отправлять сообщения только пользователям с ролью диспетчер
        $users = Role::where('name','MonitoringMessageRecipients')->first()->users;
        $mailTo = [];
        foreach ($users as $user) {
            $mailTo[] = $user->email;
        }
		// Экранирование тегов в description и action
		$trouble->description = htmlspecialchars($trouble->description);
		$trouble->action = htmlspecialchars($trouble->action);
        // Замена \r\n на <br /> в description и action
        $trouble->description = str_replace("\r\n", "<br />", $trouble->description);
        $trouble->action = str_replace("\r\n", "<br />", $trouble->action);
        $data = [
            'trouble' => $trouble,
        ];
        try {
            Mail::send('emails.channel12Hours', $data, function ($message) use ($subject, $mailTo) {
                //$message->from('monitoringPortal@binbank.ru', 'Портал мониторинга'); // Адрес отправителя и его псевдоним
                //$message->sender('sergeykopp@binbank.ru', 'Сергей'); // Сергей <sergeykopp@binbank.ru>; от имени; Портал мониторинга <monitoringPortal@binbank.ru>
                $message->to($mailTo); // Кому
                //$message->to(['kopp2@binbank.ru', 'aanikiforov@binbank.ru']); // Кому (несколько)
                //$message->cc('kopp2@binbank.ru'); // Копия
                //$message->bcc('kopp2@binbank.ru'); // Скрытая копия
                $message->subject($subject); // Тема
            });
        } catch (\Exception $e) {
            $message = '    Ошибка при попытке отправки сообщения : ' . $subject . "\r\n";
            $message .= '    $e->getMessage() : ' . $e->getMessage() . "\r\n";
            LogDriver::error($message);
        }
    }

    // Отправка писем сводного отчёта
    public static function consolidatedReport($troubles = [], $file = null)
    {
        $now = time();
        $subject = 'Сводный отчёт о событиях процесса мониторинга ИТ инфраструктуры Банка за период c ' . strftime('%d.%m.%Y', $now - 24 * 60 * 60) . ' по ' . strftime('%d.%m.%Y', $now);
		// $mailTo = ['koppsv@open.ru'];
        // $mailTo = ['open24@open.ru'];
		$mailTo = ['reportsmonitoringDIT@open.ru'];
        $bcc = ['open24@open.ru', 'koppsv@open.ru'];
        $data = [
            'troubles' => $troubles,
            'subject' => $subject,
        ];
        try {
            Mail::send('emails.consolidatedReport', $data, function ($message) use ($subject, $mailTo, $bcc, $file) {
                $message->from('open24@open.ru', 'Мониторинг'); // Мониторинг <open24@open.ru>
                $message->to($mailTo); // Кому
                //$message->cc('kopp2@binbank.ru'); // Копия
                $message->bcc($bcc); // Скрытая копия
                $message->subject($subject); // Тема
				if(null != $file) {
					$message->attach(public_path() . '/' . $file, ['as' => preg_replace("/reports/u", 'Отчёт ', $file)]); // Вложение
				}
            });
        } catch (\Exception $e) {
            $message = '    Ошибка при попытке отправки сообщения : ' . $subject . "\r\n";
            $message .= '    $e->getMessage() : ' . $e->getMessage() . "\r\n";
            LogDriver::error($message);
        }
    }

    // Отправка писем при сбросе пароля
    public static function resetPassword($to, $token)
    {
        $data = [
            'title' => 'Сброс пароля',
            'url' => url(route('password.reset', $token, false)),
        ];
        try {
            Mail::send('emails.resetPassword', $data, function ($m) use ($to) {
                $m->to($to);
                $m->subject('Сброс пароля');
            });
        } catch (\Exception $e) {
            $message = '    Ошибка при попытке отправки сообщения : ' . $data['title'] . "\r\n";
            $message .= '    $e->getMessage() : ' . $e->getMessage() . "\r\n";
            LogDriver::error($message);
        }
    }
}