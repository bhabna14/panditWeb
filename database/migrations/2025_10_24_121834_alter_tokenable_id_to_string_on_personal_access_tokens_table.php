<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // MySQL syntax: adjust if needed for your DB
        DB::statement('ALTER TABLE `personal_access_tokens` DROP INDEX `personal_access_tokens_tokenable_type_tokenable_id_index`');
        DB::statement('ALTER TABLE `personal_access_tokens` MODIFY `tokenable_id` VARCHAR(255) NOT NULL');
        DB::statement('CREATE INDEX `personal_access_tokens_tokenable_type_tokenable_id_index` ON `personal_access_tokens` (`tokenable_type`, `tokenable_id`)');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX `personal_access_tokens_tokenable_type_tokenable_id_index` ON `personal_access_tokens`');
        DB::statement('ALTER TABLE `personal_access_tokens` MODIFY `tokenable_id` BIGINT UNSIGNED NOT NULL');
        DB::statement('CREATE INDEX `personal_access_tokens_tokenable_type_tokenable_id_index` ON `personal_access_tokens` (`tokenable_type`, `tokenable_id`)');
    }
};
