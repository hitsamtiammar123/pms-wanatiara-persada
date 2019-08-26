<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\KPIEndorsement;
use App\Notifications\EndorsementNotification;

class KPIEndorsementController extends Controller
{


    protected function fireEndorsementEvent($endorse){
        $auth_user=auth_user();
        $header=$endorse->kpiheader;
        $employee=$auth_user->employee;
        $userToSend=$endorse->employee->atasan->user;

        $userToSend->notify(new EndorsementNotification($header,$employee));

    }


    public function store(Request $request)
    {
        //
    }


    public function show($id)
    {
        //
    }



    public function update(Request $request, $id)
    {
        //
        $endorse=KPIEndorsement::find($id);
        if($endorse){
            $endorse->verified=$request->verified;
            $endorse->save();
            $this->fireEndorsementEvent($endorse);

            return [
                'status'=>1,
                'message'=>'Sudah disahkan',
                'user'=>$endorse->kpiheader->employee
            ];
        }
        return send_404_error('Data tidak ditemukan');
    }

}
