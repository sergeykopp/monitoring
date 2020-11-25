<?php

namespace Kopp\Drivers;

use Illuminate\Support\Facades\Auth;
use Kopp\Models\City;
use Kopp\Models\Office;
use Kopp\Models\Service;
use Kopp\Models\Trouble;
use Kopp\Models\Status;

class TroublesDriver
{
    // Получение актуальных проблем
    public static function actualTroubles()
    {
        $superhigh = Status::where('name', 'Чрезвычайный')->first()->id;
        $high = Status::where('name', 'Высокий')->first()->id;
        $middle = Status::where('name', 'Средний')->first()->id;
        $low = Status::where('name', 'Предупреждение')->first()->id;
        $info = Status::where('name', 'Информация')->first()->id;
        $channel = Service::where('name', 'Канал связи')->first()->id;
        $elektro = Service::where('name', 'Электропитание')->first()->id;
        // Не завершённые проблемы чрезвычайной, высокой и средней критичности
        $actualTroubles =  Trouble::whereNull('finished_at')->
            whereIn('id_status', [$superhigh, $high, $middle]);
        // Не завершённые проблемы с пометкой ПОЗВОНИТЬ
        $callNeed = Trouble::whereNull('finished_at')->
            where('action', 'like', '%позвонить%')->
            orWhere('description', 'like', '%позвонить%');
        // Не завершённые проблемы предупреждения и информации по каналам связи и электропитанию
        $troubles = Trouble::whereNull('finished_at')->
            whereIn('id_status', [$low, $info])->
            whereIn('id_service', [$channel, $elektro])->
            with('directorate', 'filial', 'city', 'office', 'source', 'service', 'status', 'user')->
            union($actualTroubles)->
            union($callNeed)->
            orderBy('id_status', 'asc')->
            orderBy('started_at', 'desc')->
            orderBy('id', 'desc')->
            get();
        $pattern_call = "/(позвонить)/iu";
        $replacement_call = '<span class="selectSearchPhrase_call">${1}</span>';
        $userCanCall = Auth::check();
        foreach ($troubles as $key=>$trouble) {
            // Экранирование тэгов в description и action
            $troubles[$key]->description = htmlspecialchars($trouble->description);
            $troubles[$key]->action = htmlspecialchars($trouble->action);
            if (null != $trouble->description) {
                // Выделение ПОЗВОНИТЬ для description, если проблема актуальна
                if ($userCanCall and null == $trouble->finished_at and preg_match($pattern_call, $troubles[$key]->description)) {
                    $troubles[$key]->description = preg_replace($pattern_call, $replacement_call, $troubles[$key]->description);
                } else {
                    // Укорачивание description
                    $troubles[$key]->description = self::shortMessage($trouble->description, $trouble->id, 'description');
                }
				// Выделение ссылок http:// и https:// в description, если не произошло укорачивание
				if(false == stripos($troubles[$key]->description, 'читать полностью')){
					$troubles[$key]->description = preg_replace("/(https??:\/\/\S+)/iu", "<a href=\"\\1\" target=\"_blank\">\\1</a>", $trouble->description);
				}
            }
            if (null != $trouble->action) {
                // Выделение ПОЗВОНИТЬ для action, если проблема актуальна
                if ($userCanCall and null == $trouble->finished_at and preg_match($pattern_call, $troubles[$key]->action)) {
                    $troubles[$key]->action = preg_replace($pattern_call, $replacement_call, $troubles[$key]->action);
                } else {
                    // Укорачивание action
                    $troubles[$key]->action = self::shortMessage($trouble->action, $trouble->id, 'action');
                }
				// Выделение ссылок http:// и https:// в action, если не произошло укорачивание
				if(false == stripos($troubles[$key]->action, 'читать полностью')){
					$troubles[$key]->action = preg_replace("/(https??:\/\/\S+)/iu", "<a href=\"\\1\" target=\"_blank\">\\1</a>", $trouble->action);
				}
            }
        }
        // Замена \r\n на <br /> в description и action
        foreach ($troubles as $key=>$trouble) {
            if (null != $trouble->description) {
                $troubles[$key]->description = str_replace("\r\n", "<br />", $trouble->description);
            }
            if (null != $trouble->action) {
                $troubles[$key]->action = str_replace("\r\n", "<br />", $trouble->action);
            }
        }
        return $troubles;
    }

