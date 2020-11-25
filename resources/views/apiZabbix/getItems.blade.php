@extends('mainAPIZabbix')

@section('title')
	getItems
@endsection

@section('body')
	<form>
		<h1>Поиск элементов в узлах по их именам</h1>
		<h2>Предназначен для изменения элементов данных</h2>
		<fieldset class="inline-block">
			<legend>Элементы</legend>
			<fieldset>
				<label>Имена элементов содержат:</label>
				<input type="text" name="itemName" placeholder="number of processes" autocomplete="off" />
			</fieldset>
			<fieldset class="inline-block">
				<legend>Состояние элементов</legend>
				<input type="radio" name="itemStatus" value="0" checked /> Активированы
				<input type="radio" name="itemStatus" value="1" /> Деактивированы
			</fieldset>
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
			<input type="radio" name="typeQuery" value="get" checked /> Создать список элементов
			<input type="radio" name="typeQuery" value="deactivate" /> Деактивировать элементы
			<input type="radio" name="typeQuery" value="activate" /> Активировать элементы
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
				<td>Имя элемента</td>
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
	<script language="JavaScript" src="/js/apiZabbix/getItems.js?version=36"></script>
@endsection