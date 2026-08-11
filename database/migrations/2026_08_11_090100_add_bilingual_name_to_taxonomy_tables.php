<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = ['property_types', 'cities', 'districts', 'amenities'];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->string('name_en')->nullable()->after('name');
                $blueprint->string('name_ar')->nullable()->after('name_en');
            });

            DB::table($table)->update(['name_en' => DB::raw('name')]);
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->dropColumn(['name_en', 'name_ar']);
            });
        }
    }
};
