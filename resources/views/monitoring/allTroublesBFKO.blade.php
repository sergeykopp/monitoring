@extends('mainMonitoring')

@section('content')
    @if(0 < count($troubles))
        @php
            $reportStartTime = strtotime(strftime('%Y-%m-%d', time() - 24 * 60 * 60) . ' 09:00');
            $reportFinishTime = strtotime(strftime('%Y-%m-%d', time()) . ' 09:00');
        @endphp
        <table id="tableTroubles">
            <tr style="color: #183483; background: #6CAEDF; font-weight: bold;">
                <td style="text-align: center;">Дирекция<br />Филиал<br />Город<br />Подразделение</td>
                <td style="text-align: center;">Время<br />события<br />(МСК)</td>
                <td style="text-align: center;">Источник события</td>
                <td style="text-align: center;">Описание</td>
                <td style="text-align: center;">Решение</td>
                <td style="text-align: center;">Заявка в ОТРС</td>
                <td style="text-align: center;">Время<br />завершения<br />(МСК)</td>
                <td style="text-align: center;">Сервис</td>
                <td style="text-align: center;">Приоритет события</td>
                @can('update', new \Kopp\Models\Trouble())
                    <td style="text-align: center;">Дежурный</td>
                @endcan
            </tr>
            @foreach($troubles as $key => $trouble)
                <tr
                    @if($trouble->finished_at == null)
                        @if($trouble->status->name == 'Чрезвычайный')
                            class="supercritical"
                        @elseif($trouble->status->name == 'Высокий')
                            class="critical"
                        @elseif($trouble->status->name == 'Средний')
                            class="warning"
                        @endif
                    @endif
                    @can('update', $trouble)
                        @if(true == $trouble->risk and $reportStartTime <= strtotime($trouble->started_at) and $reportFinishTime >= strtotime($trouble->started_at))
                            style="background-color: #e3f0f9;"
                        @else
                            @if($key % 2 != 0)
                                style="background-color: #eee;"
                            @endif
                        @endif
                    @else
                        @if($key % 2 != 0)
                            style="background-color: #eee;"
                        @endif
                    @endcan
                >
                    <td style="text-align: center;">
                        @if(null != $trouble->directorate)
                            {{ $trouble->directorate->name }} дирекция
                            @if(null != $trouble->filial)
                                <br />{{ $trouble->filial->name }} филиал
                            @endif
                            @if(null != $trouble->city)
                                <br />г. {{ $trouble->city->name }}
                            @endif
                            @if(null != $trouble->office)
                                <br />{{ $trouble->office->name }}
                                <br />{!! $trouble->office->address !!}
                            @endif
                        @else
                            Все дирекции
                        @endif

                    </td>
                    <td style="text-align: center;">{{ $trouble->started_at }}</td>
                    <td style="text-align: center;">{{ $trouble->source->name }}</td>
                    <td style="max-width: 600px;">{!! (('' != $trouble->description) ? $trouble->description : '<br />') !!}</td>
                    <td style="max-width: 600px;">{!! (('' != $trouble->action) ? $trouble->action : '<br />') !!}</td>
                    <td style="text-align: center;">{!! ($trouble->incident ?? '<br />') !!}</td>
                    <td style="text-align: center;">{!! ($trouble->finished_at ?? '<br />') !!}</td>
                    <td style="text-align: center;">{{ $trouble->service->name }}</td>
                    <td style="text-align: center;">{{ $trouble->status->name }}</td>
                    @can('update', $trouble)
                        @php
                            $words = explode(' ', $trouble->user->name);
                            $firstLetter = mb_substr($words[1],0,1,"UTF-8");
                            $lastLetter = mb_substr($words[2],0,1,"UTF-8");
                        @endphp
                        <td style="text-align: center;">{{ $words[0] }} {{ $firstLetter }}.{{ $lastLetter }}.</td>
                    @endcan
                    <td style="text-align: center;">
						<a href="\edit\{{ $trouble->id }}">Правка</a><br /><br />
						<a href="\new\{{ $trouble->id }}">Копия</a>
					</td>
                </tr>
            @endforeach
        </table>
    @else
        <script>
            document.getElementById("content").style.display = "block";
        </script>
    @endif
