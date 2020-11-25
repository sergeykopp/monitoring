@extends('mainErrors')

@section('code', '401')
@section('title', __('Ошибка авторизации'))

@section('image')
	<div style="background-image: url({{ asset('/svg/403.svg') }});" class="absolute pin bg-cover bg-no-repeat md:bg-left lg:bg-center">
	</div>
@endsection

@section('message', __('Извините, но для просмотра этой страницы Вы не авторизованы.'))
