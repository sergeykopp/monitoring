<?php

namespace Kopp\Http\Controllers;

use Illuminate\Http\Request;
use Kopp\Drivers\AdministrationDriver;
use Kopp\Drivers\TroublesDriver;
use Kopp\Models\Trouble;
use Kopp\Models\Action;
use Kopp\Models\AuditLog;
use Kopp\Models\UserZabbix;
use Kopp\Models\Host;
use Kopp\Models\Item;
use Kopp\Models\Trigger;

class AdministrationController extends Controller
{
    public function __construct()
    {
		parent::__construct();
        $this->template = 'administration.';
        $this->middleware('auth');
    }

    public function backup(Request $request)
    {
        if(true === $request->user()->cannot('backup', new Trouble())){
            return response()->view('errors.403', [], 403);
        }
        if (session()->has('backup')) {
            session()->forget('backup');
        }
        if (session()->has('error')) {
            session()->forget('error');
        }
        if ($request->isMethod('post')) {
            if ($request->has('export')) {
                $res = AdministrationDriver::exportToXML();
                if (true === $res) {
                    session(['backup' => 'Экспорт базы данных в файл выполнен!!!']);
                } else {
                    session(['error' => $res]);
                }
            } elseif ($request->has('import')) {
                $res = AdministrationDriver::importFromXML();
                if (true === $res) {
                    session(['backup' => 'Импорт в базу данных из файла выполнен!!!']);
                } else {
                    session(['error' => $res]);
                }
            }
        }
        $this->data['title'] = 'Резервное копирование';
        $this->template .= 'backup';
        return $this->renderOutput();
    }

    /**
     * @return $this
     */
    public function channelsWithoutOffice(Request $request)
    {
        if(true === $request->user()->cannot('backup', new Trouble())){
            return response()->view('errors.403', [], 403);
        }

        $this->data['troubles'] = TroublesDriver::getChannelsWithoutOffice();
        $this->data['title'] = 'Каналы связи без указания подразделения';
        $this->template .= 'channelsWithoutOffice';
        return $this->renderOutput();
    }
}
