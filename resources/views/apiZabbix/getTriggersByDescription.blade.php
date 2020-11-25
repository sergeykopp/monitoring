@extends('mainAPIZabbix')

@section('title')
    getTriggersByDescription
@endsection

@section('body')
    <form>
        <h1>Поиск триггеров в узлах по их именам</h1>
        <h2>Предназначен для изменения триггеров</h2>
        <fieldset class="inline-block">
            <legend>Триггеры</legend>
            <fieldset>
                <label>Имена триггеров содержат:</label>
                <input type="text" name="triggerName" placeholder="processor load is too high" autocomplete="off" />
            </fieldset>
            <fieldset class="inline-block">
                <legend>Состояние триггеров</legend>
                <input type="radio" name="triggerStatus" value="0" checked /> Активированы
                <input type="radio" name="triggerStatus" value="1" /> Деактивированы
            </fieldset><br /><br />
			<input type="checkbox" name="triggerValue" /> Только в состоянии <span style="color: red">ПРОБЛЕМА</span><br /><br />
			<input type="checkbox" name="onlyDeactivateItems" /> Только при <span style="color: red">деактивированных</span> элементах
        </fieldset>
        <fieldset class="inline-block">
            <legend>Узлы</legend>
            <fieldset>
                <label>Имена узлов содержат:</label>
                <input type="text" name="hostName" placeholder="mos-xen" autocomplete="off" />
            </fieldset>
            <fieldset class="inline-block">
                <legend>Состояние узлов</legend>
                <input type="radio" name="hostStatus" value="0" checked /> Активированы
                <input type="radio" name="hostStatus" value="1" /> Деактивированы
            </fieldset>
        </fieldset>
        <fieldset class="inline-block">
            <legend>Процесс запроса</legend>
            <label name="process" class="process"></label>
        </fieldset><br />
        <fieldset class="inline-block">
            <legend>Тип запроса</legend>
            <input type="radio" name="typeQuery" value="get" checked /> Создать список триггеров
            <input type="radio" name="typeQuery" value="deactivate" /> Деактивировать триггеры
            <input type="radio" name="typeQuery" value="activate" /> Активировать триггеры
            <p class="warning">Для внесения изменений необходимо добавить группу "Отдел мониторинга (RW)" к учётке ABSmonitoring в Zabbix</p>
        </fieldset>
        <fieldset>
            <input type="button" onclick="auth()" value="Запрос" />
        </fieldset>
    </form>

    <div id="content">
        <table>
            <thead>
            <tr>
                <td>Имя триггера</td>
                <td>Важность триггера</td>
                <td>Состояние триггера</td>
                <td>Имя узла сети</td>
                <td>Видимое имя узла сети</td>
                <td>Прокси узла сети</td>
            </tr>
            </thead>
            <tbody name="target">
            </tbody>
        </table>
    </div>
@endsection

@section('script')
	<script language="JavaScript" src="/js/apiZabbix/getTriggersByDescription.js?version=37"></script>
@endsection