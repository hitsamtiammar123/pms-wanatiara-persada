<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
@foreach ($css_list as $css)
<link rel="stylesheet" href="{{$css}}">
@endforeach
<link rel="icon" href="{{env('APP_RES')}}/img/logo.png">
@foreach ($js_vendors as $vendor )
<script src="{{$vendor}}"></script>
@endforeach
<title>PT Wanatiara Persada</title>
