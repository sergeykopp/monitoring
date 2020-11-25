@extends('mainAdministration')

@section('content')
    @if(count($users) > 0)
        <h2>Всего пользователей {{ count($users) }}</h2><br />
        @foreach($users as $user)
            <a href="/user/edit/{{ $user->id }}">{{ $user->name }}</a>
            <span style="color: red">{{ $user->login }}</span><br />
            <span style="color: green">{{ $user->email }}</span><br /><br />
        @endforeach
    @endif
    <script>
        document.getElementById("content").style.display = "block";
    </script>
@endsection