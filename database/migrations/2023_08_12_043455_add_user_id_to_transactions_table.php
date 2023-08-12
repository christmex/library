<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // $table->dropForeign('user_id');
            $table->dropForeign(['user_id']);
            // $table->dropForeign('user_id');
            $table->dropColumn('user_id');
            // $table->dropForeign('transactions_user_id_foreign');
            // $table->dropIndex('transactions_user_id_index');
            // $table->dropColumn('user_id');
        });
    }
};
