<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->string('title_en')->nullable()->after('title');
            $table->string('title_ar')->nullable()->after('title_en');
            $table->longText('description_en')->nullable()->after('description');
            $table->longText('description_ar')->nullable()->after('description_en');
            $table->string('address_en')->nullable()->after('address');
            $table->string('address_ar')->nullable()->after('address_en');
        });

        DB::table('properties')->update([
            'title_en' => DB::raw('title'),
            'description_en' => DB::raw('description'),
            'address_en' => DB::raw('address'),
        ]);
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn(['title_en', 'title_ar', 'description_en', 'description_ar', 'address_en', 'address_ar']);
        });
    }
};
