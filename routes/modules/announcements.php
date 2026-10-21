<?php

use App\Http\Controllers\Announcements\AnnouncementController;
use Illuminate\Support\Facades\Route;

Route::prefix('announcements')->name('announcements.')->controller(AnnouncementController::class)->group(function (): void {
    Route::get('/', 'index')->name('index');
    Route::get('/datatable', 'datatable')->name('datatable');
    Route::get('/export', 'export')->name('export');
    Route::get('/pdf', 'pdf')->name('pdf');
    Route::get('/create', 'create')->name('create');
    Route::post('/', 'store')->name('store');
    Route::get('/{announcement}', 'show')->name('show');
    Route::get('/{announcement}/edit', 'edit')->name('edit');
    Route::put('/{announcement}', 'update')->name('update');
    Route::delete('/{announcement}', 'destroy')->name('destroy');
});
