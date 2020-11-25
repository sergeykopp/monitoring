<?php

namespace Kopp\Http\Controllers {

    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;
    use Kopp\Models\Filial;
    use Kopp\Drivers\LogDriver;
    use Kopp\Drivers\MailDriver;
    use Kopp\Http\Requests\StoreCityRequest;
    use Kopp\Models\City;
    use Kopp\Models\Trouble;

    class CityController extends Controller
    {
        public function __construct()
        {
            parent::__construct();
            $this->template = 'city.';
        }

        // Просмотр всех городов
        public function cities(Request $request)
        {
            if (true === $request->user()->cannot('backup', new Trouble())) {
                return response()->view('errors.403', [], 403);
            }

            $cities = City::orderBy('name')->get();

            $this->data['title'] = 'Все города';
            $this->data['cities'] = $cities;
            $this->template .= 'cities';
            return $this->renderOutput();
        }

        // Создание нового города
        public function newCity(Request $request)
        {
            if (true === $request->user()->cannot('backup', new Trouble())) {
                return response()->view('errors.403', [], 403);
            }
            $filials = Filial::where('actual', '1')->orderBy('name', 'asc')->get();
            $this->data['title'] = 'Новый город';
            $this->data['filials'] = $filials;
            $this->template .= 'newCity';
            return $this->renderOutput();
        }

        // Редактирование города
        public function editCity(Request $request, $id)
        {
            if (true === $request->user()->cannot('update', new Trouble())) {
                return response()->view('errors.403', [], 403);
            }
            $city = City::find($id);
            $filials = Filial::where('actual', '1')->orderBy('name', 'asc')->get();
            if (null == $city) {
                return response()->view('errors.404', [], 404);
            }
            $this->data['title'] = 'Редактор города';
            $this->data['filials'] = $filials;
            $this->data['city'] = $city;
            $this->template .= 'editCity';
            return $this->renderOutput();
        }

        // Сохранение города
        // StoreCityRequest для верификации данных
        public function storeCity(StoreCityRequest $request)
        {
            if ($request->has('id_city')) {
                $city = City::find($request->input('id_city'));
                if (null == $city) {
                    return response()->view('errors.404', [], 404);
                }
                // Если редактирование города
                if ($request->has('update')) {
                    LogDriver::storeCity("Город id=$city->id до редактирования", $city);
                    MailDriver::storeCity("Город id=$city->id до редактирования", $city);
                    self::setCityParameters($city, $request);
                    $city->save();
                    $city = City::find($city->id);
                    LogDriver::storeCity("Город id=$city->id после редактирования", $city);
                    MailDriver::storeCity("Город id=$city->id после редактирования", $city);
                    return redirect()->route('editCity', ['id' => $city->id])->with('message', 'Информация сохранена');
                    // Если удаление города
                } elseif ($request->has('delete')) {
                    return redirect()->route('editCity', ['id' => $city->id])->with('message', 'Удаление города пока не действует');
//                    $city->actual = false;
//                    $city->save();
                }
                // Если новый город
            } else {
                $city = new City();
                self::setCityParameters($city, $request);
                $city->save();
                $id = DB::connection()->getPdo()->lastInsertId();
                $city = City::find($id);
                LogDriver::storeCity("Создан новый город id=$id", $city);
                MailDriver::storeCity("Создан новый город id=$id", $city);
                return redirect()->route('admin')->with('message', 'Новый город добавлен');
            }
            return redirect()->route('admin');
        }

        // Заполнение полей города
        private static function setCityParameters(City $city, $request)
        {
            $parameters = $request->all();
            if($request->has('update') and $city->id_filial !== $parameters['id_filial'] and 0 !== count($city->troubles)){
                $id_filial = $parameters['id_filial'];
                $id_directorate = Filial::find($id_filial)->id_directorate;
                foreach($city->troubles as $trouble){
                    $trouble->id_directorate = $id_directorate;
                    $trouble->id_filial = $id_filial;
                    $trouble->save();
                }
            }
            $city->id_filial = $parameters['id_filial'];
            $city->name = $parameters['name'];
        }
    }
}