@endsection

@section('pageNavigate')
    @if($countPages > 0)
        <!-- Ссылка на первую страницу -->
        @if($limitPages['firstPage'] > 1)
            <span class="link" onclick="document.navForm.currentPage.value=1; document.navForm.submit();">Первая</span>
        @endif

        <!-- Вывод ссылок страниц до текущей страницы -->
        @for($i = $limitPages['firstPage']; $i < $currentPage; $i++)
            <span class="link" onclick="document.navForm.currentPage.value={{ $i }}; document.navForm.submit();">{{ $i }}</span>
        @endfor

        <!-- Вывод номера текущей страницы -->
        @if($countPages > 1)
            <span id="unlink">
                @if($countPages != 1)
                    {{ $currentPage }}
                @endif
            </span>
        @endif

        <!-- Вывод ссылок страниц после текущей страницы -->
        @for($i = $currentPage + 1; $i <= $limitPages['lastPage']; $i++)
            <span class="link" onclick="document.navForm.currentPage.value={{ $i }}; document.navForm.submit();">{{ $i }}</span>
        @endfor

        <!-- Ссылка на последнюю страницу -->
        @if($limitPages['lastPage'] != $countPages)
            <span class="link" onclick="document.navForm.currentPage.value={{ $countPages }}; document.navForm.submit();">Последняя</span>
        @endif

        @if($countPages > 1)
            <br /><br />
        @endif
    @endif

    <form name="navForm" method="post">
        {{ csrf_field() }}
        <fieldset id="fieldSearch" style="position: relative">
            <label>Фраза: </label>
            <span id="ajaxList" style="display: none; position: absolute; text-align: left; background-color: white; border: 1px dotted black; padding: 0 5px 0 5px;"></span>
            <input type="text" name="searchPhrase" value="{{ $searchPhrase }}" size="30" autocomplete="off" />
            <input type="submit" value="Поиск" />
        </fieldset>
        @if(session()->has('errorDate'))
            <span style="color: red; font-weight: bold;">{{ session()->get('errorDate') }}</span>
        @endif
        <fieldset>
            <label>Дата: </label>
            <input type="date" name="date" autocomplete="off" placeholder="00.00.0000" value="{{ $date }}" />
        </fieldset>
        <fieldset>
            <label>Показывать по: </label>
            <select name="countTroublesInPage" onchange="document.navForm.currentPage.value=1; document.navForm.submit();">
                <option value="100" @if($countTroublesInPage == 50)selected="selected"@endif>100</option>
                <option value="50" @if($countTroublesInPage == 50)selected="selected"@endif>50</option>
                <option value="25" @if($countTroublesInPage == 25)selected="selected"@endif>25</option>
                <option value="10" @if($countTroublesInPage == 10)selected="selected"@endif>10</option>
                <option value="5" @if($countTroublesInPage == 5)selected="selected"@endif>5</option>
            </select>
            <label>Сервис: </label>
            <select name="selectService" onchange="document.navForm.currentPage.value=1;document.navForm.submit();">
                <option value="">Все сервисы</option>
                @foreach($services as $service)
                    <option value="{{ $service->id }}"
                        @if($selectService == $service->id)
                            selected="selected"
                        @endif
                    >{{ $service->name }}</option>
                @endforeach
            </select>
        </fieldset>
        <input type="button" value="Очистить всё" onclick="window.location.replace(window.location)" />
        <input type="hidden" name="currentPage" value="{{ $currentPage }}" />
    </form>
    <script language="JavaScript" src="/js/searchInfo.js?version=18"></script>
    <script language="JavaScript" src="/js/xmlHttpRequest.js"></script>
    <script language="JavaScript" src="/js/searchPhraseBFKO.js?version=4"></script>
    <script language="JavaScript" src="/js/searchFullText.js"></script>
@endsection

