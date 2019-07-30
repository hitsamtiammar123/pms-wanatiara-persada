<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKPIResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kpiresults', function (Blueprint $table) {
            $table->string('id',18);
            $table->string('name');
            $table->string('kpi_header_id',18);
            $table->string('unit',15)->default('$');
            $table->decimal('pw_1',10,2)->nullable();
            $table->decimal('pw_2',10,2)->nullable();
            $table->decimal('pt_t1',10,2)->nullable();
            $table->decimal('pt_k1',10,2)->nullable();
            $table->decimal('pt_t2',10,2)->nullable();
            $table->decimal('pt_k2',10,2)->nullable();
            $table->decimal('real_t1',10,2)->nullable();
            $table->decimal('real_k1',10,2)->nullable();
            $table->decimal('real_t2',10,2)->nullable();
            $table->decimal('real_k2',10,2)->nullable();
            $table->primary('id');
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
        Schema::dropIfExists('kpiresults');
    }
}
