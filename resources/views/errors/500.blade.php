@extends('mainErrors')

@section('code', '500')
@section('title', __('Ошибка'))

@section('image')
	<div style="background-image: url({{ asset('/svg/500.svg') }});" class="absolute pin bg-cover bg-no-repeat md:bg-left lg:bg-center">
	</div>
@endsection

@section('message', __('Извините, что-то пошло не так, обратитесь к разработчику.'))
