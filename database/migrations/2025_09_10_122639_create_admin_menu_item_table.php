<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('admin_menu_item', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id');
            $table->unsignedBigInteger('menu_item_id');
            $table->timestamps();

            $table->unique(['admin_id','menu_item_id']);

            $table->foreign('admin_id')
                  ->references('id')->on('admins')
                  ->cascadeOnDelete();

            $table->foreign('menu_item_id')
                  ->references('id')->on('menu_items')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_menu_item');
    }
};
