<!DOCTYPE html>
<html lang="en">
<head>
    @include($head)
    <link rel="stylesheet" href="css-laravel/index.css">
</head>
<body>
    <div class="container">
        <header class="row">
                <div class="page-header">
                        <div class="col-sm-1">
                            <a href="{{route('index')}}">
                                <img src="img/logo-removebg.png" class="head-img">
                        </a></div>
                        <div><h1 class="head-title bold"> PT Wanatiara Persada</h1></div>
                    </div>
        </header>

        @yield('content')

    </div>
</body>
</html>
