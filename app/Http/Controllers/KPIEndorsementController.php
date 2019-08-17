<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\KPIEndorsement;

class KPIEndorsementController extends Controller
{



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $endorse=KPIEndorsement::find($id);
        if($endorse){
            $endorse->verified=$request->verified;
            $endorse->save();
            return [
                'status'=>1,
                'message'=>'Sudah disahkan',
                'user'=>$endorse->kpiheader->employee
            ];
        }
        return send_404_error('Data tidak ditemukan');
    }

}
