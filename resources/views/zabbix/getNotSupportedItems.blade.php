@extends('mainZabbix')

@section('title')
	getNotSupportedItems
@endsection

@section('body')
	<h1>Неподдерживаемые элементы данных</h1>
	<h2>Всего элементов данных {{ count($items) }}</h2>

	<div id="content">
		<table>
			<thead>
			<tr>
				<td>Имя узла</td>
				<td>Имя элемента</td>
			</tr>
			</thead>
			<tbody name="target">
				@foreach($items as $key=>$item)
					<tr
						@if($key % 2 != 0)
							style="background-color: #eee;"
						@endif
					>
						<td>{{ $item->host->name }}</td>
						<td>{{ $item->name }}</td>
						<td><a target="_blank" href="https://msk-zabbixfe01.corp.icba.biz/zabbix/items.php?form=update&hostid={{ $item->hostid }}&itemid={{ $item->itemid }}">Ссылка на элемент</a></td>
					<tr/>
				@endforeach
			</tbody>
		</table>
	</div>
@endsection