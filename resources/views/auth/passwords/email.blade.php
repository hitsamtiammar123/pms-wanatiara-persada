@extends('layouts.pms',['head'=>'include.head-landing'])

@section('content')
    <div class="row">
        <div class="col-sm-3"></div>
        <div class="col-sm-6">
            <form action="{{ route('password.email') }}" method="POST" class="form-horizontal">
                    @csrf
                    <div class="form-group">
                        <label for="id">Silakan masukan email yang terdaftar	: </label>
                        <input class="form-control" id="email" type="text" name="email" required autocomplete="email" autofocus>
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
                        @if (session('status'))
                        <div class="alert alert-success success-p" role="alert">
                            {{ session('status') }}
                        </div>
                        @endif
                    </div>
            </form>

        </div>
    </div>
@endsection



