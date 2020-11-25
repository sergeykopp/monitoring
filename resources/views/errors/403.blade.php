@extends('mainErrors')

@section('code', '403')
@section('title', __('Доступ запрещен'))

@section('image')
	<div style="background-image: url({{ asset('/svg/403.svg') }});" class="absolute pin bg-cover bg-no-repeat md:bg-left lg:bg-center">
	</div>
@endsection

@section('message', __('Извините, но вам запрещен доступ к этой странице.'))
