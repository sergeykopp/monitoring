<?php

namespace Kopp\Http\Controllers {

    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;
    use Kopp\Models\Directorate;
    use Kopp\Drivers\LogDriver;
    use Kopp\Drivers\MailDriver;
    use Kopp\Http\Requests\StoreDirectorateRequest;
    use Kopp\Models\Trouble;

    class DirectorateController extends Controller
    {
        public function __construct()
        {
            parent::__construct();
            $this->template = 'directorate.';
        }

        // Просмотр всех дирекций
        public function directorates(Request $request)
        {
            if (true === $request->user()->cannot('backup', new Trouble())) {
                return response()->view('errors.403', [], 403);
            }

            $directorates = Directorate::orderBy('name')->get();

            $this->data['title'] = 'Все дирекции';
            $this->data['directorates'] = $directorates;
            $this->template .= 'directorates';
            return $this->renderOutput();
        }

        // Создание новой дирекции
        public function newDirectorate(Request $request)
        {
            if (true === $request->user()->cannot('backup', new Trouble())) {
                return response()->view('errors.403', [], 403);
            }
            $this->data['title'] = 'Новая дирекция';
            $this->template .= 'newDirectorate';
            return $this->renderOutput();
        }

        // Редактирование дирекции
        public function editDirectorate(Request $request, $id)
        {
            if (true === $request->user()->cannot('update', new Trouble())) {
                return response()->view('errors.403', [], 403);
            }
            $directorate = Directorate::find($id);
            if (null == $directorate) {
                return response()->view('errors.404', [], 404);
            }
            $this->data['title'] = 'Редактор дирекции';
            $this->data['directorate'] = $directorate;
            $this->template .= 'editDirectorate';
            return $this->renderOutput();
        }

        // Сохранение дирекции
        // StoreDirectorateRequest для верификации данных
        public function storeDirectorate(StoreDirectorateRequest $request)
        {
            if ($request->has('id_directorate')) {
                $directorate = Directorate::find($request->input('id_directorate'));
                if (null == $directorate) {
                    return response()->view('errors.404', [], 404);
                }
                // Если редактирование дирекции
                if ($request->has('update')) {
                    LogDriver::storeDirectorate("Дирекция id=$directorate->id до редактирования", $directorate);
                    MailDriver::storeDirectorate("Дирекция id=$directorate->id до редактирования", $directorate);
                    self::setDirectorateParameters($directorate, $request);
                    $directorate->save();
                    $directorate = Directorate::find($directorate->id);
                    LogDriver::storeDirectorate("Дирекция id=$directorate->id после редактирования", $directorate);
                    MailDriver::storeDirectorate("Дирекция id=$directorate->id после редактирования", $directorate);
                    return redirect()->route('editDirectorate', ['id' => $directorate->id])->with('message', 'Информация сохранена');
                    // Если удаление дирекции
                } elseif ($request->has('delete')) {
                    return redirect()->route('editDirectorate', ['id' => $directorate->id])->with('message', 'Удаление дирекции пока не действует');
//                    $directorate->actual = false;
//                    $directorate->save();
                }
                // Если новая дирекция
            } else {
                $directorate = new Directorate();
                self::setDirectorateParameters($directorate, $request);
                $directorate->save();
                $id = DB::connection()->getPdo()->lastInsertId();
                $directorate = Directorate::find($id);
                LogDriver::storeDirectorate("Создана новая дирекция id=$id", $directorate);
                MailDriver::storeDirectorate("Создана новая дирекция id=$id", $directorate);
                return redirect()->route('admin')->with('message', 'Новая дирекция добавлена');
            }
            return redirect()->route('admin');
        }

        // Заполнение полей дирекции
        private static function setDirectorateParameters(Directorate $directorate, $request)
        {
            $parameters = $request->all();
            if($request->has('update') and 0 !== count($directorate->troubles)){
                $id_directorate = $parameters['id_directorate'];
                foreach($directorate->troubles as $trouble){
                    $trouble->id_directorate = $id_directorate;
                    $trouble->save();
                }
            }
            $directorate->name = $parameters['name'];
        }
    }
}
