@php
    $title=isset($title)?$title:'Halaman Print';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="{{asset('img/logo-removebg.png')}}">
    {!!style('bootstrap.min.css','/vendor/css','angular_resource')!!}
    {!!style('print.css','/css-laravel','public')!!}
    <title>{{$title}}</title>
    <script>
        window.onload=function(){
            window.print();
        }
    </script>
</head>
<body>
    @yield('content')
</body>
</html>
