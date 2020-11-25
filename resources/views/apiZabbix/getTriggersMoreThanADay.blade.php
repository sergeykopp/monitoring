@extends('mainAPIZabbix')

@section('title')
	getTriggersMoreThanADay
@endsection

@section('body')
	<form>
		<h1>Поиск триггеров, которые активны более суток</h1>
		<h2 name="dateStr"></h2>
		<fieldset class="inline-block">
			<legend>Процесс запроса</legend>
			<label name="process" class="process"></label>
		</fieldset>
	</form>
	<div id="content">
		<table>
			<thead>
				<tr>
					<td>Узел сети</td>
					<td>Имя триггера</td>
					<td>Важность триггера</td>
					<td>Последнее изменение</td>
					<td>Возраст</td>
				</tr>
			</thead>
			<tbody name="target">
			</tbody>
		</table>
	</div>
@endsection

@section('script')
	<script language="JavaScript" src="/js/apiZabbix/getTriggersMoreThanADay.js?version=1"></script>
@endsection