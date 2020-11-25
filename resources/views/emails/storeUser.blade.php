<html>
<head>
    <style>
        body {
            font-family: Verdana;
            font-size: 12px;
            margin: 0px;
        }
    </style>
</head>
<body>
<p><strong>ФИО: </strong><br/>{{ $user->name }}</p>
<p><strong>Имя пользователя: </strong><br/>{{ $user->login }}</p>
<p><strong>Почтовый адрес: </strong><br/>{{ $user->email }}</p>
<p><strong>Роли: </strong><br/>
    @foreach($user->roles as $role)
        {{ $role->name }},
    @endforeach
</p>
</body>
</html>