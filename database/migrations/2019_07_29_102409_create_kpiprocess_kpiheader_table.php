<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKpiprocessKpiheaderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kpiprocesses_kpiheaders', function (Blueprint $table) {
            $table->string('kpi_proccess_id',18);
            $table->string('kpi_header_id',18);
            $table->primary(['kpi_proccess_id','kpi_header_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kpiprocesses_kpiheaders');
    }
}
