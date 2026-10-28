<?php

use App\Http\Controllers\Admin\CostController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomScenarioController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\PracticeSessionController;
use App\Http\Controllers\ScenarioController;
use App\Http\Controllers\SlicePracticeController;
use App\Http\Controllers\StickyNoteController;
use App\Http\Controllers\StorageController;
use App\Http\Controllers\TurnController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - AI-Powered Real-World English Communication Simulator
|--------------------------------------------------------------------------
*/

// Public Landing Page (Giới thiệu, Warm Humanist, fade-in animation)
Route::get('/', [LandingPageController::class, 'index'])->name('landing');

// Personalized Sticky Notes with Spaced Repetition (/notes)
Route::get('/notes', [StickyNoteController::class, 'index'])->name('notes.index');
Route::middleware(['auth'])->group(function () {
    Route::post('/notes', [StickyNoteController::class, 'store'])->name('notes.store');
    Route::post('/notes/{note}/review', [StickyNoteController::class, 'review'])->name('notes.review');
    Route::delete('/notes/{note}', [StickyNoteController::class, 'destroy'])->name('notes.destroy');
    Route::post('/notes/reminder-settings', [StickyNoteController::class, 'updateReminder'])->name('notes.reminder');
});

// Custom Stories (Dedicated Page, AI Generator, Store Scenario)
Route::middleware(['auth'])->group(function () {
    Route::get('/custom-stories/create', [CustomScenarioController::class, 'create'])->name('custom-stories.create');
    Route::post('/custom-stories', [CustomScenarioController::class, 'store'])->name('custom-stories.store');
    Route::post('/custom-stories/ai-generate', [CustomScenarioController::class, 'generateAi'])->name('custom-stories.generate-ai');
});

// Screen 1: App Workspace - Luyện nói (Catalog & Scenario Discovery)
Route::get('/practice', [ScenarioController::class, 'index'])->name('scenarios.index');

// Hover Peek Flyout AJAX Endpoint
Route::get('/scenarios/{idOrSlug}/peek', [ScenarioController::class, 'peek'])
    ->name('scenarios.peek');
Route::get('/api/scenarios/{idOrSlug}/peek', [ScenarioController::class, 'peek']);

// Screen 2: Pre-Flight Lobby (Scenario Details, Persona Selection & Mic Preflight)
Route::get('/scenarios/{idOrSlug}', [ScenarioController::class, 'show'])->name('scenarios.show');

// Favorites: Toggle Pin / Favorite Scenario
Route::post('/scenarios/{scenarioId}/favorite', [FavoriteController::class, 'toggle'])
    ->whereNumber('scenarioId')
    ->name('favorites.toggle');

// Practice Session Lifecycle
Route::post('/sessions', [PracticeSessionController::class, 'store'])->name('practice.store');

// Screen 3: Video Call Simulator Room
Route::get('/sessions/{sessionCode}', [PracticeSessionController::class, 'show'])->name('practice.room');

// Core Turn Processing (Speech -> AI Dual-Channel -> State Engine)
Route::post('/sessions/{sessionCode}/turn', [TurnController::class, 'processTurn'])->name('practice.turn');

// Screen 4: Summary & CSI Turn Inspector
Route::get('/sessions/{sessionCode}/summary', [PracticeSessionController::class, 'summary'])->name('practice.summary');

// Screen 5: Practice Session History & Aggregate Performance
Route::get('/history', [HistoryController::class, 'index'])->name('history.index');

// Hero Action: Re-practice Weakest Slice (3-Step Micro Loop)
Route::post('/sessions/{sessionCode}/slice-retry', [SlicePracticeController::class, 'retry'])->name('practice.slice-retry');

// Storage & Audio Upload (Cloudflare R2 Direct Upload & Local Fallback)
Route::post('/api/storage/presigned-url', [StorageController::class, 'getPresignedUploadUrl'])->name('storage.presigned');
Route::match(['PUT', 'POST'], '/api/storage/local-upload', [StorageController::class, 'handleLocalUpload'])->name('storage.local-upload');

// User Authentication (Register, Login, Logout)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Area - Centralized API Cost Management
Route::prefix('admin')->middleware(['web', 'auth', 'admin'])->group(function () {
    Route::get('/costs', [CostController::class, 'index'])->name('admin.costs');
});
