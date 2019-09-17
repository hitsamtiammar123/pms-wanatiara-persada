<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetForeignPriviledgeKpiResult extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('priviledgedetail', function (Blueprint $table) {
            //
            $table->foreign('h_id')->references('id')
            ->on('kpiresultsheader')->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('p_id')->references('id')
            ->on('priviledgekpiresults')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('priviledgedetail', function (Blueprint $table) {
            //
            $table->dropForeign('priviledgedetail_h_id_foreign');
            $table->dropForeign('priviledgedetail_p_id_foreign');
        });
    }
}
