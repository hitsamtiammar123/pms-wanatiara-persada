<!DOCTYPE html>
<html lang="en">
<head>
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('include.head-app')

<link rel="stylesheet" href="css/style.css">
<script src="{{route('js.app')}}"></script>
</head>
<body>
  <div id="app"></div>
</body>
</html>
