<?php

namespace App\Model\Traits;

trait ErrorMessages{
    protected function sendNotFound($id){
        return send_404_error('Notifikasi dengan id '.$id.' tidak ditemukan');
    }

    protected function sendUserNotFound($employeeID){
        return send_404_error('Pengguna dengan id '.$employeeID.' tidak ditemukan');
    }

    protected function sendAuthUserNotFound(){
        return send_404_error('Data Pengguna yang ter-autentikasi tidak ditemukan');
    }
}