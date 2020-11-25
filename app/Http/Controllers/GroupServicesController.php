<?php

namespace Kopp\Http\Controllers {

    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;
    use Kopp\Http\Requests\StoreGroupServicesRequest;
    use Kopp\Models\GroupServices;
    use Kopp\Drivers\LogDriver;
    use Kopp\Drivers\MailDriver;
    use Kopp\Http\Requests\StoreSourceRequest;
    use Kopp\Models\Trouble;

    class GroupServicesController extends Controller
    {
        public function __construct()
        {
            parent::__construct();
            $this->template = 'groupservices.';
        }

        // Просмотр всех групп сервисов
        public function groupsServices(Request $request)
        {
            if (true === $request->user()->cannot('backup', new Trouble())) {
                return response()->view('errors.403', [], 403);
            }

            $groupsServices = GroupServices::orderBy('name')->get();

            $this->data['title'] = 'Все группы сервисов';
            $this->data['groupsServices'] = $groupsServices;
            $this->template .= 'groupsServices';
            return $this->renderOutput();
        }

        // Создание новой группы сервисов
        public function newGroupServices(Request $request)
        {
            if (true === $request->user()->cannot('backup', new Trouble())) {
                return response()->view('errors.403', [], 403);
            }
            $this->data['title'] = 'Новая группа сервисов';
            $this->template .= 'newGroupServices';
            return $this->renderOutput();
        }

        // Редактирование группы сервисов
        public function editGroupServices(Request $request, $id)
        {
            if (true === $request->user()->cannot('update', new Trouble())) {
                return response()->view('errors.403', [], 403);
            }
            $groupServices = GroupServices::find($id);
            if (null == $groupServices) {
                return response()->view('errors.404', [], 404);
            }
            $this->data['title'] = 'Редактор источника событий';
            $this->data['groupServices'] = $groupServices;
            $this->template .= 'editGroupServices';
            return $this->renderOutput();
        }

        // Сохранение группы сервисов
        // StoreGroupServicesRequest для верификации данных
        public function storeGroupServices(StoreGroupServicesRequest $request)
        {
            if ($request->has('id_groupservices')) {
                $groupServices = GroupServices::find($request->input('id_groupservices'));
                if (null == $groupServices) {
                    return response()->view('errors.404', [], 404);
                }
                // Если редактирование группы сервисов
                if ($request->has('update')) {
                    LogDriver::storeGroupServices("Группа сервисов id=$groupServices->id до редактирования", $groupServices);
                    MailDriver::storeGroupServices("Группа сервисов id=$groupServices->id до редактирования", $groupServices);
                    self::setGroupServicesParameters($groupServices, $request);
                    $groupServices->save();
                    $groupServices = GroupServices::find($groupServices->id);
                    LogDriver::storeGroupServices("Группа сервисов id=$groupServices->id после редактирования", $groupServices);
                    MailDriver::storeGroupServices("Группа сервисов id=$groupServices->id после редактирования", $groupServices);
                    return redirect()->route('editGroupServices', ['id' => $groupServices->id])->with('message', 'Информация сохранена');
                    // Если удаление источника событий
                } elseif ($request->has('delete')) {
                    return redirect()->route('editGroupServices', ['id' => $groupServices->id])->with('message', 'Удаление группы сервисов пока не действует');
                    //$groupServices->actual = false;
                    //$groupServices->save();
                }
                // Если новый источник событий
            } else {
                $groupServices = new GroupServices();
                self::setGroupServicesParameters($groupServices, $request);
                $groupServices->save();
                $id = DB::connection()->getPdo()->lastInsertId();
                $groupServices = GroupServices::find($id);
                LogDriver::storeGroupServices("Создана новая группа сервисов id=$id", $groupServices);
                MailDriver::storeGroupServices("Создана новая группа сервисов id=$id", $groupServices);
                return redirect()->route('admin')->with('message', 'Новая группа сервисов добавлена');
            }
            return redirect()->route('admin');
        }

        // Заполнение полей источника событий
        private static function setGroupServicesParameters(GroupServices $groupServices, $request)
        {
            $parameters = $request->all();
            $groupServices->name = $parameters['name'];
        }
    }
}
