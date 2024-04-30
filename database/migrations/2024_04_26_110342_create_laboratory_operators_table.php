<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaboratoryOperatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('laboratory_operators', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->bigInteger('laboratory_id');
            $table->integer('status')->default(null); 
            $table->timestamps();

            // $table->foreign('laboratory_id')->references('id')->on('laboratories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('laboratory_operators');
    }
}
