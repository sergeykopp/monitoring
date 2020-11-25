@extends('mainErrors')

@section('code', '503')
@section('title', __('Сервис недоступен'))

@section('image')
    <div style="background-image: url({{ asset('/svg/503.svg') }});" class="absolute pin bg-cover bg-no-repeat md:bg-left lg:bg-center">
    </div>
@endsection

@section('message', __('По техническим причинам ресурс временно не работает, приносим извинения.'))
