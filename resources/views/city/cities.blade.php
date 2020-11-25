@extends('mainAdministration')

@section('content')
    @if(count($cities) > 0)
        <h2>Всего городов {{ count($cities) }}</h2><br />
        @foreach($cities as $city)
            <a href="/city/edit/{{ $city->id }}">{{ $city->name }}</a><br /><br />
        @endforeach
    @endif
    <script>
        document.getElementById("content").style.display = "block";
    </script>
@endsection