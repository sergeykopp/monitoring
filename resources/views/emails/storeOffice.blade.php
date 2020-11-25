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
    <p><strong>Город: </strong><br/>{{ $office->city->name }}</p>
    <p><strong>Наименование: </strong><br/>{{ $office->name }}</p>
    <p><strong>Адрес: </strong><br/>{{ $office->address }}</p>
    <p><strong>Заметки: </strong><br/>{!! $office->notes !!}</p>
</body>
</html>