    // Поиск проблем по заданным параметрам
    public static function findByParameters($parameters)
    {
        $searchPhrase = trim($parameters['searchPhrase']);
        $selectService = $parameters['selectService'];
        $date = $parameters['date'];
        $countTroublesInPage = $parameters['countTroublesInPage'];
        $currentPage = $parameters['currentPage'];
        $firstTrouble = ($currentPage - 1) * $countTroublesInPage;
        $query = null;
        if ('' != $selectService) {
            $query = Trouble::where('id_service', $selectService);
        }
        if ('' == $date) {
            session()->forget('errorDate');
        } else {
            $date = trim($date);
            if (preg_match("/^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})$/u", $date, $regs)) {
                $date = "$regs[1]-$regs[2]-$regs[3]";
                session()->forget('errorDate');
                if (null != $query) {
                    $query->whereBetween('started_at', [$date . ' 00:00:00', $date . ' 23:59:59']);
                } else {
                    $query = Trouble::whereBetween('started_at', [$date . ' 00:00:00', $date . ' 23:59:59']);
                }
            } elseif (preg_match("/^([0-9]{1,2})\.([0-9]{1,2})\.([0-9]{4})$/u", $date, $regs)) {
                $date = "$regs[3]-$regs[2]-$regs[1]";
                session()->forget('errorDate');
                if (null != $query) {
                    $query->whereBetween('started_at', [$date . ' 00:00:00', $date . ' 23:59:59']);
                } else {
                    $query = Trouble::whereBetween('started_at', [$date . ' 00:00:00', $date . ' 23:59:59']);
                }
            } else {
                session(['errorDate' => 'Неверно задана дата, ожидаемый формат 00.00.0000']);
            }
        }
        if ('' != $searchPhrase) {
            $searchPhrase = preg_replace('/([^ \(\)\[\]\.\/,=:№@a-zа-яё0-9-])/iu', '\\\${1}', $searchPhrase); // Экранируем спец. символы
            $id_offices = Office::select('id')->
                where('name', 'like', "%$searchPhrase%")->
                orWhere('address', 'like', "%$searchPhrase%")->
                get();
            $id_cities = City::select('id')->
                where('name', 'like', "%$searchPhrase%")->
                get();
            if (null != $query) {
                $query->where(function ($query) use ($searchPhrase, $id_cities, $id_offices) {
                    $query->where('description', 'like', "%$searchPhrase%")->
						orWhere('action', 'like', "%$searchPhrase%")->
						orWhere('incident', 'like', "%$searchPhrase%")->
                        orWhereIn('id_city', $id_cities)->
                        orWhereIn('id_office', $id_offices);
                });
            } else {
                $query = Trouble::where('description', 'like', "%$searchPhrase%")->
					orWhere('action', 'like', "%$searchPhrase%")->
					orWhere('incident', 'like', "%$searchPhrase%")->
                    orWhereIn('id_city', $id_cities)->
                    orWhereIn('id_office', $id_offices);
            }
        }
        // Если запрос не пустой
        if (null != $query) {
            $countTroubles = $query->count();
            $countPages = (int)($countTroubles / $countTroublesInPage);
            if ($countPages != $countTroubles / $countTroublesInPage) {
                $countPages++;
            }
            if ($currentPage > $countPages) {
                $currentPage = 1;
                $firstTrouble = ($currentPage - 1) * $countTroublesInPage;
            }
            $query->orderBy('started_at', 'desc')->
                orderBy('id', 'desc')->
                limit($countTroublesInPage)->
                offset($firstTrouble)->
                with('directorate', 'filial', 'city', 'office', 'source', 'service', 'status', 'user');
            $troubles = $query->get();
			// Экранирование тэгов в description и action
			foreach($troubles as $key => $value) {
				$troubles[$key]->description = htmlspecialchars($troubles[$key]->description);
				$troubles[$key]->action = htmlspecialchars($troubles[$key]->action);
			}
            if (0 < count($troubles)) {
                if ('' != $searchPhrase) {
                    $searchPhrase = preg_replace("/\(/u", "\(", $searchPhrase);
                    $searchPhrase = preg_replace("/\)/u", "\)", $searchPhrase);
                    $searchPhrase = preg_replace("/\[/u", "\[", $searchPhrase);
                    $searchPhrase = preg_replace("/\]/u", "\]", $searchPhrase);
                    $searchPhrase = preg_replace("/\./u", "\.", $searchPhrase);
                    $searchPhrase = preg_replace("/\//u", "\/", $searchPhrase);
                    $searchPhrase = preg_replace('/\\\"/u', '&quot;', $searchPhrase);
                    $searchPhrase = preg_replace('/\</u', '&lt;', $searchPhrase);
                    $searchPhrase = preg_replace('/\>/u', '&gt;', $searchPhrase);
                    $pattern = "/($searchPhrase)/iu";
                    $replacement = '<span class="selectSearchPhrase">${1}</span>';
                    $pattern_call = "/(позвонить)/iu";
                    $replacement_call = '<span class="selectSearchPhrase_call">${1}</span>';
                    $userCanCall = Auth::check();
                    foreach ($troubles as $key => $value) {
                        // Выделение ПОЗВОНИТЬ
                        if ($userCanCall and 'ПОЗВОНИТЬ' != mb_strtoupper($searchPhrase) and null == $troubles[$key]->finished_at) {
                            $troubles[$key]->description = preg_replace($pattern_call, $replacement_call, $troubles[$key]->description);
                            $troubles[$key]->action = preg_replace($pattern_call, $replacement_call, $troubles[$key]->action);
                        }
                        // Выделение фразы поиска
                        $troubles[$key]->description = preg_replace($pattern, $replacement, $troubles[$key]->description);
                        $troubles[$key]->action = preg_replace($pattern, $replacement, $troubles[$key]->action);
                        $troubles[$key]->incident = preg_replace($pattern, $replacement, $troubles[$key]->incident);
                        if (null != $troubles[$key]->office) {
                            if (false == strpos($troubles[$key]->office->name, 'selectSearchPhrase')) {
                                $troubles[$key]->office->name= preg_replace($pattern, $replacement, $troubles[$key]->office->name);
                            }
                            if (false == strpos($troubles[$key]->office->address, 'selectSearchPhrase')) {
                                $troubles[$key]->office->address = preg_replace($pattern, $replacement, $troubles[$key]->office->address);
                            }
                        }
                        if (null != $troubles[$key]->city) {
                            if (false == strpos($troubles[$key]->city->name, 'selectSearchPhrase')) {
                                $troubles[$key]->city->name = preg_replace($pattern, $replacement, $troubles[$key]->city->name);
                            }
                        }
                    }
                }
            }
            $result['troubles'] = $troubles;
            $result['countPages'] = $countPages;
            $result['limitPages'] = self::getLimitPages($currentPage, $countPages);
            // Выделение ссылок http:// и https:// в description и action
            // Замена \r\n на <br /> в description и action
            foreach ($troubles as $key=>$trouble) {
                if (null != $trouble->description) {
                    $link = [];
                    preg_match("/https??:\/\/\S+/iu", $trouble->description, $link);
                    if(!empty($link)){
                        // Ссылки, включающие фразу поиска, не делать ссылками
                        if(false == stripos($link[0], 'span')){
                            $troubles[$key]->description = preg_replace("/(https??:\/\/\S+)/iu", "<a href=\"\\1\" target=\"_blank\">\\1</a>", $trouble->description);
                        }
                    }
                    $troubles[$key]->description = str_replace("\r\n", "<br />", $trouble->description);
                }
                if (null != $trouble->action) {
                    $link = [];
                    preg_match("/https??:\/\/\S+/iu", $trouble->action, $link);
                    if(!empty($link)){
                        // Ссылки, включающие фразу поиска, не делать ссылками
                        if(false == stripos($link[0], 'span')){
                            $troubles[$key]->action = preg_replace("/(https??:\/\/\S+)/iu", "<a href=\"\\1\" target=\"_blank\">\\1</a>", $trouble->action);
                        }
                    }
                    $troubles[$key]->action = str_replace("\r\n", "<br />", $trouble->action);
                }
            }
            $result['currentPage'] = $currentPage;
            return $result;
        }
        // Если запрос пустой
        $countTroubles = Trouble::all()->count();
        $countPages = (int)($countTroubles / $countTroublesInPage);
        if ($countPages != $countTroubles / $countTroublesInPage) {
            $countPages++;
        }
        if ($currentPage > $countPages) {
            $currentPage = 1;
            $firstTrouble = ($currentPage - 1) * $countTroublesInPage;
        }
        $result['countPages'] = $countPages;
        $result['limitPages'] = self::getLimitPages($currentPage, $countPages);
        $troubles = Trouble::orderBy('started_at', 'desc')->
            orderBy('id', 'desc')->
            limit($countTroublesInPage)->
            offset($firstTrouble)->
            with('directorate', 'filial', 'city', 'office', 'source', 'service', 'status', 'user')->
            get();
        $pattern_call = "/(позвонить)/iu";
        $replacement_call = '<span class="selectSearchPhrase_call">${1}</span>';
        $userCanCall = Auth::check();
        foreach ($troubles as $key=>$trouble) {
            // Экранирование тэгов в description и action
            $troubles[$key]->description = htmlspecialchars($trouble->description);
            $troubles[$key]->action = htmlspecialchars($trouble->action);
            if (null != $trouble->description) {
				// Выделение ПОЗВОНИТЬ для description, если проблема актуальна
                if ($userCanCall and null == $trouble->finished_at and preg_match($pattern_call, $troubles[$key]->description)) {
                    $troubles[$key]->description = preg_replace($pattern_call, $replacement_call, $troubles[$key]->description);
                } else {
                    // Укорачивание description
                    $troubles[$key]->description = self::shortMessage($trouble->description, $trouble->id, 'description');
                }
				// Выделение ссылок http:// и https:// в description, если не произошло укорачивание
				if(false == stripos($troubles[$key]->description, 'читать полностью')){
					$troubles[$key]->description = preg_replace("/(https??:\/\/\S+)/iu", "<a href=\"\\1\" target=\"_blank\">\\1</a>", $trouble->description);
				}
            }
            if (null != $trouble->action) {
				// Выделение ПОЗВОНИТЬ для action, если проблема актуальна
                if ($userCanCall and null == $trouble->finished_at and preg_match($pattern_call, $troubles[$key]->action)) {
                    $troubles[$key]->action = preg_replace($pattern_call, $replacement_call, $troubles[$key]->action);
                } else {
                    // Укорачивание action
                    $troubles[$key]->action = self::shortMessage($trouble->action, $trouble->id, 'action');
                }
				// Выделение ссылок http:// и https:// в action, если не произошло укорачивание
				if(false == stripos($troubles[$key]->action, 'читать полностью')){
					$troubles[$key]->action = preg_replace("/(https??:\/\/\S+)/iu", "<a href=\"\\1\" target=\"_blank\">\\1</a>", $trouble->action);
				}
            }
        }
        // Замена \r\n на <br /> в description и action
        foreach ($troubles as $key=>$trouble) {
            if (null != $trouble->description) {
                $troubles[$key]->description = str_replace("\r\n", "<br />", $trouble->description);
            }
            if (null != $trouble->action) {
                $troubles[$key]->action = str_replace("\r\n", "<br />", $trouble->action);
            }
        }
        $result['currentPage'] = $currentPage;
        $result['troubles'] = $troubles;
        return $result;
    }

