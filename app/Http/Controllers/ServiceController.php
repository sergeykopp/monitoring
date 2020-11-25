<?php

namespace Kopp\Http\Controllers {

    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;
    use Kopp\Models\GroupServices;
    use Kopp\Models\Service;
    use Kopp\Drivers\LogDriver;
    use Kopp\Drivers\MailDriver;
    use Kopp\Http\Requests\StoreServiceRequest;
    use Kopp\Models\Trouble;

    class ServiceController extends Controller
    {
        public function __construct()
        {
            parent::__construct();
            $this->template = 'service.';
        }

        // Просмотр всех сервисов
        public function services(Request $request)
        {
            if (true === $request->user()->cannot('backup', new Trouble())) {
                return response()->view('errors.403', [], 403);
            }

            $services = Service::orderBy('name')->get();

            $this->data['title'] = 'Все сервисы';
            $this->data['services'] = $services;
            $this->template .= 'services';
            return $this->renderOutput();
        }

        // Просмотр всех актуальных сервисов
        public function actualServices(Request $request)
        {
            if (true === $request->user()->cannot('backup', new Trouble())) {
                return response()->view('errors.403', [], 403);
            }

            $services = Service::where('actual', true)->
                orderBy('name')->
                get();

            $this->data['title'] = 'Актуальные сервисы';
            $this->data['services'] = $services;
            $this->template .= 'services';
            return $this->renderOutput();
        }

        // Создание нового сервиса
        public function newService(Request $request)
        {
            if (true === $request->user()->cannot('backup', new Trouble())) {
                return response()->view('errors.403', [], 403);
            }
            $groupsServices = GroupServices::where('actual', '1')->orderBy('name', 'asc')->get();
            $this->data['title'] = 'Новый сервис';
            $this->data['groupsServices'] = $groupsServices;
            $this->template .= 'newService';
            return $this->renderOutput();
        }

        // Редактирование сервиса
        public function editService(Request $request, $id)
        {
            if (true === $request->user()->cannot('update', new Trouble())) {
                return response()->view('errors.403', [], 403);
            }
            $service = Service::find($id);
            $groupsServices = GroupServices::where('actual', '1')->orderBy('name', 'asc')->get();
            if (null == $service) {
                return response()->view('errors.404', [], 404);
            }
            $this->data['title'] = 'Редактор сервиса';
            $this->data['groupsServices'] = $groupsServices;
            $this->data['service'] = $service;
            $this->template .= 'editService';
            return $this->renderOutput();
        }

        // Сохранение сервиса
        // StoreServiceRequest для верификации данных
        public function storeService(StoreServiceRequest $request)
        {
            if ($request->has('id_service')) {
                $service = Service::find($request->input('id_service'));
                if (null == $service) {
                    return response()->view('errors.404', [], 404);
                }
                // Если редактирование сервиса
                if ($request->has('update')) {
                    LogDriver::storeService("Сервис id=$service->id до редактирования", $service);
                    MailDriver::storeService("Сервис id=$service->id до редактирования", $service);
                    self::setServiceParameters($service, $request);
                    $service->save();
                    $service = Service::find($service->id);
                    LogDriver::storeService("Сервис id=$service->id после редактирования", $service);
                    MailDriver::storeService("Сервис id=$service->id после редактирования", $service);
                    return redirect()->route('editService', ['id' => $service->id])->with('message', 'Информация сохранена');
                    // Если удаление сервиса
                } elseif ($request->has('delete')) {
                    $service->actual = false;
                    $service->save();
                    LogDriver::storeService("Сервис id=$service->id удалён", $service);
                    MailDriver::storeService("Сервис id=$service->id удалён", $service);
                    return redirect()->route('main')->with('message', 'Сервис удалён');
                }
                // Если новый сервис
            } else {
                $service = new Service();
                self::setServiceParameters($service, $request);
                $service->save();
                $id = DB::connection()->getPdo()->lastInsertId();
                $service = Service::find($id);
                LogDriver::storeService("Создан новый сервис id=$id", $service);
                MailDriver::storeService("Создан новый сервис id=$id", $service);
                return redirect()->route('admin')->with('message', 'Новый сервис добавлен');
            }
            return redirect()->route('admin');
        }

        // Заполнение полей сервиса
        private static function setServiceParameters(Service $service, $request)
        {
            $parameters = $request->all();
            $service->id_group_services = $parameters['id_group_services'];
            $service->name = $parameters['name'];
        }
    }
}
