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
        Schema::table('property_views', function (Blueprint $table) {
            $table->index(['user_id', 'property_id']);
            $table->index('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('property_views', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'property_id']);
            $table->dropIndex(['updated_at']);
        });
    }
};
