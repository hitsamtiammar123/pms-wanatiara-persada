<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
@foreach ($css_vendors as $css)
<link rel="stylesheet" href="{{$css}}">
@endforeach
<link rel="icon" href="img/logo-removebg.png">
@foreach ($js_vendors as $vendor )
<script src="{{$vendor}}"></script>
@endforeach
<title>PT Wanatiara Persada</title>
