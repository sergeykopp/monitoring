@extends('mainReports')

@section('content')
    <table class="tableInterval">
        <tr>
            <td style="vertical-align: top">
                <table>
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
                        <td style="text-align: center;">Дежурный</td>
                    </tr>
                    @if (0 < count($troubles))
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
                                    @if($key % 2 != 0)
                                    style="background-color: #eee;"
                                    @endif
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
                                <td style="text-align: center;">{{ $trouble->user->name }}</td>
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
    </table>
@endsection
