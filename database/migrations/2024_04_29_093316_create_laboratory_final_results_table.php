<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaboratoryFinalResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('laboratory_final_results', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('dalolatnoma_id');
            $table->bigInteger('operator_id');
            $table->bigInteger('klassiyor_id');
            $table->bigInteger('director_id')->nullable();
            $table->bigInteger('number');
            $table->date('date')->nullable();
            $table->string('from')->nullable();
            $table->string('vakili')->nullable();
            $table->string('vakil_name')->nullable();
            $table->float('namlik')->nullable();
            $table->float('harorat')->nullable();
            $table->float('yoruglik')->nullable();
            $table->integer('status')->default(0);
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
        Schema::dropIfExists('laboratory_final_results');
    }
}
