@extends('mainAdministration')

@section('content')
    @if(count($services) > 0)
        <h2>Всего сервисов {{ count($services) }}</h2><br />
        @foreach($services as $service)
            <a href="/service/edit/{{ $service->id }}">{{ $service->name }}</a><br /><br />
        @endforeach
    @endif
    <script>
        document.getElementById("content").style.display = "block";
    </script>
@endsection