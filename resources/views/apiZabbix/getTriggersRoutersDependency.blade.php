@extends('mainAPIZabbix')

@section('title')
    getTriggersRoutersDependency
@endsection

@section('body')
    <form>
        <h1>Поиск неправильных зависимостей по маршрутизаторам</h1>
        <h2>Предназначен для исправления зависимостей</h2>
        <fieldset class="inline-block">
            <legend>Процесс запроса</legend>
            <label name="process" class="process"></label>
        </fieldset>
    </form>

    <div id="content">
        <table>
            <thead>
            <tr>
                <td>Имя триггера</td>
                <td>Зависимость</td>
                <td>Видимое имя узла сети</td>
            </tr>
            </thead>
            <tbody name="target">
            </tbody>
        </table>
    </div>
@endsection

@section('script')
    <script language="JavaScript" src="/js/apiZabbix/getTriggersRoutersDependency.js?version=32"></script>
@endsection