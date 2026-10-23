<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $table = config('activitylog.table_name', 'activity_log');

        Schema::table($table, function (Blueprint $table_) use ($table) {
            if (! Schema::hasColumn($table, 'event')) {
                $table_->string('event')->nullable()->after('subject_type');
            }
            if (! Schema::hasColumn($table, 'batch_uuid')) {
                $table_->uuid('batch_uuid')->nullable()->after('properties');
            }
        });
    }

    public function down(): void
    {
        $table = config('activitylog.table_name', 'activity_log');

        Schema::table($table, function (Blueprint $table_) use ($table) {
            foreach (['event', 'batch_uuid'] as $column) {
                if (Schema::hasColumn($table, $column)) {
                    $table_->dropColumn($column);
                }
            }
        });
    }
};
