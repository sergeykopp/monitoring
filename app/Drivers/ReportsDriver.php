<?php

namespace Kopp\Drivers;

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\DB;
use Kopp\Models\Event;
use Kopp\Models\Functions;
use Kopp\Models\Host;
use Kopp\Models\Service;
use Kopp\Models\Trigger;
use Kopp\Models\Trouble;

class ReportsDriver
{
    // Недоступность подразделений для операционных рисков
    public static function forOperRisk($firstDate, $lastDate, $firstDate2, $lastDate2, $fileName)
    {
        $troubles = Trouble::where('risk', true)
            ->whereNotNull('finished_at')
            ->whereBetween('started_at', [$firstDate2 . ' 00:00:00', $lastDate2 . ' 23:59:59'])
            ->with('directorate', 'filial', 'city', 'office', 'service', 'cause')
            ->orderBy('started_at', 'desc')
            ->get();
        // Подготовка результата
        foreach ($troubles as $key => $trouble) {
            $troubles[$key]->interval = self::intervalToStr(strtotime($trouble->finished_at . ':00') - strtotime($trouble->started_at . ':00'));
        }
        // Запись недоступных подразделений в файл Excel для операционных рисков
        self::saveOperRiskReportsToExcel($troubles, $firstDate, $lastDate, $fileName);
        // Замена \r\n на <br />
        foreach ($troubles as $key => $trouble) {
            if (null != $trouble->action) {
                $troubles[$key]->action = str_replace("\r\n", "<br />", $trouble->action);
            }
            if (null != $trouble->description) {
                $troubles[$key]->description = str_replace("\r\n", "<br />", $trouble->description);
            }
        }
        return $troubles;
    }

    // Перевод количества секунд в строку (дни, часы, минуты и секунды)
    private static function intervalToStr($seconds)
    {
        $str = '';
        $days = floor($seconds / 86400);
        $seconds -= $days * 86400;
        if (0 != $days) {
            $str .= $days . ' ' . Lang::choice('reports.days', $days);
        }
        $hours = floor($seconds / 3600);
        $seconds -= $hours * 3600;
        if (0 != $hours) {
            if ('' != $str) {
                $str .= ' ';
            }
            $str .= $hours . ' ' . Lang::choice('reports.hours', $hours);
        }
        $minutes = floor($seconds / 60);
        $seconds -= $minutes * 60;
        if (0 != $minutes) {
            if ('' != $str) {
                $str .= ' ';
            }
            $str .= $minutes . ' ' . Lang::choice('reports.minutes', $minutes);
        }
        if (0 != $seconds) {
            if ('' != $str) {
                $str .= ' ';
            }
            $str .= $seconds . ' ' . Lang::choice('reports.seconds', $seconds);
        }
        return $str;
    }

