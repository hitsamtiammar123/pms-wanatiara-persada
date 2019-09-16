<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKpiResultDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kpiresultsheader', function (Blueprint $table) {
            $table->string('id',18);
            $table->string('kpi_result_id',18);
            $table->decimal('pw',15,2)->nullable();
            $table->decimal('pt_t',15,2)->nullable();
            $table->decimal('pt_k',15,2)->nullable();
            $table->decimal('real_t',15,2)->nullable();
            $table->decimal('real_k',15,2)->nullable();
            $table->primary('id');
            $table->string('kpi_header_id',18);
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
        Schema::dropIfExists('kpiresultsheader');
    }
}
