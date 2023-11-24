<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        Schema::create('employees', function (Blueprint $table) {
//            $table->id();
//            $table->integer('user_id')->default(0);
//            $table->string('name')->nullable();
//            $table->string('lastname',30)->nullable();
//            $table->string('display_name',30)->nullable();
//            $table->tinyInteger('gender')->nullable();
//            $table->date('birth_date')->nullable();
//            $table->string('email')->nullable();
//            $table->string('contact_person')->nullable();
//            $table->string('password')->nullable();
//            $table->string('mobile_no')->nullable();
//            $table->string('landline_no')->nullable();
//            $table->text('address')->nullable();
//            $table->string('image')->nullable();
//            $table->date('join_date')->nullable();
//            $table->string('designation',30)->nullable();
//            $table->date('left_date')->nullable();
//            $table->string('account_no')->nullable();
//            $table->string('ifs_code')->nullable();
//            $table->string('branch_name')->nullable();
//            $table->string('tin_no')->nullable();
//            $table->string('pan_no')->nullable();
//            $table->string('gst_no')->nullable();
//            $table->integer('country_id')->nullable();
//            $table->string('state_id')->nullable();
//            $table->string('city_id')->nullable();
//            $table->string('role',30)->nullable();
//            $table->string('language')->nullable();
//            $table->string('timezone')->nullable();
//            $table->string('custom_field')->nullable();
//            $table->string('remember_token',100)->nullable();
//            $table->tinyInteger('type_id')->nullable();
//            $table->bigInteger('external_id')->nullable()->unsigned();
//            $table->string('api_token', 80)->unique()->nullable();
//            $table->timestamps();
//        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employees');
    }
}
