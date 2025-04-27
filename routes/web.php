<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HomeController; // Make sure HomeController is imported if used

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Publicly accessible route (optional, often points to a welcome page)
Route::get('/', function () {
    // You might want to redirect logged-in users or show a welcome page
    if (auth()->check()) {
        return redirect()->route('events.index');
    }
    // If using Breeze, it usually provides a welcome view
    // Or redirect to login
     return redirect()->route('login');
   // return view('welcome');
});

// Routes requiring authentication
Route::middleware(['auth'])->group(function () {
    // Dashboard or Home Route (Breeze might set up its own dashboard route)
    // The HomeController logic will redirect based on role anyway
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Event Management Routes
    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/events/create', [EventController::class, 'create'])->name('events.create')->middleware('admin'); // Protect with admin middleware
    Route::post('/events', [EventController::class, 'store'])->name('events.store')->middleware('admin');
    Route::get('/events/code', [EventController::class, 'create'])->name('events.code')->middleware('user');  // Protect with admin middleware
    Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
    Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('events.edit')->middleware('admin'); // Protect with admin middleware
    Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update')->middleware('admin'); // Protect with admin middleware
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy')->middleware('admin'); // Protect with admin middleware

    // QR Code and Attendance Routes (Admin generates, any logged-in user can record)
    Route::get('/events/{event}/qrcode', [EventController::class, 'generateQrCode'])->name('events.qrcode')->middleware('admin'); // Only admin generates QR
    Route::get('/events/{event}/attendance/record', [EventController::class, 'recordAttendance'])->name('events.attendance.record'); // Logged-in users record attendance
    Route::get('/events/{event}/attendance', [EventController::class, 'showAttendance'])->name('events.attendance')->middleware('admin'); // Only admin views full attendance list
    Route::get('/events/{event}/qrcode/download', [EventController::class, 'downloadQrCode'])
    ->name('events.qrcode.download')
    ->middleware(['auth', 'admin']); // Check middleware placement
    // Add other authenticated routes here (e.g., profile management if added by Breeze)
    Route::get('/home', [HomeController::class, 'index'])->name('home');
});

    // *** ADDED: Route for updating QR Code settings (Admin only) ***
    Route::put('/events/{event}/update-qr-settings', [EventController::class, 'updateQrCodeSettings'])
        ->name('events.updateQrSettings') // This is the route name needed
        ->middleware('admin'); // Ensure only admins can access

// Include authentication routes provided by Laravel Breeze
// In routes/web.php
// Example from Breeze routes/auth.php


// Example:
Route::get('/dashboard', /* ... controller or function ... */)->middleware(['auth', 'verified'])->name('dashboard');

// Example from Breeze routes/auth.php
use App\Http\Controllers\Auth\AuthenticatedSessionController; // Make sure the controller is imported

// ... other routes ...
// Inside routes/auth.php
Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');


Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
            ->middleware('auth') // Usually requires the user to be authenticated
            ->name('logout');   // <-- This names the route 'logout'

            // In routes/web.php
require __DIR__.'/auth.php';

// INCORRECT EXAMPLE - Missing Action:
// CORRECT EXAMPLE - With Action (Controller):
use App\Http\Controllers\DashboardController; // Import the controller

Route::get('/dashboard', [DashboardController::class, 'index']) // Tells Laravel to call the 'index' method on DashboardController
    ->middleware(['auth', 'verified'])
    ->name('dashboard');