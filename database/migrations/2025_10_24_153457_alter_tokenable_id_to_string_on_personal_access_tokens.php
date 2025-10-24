<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the composite index so we can change the column type
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            // Drop by columns (works even if the index name differs across versions)
            $table->dropIndex(['tokenable_type', 'tokenable_id']);
        });

        // Change tokenable_id to VARCHAR (no doctrine/dbal needed with raw SQL)
        DB::statement('ALTER TABLE personal_access_tokens MODIFY tokenable_id VARCHAR(64) NOT NULL');

        // Re-create the composite index
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->index(['tokenable_type', 'tokenable_id']);
        });
    }

    public function down(): void
    {
        // Roll back to BIGINT UNSIGNED if you ever need to
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->dropIndex(['tokenable_type', 'tokenable_id']);
        });

        DB::statement('ALTER TABLE personal_access_tokens MODIFY tokenable_id BIGINT UNSIGNED NOT NULL');

        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->index(['tokenable_type', 'tokenable_id']);
        });
    }
};
