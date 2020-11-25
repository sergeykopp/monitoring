<?php

namespace Kopp\Http\Controllers {

    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;
    use Kopp\Models\Directorate;
    use Kopp\Drivers\LogDriver;
    use Kopp\Drivers\MailDriver;
    use Kopp\Http\Requests\StoreFilialRequest;
    use Kopp\Models\Filial;
    use Kopp\Models\Trouble;

    class FilialController extends Controller
    {
        public function __construct()
        {
            parent::__construct();
            $this->template = 'filial.';
        }

        // Просмотр всех филиалов
        public function filials(Request $request)
        {
            if (true === $request->user()->cannot('backup', new Trouble())) {
                return response()->view('errors.403', [], 403);
            }

            $filials = Filial::orderBy('name')->get();

            $this->data['title'] = 'Все филиалы';
            $this->data['filials'] = $filials;
            $this->template .= 'filials';
            return $this->renderOutput();
        }

        // Создание нового филиала
        public function newFilial(Request $request)
        {
            if (true === $request->user()->cannot('backup', new Trouble())) {
                return response()->view('errors.403', [], 403);
            }
            $directorates = Directorate::where('actual', '1')->orderBy('name', 'asc')->get();
            $this->data['title'] = 'Новый филиал';
            $this->data['directorates'] = $directorates;
            $this->template .= 'newFilial';
            return $this->renderOutput();
        }

        // Редактирование филиала
        public function editFilial(Request $request, $id)
        {
            if (true === $request->user()->cannot('update', new Trouble())) {
                return response()->view('errors.403', [], 403);
            }
            $filial = Filial::find($id);
            $directorates = Directorate::where('actual', '1')->orderBy('name', 'asc')->get();
            if (null == $filial) {
                return response()->view('errors.404', [], 404);
            }
            $this->data['title'] = 'Редактор филиала';
            $this->data['directorates'] = $directorates;
            $this->data['filial'] = $filial;
            $this->template .= 'editFilial';
            return $this->renderOutput();
        }

        // Сохранение филиала
        // StoreFilialRequest для верификации данных
        public function storeFilial(StoreFilialRequest $request)
        {
            if ($request->has('id_filial')) {
                $filial = Filial::find($request->input('id_filial'));
                if (null == $filial) {
                    return response()->view('errors.404', [], 404);
                }
                // Если редактирование филиала
                if ($request->has('update')) {
                    LogDriver::storeFilial("Филиал id=$filial->id до редактирования", $filial);
                    MailDriver::storeFilial("Филиал id=$filial->id до редактирования", $filial);
                    self::setFilialParameters($filial, $request);
                    $filial->save();
                    $filial = Filial::find($filial->id);
                    LogDriver::storeFilial("Филиал id=$filial->id после редактирования", $filial);
                    MailDriver::storeFilial("Филиал id=$filial->id после редактирования", $filial);
                    return redirect()->route('editFilial', ['id' => $filial->id])->with('message', 'Информация сохранена');
                    // Если удаление филиала
                } elseif ($request->has('delete')) {
                    return redirect()->route('editFilial', ['id' => $filial->id])->with('message', 'Удаление филиала пока не действует');
//                    $filial->actual = false;
//                    $filial->save();
                }
                // Если новый филиал
            } else {
                $filial = new Filial();
                self::setFilialParameters($filial, $request);
                $filial->save();
                $id = DB::connection()->getPdo()->lastInsertId();
                $filial = Filial::find($id);
                LogDriver::storeFilial("Создан новый филиал id=$id", $filial);
                MailDriver::storeFilial("Создан новый филиал id=$id", $filial);
                return redirect()->route('admin')->with('message', 'Новый филиал добавлен');
            }
            return redirect()->route('admin');
        }

        // Заполнение полей филиала
        private static function setFilialParameters(Filial $filial, $request)
        {
            $parameters = $request->all();
            if($request->has('update') and $filial->id_directorate !== $parameters['id_directorate'] and 0 !== count($filial->troubles)){
                $id_directorate = $parameters['id_directorate'];
                foreach($filial->troubles as $trouble){
                    $trouble->id_directorate = $id_directorate;
                    $trouble->save();
                }
            }
            $filial->id_directorate = $parameters['id_directorate'];
            $filial->name = $parameters['name'];
        }
    }
}
