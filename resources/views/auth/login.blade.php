@extends('layouts.pms',['head'=>'include.head-landing'])

@section('content')
    <div class="row">
        <div class="col-sm-3"></div>
        <div class="col-sm-6">
            <form action="{{ route('login') }}" method="POST" class="form-horizontal">
                    @csrf
                    <div class="form-group">
                        <label for="id">Email. 电子邮件	: </label>
                        <input class="form-control" id="email" type="text" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Kata Sandi 密码	:  </label>
                        <input class="form-control" id="password" type="password" name="password" required>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-default">Masuk</button>
                        <a href="{{route('password.request')}}">Lupa Password</a>
                        @if ($errors->any())
                        <style>
                            .error-p{
                                color:red;
                            }
                        </style>
                            <p>
                                @foreach ($errors->all() as $error)
                                    <div class="error-p">{{$error}}</div>
                                @endforeach

                            </p>
                        @endif
                    </div>
            </form>

        </div>
    </div>
@endsection
