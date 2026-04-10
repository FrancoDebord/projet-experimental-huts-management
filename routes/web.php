<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\HutController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\SleeperController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SettingsController;

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout',[AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Protected routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::get('/', fn() => redirect()->route('dashboard'));

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Sites ───────────────────────────────────────────────────────────────
    Route::resource('sites', SiteController::class);
    Route::post('/sites/{id}/force-delete', [SiteController::class, 'forceDestroy'])->name('sites.force-delete');
    Route::post('/sites/{id}/restore',      [SiteController::class, 'restore'])->name('sites.restore');

    // ── Huts ────────────────────────────────────────────────────────────────
    Route::resource('huts', HutController::class);
    Route::patch ('/huts/{hut}/status',            [HutController::class, 'updateStatus'])->name('huts.update-status');
    Route::post  ('/huts/{hut}/usages',            [HutController::class, 'addUsage'])->name('huts.add-usage');
    Route::delete('/huts/{hut}/usages/{usage}',    [HutController::class, 'removeUsage'])->name('huts.remove-usage');
    Route::post  ('/huts/{id}/force-delete',        [HutController::class, 'forceDestroy'])->name('huts.force-delete');
    Route::post  ('/huts/{id}/restore',             [HutController::class, 'restore'])->name('huts.restore');

    // ── Projects (read-only) ────────────────────────────────────────────────
    Route::get('/projects',         [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/{project}',[ProjectController::class, 'show'])->name('projects.show');

    // ── Assignment Wizard ───────────────────────────────────────────────────
    Route::get   ('/assignments/create',                    [AssignmentController::class, 'create'])->name('assignments.create');
    Route::post  ('/assignments',                           [AssignmentController::class, 'store'])->name('assignments.store');
    Route::get   ('/assignments/{assignment}',              [AssignmentController::class, 'show'])->name('assignments.show');
    Route::get   ('/assignments/{assignment}/edit',         [AssignmentController::class, 'edit'])->name('assignments.edit');
    Route::patch ('/assignments/{assignment}',              [AssignmentController::class, 'update'])->name('assignments.update');
    Route::patch ('/assignments/{assignment}/complete',     [AssignmentController::class, 'complete'])->name('assignments.complete');
    Route::delete('/assignments/{assignment}',              [AssignmentController::class, 'destroy'])->name('assignments.destroy');
    Route::post  ('/assignments/{id}/force-delete',         [AssignmentController::class, 'forceDestroy'])->name('assignments.force-delete');
    Route::post  ('/assignments/{id}/restore',              [AssignmentController::class, 'restore'])->name('assignments.restore');
    Route::post  ('/assignments/{assignment}/observations', [AssignmentController::class, 'addObservation'])->name('assignments.observations');
    Route::delete('/assignments/{assignment}/observations/{observation}', [AssignmentController::class, 'destroyObservation'])->name('assignments.observations.destroy');
    Route::post  ('/assignments/{assignment}/sleepers',     [AssignmentController::class, 'updateSleepers'])->name('assignments.sleepers');

    // ── Sleepers ────────────────────────────────────────────────────────────
    Route::resource('sleepers', SleeperController::class)->except(['show']);
    Route::post('/sleepers/{id}/force-delete', [SleeperController::class, 'forceDestroy'])->name('sleepers.force-delete');
    Route::post('/sleepers/{id}/restore',      [SleeperController::class, 'restore'])->name('sleepers.restore');

    // ── Incidents ───────────────────────────────────────────────────────────
    Route::resource('incidents', IncidentController::class);
    Route::post('/incidents/{id}/force-delete', [IncidentController::class, 'forceDestroy'])->name('incidents.force-delete');
    Route::post('/incidents/{id}/restore',      [IncidentController::class, 'restore'])->name('incidents.restore');

    // ── Notifications ────────────────────────────────────────────────────────
    Route::get ('/notifications',               [NotificationController::class, 'index'])->name('notifications.index');
    Route::get ('/notifications/unread-count',  [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::get ('/notifications/latest',        [NotificationController::class, 'latest'])->name('notifications.latest');
    Route::post('/notifications/{id}/read',     [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all',      [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::post('/notifications/push-subscribe',  [NotificationController::class, 'pushSubscribe'])->name('notifications.push-subscribe');
    Route::post('/notifications/push-unsubscribe',[NotificationController::class, 'pushUnsubscribe'])->name('notifications.push-unsubscribe');

    // ── Settings ─────────────────────────────────────────────────────────────
    Route::get ('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // ── Maps ──────────────────────────────────────────────────────────────────
    Route::get('/maps', [MapController::class, 'index'])->name('maps.index');

    // ── Availability ──────────────────────────────────────────────────────────
    Route::get ('/availability',       [AvailabilityController::class, 'index'])->name('availability.index');
    Route::post('/availability/check', [AvailabilityController::class, 'check'])->name('availability.check');
});

// Map data API
Route::get('/api/maps/data', [MapController::class, 'data'])->middleware('auth');
