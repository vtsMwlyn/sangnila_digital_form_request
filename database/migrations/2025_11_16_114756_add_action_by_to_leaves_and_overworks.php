<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            if (!Schema::hasColumn('leaves', 'action_by')) {
                $table->string('action_by')->nullable()->after('request_status');
            }
        });

        Schema::table('overworks', function (Blueprint $table) {
            if (!Schema::hasColumn('overworks', 'action_by')) {
                $table->string('action_by')->nullable()->after('request_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            if (Schema::hasColumn('leaves', 'action_by')) {
                $table->dropColumn('action_by');
            }
        });

        Schema::table('overworks', function (Blueprint $table) {
            if (Schema::hasColumn('overworks', 'action_by')) {
                $table->dropColumn('action_by');
            }
        });
    }
};