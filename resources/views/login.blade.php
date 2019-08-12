@extends('layouts.pms',['head'=>'include.head-landing'])
@section('content')
    <div class="row">
        <div class="col-sm-3"></div>
        <div class="col-sm-6">
            <form action="" class="form-horizontal">
                    <div class="form-group">
                        <label for="id">ID. 用户识别	: </label>
                        <input class="form-control" id="id" type="text" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Kata Sandi 密码	:  </label>
                        <input class="form-control" id="password" type="password" required>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-default">Masuk</button>
                    </div>
            </form>
            <div class="form-group">

            </div>
        </div>
    </div>
@endsection
