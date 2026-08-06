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
      Schema::create('properties', function (Blueprint $table) {
    $table->id();

    $table->foreignId('user_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->foreignId('property_type_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->foreignId('city_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->foreignId('district_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->string('title');
    $table->string('slug')->unique();

    $table->longText('description');

    $table->enum('purpose', ['sale', 'rent']);

    $table->enum('rent_period', [
        'monthly',
        'yearly'
    ])->nullable();

    $table->decimal('price', 12, 2);

    $table->decimal('area', 10, 2);

    $table->unsignedTinyInteger('bedrooms')->default(0);

    $table->unsignedTinyInteger('bathrooms')->default(0);

    $table->unsignedTinyInteger('living_rooms')->default(0);

    $table->unsignedTinyInteger('kitchens')->default(0);

    $table->unsignedTinyInteger('floor')->nullable();

    $table->unsignedTinyInteger('parking_spaces')->default(0);

    $table->boolean('furnished')->default(false);

    $table->string('address');

    $table->decimal('latitude', 10, 8)->nullable();

    $table->decimal('longitude', 11, 8)->nullable();

    $table->enum('status', [
        'pending',
        'approved',
        'rejected'
    ])->default('pending');

    $table->enum('availability', [
        'available',
        'reserved',
        'sold',
        'rented'
    ])->default('available');

    $table->boolean('featured')->default(false);

    $table->timestamp('published_at')->nullable();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
