@extends('mainAdministration')

@section('content')
    @if(count($directorates) > 0)
        <h2>Всего дирекций {{ count($directorates) }}</h2><br />
        @foreach($directorates as $directorate)
            <a href="/directorate/edit/{{ $directorate->id }}">{{ $directorate->name }}</a><br /><br />
        @endforeach
    @endif
    <script>
        document.getElementById("content").style.display = "block";
    </script>
@endsection