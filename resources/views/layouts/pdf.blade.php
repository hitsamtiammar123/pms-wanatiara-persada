<!DOCTYPE html>
<html lang="en">
<head>>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" href="{{res_url('vendor/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{public_path('css-laravel/pdf.css')}}">
    <title>{{isset($title)?$title:'PDF PMS'}}</title>
</head>
<body>
    @yield('content')
</body>
</html>
