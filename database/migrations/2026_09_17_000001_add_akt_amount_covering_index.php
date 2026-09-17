<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Covering index for organization-company-report: bale count and mass sums
     * are read from the index instead of the 2M+ row table.
     */
    public function up()
    {
        Schema::table('akt_amount', function (Blueprint $table) {
            $table->index(['dalolatnoma_id', 'amount']);
        });
    }

    public function down()
    {
        Schema::table('akt_amount', function (Blueprint $table) {
            $table->dropIndex(['dalolatnoma_id', 'amount']);
        });
    }
};
