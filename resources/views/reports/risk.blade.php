@extends('mainReports')

@section('content')
    <table class="tableInterval">
        <tr>
            <td style="vertical-align: top">
                <table>
                    <tr style="color: #183483; background: #6CAEDF; font-weight: bold;">
                        <td style="text-align: center;">Дирекция</td>
                        <td style="text-align: center;">Филиал</td>
                        <td style="text-align: center;">Город</td>
                        <td style="text-align: center;">Подразделение</td>
                        <td style="text-align: center;">Время<br />события<br />(МСК)</td>
                        <td style="text-align: center;">Описание</td>
                        <td style="text-align: center;">Решение</td>
                        <td style="text-align: center;">Причина</td>
                        <td style="text-align: center;">Интервал</td>
                        <td style="text-align: center;">Время<br />завершения<br />(МСК)</td>
                        <td style="text-align: center;">Сервис</td>
                    </tr>
                    @if (0 < count($troubles))
                        @foreach($troubles as $key => $trouble)
                            <tr
                                @if($key % 2 != 0)
                                    style="background-color: #eee;"
                                @endif
                            >
                                <td style="text-align: center;">{{ ($trouble->directorate) ?  $trouble->directorate->name : 'Все дирекции' }}</td>
                                <td style="text-align: center;">{!! ($trouble->filial) ? $trouble->filial->name : '<br />' !!}</td>
                                <td style="text-align: center;">{!! ($trouble->city) ?  $trouble->city->name : '<br />' !!}</td>
                                <td style="text-align: center;">{!! ($trouble->office) ? $trouble->office->name . '<br />' . $trouble->office->address : '<br />' !!}</td>
                                <td style="text-align: center;">{{ $trouble->started_at }}</td>
                                <td style="text-align: left;">{!! (('' != $trouble->description) ? $trouble->description : '<br />') !!}</td>
                                <td style="text-align: left;">{!! (('' != $trouble->action) ? $trouble->action : '<br />') !!}</td>
                                <td style="text-align: center;">{!! ($trouble->cause) ? $trouble->cause->name . (('' != $trouble->detail) ? '<br />' . $trouble->detail : '') : '' !!}</td>
                                <td style="text-align: center;">{{ $trouble->interval }}</td>
                                <td style="text-align: center;">{{ $trouble->finished_at }}</td>
                                <td style="text-align: center;">{{ $trouble->service->name }}</td>
                            </tr>
                        @endforeach
                    @else
                        <script>
                            document.getElementById("content").style.display = "block";
                        </script>
                    @endif
                </table>
            </td>
            <td style="border: none; vertical-align: top;">
                <form id="interval" method="post">
                    {{ csrf_field() }}
                    @if (session()->has('errorInterval'))
                        <span style="color: red; font-weight: bold;">{{ session()->get('errorInterval') }}</span><br /><br />
                    @endif
                    @if (session()->has('errorFirstDate'))
                        <span style="color: red; font-weight: bold;">{{ session()->get('errorFirstDate') }}</span><br /><br />
                    @endif
                    <fieldset>
                        <label>От:</label>
                        <input type="text" name="firstDate" autocomplete="off" placeholder="00.00.0000" value="{{ $period['firstDate'] }}" />
                    </fieldset>
                    @if (session()->has('errorLastDate'))
                        <span style="color: red; font-weight: bold;">{{ session()->get('errorLastDate') }}</span><br /><br />
                    @endif
                    <fieldset>
                        <label>До:</label>
                        <input type="text" name="lastDate" autocomplete="off" placeholder="00.00.0000" value="{{ $period['lastDate'] }}" />
                    </fieldset>
                    <fieldset>
                        <input type="submit" value="Сформировать отчёт" />
                    </fieldset>
                    @if (0 < count($troubles))
                        <br /><a href="{{ asset($fileName) }}">Скачать отчёт xls</a><br /><br />
                    @endif
                    <div id="timeRange">
                        <p onclick="document.getElementsByName('firstDate')[0].value='{{ $timeRanges['aWeekAgo'] }}'; document.getElementsByName('lastDate')[0].value='{{ $timeRanges['today'] }}';">За последнюю неделю</p>
                        <p onclick="document.getElementsByName('firstDate')[0].value='{{ $timeRanges['aTwoWeekAgo'] }}'; document.getElementsByName('lastDate')[0].value='{{ $timeRanges['today'] }}';">За последние 2 недели</p>
                        <p onclick="document.getElementsByName('firstDate')[0].value='{{ $timeRanges['aMonthAgo'] }}'; document.getElementsByName('lastDate')[0].value='{{ $timeRanges['today'] }}';">За последний месяц</p>
                        <p onclick="document.getElementsByName('firstDate')[0].value='{{ $timeRanges['aThreeMonthAgo'] }}'; document.getElementsByName('lastDate')[0].value='{{ $timeRanges['today'] }}';">За последние 3 месяца</p>
                        <p onclick="document.getElementsByName('firstDate')[0].value='{{ $timeRanges['aSixMonthAgo'] }}'; document.getElementsByName('lastDate')[0].value='{{ $timeRanges['today'] }}';">За последние 6 месяцев</p>
                        <p onclick="document.getElementsByName('firstDate')[0].value='{{ $timeRanges['aYearAgo'] }}'; document.getElementsByName('lastDate')[0].value='{{ $timeRanges['today'] }}';">За последний год</p>
                    </div>
                </form>
            </td>
        </tr>
        @if (0 == count($troubles))
            <tr>
                <td>Последний технический риск зарегистрирован {{ $lastRiskData }}</td>
            </tr>
        @endif
    </table>
@endsection