<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChatApiController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ChatLockController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\LogController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Redirect dashboard to chats (no dashboard page)
Route::get('/dashboard', function () {
    return redirect()->route('chats.index');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| WhatsApp Demo Routes
|--------------------------------------------------------------------------
| Protected by auth + verified (same as dashboard)
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // WhatsApp UI
    Route::get('/chats', [ChatController::class, 'index'])->name('chats.index');
    Route::get('/chats/list', [ChatController::class, 'list'])->name('chats.list');
    Route::get('/chats/{contact}', [ChatController::class, 'show'])->name('chats.show');

    // Contacts
    Route::post('/contacts', [ContactController::class, 'store'])->name('contacts.store');
    Route::put('/contacts/{contact}', [ContactController::class, 'update'])->name('contacts.update');
    Route::post('/contacts/sync-recent', [ContactController::class, 'syncRecent'])->name('contacts.syncRecent');

    // JSON endpoints (polling + send + sync)
    Route::get('/chats/{contact}/messages', [ChatApiController::class, 'messages'])->name('chats.messages');
    Route::post('/chats/{contact}/send', [ChatApiController::class, 'send'])->name('chats.send');
    Route::post('/chats/{contact}/sync', [ChatApiController::class, 'sync'])->name('chats.sync');
    Route::post('/chats/{contact}/read', [ChatApiController::class, 'markRead'])->name('chats.read');
    Route::post('/chats/sync-all', [ChatApiController::class, 'syncAll'])->name('chats.syncAll');

    // Chat locking (multi-admin safety)
    Route::get('/chats/{contact}/lock', [ChatLockController::class, 'status'])->name('chats.lock.status');
    Route::post('/chats/{contact}/lock/acquire', [ChatLockController::class, 'acquire'])->name('chats.lock.acquire');
    Route::post('/chats/{contact}/lock/release', [ChatLockController::class, 'release'])->name('chats.lock.release');

    // Logs page
    Route::get('/logs', [LogController::class, 'index'])->name('logs.index');
});

/*
|--------------------------------------------------------------------------
| Breeze Profile Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
