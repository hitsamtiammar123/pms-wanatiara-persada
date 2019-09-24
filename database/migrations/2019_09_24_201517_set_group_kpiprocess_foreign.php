<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetGroupKpiprocessForeign extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('groupkpiprocess', function (Blueprint $table) {
            $table->foreign('kpi_process_id')->references('id')->on('kpiprocesses')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('groupkpiprocess', function (Blueprint $table) {
            $table->dropForeign('groupkpiprocess_kpi_process_id_foreign');
            $table->dropForeign('groupkpiprocess_role_id_foreign');
        });
    }
}