    // Ограничение списка ссылок страниц
    private static function getLimitPages($currentPage, $countPages)
    {
        // Ограничение количества ссылок c каждой стороны от текущей
        $limit = config('settings.navigation_limit_pages');
        // Вычисление первой и последней ссылок
        $firstPage = $currentPage - $limit;
        $lastPage = $currentPage + $limit;
        if ($firstPage < 1) {
            $firstPage = 1;
        }
        if ($lastPage > $countPages) {
            $lastPage = $countPages;
        }
        $countLinks = ($limit * 2) - ($lastPage - $firstPage);
        // Добавление ссылок с одной из сторон, если другая сторона достигла предела
        if (0 != $countLinks) {
            if (1 == $firstPage) {
                $lastPage += $countLinks;
                if ($lastPage > $countPages) {
                    $lastPage = $countPages;
                }
            } elseif ($lastPage == $countPages) {
                $firstPage -= $countLinks;
                if ($firstPage < 1) {
                    $firstPage = 1;
                }
            }
        }
        // Помещаем значения в массив
        $limitPages['firstPage'] = $firstPage;
        $limitPages['lastPage'] = $lastPage;
        // И возвращаем массив
        return $limitPages;
    }

    // Ограничение сообщения
    private static function shortMessage($message, $id, $field)
    {
        if (mb_strlen($message, "UTF-8") > config('settings.short_message')) {
            if (config('settings.over_short_message') <= (mb_strlen($message, "UTF-8") - config('settings.short_message'))) {
                $message = mb_substr($message, 0, config('settings.short_message'), "UTF-8");
                $message .= '<br />... <span class="shortMessageLink" onclick="search_ajax_full_text(this.parentNode,\'' . $id . '\',\'' . $field . '\')">читать полностью</span>';
            }
        }
        return $message;
    }
	
