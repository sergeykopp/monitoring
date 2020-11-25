@extends('mainAdministration')

@section('content')
    @if(count($filials) > 0)
        <h2>Всего филиалов {{ count($filials) }}</h2><br />
        @foreach($filials as $filial)
            <a href="/filial/edit/{{ $filial->id }}">{{ $filial->name }}</a><br /><br />
        @endforeach
    @endif
    <script>
        document.getElementById("content").style.display = "block";
    </script>
@endsection