<?php

use App\Http\Controllers\Admin\AuditTrailController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function (): void {
    // Users are deactivated, never deleted, so the audit trail keeps who did what.
    Route::prefix('users')->name('users.')->controller(UserController::class)->group(function (): void {
        Route::get('/', 'index')->name('index');
        Route::get('/datatable', 'datatable')->name('datatable');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        // UserPolicy: the edit form can reset a password, so an account above yours stays closed.
        Route::get('/{user}/edit', 'edit')->name('edit')->can('update', 'user');
        Route::put('/{user}', 'update')->name('update')->can('update', 'user');
    });

    Route::prefix('roles')->name('roles.')->controller(RoleController::class)->group(function (): void {
        Route::get('/', 'index')->name('index');
        Route::get('/datatable', 'datatable')->name('datatable');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{role}/edit', 'edit')->name('edit');
        Route::put('/{role}', 'update')->name('update');
        Route::delete('/{role}', 'destroy')->name('destroy');
    });

    Route::prefix('audit-trail')->name('audit-trail.')->controller(AuditTrailController::class)->group(function (): void {
        Route::get('/', 'index')->name('index');
        Route::get('/datatable', 'datatable')->name('datatable');
        Route::get('/{activity}', 'show')->name('show');
    });
});
