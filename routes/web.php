<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\View\PropertyController as PropertyViewController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home page
Route::get('/', function () {
    return view('welcome');
});

// Admin dashboard routes (Properties Views)
// Protected by auth + role:admin
Route::middleware(['auth', 'checkRole:admin'])
    ->prefix('dashboard/admin')
    ->name('admin.')
    ->group(function () {

        // CRUD Views for Properties
        // index, create, store, show, edit, update, destroy
        Route::resource('properties', PropertyViewController::class);

        // Property types management page
        Route::get('properties/types', [PropertyViewController::class, 'types'])
            ->name('properties.types');
    });
