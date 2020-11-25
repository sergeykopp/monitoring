@extends('mainReports')

@section('content')
    <table class="tableInterval">
        <tr>
            <td style="vertical-align: top;">
                <table class="tableInterval">
                    <tr style="color: #183483; background: #6CAEDF; font-weight: bold;">
                        <td style="text-align: center;">Дирекция<br />Филиал<br />Город<br />Подразделение</td>
                        <td style="text-align: center;">Недоступность по<br />Каналам связи</td>
                        <td style="text-align: center;">Недоступность по<br />Электропитанию</td>
                        <td style="text-align: center;">Совокупная<br />недоступность</td>
                    </tr>
                    @if (0 < count($rows))
                        @foreach($rows as $row)
                            <tr>
                                <td style="text-align: center;">{{ $row['directorate'] }} дирекция, {{ $row['filial'] }} филиал
                                    <br />г. {{ $row['city'] }}, {{ $row['address'] }}
                                    <br />{{ $row['office'] }}</td>
                                <td style="text-align: center;">{!! ($row['channel']) ? $row['channel'] : '<br />' !!}</td>
                                <td style="text-align: center;">{!! ($row['elektro']) ? $row['elektro'] : '<br />' !!}</td>
                                <td style="text-align: center;">{!! ($row['all']) ? $row['all'] : '<br />' !!}</td>
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
                        <label>Сортировать по столбцу:</label>
                        <select name="sortBy">
                            <option value="all" @if($sortBy == 'all') selected="selected" @endif>Совокупная</option>
                            <option value="channel" @if($sortBy == 'channel') selected="selected" @endif>Канал связи</option>
                            <option value="elektro" @if($sortBy == 'elektro') selected="selected" @endif>Электропитание</option>
                        </select>
                    </fieldset>
                    <fieldset>
                        <input type="submit" value="Сформировать отчёт" />
                    </fieldset>
                    @if (0 < count($rows))
                        <br /><br /><a href="{{ asset($fileName) }}">Скачать отчёт xls</a>
                    @endif
                </form>
            </td>
            <td id="timeRange">
                <p onclick="document.getElementsByName('firstDate')[0].value='{{ $timeRanges['aWeekAgo'] }}'; document.getElementsByName('lastDate')[0].value='{{ $timeRanges['today'] }}';">За последнюю неделю</p>
                <p onclick="document.getElementsByName('firstDate')[0].value='{{ $timeRanges['aTwoWeekAgo'] }}'; document.getElementsByName('lastDate')[0].value='{{ $timeRanges['today'] }}';">За последние 2 недели</p>
                <p onclick="document.getElementsByName('firstDate')[0].value='{{ $timeRanges['aMonthAgo'] }}'; document.getElementsByName('lastDate')[0].value='{{ $timeRanges['today'] }}';">За последний месяц</p>
                <p onclick="document.getElementsByName('firstDate')[0].value='{{ $timeRanges['aThreeMonthAgo'] }}'; document.getElementsByName('lastDate')[0].value='{{ $timeRanges['today'] }}';">За последние 3 месяца</p>
                <p onclick="document.getElementsByName('firstDate')[0].value='{{ $timeRanges['aSixMonthAgo'] }}'; document.getElementsByName('lastDate')[0].value='{{ $timeRanges['today'] }}';">За последние 6 месяцев</p>
                <p onclick="document.getElementsByName('firstDate')[0].value='{{ $timeRanges['aYearAgo'] }}'; document.getElementsByName('lastDate')[0].value='{{ $timeRanges['today'] }}';">За последний год</p>
            </td>
        </tr>
    </table>
@endsection