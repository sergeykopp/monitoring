<?php

namespace Kopp\Http\Controllers;

use Illuminate\Http\Request;
use Kopp\Models\Office;
use Kopp\Models\Trouble;
use Kopp\Models\Directorate;
use Kopp\Models\Filial;
use Kopp\Models\City;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class AjaxController extends Controller
{
	public function __construct()
    {
		parent::__construct();
    }
	
    // Получение всех филиалов по заданной дирекции
    public function getFilials(Request $request)
    {
        $parameters = $request->all();
        if (isset($parameters['id_directorate'])) {
            $filials = Directorate::find($parameters['id_directorate'])->filials;
            $response = '<option value=""></option>';
            foreach ($filials as $filial) {
                if (true == $filial->actual) {
                    $response .= '<option value="' . $filial->id . '">' . $filial->name . '</option>';
                }
            }
            echo $response;
        }
    }

    // Получение всех городов по заданному филиалу
    public function getCities(Request $request)
    {
        $parameters = $request->all();
        if (isset($parameters['id_filial'])) {
            $cities = Filial::find($parameters['id_filial'])->cities;
            $response = '<option value=""></option>';
            foreach ($cities as $city) {
                if (true == $city->actual) {
                    $response .= '<option value="' . $city->id . '">' . $city->name . '</option>';
                }
            }
            echo $response;
        }
    }

    // Получение всех подразделений по заданному городу
    public function getOffices(Request $request)
    {
        $parameters = $request->all();
        if (isset($parameters['id_city'])) {
            $offices = City::find($parameters['id_city'])->offices;
            $response = '<option value=""></option>';
            foreach ($offices as $office) {
                if (true == $office->actual) {
                    $response .= '<option value="' . $office->id . '">' . $office->name . '</option>';
                }
            }
            echo $response;
        }
    }

    // Получение информации о подразделении
    public function getInfoOffice(Request $request)
    {
        $response = '';
        $parameters = $request->all();
        if (isset($parameters['id_office'])) {
            $office = Office::find($parameters['id_office']);
            $office->notes = str_replace("\r\n", "<br />", $office->notes);
            $response = json_encode($office);
        }
        echo $response;
    }

    // Получение списка похожих фраз при поиске
    public function getPhrases(Request $request)
    {
		$response = '';
        if ($request->has('searchPhrase')) {
            $searchPhrase = trim($request->input('searchPhrase'));
            $searchPhrase = preg_replace("/plusplusplus/u", "+", $searchPhrase); // Восстановление знака + (так как он удаляется сервером)
            if ('' != $searchPhrase) {
                $searchPhrase = preg_replace('/([^ \(\)\[\]\.\/,=:№@a-zа-яё0-9-])/iu', '\\\${1}', $searchPhrase); // Экранируем спец. символы
                $id_offices = Office::select('id')->
                    where('name', 'like', "%$searchPhrase%")->
                    orWhere('address', 'like', "%$searchPhrase%")->
                    get();
                $id_cities = City::select('id')->
                    where('name', 'like', "%$searchPhrase%")->
                    get();
                $troubles = Trouble::where('description', 'like', "%$searchPhrase%")->
                    orWhere('action', 'like', "%$searchPhrase%")->
                    orWhere('incident', 'like', "%$searchPhrase%")->
                    orWhereIn('id_city', $id_cities)->
                    orWhereIn('id_office', $id_offices)->
                    with('office')->
                    get();
                // Ищем похожие варианты
                $arr = [];
                $searchPhrase = preg_replace("/\(/u", "\(", $searchPhrase);
                $searchPhrase = preg_replace("/\)/u", "\)", $searchPhrase);
                $searchPhrase = preg_replace("/\[/u", "\[", $searchPhrase);
                $searchPhrase = preg_replace("/\]/u", "\]", $searchPhrase);
                $searchPhrase = preg_replace("/\./u", "\.", $searchPhrase);
                $searchPhrase = preg_replace("/\//u", "\/", $searchPhrase);
                $pattern = '/[\w\d-]*' . $searchPhrase . '[\w\d-]*/iu';
                foreach ($troubles as $trouble) {
                    preg_match_all($pattern, $trouble->description, $words1, PREG_PATTERN_ORDER);
                    preg_match_all($pattern, $trouble->action, $words2, PREG_PATTERN_ORDER);
                    preg_match_all($pattern, $trouble->incident, $words3, PREG_PATTERN_ORDER);
                    if (null != $trouble->office) {
                        preg_match_all($pattern, $trouble->office->name, $words4, PREG_PATTERN_ORDER);
                        preg_match_all($pattern, $trouble->office->address, $words5, PREG_PATTERN_ORDER);
                    }
                    if (null != $trouble->city) {
                        preg_match_all($pattern, $trouble->city->name, $words6, PREG_PATTERN_ORDER);
                    }
                    // Добавляем только новые, которых ещё нет в массиве
                    foreach ($words1[0] as $word) {
                        if (1 == mb_strlen($word, "UTF-8")) {
                            continue;
                        }
                        $word = mb_strtoupper($word, 'utf-8');
                        if (!in_array($word, $arr)) {
                            $arr[] = $word;
                        }
                    }
                    foreach ($words2[0] as $word) {
                        if (1 == mb_strlen($word, "UTF-8")) {
                            continue;
                        }
                        $word = mb_strtoupper($word, 'utf-8');
                        if (!in_array($word, $arr)) {
                            $arr[] = $word;
                        }
                    }
                    foreach ($words3[0] as $word) {
                        if (1 == mb_strlen($word, "UTF-8")) {
                            continue;
                        }
                        $word = mb_strtoupper($word, 'utf-8');
                        if (!in_array($word, $arr)) {
                            $arr[] = $word;
                        }
                    }
                    if (null != $trouble->office){
                        foreach ($words4[0] as $word) {
                            if (1 == mb_strlen($word, "UTF-8")) {
                                continue;
                            }
                            $word = mb_strtoupper($word, 'utf-8');
                            if (!in_array($word, $arr)) {
                                $arr[] = $word;
                            }
                        }
                        foreach ($words5[0] as $word) {
                            if (1 == mb_strlen($word, "UTF-8")) {
                                continue;
                            }
                            $word = mb_strtoupper($word, 'utf-8');
                            if (!in_array($word, $arr)) {
                                $arr[] = $word;
                            }
                        }
                    }
                    if (null != $trouble->city){
                        foreach ($words6[0] as $word) {
                            if (1 == mb_strlen($word, "UTF-8")) {
                                continue;
                            }
                            $word = mb_strtoupper($word, 'utf-8');
                            if (!in_array($word, $arr)) {
                                $arr[] = $word;
                            }
                        }
                    }
                }

                // Если более 50 вариантов, то отбираем только те, которые начинаются с ключевой фразы
                if (50 < count($arr)) {
                    $searchPhrase = mb_strtoupper($searchPhrase, 'utf-8');
                    $newArr = [];
                    foreach ($arr as $value) {
                        if (0 === strpos($value, $searchPhrase)) {
                            $newArr[] = $value;
                        }
                    }
                    $arr = $newArr;
                }

                // Выводим не более 30 вариантов
                if (count($arr) <= 50 and count($arr) > 0) {
                    asort($arr);
                    foreach ($arr as $value) {
                        $response .= '<span onclick="document.navForm.searchPhrase.value = this.firstChild.nodeValue; document.navForm.submit();"
						onmouseover="clearStyleAjax(); this.style.backgroundColor = \'#6CAEDF\'; this.style.cursor = \'pointer\';">'
                            . htmlspecialchars($value) . '</span><br />';
                    }
                }
            }
        }
        echo $response;
    }

    // Получение полного текста вместо короткого сообщения
    public function getFullText(Request $request)
    {
        if ($request->has('id') and $request->has('field')){
            $res = DB::table('troubles')->select("{$request->input('field')}")->where('id', $request->input('id'))->first();
            if (null !== $res){
                if ('action' == $request->input('field')){
					$res->action = htmlspecialchars($res->action);
					$res->action = preg_replace("/(https??:\/\/\S+)/iu", "<a href=\"\\1\" target=\"_blank\">\\1</a>", $res->action);
                    echo str_replace("\r\n", "<br />", $res->action);
                } else{
					$res->description = htmlspecialchars($res->description);
					$res->description = preg_replace("/(https??:\/\/\S+)/iu", "<a href=\"\\1\" target=\"_blank\">\\1</a>", $res->description);
                    echo str_replace("\r\n", "<br />", $res->description);
                }
            }
        }
        echo '';
    }

    // Получение текущего времени
    public function getNow()
    {
        echo strftime("%d.%m.%Y %H:%M");
    }

    // Получение истории изменений проблемы
    public function getHistory(Request $request)
    {
        $result = '';
        if ($request->has('id')){
            $file = File::get(config('settings.monitoring_log'));
            preg_match_all('/\[.*(Создана новая проблема id\=' . $request->input('id') . ' |Проблема id\=' . $request->input('id') . ' после редактирования)(.*\r\n)*    Дежурный.*\r\n/imU', $file, $res, PREG_PATTERN_ORDER);
            foreach ($res[0] as $item){
                $result .= $item;
            }
            //$result = str_replace("\r\n", "<br />", $result);
        }
        echo $result;
    }

    // Получение списка похожих фраз при поиске для BFKO
    /**
     * @param Request $request
     */
    public function getPhrasesBFKO(Request $request)
    {
        $response = '';
        if ($request->has('searchPhrase')) {
            $searchPhrase = trim($request->input('searchPhrase'));
            $searchPhrase = preg_replace("/plusplusplus/u", "+", $searchPhrase); // Восстановление знака + (так как он удаляется сервером)
            if ('' != $searchPhrase) {
                $searchPhrase = preg_replace('/([^ \(\)\[\]\.\/,=:№@a-zа-яё0-9-])/iu', '\\\${1}', $searchPhrase); // Экранируем спец. символы
                $id_offices = Office::select('id')->
                    where('address', 'like', "%$searchPhrase%")->
                    get();
                $query = Trouble::whereIn('id_user', [21,22,23,24]);
                $query->where(function ($query) use ($searchPhrase, $id_offices) {
                    $query->where('description', 'like', "%$searchPhrase%")->
                    orWhere('action', 'like', "%$searchPhrase%")->
                    orWhere('incident', 'like', "%$searchPhrase%")->
                    orWhereIn('id_office', $id_offices);
                });
                $troubles = $query->with('office')->get();
                // Ищем похожие варианты
                $arr = [];
                $searchPhrase = preg_replace("/\(/u", "\(", $searchPhrase);
                $searchPhrase = preg_replace("/\)/u", "\)", $searchPhrase);
                $searchPhrase = preg_replace("/\[/u", "\[", $searchPhrase);
                $searchPhrase = preg_replace("/\]/u", "\]", $searchPhrase);
                $searchPhrase = preg_replace("/\./u", "\.", $searchPhrase);
                $searchPhrase = preg_replace("/\//u", "\/", $searchPhrase);
                $pattern = '/[\w\d-]*' . $searchPhrase . '[\w\d-]*/iu';
                foreach ($troubles as $trouble) {
                    preg_match_all($pattern, $trouble->description, $words1, PREG_PATTERN_ORDER);
                    preg_match_all($pattern, $trouble->action, $words2, PREG_PATTERN_ORDER);
                    preg_match_all($pattern, $trouble->incident, $words3, PREG_PATTERN_ORDER);
                    if (null != $trouble->office) {
                        preg_match_all($pattern, $trouble->office->address, $words4, PREG_PATTERN_ORDER);
                    }
                    // Добавляем только новые, которых ещё нет в массиве
                    foreach ($words1[0] as $word) {
                        if (1 == mb_strlen($word, "UTF-8")) {
                            continue;
                        }
                        $word = mb_strtoupper($word, 'utf-8');
                        if (!in_array($word, $arr)) {
                            $arr[] = $word;
                        }
                    }
                    foreach ($words2[0] as $word) {
                        if (1 == mb_strlen($word, "UTF-8")) {
                            continue;
                        }
                        $word = mb_strtoupper($word, 'utf-8');
                        if (!in_array($word, $arr)) {
                            $arr[] = $word;
                        }
                    }
                    foreach ($words3[0] as $word) {
                        if (1 == mb_strlen($word, "UTF-8")) {
                            continue;
                        }
                        $word = mb_strtoupper($word, 'utf-8');
                        if (!in_array($word, $arr)) {
                            $arr[] = $word;
                        }
                    }
                    if (null != $trouble->office){
                        foreach ($words4[0] as $word) {
                            if (1 == mb_strlen($word, "UTF-8")) {
                                continue;
                            }
                            $word = mb_strtoupper($word, 'utf-8');
                            if (!in_array($word, $arr)) {
                                $arr[] = $word;
                            }
                        }
                    }
                }

                // Если более 50 вариантов, то отбираем только те, которые начинаются с ключевой фразы
                if (50 < count($arr)) {
                    $searchPhrase = mb_strtoupper($searchPhrase, 'utf-8');
                    $newArr = [];
                    foreach ($arr as $value) {
                        if (0 === strpos($value, $searchPhrase)) {
                            $newArr[] = $value;
                        }
                    }
                    $arr = $newArr;
                }

                // Выводим не более 30 вариантов
                if (count($arr) <= 50 and count($arr) > 0) {
                    asort($arr);
                    foreach ($arr as $value) {
                        $response .= '<span onclick="document.navForm.searchPhrase.value = this.firstChild.nodeValue; document.navForm.submit();"
						onmouseover="clearStyleAjax(); this.style.backgroundColor = \'#6CAEDF\'; this.style.cursor = \'pointer\';">'
                            . htmlspecialchars($value) . '</span><br />';
                    }
                }
            }
        }
        echo $response;
    }
}
