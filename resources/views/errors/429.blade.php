@extends('mainErrors')

@section('code', '429')
@section('title', __('Много запросов'))

@section('image')
	<div style="background-image: url({{ asset('/svg/403.svg') }});" class="absolute pin bg-cover bg-no-repeat md:bg-left lg:bg-center">
	</div>
@endsection

@section('message', __('Извините, вы делаете слишком много запросов к нашим серверам.'))
