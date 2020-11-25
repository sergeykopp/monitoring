@extends('mainAdministration')

@section('content')
    @if(count($sources) > 0)
        <h2>Всего источников событий {{ count($sources) }}</h2><br />
        @foreach($sources as $source)
            <a href="/source/edit/{{ $source->id }}">{{ $source->name }}</a><br /><br />
        @endforeach
    @endif
    <script>
        document.getElementById("content").style.display = "block";
    </script>
@endsection