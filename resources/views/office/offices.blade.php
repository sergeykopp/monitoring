@extends('mainAdministration')

@section('content')
    @if(count($offices) > 0)
        <h2>Всего подразделений {{ count($offices) }}</h2><br />
        @foreach($offices as $office)
            <a href="/office/edit/{{ $office->id }}">{{ $office->name }}</a>
            <span style="color: red">{{ $office->address }}</span><br />
            <span style="color: green">{{ $office->notes }}</span><br /><br />
        @endforeach
    @endif
    <script>
        document.getElementById("content").style.display = "block";
    </script>
@endsection