    // Запись недоступных подразделений в файл Excel для операционных рисков
    private static function saveOperRiskReportsToExcel($troubles, $firstDate, $lastDate, $fileName)
    {
        $objPHPExcel = new \PHPExcel();

        // Свойства документа
        $objPHPExcel->getProperties()->setCreator("Копп Сергей Владимирович")
            ->setLastModifiedBy("Копп Сергей Владимирович")
            ->setTitle("Критичные события")
            ->setSubject("Отчёт для операционных рисков")
            ->setDescription("По всем вопросам обращаться в отдел мониторинга т.50248")
            ->setKeywords("Отчёт отдела мониторинга")
            ->setCategory("Отчётность");

        // Стили
        $styleHeader = [
            'font' => [
                'name' => 'Verdana',
                'color' => ['rgb' => '183483'],
                'bold' => true,
                'size' => 10,
            ],
            'fill' => [
                'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                'color' => ['rgb' => '6CAEDF'],
            ],
            'alignment' => [
                'wrap' => true,
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ],
        ];

        $styleCell = [
            'font' => [
                'name' => 'Verdana',
                'color' => ['rgb' => '000000'],
                'bold' => false,
                'size' => 10,
            ],
            'borders' => [
                'top' => [
                    'color' => ['rgb' => '000000'],
                    'style' => \PHPExcel_Style_Border::BORDER_DOTTED,
                ],
                'bottom' => [
                    'color' => ['rgb' => '000000'],
                    'style' => \PHPExcel_Style_Border::BORDER_DOTTED,
                ],
                'left' => [
                    'color' => ['rgb' => '000000'],
                    'style' => \PHPExcel_Style_Border::BORDER_DOTTED,
                ],
                'right' => [
                    'color' => ['rgb' => '000000'],
                    'style' => \PHPExcel_Style_Border::BORDER_DOTTED,
                ],
            ],
            'alignment' => [
                'wrap' => true,
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ],
        ];

        $styleAlignLeft = $styleCell;
        $styleAlignLeft['alignment']['horizontal'] = null;

        $styleFillCell = $styleCell;
        $styleFillCell['fill'] = [
            'type' => \PHPExcel_Style_Fill::FILL_SOLID,
            'color' => ['rgb' => 'EEEEEE'],
        ];

        $styleFillAlignLeft = $styleFillCell;
        $styleFillAlignLeft['alignment']['horizontal'] = null;

        // Указание периода
        $objPHPExcel->getActiveSheet()->mergeCells('A1:I1');
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', "Отчёт о критичных событиях за период с {$firstDate} по {$lastDate}");

        // Добавление шапки таблицы
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A3', "Дирекция")
            ->setCellValue('B3', "Филиал")
            ->setCellValue('C3', "Город")
            ->setCellValue('D3', "Подразделение")
            ->setCellValue('E3', "Время\nсобытия\n(МСК)")
            ->setCellValue('F3', "Описание")
            ->setCellValue('G3', "Решение")
            ->setCellValue('H3', "Причина")
            ->setCellValue('I3', "Интервал")
            ->setCellValue('J3', "Время\nзавершения\n(МСК)")
            ->setCellValue('K3', "Сервис");

        $objPHPExcel->getActiveSheet()->getStyle('A3')->applyFromArray($styleHeader);
        $objPHPExcel->getActiveSheet()->getStyle('B3')->applyFromArray($styleHeader);
        $objPHPExcel->getActiveSheet()->getStyle('C3')->applyFromArray($styleHeader);
        $objPHPExcel->getActiveSheet()->getStyle('D3')->applyFromArray($styleHeader);
        $objPHPExcel->getActiveSheet()->getStyle('E3')->applyFromArray($styleHeader);
        $objPHPExcel->getActiveSheet()->getStyle('F3')->applyFromArray($styleHeader);
        $objPHPExcel->getActiveSheet()->getStyle('G3')->applyFromArray($styleHeader);
        $objPHPExcel->getActiveSheet()->getStyle('H3')->applyFromArray($styleHeader);
        $objPHPExcel->getActiveSheet()->getStyle('I3')->applyFromArray($styleHeader);
        $objPHPExcel->getActiveSheet()->getStyle('J3')->applyFromArray($styleHeader);
        $objPHPExcel->getActiveSheet()->getStyle('K3')->applyFromArray($styleHeader);

        // Добавление данных
        $cell = 4;
        foreach ($troubles as  $key => $trouble) {
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $cell, ($trouble->directorate) ?  $trouble->directorate->name : 'Все дирекции')
                ->setCellValue('B' . $cell, ($trouble->filial) ? $trouble->filial->name : '')
                ->setCellValue('C' . $cell, ($trouble->city) ?  $trouble->city->name : '')
                ->setCellValue('D' . $cell, ($trouble->office) ? $trouble->office->name . "\n" . $trouble->office->address : '')
                ->setCellValue('E' . $cell, $trouble->started_at)
                ->setCellValue('F' . $cell, $trouble->description)
                ->setCellValue('G' . $cell, $trouble->action)
                ->setCellValue('H' . $cell, ($trouble->cause) ? $trouble->cause->name . (('' != $trouble->detail) ? "\n" . $trouble->detail : '') : '')
                ->setCellValue('I' . $cell, $trouble->interval)
                ->setCellValue('J' . $cell, $trouble->finished_at)
                ->setCellValue('K' . $cell, ($trouble->service) ? $trouble->service->name : '');
            $objPHPExcel->getActiveSheet()->getStyle('A' . $cell)->applyFromArray(($key % 2 == 0) ? ($styleCell) : ($styleFillCell));
            $objPHPExcel->getActiveSheet()->getStyle('B' . $cell)->applyFromArray(($key % 2 == 0) ? ($styleCell) : ($styleFillCell));
            $objPHPExcel->getActiveSheet()->getStyle('C' . $cell)->applyFromArray(($key % 2 == 0) ? ($styleCell) : ($styleFillCell));
            $objPHPExcel->getActiveSheet()->getStyle('D' . $cell)->applyFromArray(($key % 2 == 0) ? ($styleCell) : ($styleFillCell));
            $objPHPExcel->getActiveSheet()->getStyle('E' . $cell)->applyFromArray(($key % 2 == 0) ? ($styleCell) : ($styleFillCell));
            $objPHPExcel->getActiveSheet()->getStyle('F' . $cell)->applyFromArray(($key % 2 == 0) ? ($styleAlignLeft) : ($styleFillAlignLeft));
            $objPHPExcel->getActiveSheet()->getStyle('G' . $cell)->applyFromArray(($key % 2 == 0) ? ($styleAlignLeft) : ($styleFillAlignLeft));
            $objPHPExcel->getActiveSheet()->getStyle('H' . $cell)->applyFromArray(($key % 2 == 0) ? ($styleCell) : ($styleFillCell));
            $objPHPExcel->getActiveSheet()->getStyle('I' . $cell)->applyFromArray(($key % 2 == 0) ? ($styleCell) : ($styleFillCell));
            $objPHPExcel->getActiveSheet()->getStyle('J' . $cell)->applyFromArray(($key % 2 == 0) ? ($styleCell) : ($styleFillCell));
            $objPHPExcel->getActiveSheet()->getStyle('K' . $cell)->applyFromArray(($key % 2 == 0) ? ($styleCell) : ($styleFillCell));
            $cell++;
        }

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);

        // Наименование листа
        $objPHPExcel->getActiveSheet()->setTitle('Критичные события');

        // Назначение активного листа
        $objPHPExcel->setActiveSheetIndex(0);

        // Запись в файл Excel2007
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save(public_path() . '/' . $fileName);
    }

    // Недоступность подразделений
    public static function availability($firstDate, $lastDate, $firstDate2, $lastDate2, $sortBy, $fileName)
    {
        $channel = Service::where('name', 'Канал связи')->first()->id;
        $elektro = Service::where('name', 'Электропитание')->first()->id;
        $prefix = config('database.connections.' . config('database.default') . '.prefix');
        $query = "SELECT id_office, id_service, dir.name AS directorate, fil.name AS filial, cit.name AS city, off.name AS office, off.address AS address, TIMESTAMPDIFF(SECOND, started_at, finished_at) AS 'interval'
                    FROM " . $prefix . "troubles AS tr
                    JOIN " . $prefix . "directorates AS dir ON tr.id_directorate=dir.id
                    JOIN " . $prefix . "filials AS fil ON tr.id_filial=fil.id
                    JOIN " . $prefix . "cities AS cit ON tr.id_city=cit.id
                    JOIN " . $prefix . "offices AS off ON tr.id_office=off.id
                    WHERE id_service IN (?,?)
                    AND id_office IS NOT NULL 
                    AND finished_at IS NOT NULL
                    AND (started_at BETWEEN STR_TO_DATE('" . $firstDate2 . " 00:00:00', '%Y-%m-%d %H:%i:%s') AND STR_TO_DATE('" . $lastDate2 . " 23:59:59', '%Y-%m-%d %H:%i:%s'))";
        $rows = DB::select($query, [$channel, $elektro]);
        // Группировка по подразделениям и подсчёт интервалов
        $arr1 = [];
        foreach ($rows as $row) {
            if (array_key_exists($row->id_office, $arr1)) {
                if ($row->id_service == $channel) {
                    $arr1[$row->id_office]['channel'] += $row->interval;
                    $arr1[$row->id_office]['all'] += $row->interval;
                } else {
                    $arr1[$row->id_office]['elektro'] += $row->interval;
                    $arr1[$row->id_office]['all'] += $row->interval;
                }
            } else {
                $arr1[$row->id_office]['id_office'] = $row->id_office;
                $arr1[$row->id_office]['all'] = $row->interval;
                $arr1[$row->id_office]['directorate'] = $row->directorate;
                $arr1[$row->id_office]['filial'] = $row->filial;
                $arr1[$row->id_office]['city'] = $row->city;
                $arr1[$row->id_office]['office'] = $row->office;
                $arr1[$row->id_office]['address'] = $row->address;
                if ($row->id_service == $channel) {
                    $arr1[$row->id_office]['channel'] = $row->interval;
                    $arr1[$row->id_office]['elektro'] = 0;
                } else {
                    $arr1[$row->id_office]['channel'] = 0;
                    $arr1[$row->id_office]['elektro'] = $row->interval;
                }
            }
        }
        // Сортировка по параметру sortBy
        // arr1 - ключи id_office
        // arr2 - ключи 0 1 2 3 ...
        $arr2 = [];
        foreach ($arr1 as $item) {
            $arr2[] = $item;
        }
        $n = count($arr2);
        for ($i = 0; $i < $n - 1; $i++) {
            for ($k = $i + 1; $k < $n; $k++) {
                if ($arr2[$i][$sortBy] < $arr2[$k][$sortBy]) {
                    $tempArr = $arr2[$i];
                    $arr2[$i] = $arr2[$k];
                    $arr2[$k] = $tempArr;
                }
            }
        }
        // Подготовка результата
        foreach ($arr2 as $key => $item) {
            $arr2[$key]['channel'] = self::intervalToStr($item['channel']);
            $arr2[$key]['elektro'] = self::intervalToStr($item['elektro']);
            $arr2[$key]['all'] = self::intervalToStr($item['all']);
        }
        // Запись недоступных подразделений в файл Excel
        self::saveAvailableReportsToExcel($arr2, $firstDate, $lastDate, $fileName);
        return $arr2;
    }

    // Запись недоступных подразделений в файл Excel
    private static function saveAvailableReportsToExcel($rows, $firstDate, $lastDate, $fileName)
    {
        $objPHPExcel = new \PHPExcel();

        // Свойства документа
        $objPHPExcel->getProperties()->setCreator("Копп Сергей Владимирович")
            ->setLastModifiedBy("Копп Сергей Владимирович")
            ->setTitle("Недоступность позразделений")
            ->setSubject("Отчёт по недоступности позразделений в заданный период")
            ->setDescription("По всем вопросам обращаться в отдел мониторинга т.50248")
            ->setKeywords("Отчёт отдела мониторинга")
            ->setCategory("Отчётность");

        // Стили
        $styleHeader = [
            'font' => [
                'name' => 'Verdana',
                'color' => ['rgb' => '183483'],
                'bold' => true,
                'size' => 10,
            ],
            'fill' => [
                'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                'color' => ['rgb' => '6CAEDF'],
            ],
            'alignment' => [
                'wrap' => true,
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ],
        ];

        $styleCell = [
            'font' => [
                'name' => 'Verdana',
                'color' => ['rgb' => '000000'],
                'bold' => false,
                'size' => 10,
            ],
            'borders' => [
                'top' => [
                    'color' => ['rgb' => '000000'],
                    'style' => \PHPExcel_Style_Border::BORDER_DOTTED,
                ],
                'bottom' => [
                    'color' => ['rgb' => '000000'],
                    'style' => \PHPExcel_Style_Border::BORDER_DOTTED,
                ],
                'left' => [
                    'color' => ['rgb' => '000000'],
                    'style' => \PHPExcel_Style_Border::BORDER_DOTTED,
                ],
                'right' => [
                    'color' => ['rgb' => '000000'],
                    'style' => \PHPExcel_Style_Border::BORDER_DOTTED,
                ],
            ],
            'alignment' => [
                'wrap' => true,
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ],
        ];

        // Указание периода
        $objPHPExcel->getActiveSheet()->mergeCells('A1:D1');
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', "Отчёт о недоступности подразделений за период с {$firstDate} по {$lastDate}");

        // Добавление шапки таблицы
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A3', "Дирекция\nФилиал\nГород\nПодразделение")
            ->setCellValue('B3', "Недоступность по\nКаналам связи")
            ->setCellValue('C3', "Недоступность по\nЭлектропитанию")
            ->setCellValue('D3', "Совокупная\nнедоступность");

        $objPHPExcel->getActiveSheet()->getStyle('A3')->applyFromArray($styleHeader);
        $objPHPExcel->getActiveSheet()->getStyle('B3')->applyFromArray($styleHeader);
        $objPHPExcel->getActiveSheet()->getStyle('C3')->applyFromArray($styleHeader);
        $objPHPExcel->getActiveSheet()->getStyle('D3')->applyFromArray($styleHeader);

        // Добавление данных
        $cell = 4;
        foreach ($rows as $row) {
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $cell,
                    $row['directorate'] . " дирекция, " .
                    $row['filial'] . " филиал\nг. " .
                    $row['city'] . ", " .
                    $row['address'] . "\n" .
                    $row['office'])
                ->setCellValue('B' . $cell, $row['channel'])
                ->setCellValue('C' . $cell, $row['elektro'])
                ->setCellValue('D' . $cell, $row['all']);
            $objPHPExcel->getActiveSheet()->getStyle('A' . $cell)->applyFromArray($styleCell);
            $objPHPExcel->getActiveSheet()->getStyle('B' . $cell)->applyFromArray($styleCell);
            $objPHPExcel->getActiveSheet()->getStyle('C' . $cell)->applyFromArray($styleCell);
            $objPHPExcel->getActiveSheet()->getStyle('D' . $cell)->applyFromArray($styleCell);
            $cell++;
        }

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);

        // Наименование листа
        $objPHPExcel->getActiveSheet()->setTitle('Недоступность позразделений');

        // Назначение активного листа
        $objPHPExcel->setActiveSheetIndex(0);

        // Запись в файл Excel2007
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save(public_path() . '/' . $fileName);
    }

    // Формирование массива разных дат
    public static function timeRanges()
    {
        $format = 'd.m.Y';
        $now = new \DateTime(null, new \DateTimeZone(config('app.timezone')));
        $oneWeek = new \DateInterval('P1W');
        $twoWeek = new \DateInterval('P2W');
        $oneMonth = new \DateInterval('P1M');
        $threeMonth = new \DateInterval('P3M');
        $sixMonth = new \DateInterval('P6M');
        $oneYear = new \DateInterval('P1Y');
        $timeRanges['today'] = $now->format($format); // Текущая дата
        $now->sub($oneWeek);
        $timeRanges['aWeekAgo'] = $now->format($format); // Минус одна неделя
        $now->add($oneWeek)->sub($twoWeek);
        $timeRanges['aTwoWeekAgo'] = $now->format($format); // Минус две недели
        $now->add($twoWeek)->sub($oneMonth);
        $timeRanges['aMonthAgo'] = $now->format($format); // Минус один месяц
        $now->add($oneMonth)->sub($threeMonth);
        $timeRanges['aThreeMonthAgo'] = $now->format($format); // Минус три месяца
        $now->add($threeMonth)->sub($sixMonth);
        $timeRanges['aSixMonthAgo'] = $now->format($format); // Минус шесть месяцев
        $now->add($sixMonth)->sub($oneYear);
        $timeRanges['aYearAgo'] = $now->format($format); // Минус один год
        return $timeRanges;
    }

    // Дата последнего зарегистрированного технического риска
    public static function getLastRiskData()
    {
        $trouble = Trouble::select('started_at')
			->where('risk', true)
            ->whereNotNull('finished_at')
            ->orderBy('started_at', 'desc')
            ->first();
        return $trouble->started_at;
    }

    // Ежедневный сводный отчёт
    public static function consolidatedReport($fileName)
    {
        $now = time();
        $dayAgo = $now - 60 * 60 * 24;
		try{
			$triggers = Trigger::select('triggerid', 'description', 'lastchange', 'priority')
            ->where('lastchange', '<', $dayAgo)
            ->where('value', 1)
            ->where('status', 0)
            ->where('priority', '>=', 2)
            ->orderBy('lastchange', 'desc')
            ->get();
		} catch (\Exception $e) {
            $message = '    Ошибка при попытке обращения к Zabbix' . "\r\n";
            $message .= '    $e->getMessage() : ' . $e->getMessage() . "\r\n";
            LogDriver::error($message);
			return false;
        }
        $triggerid_arr = [];
        foreach($triggers as $trigger) {
            $triggerid_arr[] = $trigger->triggerid;
        }
        $functions = Functions::select('itemid', 'triggerid')
            ->whereIn('triggerid', $triggerid_arr)
            ->with('item')
            ->get();
        $hostid_arr = [];
        foreach($functions as $function) {
            $hostid_arr[] = $function->item->hostid;
        }
        $hosts = Host::select('hostid', 'name')
            ->whereIn('hostid', $hostid_arr)
            ->where('status', 0)
            ->get();
        $activeTriggers = [];
        foreach($triggers as $trigger){
            foreach($functions as $function){
                if ($function->triggerid == $trigger->triggerid){
                    $item = $function->item;
                    if (0 == $item->status){
                        foreach($hosts as $host){
                            if ($host->hostid == $item->hostid){
                                $trigger->hostName = $host->name;
                                $trigger->description = preg_replace("/\{HOST\.NAME\}/iu", $host->name, $trigger->description);
                                if(preg_match("/\{ITEM\.VALUE\}/iu", $trigger->description)){
                                    switch($item->units){
                                        case "B": $measure = " Gb"; $multiplier = 1/1024/1024/1024; break;
                                        case "b": $measure = " Gb"; $multiplier = 1/1024/1024/1024; break;
                                        case "G": $measure = " Gb"; $multiplier = 1/1024/1024/1024; break;
                                        case "Gb": $measure = " Gb"; $multiplier = 1/1024/1024/1024; break;
                                        case "M": $measure = " Mb"; $multiplier = 1/1024/1024; break;
                                        case "Mb": $measure = " Mb"; $multiplier = 1/1024/1024; break;
                                        case "KB": $measure = " Kb"; $multiplier = 1/1024; break;
                                        case "kB": $measure = " Kb"; $multiplier = 1/1024; break;
                                        case "kb": $measure = " Kb"; $multiplier = 1/1024; break;
                                        case "%": $measure = " %"; $multiplier = 1; break;
                                        default: $measure = " " . $item->units; $multiplier = 1; break;
                                    }
                                    switch ($item->value_type) {
                                        case 0: $trigger->description = preg_replace("/\{ITEM\.VALUE\}/iu", round($item->lastHistory->value * $multiplier, 2, PHP_ROUND_HALF_UP) . $measure, $trigger->description);
                                            break;
                                        case 3:
                                            $trigger->description = preg_replace("/\{ITEM\.VALUE\}/iu", round($item->lastHistoryUint->value * $multiplier, 2, PHP_ROUND_HALF_UP) . $measure, $trigger->description);
                                            break;
                                        default: break;
                                    }
                                }
//                                $trigger->description = preg_replace("/\{ITEM\.VALUE\}/iu", $function->item->value, $trigger->description);
//                                $trigger->description = preg_replace("/\{ITEM\.LASTVALUE\}/iu", $function->item->lastvalue, $trigger->description);
                                $activeTriggers[] = $trigger;
                                break;
                            }
                        }
                    }
                    break;
                }
            }
        }
        $priority = ['Не классифицировано', 'Информация' , 'Предупреждение' , 'Средняя', 'Высокая', 'Чрезвычайная'];
        foreach($activeTriggers as $key=>$trigger){
            $activeTriggers[$key]->age = self::intervalToStr($now - $trigger->lastchange);
            $activeTriggers[$key]->priority = $priority[$activeTriggers[$key]->priority];
        }
        $topTriggers = self::topTriggers();
        // Запись триггеров в файл Excel
        self::saveTriggersReportsToExcel($activeTriggers, $topTriggers, $fileName, strftime('%d.%m.%Y %H:%M', $dayAgo));
		return true;
    }

    // 100 наиболее активных триггеров
    private static function topTriggers()
    {
        // Вытаскиваем все события за данный период
        $now = time();
        strftime('%d.%m.%Y', $now);
        $firstDate = strtotime(strftime('%d.%m.%Y', $now) . ' 00:00');
        $lastDate = strtotime(strftime('%d.%m.%Y', $now + 24 * 60 * 60) . ' 00:00');
        $events = Event::select('objectid')
            ->where('object', 0)
            ->whereBetween('clock', [$firstDate , $lastDate])
            ->get();
        // Создаём массивы уникальных id триггеров и их количество срабатываний
        $arrTriggerIds = [];
        $arrQuantityEvents = [];
        foreach($events as $event) {
            $key = array_search($event->objectid, $arrTriggerIds);
            if(false == $key) {
                $arrTriggerIds[] = $event->objectid;
                $arrQuantityEvents[] = 1;
            } else {
                $arrQuantityEvents[$key]++;
            }
        }
        // Вытаскиваем сотню самых больших срабатываний
        // сортировка от максимального к минимальному
        $resTriggerIds = [];
        $resQuantityEvents = [];
        for($i=0; $i<100; $i++) {
            if(0 == count($arrQuantityEvents)){
                break;
            }
            $max_value = 0;
            $max_key = 0;
            foreach($arrQuantityEvents as $key=>$quantity){
                if($quantity > $max_value){
                    $max_value = $quantity;
                    $max_key = $key;
                }
            }
            $resTriggerIds[] = $arrTriggerIds[$max_key];
            $resQuantityEvents[] = $arrQuantityEvents[$max_key];
            unset($arrTriggerIds[$max_key]);
            unset($arrQuantityEvents[$max_key]);
        }
        // Вытаскиваем триггеры с id из массива
        $triggers = Trigger::select('triggerid', 'description', 'priority')
            ->whereIn('triggerid', $resTriggerIds)
            ->get();
        // Вытаскиваем функции триггеров с id из массива
        $functions = Functions::select('itemid', 'triggerid')
            ->whereIn('triggerid', $resTriggerIds)
            ->with('item')
            ->get();
        // Создаём массив узлов
        $hostid_arr = [];
        foreach($functions as $function) {
            $hostid_arr[] = $function->item->hostid;
        }
        $hosts = Host::select('hostid', 'name')
            ->whereIn('hostid', $hostid_arr)
            ->get();
        // Вытаскиваем соответствия триггеров узлам
        $result = [];
        $priority = ['Не классифицировано', 'Информация' , 'Предупреждение' , 'Средняя', 'Высокая', 'Чрезвычайная'];
        foreach($resTriggerIds as $key=>$id) {
            $flag = false;
            foreach($triggers as $keyTr=>$trigger){
                if($id == $trigger->triggerid) {
                    foreach($functions as $function){
                        if ($function->triggerid == $trigger->triggerid){
                            foreach($hosts as $host){
                                $item = $function->item;
                                if ($host->hostid == $item->hostid){
                                    $trigger->description = preg_replace("/\{HOST\.NAME\}/iu", $host->name, $trigger->description);
                                    if(preg_match("/\{ITEM\.VALUE\}/iu", $trigger->description)){
                                        switch($item->units){
                                            case "B": $measure = " Gb"; $multiplier = 1/1024/1024/1024; break;
                                            case "b": $measure = " Gb"; $multiplier = 1/1024/1024/1024; break;
                                            case "G": $measure = " Gb"; $multiplier = 1/1024/1024/1024; break;
                                            case "Gb": $measure = " Gb"; $multiplier = 1/1024/1024/1024; break;
                                            case "M": $measure = " Mb"; $multiplier = 1/1024/1024; break;
                                            case "Mb": $measure = " Mb"; $multiplier = 1/1024/1024; break;
                                            case "KB": $measure = " Kb"; $multiplier = 1/1024; break;
                                            case "kB": $measure = " Kb"; $multiplier = 1/1024; break;
                                            case "kb": $measure = " Kb"; $multiplier = 1/1024; break;
                                            case "%": $measure = " %"; $multiplier = 1; break;
                                            default: $measure = " " . $item->units; $multiplier = 1; break;
                                        }
                                        switch ($item->value_type) {
                                            case 0: $trigger->description = preg_replace("/\{ITEM\.VALUE\}/iu", round($item->lastHistory->value * $multiplier, 2, PHP_ROUND_HALF_UP) . $measure, $trigger->description);
                                                break;
                                            case 3:
                                                $trigger->description = preg_replace("/\{ITEM\.VALUE\}/iu", round($item->lastHistoryUint->value * $multiplier, 2, PHP_ROUND_HALF_UP) . $measure, $trigger->description);
                                                break;
                                            default: break;
                                        }
                                    }
//                                    $trigger->description = preg_replace("/\{ITEM\.VALUE\}/iu", $function->item->value, $trigger->description);
//                                    $trigger->description = preg_replace("/\{ITEM\.LASTVALUE\}/iu", $function->item->lastvalue, $trigger->description);
                                    $result[] = [
                                        'description'=>$trigger->description,
                                        'quantityEvents'=>$resQuantityEvents[$key],
                                        'priority'=>$priority[$trigger->priority],
                                        'hostName'=>$host->name
                                    ];
                                    $flag = true;
                                    break;
                                }
                                if(true == $flag) break;
                            }
                        }
                        if(true == $flag) break;
                    }
                }
            }
        }
        return $result;
    }

    // Запись триггеров в файл Excel
    private static function saveTriggersReportsToExcel($activeTriggers, $topTriggers, $fileName, $dayAgo)
    {
        $objPHPExcel = new \PHPExcel();

        // Свойства документа
        $objPHPExcel->getProperties()->setCreator("Копп Сергей Владимирович")
            ->setLastModifiedBy("Копп Сергей Владимирович")
            ->setTitle("Сводный отчёт мониторинга")
            ->setSubject("Отчёт об активных триггерах более суток")
            ->setDescription("По всем вопросам обращаться в отдел мониторинга т.50248")
            ->setKeywords("Отчёт отдела мониторинга")
            ->setCategory("Отчётность");

        // Стили
        $styleHeader = [
            'font' => [
                'name' => 'Verdana',
                'color' => ['rgb' => '183483'],
                'bold' => true,
                'size' => 10,
            ],
            'fill' => [
                'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                'color' => ['rgb' => '6CAEDF'],
            ],
            'alignment' => [
                'wrap' => true,
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ],
        ];

        $styleCell = [
            'font' => [
                'name' => 'Verdana',
                'color' => ['rgb' => '000000'],
                'bold' => false,
                'size' => 10,
            ],
            'borders' => [
                'top' => [
                    'color' => ['rgb' => '000000'],
                    'style' => \PHPExcel_Style_Border::BORDER_DOTTED,
                ],
                'bottom' => [
                    'color' => ['rgb' => '000000'],
                    'style' => \PHPExcel_Style_Border::BORDER_DOTTED,
                ],
                'left' => [
                    'color' => ['rgb' => '000000'],
                    'style' => \PHPExcel_Style_Border::BORDER_DOTTED,
                ],
                'right' => [
                    'color' => ['rgb' => '000000'],
                    'style' => \PHPExcel_Style_Border::BORDER_DOTTED,
                ],
            ],
            'alignment' => [
                'wrap' => true,
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ],
        ];

        $styleFillCell = $styleCell;
        $styleFillCell['fill'] = [
            'type' => \PHPExcel_Style_Fill::FILL_SOLID,
            'color' => ['rgb' => 'EEEEEE'],
        ];

        // Наименование листа
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setTitle('Триггеры более суток');

        // Указание периода
//        $objPHPExcel->getActiveSheet()->mergeCells('A1:D1');
//        $objPHPExcel->setActiveSheetIndex(0)
//            ->setCellValue('A1', "Отчёт об активных триггерах более суток, сработавших ранее {$dayAgo}");

        // Добавление шапки таблицы
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', "Узел сети")
            ->setCellValue('B1', "Имя триггера")
            ->setCellValue('C1', "Важность триггера")
            ->setCellValue('D1', "Последнее изменение")
            ->setCellValue('E1', "Возраст");
        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleHeader);
        $objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($styleHeader);
        $objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($styleHeader);
        $objPHPExcel->getActiveSheet()->getStyle('D1')->applyFromArray($styleHeader);
        $objPHPExcel->getActiveSheet()->getStyle('E1')->applyFromArray($styleHeader);

        // Добавление данных
        $cell = 2;
        foreach ($activeTriggers as $key => $trigger) {
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $cell, $trigger->hostName)
                ->setCellValue('B' . $cell, $trigger->description)
                ->setCellValue('C' . $cell, $trigger->priority)
                ->setCellValue('D' . $cell, strftime('%d.%m.%Y %H:%M', $trigger->lastchange))
                ->setCellValue('E' . $cell, $trigger->age);
            $objPHPExcel->getActiveSheet()->getStyle('A' . $cell)->applyFromArray(($key % 2 == 0) ? ($styleCell) : ($styleFillCell));
            $objPHPExcel->getActiveSheet()->getStyle('B' . $cell)->applyFromArray(($key % 2 == 0) ? ($styleCell) : ($styleFillCell));
            $objPHPExcel->getActiveSheet()->getStyle('C' . $cell)->applyFromArray(($key % 2 == 0) ? ($styleCell) : ($styleFillCell));
            $objPHPExcel->getActiveSheet()->getStyle('D' . $cell)->applyFromArray(($key % 2 == 0) ? ($styleCell) : ($styleFillCell));
            $objPHPExcel->getActiveSheet()->getStyle('E' . $cell)->applyFromArray(($key % 2 == 0) ? ($styleCell) : ($styleFillCell));
            $cell++;
        }
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);

        // Заголовок второго листа
        $objPHPExcel->createSheet()->setTitle('Кол-во изменений триггеров');
        $objPHPExcel->setActiveSheetIndex(1);

        // Дата 100 активных триггеров
        $objPHPExcel->getActiveSheet()->mergeCells('A1:D1');
        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleCell);
        $objPHPExcel->getActiveSheet()->setCellValue('A1', strftime('%d.%m.%Y', time()));

        // Добавление шапки таблицы
        $objPHPExcel->getActiveSheet()
            ->setCellValue('A2', "Узел сети")
            ->setCellValue('B2', "Имя триггера")
            ->setCellValue('C2', "Важность триггера")
            ->setCellValue('D2', "Количество изменений состояния");
        $objPHPExcel->getActiveSheet()->getStyle('A2')->applyFromArray($styleHeader);
        $objPHPExcel->getActiveSheet()->getStyle('B2')->applyFromArray($styleHeader);
        $objPHPExcel->getActiveSheet()->getStyle('C2')->applyFromArray($styleHeader);
        $objPHPExcel->getActiveSheet()->getStyle('D2')->applyFromArray($styleHeader);

        // Добавление данных
        $cell = 3;
        foreach ($topTriggers as $key => $trigger) {
            $objPHPExcel->setActiveSheetIndex(1)
                ->setCellValue('A' . $cell, $trigger['hostName'])
                ->setCellValue('B' . $cell, $trigger['description'])
                ->setCellValue('C' . $cell, $trigger['priority'])
                ->setCellValue('D' . $cell, $trigger['quantityEvents']);
            $objPHPExcel->getActiveSheet()->getStyle('A' . $cell)->applyFromArray(($key % 2 == 0) ? ($styleCell) : ($styleFillCell));
            $objPHPExcel->getActiveSheet()->getStyle('B' . $cell)->applyFromArray(($key % 2 == 0) ? ($styleCell) : ($styleFillCell));
            $objPHPExcel->getActiveSheet()->getStyle('C' . $cell)->applyFromArray(($key % 2 == 0) ? ($styleCell) : ($styleFillCell));
            $objPHPExcel->getActiveSheet()->getStyle('D' . $cell)->applyFromArray(($key % 2 == 0) ? ($styleCell) : ($styleFillCell));
            $cell++;
        }
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);

        // Назначение активного листа
        $objPHPExcel->setActiveSheetIndex(0);

        // Запись в файл Excel2007
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save(public_path() . '/' . $fileName);
    }

    // Ежедневный сводный отчёт
    public static function getTroublesForConsolidatedReport()
    {
        $now = time();
        $firstDate = strftime('%Y-%m-%d', $now - 24 * 60 * 60);
        $lastDate = strftime('%Y-%m-%d', $now);
        $troubles = Trouble::where('risk', true)
            ->whereBetween('started_at', [$firstDate . ' 09:00:00', $lastDate . ' 09:00:00'])
            ->with('service', 'status', 'directorate', 'filial', 'city', 'office')
            ->orderBy('started_at', 'asc')
            ->get();
        // Подсчёт интервала
        // Экранирование тегов в description и action
		// Выделение ссылок http:// и https://
        // Замена \r\n на <br />
        foreach ($troubles as $key => $trouble) {
            if(null == $trouble->finished_at) {
                $troubles[$key]->interval = '';
            } else {
                $troubles[$key]->interval = self::intervalToStr(strtotime($trouble->finished_at . ':00') - strtotime($trouble->started_at . ':00'));
            }
            if (null != $trouble->action) {
                $troubles[$key]->action = htmlspecialchars($trouble->action);
				$troubles[$key]->action = preg_replace("/(https??:\/\/\S+)/iu", "<a href=\"\\1\" target=\"_blank\">\\1</a>", $trouble->action);
                $troubles[$key]->action = str_replace("\r\n", "<br />", $trouble->action);
            }
            if (null != $trouble->description) {
                $troubles[$key]->description = htmlspecialchars($trouble->description);
				$troubles[$key]->description = preg_replace("/(https??:\/\/\S+)/iu", "<a href=\"\\1\" target=\"_blank\">\\1</a>", $trouble->description);
                $troubles[$key]->description = str_replace("\r\n", "<br />", $trouble->description);
            }
        }
        return $troubles;
    }

    // Отчёт событий БФКО
    public static function troublesBFKO($firstDate, $lastDate, $firstDate2, $lastDate2, $fileName)
    {
        $troubles = Trouble::whereIn('id_user', [21,22,23,24])
            ->whereBetween('started_at', [$firstDate2 . ' 00:00:00', $lastDate2 . ' 23:59:59'])
            ->with('directorate', 'filial', 'city', 'office', 'source', 'service', 'status', 'user')
            ->orderBy('started_at', 'desc')
            ->orderBy('id', 'desc')
            ->get();
        // Запись недоступных подразделений в файл Excel для операционных рисков
        self::saveTroublesBFKOToExcel($troubles, $firstDate, $lastDate, $fileName);
        // Замена \r\n на <br />
        foreach ($troubles as $key => $trouble) {
            if (null != $trouble->action) {
                $troubles[$key]->action = str_replace("\r\n", "<br />", $trouble->action);
            }
            if (null != $trouble->description) {
                $troubles[$key]->description = str_replace("\r\n", "<br />", $trouble->description);
            }
        }
        return $troubles;
    }

    // Запись событий БФКО в файл Excel
    private static function saveTroublesBFKOToExcel($troubles, $firstDate, $lastDate, $fileName)
    {
        $objPHPExcel = new \PHPExcel();

        // Свойства документа
        $objPHPExcel->getProperties()->setCreator("Копп Сергей Владимирович")
            ->setLastModifiedBy("Копп Сергей Владимирович")
            ->setTitle("События БФКО")
            ->setSubject("Отчёт по событиям дежурных БФКО")
            ->setDescription("По всем вопросам обращаться в отдел мониторинга т.50248")
            ->setKeywords("Отчёт отдела мониторинга")
            ->setCategory("Отчётность");

        // Стили
        $styleHeader = [
            'font' => [
                'name' => 'Verdana',
                'color' => ['rgb' => '183483'],
                'bold' => true,
                'size' => 10,
            ],
            'fill' => [
                'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                'color' => ['rgb' => '6CAEDF'],
            ],
            'alignment' => [
                'wrap' => true,
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ],
        ];

        $styleCell = [
            'font' => [
                'name' => 'Verdana',
                'color' => ['rgb' => '000000'],
                'bold' => false,
                'size' => 10,
            ],
            'borders' => [
                'top' => [
                    'color' => ['rgb' => '000000'],
                    'style' => \PHPExcel_Style_Border::BORDER_DOTTED,
                ],
                'bottom' => [
                    'color' => ['rgb' => '000000'],
                    'style' => \PHPExcel_Style_Border::BORDER_DOTTED,
                ],
                'left' => [
                    'color' => ['rgb' => '000000'],
                    'style' => \PHPExcel_Style_Border::BORDER_DOTTED,
                ],
                'right' => [
                    'color' => ['rgb' => '000000'],
                    'style' => \PHPExcel_Style_Border::BORDER_DOTTED,
                ],
            ],
            'alignment' => [
                'wrap' => true,
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ],
        ];

        $styleAlignLeft = $styleCell;
        $styleAlignLeft['alignment']['horizontal'] = null;

        $styleFillCell = $styleCell;
        $styleFillCell['fill'] = [
            'type' => \PHPExcel_Style_Fill::FILL_SOLID,
            'color' => ['rgb' => 'EEEEEE'],
        ];

        $styleFillAlignLeft = $styleFillCell;
        $styleFillAlignLeft['alignment']['horizontal'] = null;

        // Указание периода
        $objPHPExcel->getActiveSheet()->mergeCells('A1:I1');
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', "Отчёт по событиям дежурных БФКО за период с {$firstDate} по {$lastDate}");

        // Добавление шапки таблицы
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A3', "Дирекция\nФилиал\nГород\nПодразделение")
            ->setCellValue('B3', "Время\nсобытия\n(МСК)")
            ->setCellValue('C3', "Источник события")
            ->setCellValue('D3', "Описание")
            ->setCellValue('E3', "Решение")
            ->setCellValue('F3', "Заявка в ОТРС")
            ->setCellValue('G3', "Время\nзавершения\n(МСК)")
            ->setCellValue('H3', "Сервис")
            ->setCellValue('I3', "Приоритет события")
            ->setCellValue('J3', "Дежурный");

        $objPHPExcel->getActiveSheet()->getStyle('A3')->applyFromArray($styleHeader);
        $objPHPExcel->getActiveSheet()->getStyle('B3')->applyFromArray($styleHeader);
        $objPHPExcel->getActiveSheet()->getStyle('C3')->applyFromArray($styleHeader);
        $objPHPExcel->getActiveSheet()->getStyle('D3')->applyFromArray($styleHeader);
        $objPHPExcel->getActiveSheet()->getStyle('E3')->applyFromArray($styleHeader);
        $objPHPExcel->getActiveSheet()->getStyle('F3')->applyFromArray($styleHeader);
        $objPHPExcel->getActiveSheet()->getStyle('G3')->applyFromArray($styleHeader);
        $objPHPExcel->getActiveSheet()->getStyle('H3')->applyFromArray($styleHeader);
        $objPHPExcel->getActiveSheet()->getStyle('I3')->applyFromArray($styleHeader);
        $objPHPExcel->getActiveSheet()->getStyle('J3')->applyFromArray($styleHeader);

        // Добавление данных
        $cell = 4;
        foreach ($troubles as  $key => $trouble) {
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $cell, (($trouble->directorate) ?  $trouble->directorate->name . " дирекция" : "Все дирекции") .
                    (($trouble->filial) ? "\n" . $trouble->filial->name . ' филиал' : '') .
                    (($trouble->city) ?  "\nг.  " . $trouble->city->name : '') .
                    (($trouble->office) ? "\n" . $trouble->office->name . "\n" . $trouble->office->address : '') )
                ->setCellValue('B' . $cell, $trouble->started_at)
                ->setCellValue('C' . $cell, $trouble->source->name)
                ->setCellValue('D' . $cell, $trouble->description)
                ->setCellValue('E' . $cell, $trouble->action)
                ->setCellValue('F' . $cell, $trouble->incident)
                ->setCellValue('G' . $cell, $trouble->finished_at)
                ->setCellValue('H' . $cell, $trouble->service->name)
                ->setCellValue('I' . $cell, $trouble->status->name)
                ->setCellValue('J' . $cell, $trouble->user->name);
            $objPHPExcel->getActiveSheet()->getStyle('A' . $cell)->applyFromArray(($key % 2 == 0) ? ($styleCell) : ($styleFillCell));
            $objPHPExcel->getActiveSheet()->getStyle('B' . $cell)->applyFromArray(($key % 2 == 0) ? ($styleCell) : ($styleFillCell));
            $objPHPExcel->getActiveSheet()->getStyle('C' . $cell)->applyFromArray(($key % 2 == 0) ? ($styleCell) : ($styleFillCell));
            $objPHPExcel->getActiveSheet()->getStyle('D' . $cell)->applyFromArray(($key % 2 == 0) ? ($styleAlignLeft) : ($styleFillAlignLeft));
            $objPHPExcel->getActiveSheet()->getStyle('E' . $cell)->applyFromArray(($key % 2 == 0) ? ($styleAlignLeft) : ($styleFillAlignLeft));
            $objPHPExcel->getActiveSheet()->getStyle('F' . $cell)->applyFromArray(($key % 2 == 0) ? ($styleCell) : ($styleFillCell));
            $objPHPExcel->getActiveSheet()->getStyle('G' . $cell)->applyFromArray(($key % 2 == 0) ? ($styleCell) : ($styleFillCell));
            $objPHPExcel->getActiveSheet()->getStyle('H' . $cell)->applyFromArray(($key % 2 == 0) ? ($styleCell) : ($styleFillCell));
            $objPHPExcel->getActiveSheet()->getStyle('I' . $cell)->applyFromArray(($key % 2 == 0) ? ($styleCell) : ($styleFillCell));
            $objPHPExcel->getActiveSheet()->getStyle('J' . $cell)->applyFromArray(($key % 2 == 0) ? ($styleCell) : ($styleFillCell));
            $cell++;
        }

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);

        // Наименование листа
        $objPHPExcel->getActiveSheet()->setTitle('События БФКО');

        // Назначение активного листа
        $objPHPExcel->setActiveSheetIndex(0);

        // Запись в файл Excel2007
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save(public_path() . '/' . $fileName);
    }
}