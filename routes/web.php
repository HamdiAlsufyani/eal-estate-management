<?php

use App\Http\Controllers\Admin\AmenityController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DistrictController;
use App\Http\Controllers\Admin\PropertyController;
use App\Http\Controllers\Admin\PropertyTypeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Owner\DashboardController as OwnerDashboardController;
use App\Http\Controllers\Owner\InquiryController as OwnerInquiryController;
use App\Http\Controllers\Owner\PropertyController as OwnerPropertyController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified', 'approved'])
    ->name('dashboard');

Route::middleware(['auth', 'approved'])->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});

Route::middleware(['auth', 'approved'])->prefix('admin')->name('admin.')->group(function () {

    Route::post('users/bulk', [UserController::class, 'bulk'])->name('users.bulk');

    Route::patch('users/{user}/approve', [UserController::class, 'approve'])->name('users.approve');
    Route::patch('users/{user}/reject', [UserController::class, 'reject'])->name('users.reject');
    Route::patch('users/{user}/suspend', [UserController::class, 'suspend'])->name('users.suspend');
    Route::patch('users/{user}/activate', [UserController::class, 'activate'])->name('users.activate');

    Route::resource('users', UserController::class);

    Route::resource('property-types', PropertyTypeController::class);

    Route::resource('cities', CityController::class);

    Route::resource('districts', DistrictController::class);

    Route::resource('amenities', AmenityController::class);

    Route::get('cities/{city}/districts', [DistrictController::class, 'byCity'])->name('cities.districts');

    Route::post('properties/{property}/status', [PropertyController::class, 'changeStatus'])->name('properties.status');
    Route::post('properties/{property}/availability', [PropertyController::class, 'changeAvailability'])->name('properties.availability');
    Route::post('properties/{property}/images/reorder', [PropertyController::class, 'reorderImages'])->name('properties.images.reorder');
    Route::delete('properties/{property}/images/{media}', [PropertyController::class, 'destroyImage'])->name('properties.images.destroy');

    Route::resource('properties', PropertyController::class);

});

Route::middleware(['auth', 'approved'])->prefix('owner')->name('owner.')->group(function () {

    Route::get('dashboard', OwnerDashboardController::class)->name('dashboard');

    Route::get('inquiries', [OwnerInquiryController::class, 'index'])->name('inquiries.index');

    Route::post('properties/{property}/images/reorder', [OwnerPropertyController::class, 'reorderImages'])->name('properties.images.reorder');
    Route::delete('properties/{property}/images/{media}', [OwnerPropertyController::class, 'destroyImage'])->name('properties.images.destroy');

    Route::resource('properties', OwnerPropertyController::class);

});

require __DIR__.'/auth.php';