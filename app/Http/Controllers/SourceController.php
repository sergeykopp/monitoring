<?php

namespace Kopp\Http\Controllers {

    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;
    use Kopp\Models\Source;
    use Kopp\Drivers\LogDriver;
    use Kopp\Drivers\MailDriver;
    use Kopp\Http\Requests\StoreSourceRequest;
    use Kopp\Models\Trouble;

    class SourceController extends Controller
    {
        public function __construct()
        {
            parent::__construct();
            $this->template = 'source.';
        }

        // Просмотр всех источников событий
        public function sources(Request $request)
        {
            if (true === $request->user()->cannot('backup', new Trouble())) {
                return response()->view('errors.403', [], 403);
            }

            $sources = Source::orderBy('name')->get();

            $this->data['title'] = 'Все источники событий';
            $this->data['sources'] = $sources;
            $this->template .= 'sources';
            return $this->renderOutput();
        }

        // Просмотр всех актуальных источников событий
        public function actualSources(Request $request)
        {
            if (true === $request->user()->cannot('backup', new Trouble())) {
                return response()->view('errors.403', [], 403);
            }

            $sources = Source::where('actual', true)->
                orderBy('name')->
                get();

            $this->data['title'] = 'Актуальные источники событий';
            $this->data['sources'] = $sources;
            $this->template .= 'sources';
            return $this->renderOutput();
        }

        // Создание нового источника событий
        public function newSource(Request $request)
        {
            if (true === $request->user()->cannot('backup', new Trouble())) {
                return response()->view('errors.403', [], 403);
            }
            $this->data['title'] = 'Новый источник событий';
            $this->template .= 'newSource';
            return $this->renderOutput();
        }

        // Редактирование источника событий
        public function editSource(Request $request, $id)
        {
            if (true === $request->user()->cannot('update', new Trouble())) {
                return response()->view('errors.403', [], 403);
            }
            $source = Source::find($id);
            if (null == $source) {
                return response()->view('errors.404', [], 404);
            }
            $this->data['title'] = 'Редактор источника событий';
            $this->data['source'] = $source;
            $this->template .= 'editSource';
            return $this->renderOutput();
        }

        // Сохранение источника событий
        // StoreSourceRequest для верификации данных
        public function storeSource(StoreSourceRequest $request)
        {
            if ($request->has('id_source')) {
                $source = Source::find($request->input('id_source'));
                if (null == $source) {
                    return response()->view('errors.404', [], 404);
                }
                // Если редактирование источника событий
                if ($request->has('update')) {
                    LogDriver::storeSource("Источник событий id=$source->id до редактирования", $source);
                    MailDriver::storeSource("Источник событий id=$source->id до редактирования", $source);
                    self::setSourceParameters($source, $request);
                    $source->save();
                    $source = Source::find($source->id);
                    LogDriver::storeSource("Источник событий id=$source->id после редактирования", $source);
                    MailDriver::storeSource("Источник событий id=$source->id после редактирования", $source);
                    return redirect()->route('editSource', ['id' => $source->id])->with('message', 'Информация сохранена');
                    // Если удаление источника событий
                } elseif ($request->has('delete')) {
                    $source->actual = false;
                    $source->save();
                    LogDriver::storeSource("Источник событий id=$source->id удалён", $source);
                    MailDriver::storeSource("Источник событий id=$source->id удалён", $source);
                    return redirect()->route('admin')->with('message', 'Источник событий удалён');
                }
                // Если новый источник событий
            } else {
                $source = new Source();
                self::setSourceParameters($source, $request);
                $source->save();
                $id = DB::connection()->getPdo()->lastInsertId();
                $source = Source::find($id);
                LogDriver::storeSource("Создан новый источник событий id=$id", $source);
                MailDriver::storeSource("Создан новый источник событий id=$id", $source);
                return redirect()->route('admin')->with('message', 'Новый источник событий добавлен');
            }
            return redirect()->route('admin');
        }

        // Заполнение полей источника событий
        private static function setSourceParameters(Source $source, $request)
        {
            $parameters = $request->all();
            $source->name = $parameters['name'];
        }
    }
}