	// Проблемы по каналам связи более 12 часов (для эскалации)
	public static function getActualChannels()
	{
		$low = Status::where('name', 'Предупреждение')->first()->id;
		$channel = Service::where('name', 'Канал связи')->first()->id;
		return Trouble::whereNull('finished_at')->
			whereIn('id_status', [$low])->
			whereIn('id_service', [$channel])->
			with('directorate', 'filial', 'city', 'office', 'source', 'service', 'status', 'user')->
			get();
	}

	// Каналы связи без подразделения
    public static function getChannelsWithoutOffice()
    {
        $services = Service::select('id')->
			where('name', 'Канал связи')->
			orWhere('name', 'Электропитание')->
			get();
		$services_id = [];
		foreach($services as $service){
			$services_id[] = $service->id;
		}
        // $channels = Trouble::whereNull('id_office')->
            // where('description', 'LIKE', '%Недоступен канал%')->
			// whereIn('id_service', $services_id);
		$gorod = Trouble::whereNull('id_office')->
			whereNull('id_city')->
			where('description', 'LIKE', '% г.%');
		$ulitsa = Trouble::whereNull('id_office')->
			whereNull('id_city')->
			where('description', 'LIKE', '% ул.%');
		$ulitsa2 = Trouble::whereNull('id_office')->
			whereNull('id_city')->
			where('description', 'LIKE', '% ул %');
		$pomesh = Trouble::whereNull('id_office')->
			whereNull('id_city')->
			where('description', 'LIKE', '% пом.%');
		$stroen = Trouble::whereNull('id_office')->
			whereNull('id_city')->
			where('description', 'LIKE', '% стр.%');
		$prospekt = Trouble::whereNull('id_office')->
			whereNull('id_city')->
			where('description', 'LIKE', '%пр-кт%');
		$prospekt2 = Trouble::whereNull('id_office')->
			whereNull('id_city')->
			where('description', 'LIKE', '%пр-т.%');
		$prospekt3 = Trouble::whereNull('id_office')->
			whereNull('id_city')->
			where('description', 'LIKE', '%просп.%');
		$pereulok = Trouble::whereNull('id_office')->
			whereNull('id_city')->
			where('description', 'LIKE', '%пер.%');
		$microraion = Trouble::whereNull('id_office')->
			whereNull('id_city')->
			where('description', 'LIKE', '%мкр.%');
		$poselok = Trouble::whereNull('id_office')->
			whereNull('id_city')->
			where('description', 'LIKE', '%пос.%');
		$troubles = Trouble::whereNull('id_office')->
			whereNull('id_city')->
			// where('description', 'LIKE', '%Недоступен объект%')->
			where('description', 'LIKE', '%шоссе%')->
            whereIn('id_service', $services_id)->
			union($gorod)->
			union($ulitsa)->
			union($ulitsa2)->
			union($pomesh)->
			union($stroen)->
			union($prospekt)->
			union($prospekt2)->
			union($prospekt3)->
			union($pereulok)->
			union($microraion)->
			union($poselok)->
			with('directorate', 'filial', 'city', 'office', 'source', 'service', 'status')->
            orderBy('started_at', 'desc')->
            orderBy('id', 'desc')->
            get();
		$ignored = [
			'Архангельск, ул. Карла Либкнехта, д. 3',
			'Березово, ул. Семяшкина, д. 13',
			'Березово, ул. Корпоративная, д. 28',
			'Владивосток, ул. Алеутская, д. 45',
			'Екатеринбург, ул. Уральских рабочих, д. 35',
			'Игрим, ул. Корпоративная, д. 28',
			'Иркутск, ул. Свердлова, д. 36',
			'Комсомольск-на-Амуре, ул. Ленина, д. 22',
			'Красногорск, 25 км',
			'Краснокаменск, пр-кт Строителей, д. 11',
			'Красноярск, ул. Боргада, д. 15',
			'Кстово, ул. Чванова, д.3',
			'Курган, ул. Советская, 51/1',
			'Москва, проспект Мира, д. 115',
			'Москва, ул. Шарикоподшипниковская, д. 11, стр. 9',
			'Варшавское шоссе, д. 125, стр. 10',
			'Москва, ул Боровая, д. 7, ст. 10',
			'Москва, ул. Ломоносова, д. 25',
			'Москва, 1-й Неопалимовский пер., д. 10, стр. 13',
			'Москва, Звездный б-р, д. 23',
			'Муром, ул. Московская, д. 47',
			'Нефтеюганск, пос. Салым, ул. 45 лет Победы, д. 18',
			'Нефтеюганск, пос. Пойковский, мкр. 4, д. 9',
			'Нижневартовск, п. Зайцева Речка, ул. Гагарина, д. 2',
			'Нижний Новгород, ул. Родионова, д.23',
			'Новосибирск, Серебренниковская ул. 4/3',
			'Норильск, ул. Ленина, д. 19',
			'Ноябрьск, п. Уренгой, мкр. 2, д. 11а',
			'Ноябрьск, ул. Муравленко, д. 39',
			'Нягань, пос. Приобье, ул. Школьная, д. 2б',
			'Пермь, пгт. Полазна, ул. Парковая, 12',
			'Пыть-Ях, пос. Угут, Лесной, д. 3а',
			'Санкт-Петербург, Невский пр-т., д. 106',
			'Санкт-Петербург, ул. Набережная Обводного канала, д. 74',
			'Саратов, ул. Орджоникидзе, д. 11',
			'Саратов, ул. Шелковичная, д. 186',
			'Сургут, пос. Нижнесортымский, ул. Энтузиастов, д. 1',
			'Сургут, пос. Федоровский, пер. Парковый, д. 11',
			'Сургут, Нефтеюганское ш., д. 23',
			'Сургут, ул. Базовая, д. 6',
			'Тамбов, ул. М. Горького, д. 17',
			'Тюмень, ул. Ершова, д. 87/5',
			'Ханты-Мансийск, ул. Крупской, д. 25',
			'Ханты-Мансийск, ул. Комсомольская, д. 63',
			'Югорск, пгт. Междуреченский, ул. Титова, д. 15а',
			'Югорск, ул. Ленина, д. 93',
			'БЦ Лефорт',
		];
        $result = [];
		$flag = false;
        foreach($troubles as $trouble){
			$flag = false;
			foreach($ignored as $ignor){
				// if(0 != substr_count($trouble->description, 'Недоступен канал')){
					// $flag = true;
					// break;
				// }
				if(0 == substr_count($trouble->description, $ignor)){
					continue;
				} else {
					$flag = true;
					break;
				}
			}
			if(false == $flag){
				$result[] = $trouble;
			}
        }
        // Замена \r\n на <br /> в description и action
        foreach ($result as $key=>$trouble) {
            if (null != $trouble->description) {
                $result[$key]->description = str_replace("\r\n", "<br />", $trouble->description);
            }
            if (null != $trouble->action) {
                $result[$key]->action = str_replace("\r\n", "<br />", $trouble->action);
            }
        }
        return $result;
    }
	
	// Удаление старых записей навсегда
	public static function deleteOldTroubles()
	{
		Trouble::whereDate('started_at', '<=', strftime('%Y-%m-%d', time() - config('settings.periodStoreInformation') * 24 * 60 * 60))->withTrashed()->forceDelete();
	}

}