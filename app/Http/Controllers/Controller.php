<?php

namespace Kopp\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Kopp\Models\Directorate;
use Kopp\Models\Filial;
use Kopp\Models\City;
use Kopp\Models\Office;
use Kopp\Models\Service;
use Kopp\Models\Status;
use Kopp\Models\Source;
use Kopp\Models\Cause;
use Kopp\Drivers\LogDriver;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $data = [];
    protected $template;

    public function __construct()
    {
		// Исключение формирования страницы
		if (true == in_array($_SERVER["REMOTE_ADDR"], config('settings.black_list_ip_address'))){
			die();
		}

		// Логирование IP-адресов и URI запросов
		if (false == in_array($_SERVER["REMOTE_ADDR"], config('settings.white_list_ip_address'))) {
			LogDriver::requestLog($_SERVER["REMOTE_ADDR"], $_SERVER['HTTP_HOST'] ?? 'undefined', $_SERVER['REQUEST_URI'], $_POST);
		}
    }

    protected function renderOutput()
    {
        return view($this->template)->with($this->data);
    }

    protected function initDataForFields()
    {
        $this->data['directorates'] = Directorate::where('actual', '1')->orderBy('name', 'asc')->get();
        $this->data['filials'] = Filial::where('actual', '1')->orderBy('name', 'asc')->get();
        $this->data['cities'] = City::where('actual', '1')->orderBy('name', 'asc')->get();
        $this->data['offices'] = Office::where('actual', '1')->orderBy('name', 'asc')->get();
        $this->data['services'] = Service::where('actual', '1')->orderBy('name', 'asc')->get();
        $this->data['statuses'] = Status::where('actual', '1')->orderBy('id', 'asc')->get();
        $this->data['sources'] = Source::where('actual', '1')->orderBy('name', 'asc')->get();
        $this->data['causes'] = Cause::where('actual', '1')->orderBy('name', 'asc')->get();
    }
}
