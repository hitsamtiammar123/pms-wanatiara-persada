<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    {!!style('bootstrap.min.css','/vendor/css','angular_resource')!!}
    {!!style('print.css','/css-laravel','public')!!}
    <title>Halaman Print</title>
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
