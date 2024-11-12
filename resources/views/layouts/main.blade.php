<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="{{asset('css/bootstrap.css')}}">
    <style>
        .links {
        display: flex;
        gap: 10px;
    }
    </style>
</head>
<body>
    <div class="m-3">
    @yield('content')
    </div>
</body>
</html>