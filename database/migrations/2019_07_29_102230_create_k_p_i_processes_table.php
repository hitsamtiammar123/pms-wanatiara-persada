<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKPIProcessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kpiprocesses', function (Blueprint $table) {
            $table->string('id',18);
            $table->string('name');
            $table->string('unit',15)->default('规模 Skala');
            $table->decimal('pw_1',10,2)->nullable();
            $table->decimal('pw_2',10,2)->nullable();
            $table->integer('pt_1')->nullable();
            $table->integer('pt_2')->nullable();
            $table->integer('real_1')->nullable();
            $table->integer('real_2')->nullable();
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
        Schema::dropIfExists('kpiprocesses');
    }
}
