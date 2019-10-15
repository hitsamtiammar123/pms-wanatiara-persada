@extends('layouts.pms',['head'=>'include.head-landing'])

@section('content')
    <div class="row">
        <div class="col-sm-3"></div>
        <div class="col-sm-6">
            <form action="{{ route('password.update') }}" method="POST" class="form-horizontal">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <div class="form-group">
                        <label for="email">Alamat Email	: </label>
                        <input class="form-control" id="email" type="text" value="{{ $email ?? old('email') }}" name="email" required autocomplete="email" autofocus>
                    </div>
                    <div class="form-group">
                        <label for="password">Silakan masukan Kata Sandi Anda: </label>
                        <input id="password" type="password" class="form-control" name="password" required autocomplete="new-password">
                    </div>
                    <div class="form-group">
                        <label for="password-confirm">Silakan masukan kembali kata sandi anda: </label>
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                    </div>
                    <style>
                        .error-p{
                            color:red;
                        }
                        .success-p{
                            margin-top:15px;
                        }
                    </style>

                    <div class="form-group">
                        <button type="submit" class="btn btn-default">Kirim</button>
                        @if ($errors->any())

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
