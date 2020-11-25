@extends('mainAdministration')

@section('content')
    @if(count($groupsServices) > 0)
        <h2>Всего групп сервисов {{ count($groupsServices) }}</h2><br />
        @foreach($groupsServices as $groupServices)
            <a href="/groupServices/edit/{{ $groupServices->id }}">{{ $groupServices->name }}</a><br /><br />
        @endforeach
    @endif
    <script>
        document.getElementById("content").style.display = "block";
    </script>
@endsection