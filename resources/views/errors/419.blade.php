@extends('mainErrors')

@section('code', '419')
@section('title', __('Сеанс истек'))

@section('image')
	<div style="background-image: url({{ asset('/svg/403.svg') }});" class="absolute pin bg-cover bg-no-repeat md:bg-left lg:bg-center">
	</div>
@endsection

@section('message', __('Извините, ваш сеанс истек. Пожалуйста, обновите и попробуйте снова.'))
