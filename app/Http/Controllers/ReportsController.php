<?php

namespace Kopp\Http\Controllers;

use Illuminate\Http\Request;
use Kopp\Drivers\ReportsDriver;

class ReportsController extends Controller
{
    public function __construct(){
        parent::__construct();
        $this->template = 'reports.';
    }

    // Недоступность подразделений
    public function availability(Request $request)
    {
        $rows = [];
        $fileName = '';
		session()->forget('errorFirstDate');
        session()->forget('errorLastDate');
        session()->forget('errorInterval');
        if ($request->isMethod('get')) {
            $sortBy = '';
            $firstDate = '';
            $lastDate = '';
        } else {
            $sortBy = $request->input('sortBy');
            $firstDate = trim($request->input('firstDate'));
            $lastDate = trim($request->input('lastDate'));
            // Проверка firstDate
            if (preg_match("/^([0-9]{1,2})\.([0-9]{1,2})\.([0-9]{4})$/u", $firstDate, $regs)) {
                $firstDate2 = "$regs[3]-$regs[2]-$regs[1]";
            } else {
                $firstDate2 = $firstDate;
                session(['errorFirstDate' => 'Неверно задана дата начала периода, формат 00.00.0000']);
            }
            // Проверка lastDate
            if (preg_match("/^([0-9]{1,2})\.([0-9]{1,2})\.([0-9]{4})$/u", $lastDate, $regs)) {
                $lastDate2 = "$regs[3]-$regs[2]-$regs[1]";
            } else {
                $lastDate2 = $lastDate;
                session(['errorLastDate' => 'Неверно задана дата конца периода, формат 00.00.0000']);
            }
            // Проверка разности дат
            if (strtotime($firstDate2) > strtotime($lastDate2)) {
                if (!(session()->has('errorFirstDate') or session()->has('errorLastDate'))){
                    session(['errorInterval' => 'Дата начала периода позже даты конца периода']);
                }
            }
            if (!session()->has('errorFirstDate') and !session()->has('errorLastDate') and !session()->has('errorInterval')) {
                $fileName = preg_replace("/\./u", '_' . microtime() . '.', config('settings.availability_reports'));
                $rows = ReportsDriver::availability($firstDate, $lastDate, $firstDate2, $lastDate2, $sortBy, $fileName);
            }
        }
        $this->data['title'] = 'Недоступность подразделений';
        $this->data['rows'] = $rows;
        $this->data['period'] = ['firstDate' => $firstDate, 'lastDate' => $lastDate];
        $this->data['sortBy'] = $sortBy;
        $this->data['fileName'] = $fileName;
        $this->data['timeRanges'] = ReportsDriver::timeRanges();
        $this->template .= 'availability';
        return $this->renderOutput();
    }

    // Недоступность подразделений для операционных рисков
    public function forOperRisk(Request $request)
    {
        $troubles = [];
        $fileName = '';
		session()->forget('errorFirstDate');
        session()->forget('errorLastDate');
        session()->forget('errorInterval');
        if ($request->isMethod('get')) {
            $firstDate = '';
            $lastDate = '';
        } else {
            $firstDate = trim($request->input('firstDate'));
            $lastDate = trim($request->input('lastDate'));
            // Проверка firstDate
            if (preg_match("/^([0-9]{1,2})\.([0-9]{1,2})\.([0-9]{4})$/u", $firstDate, $regs)) {
                $firstDate2 = "$regs[3]-$regs[2]-$regs[1]";
            } else {
                $firstDate2 = $firstDate;
                session(['errorFirstDate' => 'Неверно задана дата начала периода, формат 00.00.0000']);
            }
            // Проверка lastDate
            if (preg_match("/^([0-9]{1,2})\.([0-9]{1,2})\.([0-9]{4})$/u", $lastDate, $regs)) {
                $lastDate2 = "$regs[3]-$regs[2]-$regs[1]";
            } else {
                $lastDate2 = $lastDate;
                session(['errorLastDate' => 'Неверно задана дата конца периода, формат 00.00.0000']);
            }
            // Проверка разности дат
            if (strtotime($firstDate2) > strtotime($lastDate2)) {
                if (!(session()->has('errorFirstDate') or session()->has('errorLastDate'))){
                    session(['errorInterval' => 'Дата начала периода позже даты конца периода']);
                }
            }
            if (!session()->has('errorFirstDate') and !session()->has('errorLastDate') and !session()->has('errorInterval')) {
                $fileName = preg_replace("/\./u", '_' . microtime() . '.', config('settings.operationRisk_reports'));
                $troubles = ReportsDriver::forOperRisk($firstDate, $lastDate, $firstDate2, $lastDate2, $fileName);
            }
        }
        $this->data['title'] = 'Для операционных рисков';
        $this->data['troubles'] = $troubles;
        $this->data['period'] = ['firstDate' => $firstDate, 'lastDate' => $lastDate];
        $this->data['fileName'] = $fileName;
        $this->data['timeRanges'] = ReportsDriver::timeRanges();
        $this->data['lastRiskData'] = ReportsDriver::getLastRiskData();
        $this->template .= 'risk';
        return $this->renderOutput();
    }

    // Отчёт событий БФКО
    public function troublesBFKO(Request $request)
    {
        $troubles = [];
        $fileName = '';
        session()->forget('errorFirstDate');
        session()->forget('errorLastDate');
        session()->forget('errorInterval');
        if ($request->isMethod('get')) {
            $firstDate = '';
            $lastDate = '';
        } else {
            $firstDate = trim($request->input('firstDate'));
            $lastDate = trim($request->input('lastDate'));
            // Проверка firstDate
            if (preg_match("/^([0-9]{1,2})\.([0-9]{1,2})\.([0-9]{4})$/u", $firstDate, $regs)) {
                $firstDate2 = "$regs[3]-$regs[2]-$regs[1]";
            } else {
                $firstDate2 = $firstDate;
                session(['errorFirstDate' => 'Неверно задана дата начала периода, формат 00.00.0000']);
            }
            // Проверка lastDate
            if (preg_match("/^([0-9]{1,2})\.([0-9]{1,2})\.([0-9]{4})$/u", $lastDate, $regs)) {
                $lastDate2 = "$regs[3]-$regs[2]-$regs[1]";
            } else {
                $lastDate2 = $lastDate;
                session(['errorLastDate' => 'Неверно задана дата конца периода, формат 00.00.0000']);
            }
            // Проверка разности дат
            if (strtotime($firstDate2) > strtotime($lastDate2)) {
                if (!(session()->has('errorFirstDate') or session()->has('errorLastDate'))){
                    session(['errorInterval' => 'Дата начала периода позже даты конца периода']);
                }
            }
            if (!session()->has('errorFirstDate') and !session()->has('errorLastDate') and !session()->has('errorInterval')) {
                $fileName = preg_replace("/\./u", '_' . microtime() . '.', config('settings.troublesBFKO_reports'));
                $troubles = ReportsDriver::troublesBFKO($firstDate, $lastDate, $firstDate2, $lastDate2, $fileName);
            }
        }
        $this->data['title'] = 'Проблемы БФКО';
        $this->data['troubles'] = $troubles;
        $this->data['period'] = ['firstDate' => $firstDate, 'lastDate' => $lastDate];
        $this->data['fileName'] = $fileName;
        $this->data['timeRanges'] = ReportsDriver::timeRanges();
        $this->template .= 'troublesBFKO';
        return $this->renderOutput();
    }
}
