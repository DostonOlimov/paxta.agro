<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('final_conclusion_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dalolatnoma_id');
            $table->string('invoice_number')->nullable();
            $table->string('vehicle_number')->nullable();
            $table->text('conclusion_part_1')->nullable();
            $table->text('conclusion_part_2')->nullable();
            $table->text('conclusion_part_3')->nullable();
            $table->tinyInteger('type')->default(1); // 1 - muvofiq, 2 - nomuvofiq
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
        Schema::dropIfExists('final_conclusion_results');
    }
};
