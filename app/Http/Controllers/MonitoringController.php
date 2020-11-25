<?php

namespace Kopp\Http\Controllers {

    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;
    use Kopp\Drivers\LogDriver;
    use Kopp\Drivers\MailDriver;
    use Kopp\Http\Requests\StoreTroubleRequest;
    use Kopp\Drivers\TroublesDriver;
    use Kopp\Models\Trouble;
    use Kopp\Models\Service;

    class MonitoringController extends Controller
    {
        public function __construct()
        {
            parent::__construct();
            $this->template = 'monitoring.';
        }

        // Просмотр всех проблем
        public function allTroubles(Request $request)
        {
            $parameters = $request->all();
            if (!session()->has('countTroublesInPage')) {
                session(['countTroublesInPage' => 5]);
            }
            if ($request->isMethod('get')) {
                $parameters['currentPage'] = 1;
                $parameters['searchPhrase'] = '';
                $parameters['date'] = '';
                $parameters['selectService'] = '';
                $parameters['countTroublesInPage'] = 5;
                $parameters['countTroublesInPage'] = session()->get('countTroublesInPage');
            } else {
                if (session('countTroublesInPage') != $parameters['countTroublesInPage']) {
                    session(['countTroublesInPage' => $parameters['countTroublesInPage']]);
                }
            }
            $result = TroublesDriver::findByParameters($parameters);
            $this->data['title'] = 'Все проблемы';
            $this->data['services'] = Service::orderBy('name', 'asc')->get();
            $this->data['troubles'] = $result['troubles'];
            $this->data['countPages'] = $result['countPages'];
            $this->data['limitPages'] = $result['limitPages'];
            $this->data['currentPage'] = $result['currentPage'];
            $this->data['searchPhrase'] = $parameters['searchPhrase'];
            $this->data['date'] = $parameters['date'];
            $this->data['countTroublesInPage'] = $parameters['countTroublesInPage'];
            $this->data['selectService'] = $parameters['selectService'];
            $this->template .= 'allTroubles';
            return $this->renderOutput();
        }

        // Просмотр актуальных проблем
        public function actualTroubles()
        {
            $this->data['title'] = 'Актуальные проблемы';
            $this->data['troubles'] = TroublesDriver::actualTroubles();
            $this->template .= 'actualTroubles';
            return $this->renderOutput();
        }

        // Создание новой проблемы
        public function newTrouble(Request $request, $id = null)
        {
            if (true === $request->user()->cannot('add', new Trouble())) {
                return response()->view('errors.403', [], 403);
            }
            if (null != $id) {
                $trouble = Trouble::find($id);
                if (null == $trouble) {
					return response()->view('errors.404', [], 404);
                }
				$trouble->incident = null;
				$trouble->finished_at = null;
				$trouble->id_cause = null;
				$trouble->detail = null;
				$trouble->risk = false;
            } else {
				$trouble = new Trouble();
			}
            $date = new \DateTime(null, new \DateTimeZone(config('app.timezone')));
            //$date->add(new \DateInterval('PT1H')); // Плюс один час
            $trouble->started_at = $date->format("d.m.Y H:i");
            $this->data['title'] = 'Новая проблема';
            $this->data['trouble'] = $trouble;
            $this->initDataForFields();
            $this->template .= 'newTrouble';
            return $this->renderOutput();
        }

        // Редактирование старой проблемы
        public function editTrouble(Request $request, $id)
        {
			if (true === $request->user()->cannot('update', new Trouble())) {
                return response()->view('errors.403', [], 403);
            }
            $trouble = Trouble::find($id);
            if (null == $trouble) {
                return response()->view('errors.404', [], 404);
            }
            $this->data['title'] = 'Редактор проблемы';
            $this->data['trouble'] = $trouble;
            $this->initDataForFields();
            $this->template .= 'editTrouble';
            return $this->renderOutput();
        }

        // Сохранение проблемы
        // StoreTroubleRequest для верификации данных
        public function storeTrouble(StoreTroubleRequest $request)
        {
            if ($request->has('id_trouble')) {
                $trouble = Trouble::find($request->input('id_trouble'));
                if (null == $trouble) {
                    return response()->view('errors.404', [], 404);
                }
                // Если редактирование проблемы
                if ($request->has('update')) {
                    LogDriver::storeTrouble("Проблема id=$trouble->id до редактирования", $trouble);
                    MailDriver::storeTrouble("Проблема id=$trouble->id до редактирования", $trouble);
                    self::setTroubleParameters($trouble, $request);
                    $trouble->save();
                    $trouble = Trouble::find($trouble->id);
                    LogDriver::storeTrouble("Проблема id=$trouble->id после редактирования", $trouble);
                    MailDriver::storeTrouble("Проблема id=$trouble->id после редактирования", $trouble);
                    // Если мягкое удаление проблемы
                } elseif ($request->has('delete')) {
                    $trouble->delete();
                    LogDriver::storeTrouble("Проблема id=$trouble->id мягко удалена", $trouble);
                    MailDriver::storeTrouble("Проблема id=$trouble->id мягко удалена", $trouble);
                }
                // Если новая проблема
            } else {
                $trouble = new Trouble();
                self::setTroubleParameters($trouble, $request);
                $trouble->id_user = $request->user()->id;
                $trouble->save();
                $id = DB::connection()->getPdo()->lastInsertId();
                $trouble = Trouble::find($id);
                LogDriver::storeTrouble("Создана новая проблема id=$id", $trouble);
                MailDriver::storeTrouble("Создана новая проблема id=$id", $trouble);
            }
            return redirect()->route('main');
        }

        // Заполнение полей проблемы
        private static function setTroubleParameters(Trouble $trouble, $request)
        {
            $parameters = $request->all();
            $trouble->id_directorate = $parameters['id_directorate'];
            $trouble->id_filial = ($parameters['id_filial'] ?? null);
            $trouble->id_city = ($parameters['id_city'] ?? null);
            $trouble->id_office = ($parameters['id_office'] ?? null);
            $trouble->id_source = $parameters['id_source'];
            $trouble->id_service = $parameters['id_service'];
            $trouble->id_status = $parameters['id_status'];
            $trouble->started_at = $parameters['started_at'];
            $trouble->finished_at = $parameters['finished_at'];
            $trouble->description = $parameters['description'];
            $trouble->incident = $parameters['incident'];
            $trouble->action = $parameters['action'];
            if (true === $request->user()->can('risk', new Trouble())) {
                $trouble->id_cause = ((!isset($parameters['id_cause']) or '' == $parameters['id_cause']) ? null : $parameters['id_cause']);
                $trouble->detail = ($parameters['detail'] ?? null);
                $trouble->risk = isset($parameters['risk']);
            }
        }

        // Просмотр справочника
        public function getInfo()
        {
            $this->data['title'] = 'Справочник';
            $this->template .= 'info';
            return $this->renderOutput();
        }
    }
}
