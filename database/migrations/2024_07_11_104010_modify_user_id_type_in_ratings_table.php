<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyUserIdTypeInRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ratings', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign('ratings_user_id_foreign');

            // If 'user_id' is indexed, drop the index
            $table->dropIndex('ratings_user_id_foreign');

            // Change the column type to VARCHAR
            $table->string('user_id')->change();

            // Re-add the foreign key constraint
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ratings', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign('ratings_user_id_foreign');

            // Change the column type back to its original type
            // Replace 'original_type' with the actual original type, e.g., unsignedBigInteger
            $table->unsignedBigInteger('user_id')->change();

            // Re-add the foreign key constraint
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }
}
