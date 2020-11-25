<?php

namespace Kopp\Http\Controllers {

    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;
    use Kopp\Models\City;
    use Kopp\Drivers\LogDriver;
    use Kopp\Drivers\MailDriver;
    use Kopp\Http\Requests\StoreOfficeRequest;
    use Kopp\Models\Office;
    use Kopp\Models\Trouble;

    class OfficeController extends Controller
    {
        public function __construct()
        {
            parent::__construct();
            $this->template = 'office.';
        }

        // Просмотр всех подразделений
        public function offices(Request $request)
        {
            if (true === $request->user()->cannot('backup', new Trouble())) {
                return response()->view('errors.403', [], 403);
            }

            $offices = Office::orderBy('name')->get();

            $this->data['title'] = 'Все подразделения';
            $this->data['offices'] = $offices;
            $this->template .= 'offices';
            return $this->renderOutput();
        }

        // Просмотр всех актуальных подразделений
        public function actualOffices(Request $request)
        {
            if (true === $request->user()->cannot('backup', new Trouble())) {
                return response()->view('errors.403', [], 403);
            }

            $offices = Office::where('actual', true)->
                orderBy('name')->
                get();

            $this->data['title'] = 'Актуальные подразделения';
            $this->data['offices'] = $offices;
            $this->template .= 'offices';
            return $this->renderOutput();
        }

        // Создание нового подразделения
        public function newOffice(Request $request)
        {
            if (true === $request->user()->cannot('backup', new Trouble())) {
                return response()->view('errors.403', [], 403);
            }
            $cities = City::where('actual', '1')->orderBy('name', 'asc')->get();
            $this->data['title'] = 'Новое подразделение';
            $this->data['cities'] = $cities;
            $this->template .= 'newOffice';
            return $this->renderOutput();
        }

        // Редактирование подразделения
        public function editOffice(Request $request, $id)
        {
            if (true === $request->user()->cannot('update', new Trouble())) {
                return response()->view('errors.403', [], 403);
            }
            $office = Office::find($id);
            $cities = City::where('actual', '1')->orderBy('name', 'asc')->get();
            if (null == $office) {
                return response()->view('errors.404', [], 404);
            }
            $this->data['title'] = 'Редактор подразделения';
            $this->data['cities'] = $cities;
            $this->data['office'] = $office;
            $this->template .= 'editOffice';
            return $this->renderOutput();
        }

        // Сохранение подразделения
        // StoreOfficeRequest для верификации данных
        public function storeOffice(StoreOfficeRequest $request)
        {
            if ($request->has('id_office')) {
                $office = Office::find($request->input('id_office'));
                if (null == $office) {
                    return response()->view('errors.404', [], 404);
                }
                // Если редактирование подразделения
                if ($request->has('update')) {
                    LogDriver::storeOffice("Подразделение id=$office->id до редактирования", $office);
                    MailDriver::storeOffice("Подразделение id=$office->id до редактирования", $office);
                    self::setOfficeParameters($office, $request);
                    $office->save();
                    $office = Office::find($office->id);
                    LogDriver::storeOffice("Подразделение id=$office->id после редактирования", $office);
                    MailDriver::storeOffice("Подразделение id=$office->id после редактирования", $office);
                    return redirect()->route('editOffice', ['id' => $office->id])->with('message', 'Информация сохранена');
                    // Если удаление подразделения
                } elseif ($request->has('delete')) {
                    $office->actual = false;
                    $office->save();
					LogDriver::storeOffice("Подразделение id=$office->id удалено", $office);
                    MailDriver::storeOffice("Подразделение id=$office->id удалено", $office);
                    return redirect()->route('main')->with('message', 'Подразделение удалено');
                }
                // Если новое подразделение
            } else {
                $office = new Office();
                self::setOfficeParameters($office, $request);
                $office->save();
                $id = DB::connection()->getPdo()->lastInsertId();
                $office = Office::find($id);
                LogDriver::storeOffice("Создано новое подразделение id=$id", $office);
                MailDriver::storeOffice("Создано новое подразделение id=$id", $office);
                return redirect()->route('admin')->with('message', 'Новое подразделение добавлено');
            }
            return redirect()->route('admin');
        }

        // Заполнение полей подразделения
        private static function setOfficeParameters(Office $office, $request)
        {
            $parameters = $request->all();
            if($request->has('update') and $office->id_city !== $parameters['id_city'] and 0 !== count($office->troubles)){
                $id_city = $parameters['id_city'];
                $id_filial = City::find($id_city)->id_filial;
                $id_directorate = City::find($id_city)->filial->id_directorate;
                foreach($office->troubles as $trouble){
                    $trouble->id_directorate = $id_directorate;
                    $trouble->id_filial = $id_filial;
                    $trouble->id_city = $id_city;
                    $trouble->save();
                }
            }
            $office->id_city = $parameters['id_city'];
            $office->name = $parameters['name'];
            $office->address = $parameters['address'];
            $office->notes = $parameters['notes'];
        }
    }
}
