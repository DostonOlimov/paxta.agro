<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Indexes for the state/factory report (vue/state-report).
     * Without sifat_sertificates.app_id the join is a full scan per application.
     */
    public function up()
    {
        Schema::table('sifat_sertificates', function (Blueprint $table) {
            $table->index('app_id');
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->index(['app_type', 'prepared_id']);
        });
    }

    public function down()
    {
        Schema::table('sifat_sertificates', function (Blueprint $table) {
            $table->dropIndex(['app_id']);
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->dropIndex(['app_type', 'prepared_id']);
        });
    }
};
