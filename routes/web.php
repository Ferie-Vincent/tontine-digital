<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForcePasswordChangeController;
use App\Http\Controllers\Auth\PhoneVerificationController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Tontine\TontineController;
use App\Http\Controllers\Tontine\MemberController;
use App\Http\Controllers\Tontine\TourController;
use App\Http\Controllers\Tontine\ContributionController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminGuideController;
use App\Http\Controllers\Admin\AdminRequestController;
use App\Http\Controllers\Admin\AdminActivityController;
use App\Http\Controllers\Admin\AdminSettingsController;
use App\Http\Controllers\Admin\ImpersonationController;
use App\Http\Controllers\Admin\TestMessagingController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\FinancialHistoryController;
use App\Http\Controllers\UserRequestController;
use App\Http\Controllers\PushSubscriptionController;
use App\Http\Controllers\Settings\SettingsController;
use App\Http\Controllers\Tontine\ActivityController;
use App\Http\Controllers\Tontine\ExportController;
use App\Http\Controllers\Tontine\MessageController;
use App\Http\Controllers\Tontine\SwapController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

// Page d'accueil -> redirection
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Routes d'authentification (invité)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::get('/forgot-password', [PasswordResetController::class, 'showForm'])->name('password.request');
    Route::post('/forgot-password/email', [PasswordResetController::class, 'sendByEmail'])->middleware('throttle:auth')->name('password.email');
    Route::post('/forgot-password/sms', [PasswordResetController::class, 'sendBySms'])->middleware('throttle:sms')->name('password.reset.sms');
    Route::get('/forgot-password/sms/verify', [PasswordResetController::class, 'showSmsVerifyForm'])->name('password.reset.sms.verify.form');
    Route::post('/forgot-password/sms/verify', [PasswordResetController::class, 'verifySmsCode'])->middleware('throttle:auth')->name('password.reset.sms.verify');
    Route::get('/reset-password', [PasswordResetController::class, 'showResetForm'])->name('password.reset.new');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->middleware('throttle:auth')->name('password.reset.submit');
});

// Déconnexion
Route::post('/logout', function () {
    // Supprimer la session de l'appareil
    \App\Models\UserSession::where('user_id', auth()->id())
        ->where('session_id', session()->getId())
        ->delete();

    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->middleware('auth')->name('logout');

// Routes authentifiées
Route::middleware('auth')->group(function () {
    // Changement de mot de passe obligatoire (pas de middleware password.force ici)
    Route::get('/password/force-change', [ForcePasswordChangeController::class, 'show'])->name('password.force');
    Route::put('/password/force-change', [ForcePasswordChangeController::class, 'update'])->name('password.force.update');

    // Vérification du numéro de téléphone
    Route::get('/phone/verify', [PhoneVerificationController::class, 'show'])->name('phone.verify');
    Route::post('/phone/verify/send', [PhoneVerificationController::class, 'sendCode'])->middleware('throttle:sms')->name('phone.verify.send');
    Route::post('/phone/verify', [PhoneVerificationController::class, 'verify'])->middleware('throttle:auth')->name('phone.verify.submit');

    // Arrêter l'impersonation (accessible sans middleware admin)
    Route::post('/impersonate/stop', [ImpersonationController::class, 'stop'])->name('impersonate.stop');

    // Toutes les autres routes protegees par le middleware password.force
    Route::middleware(['password.force', 'phone.verified'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Tontines
        Route::get('/tontines/join', [TontineController::class, 'joinForm'])->name('tontines.join');
        Route::post('/tontines/join', [TontineController::class, 'join'])->name('tontines.join.submit');
        Route::resource('tontines', TontineController::class);

        // Notifications
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->middleware('throttle:auth')->name('notifications.markAllRead');

        // Membres d'une tontine
        Route::prefix('tontines/{tontine}')->name('tontines.')->middleware('tontine.member')->group(function () {
            // Messages / Chat
            Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');

            Route::post('/clone', [TontineController::class, 'clone'])->middleware('throttle:sensitive')->name('clone');
            Route::post('/pause', [TontineController::class, 'pause'])->middleware('throttle:sensitive')->name('pause');
            Route::post('/resume', [TontineController::class, 'resume'])->middleware('throttle:sensitive')->name('resume');
            Route::put('/settings', [TontineController::class, 'updateSettings'])->name('settings.update');
            Route::get('/finances', [TontineController::class, 'finances'])->name('finances');

            Route::get('/members', [MemberController::class, 'index'])->name('members.index');
            Route::get('/members/{member}/performance', [MemberController::class, 'performance'])->name('members.performance');
            Route::get('/members/search', [MemberController::class, 'search'])->name('members.search');
            Route::post('/members/add', [MemberController::class, 'addDirectly'])->middleware('throttle:sensitive')->name('members.add');
            Route::post('/members/create-and-add', [MemberController::class, 'createAndAdd'])->middleware('throttle:sensitive')->name('members.createAndAdd');
            Route::post('/members/{member}/accept', [MemberController::class, 'accept'])->name('members.accept');
            Route::post('/members/{member}/reject', [MemberController::class, 'reject'])->name('members.reject');
            Route::post('/members/{member}/exclude', [MemberController::class, 'exclude'])->name('members.exclude');
            Route::put('/members/{member}/role', [MemberController::class, 'updateRole'])->name('members.updateRole');
            Route::put('/members/{member}/parts', [MemberController::class, 'updateParts'])->name('members.updateParts');
            Route::put('/members/positions', [MemberController::class, 'updatePositions'])->name('members.updatePositions');
            Route::post('/members/invite', [MemberController::class, 'invite'])->middleware('throttle:sensitive')->name('members.invite');
            Route::post('/members/import', [MemberController::class, 'import'])->middleware('throttle:sensitive')->name('members.import');
            Route::get('/members/import-template', [MemberController::class, 'importTemplate'])->name('members.importTemplate');
            Route::post('/leave', [MemberController::class, 'leave'])->name('leave');

            // Tours
            Route::resource('tours', TourController::class)->except(['create', 'edit']);
            Route::post('/tours/{tour}/start', [TourController::class, 'start'])->name('tours.start');
            Route::post('/tours/{tour}/complete', [TourController::class, 'complete'])->middleware('throttle:sensitive')->name('tours.complete');
            Route::post('/tours/{tour}/disburse', [TourController::class, 'disburse'])->middleware('throttle:sensitive')->name('tours.disburse');
            Route::post('/tours/{tour}/confirm-receipt', [TourController::class, 'confirmReceipt'])->middleware('throttle:sensitive')->name('tours.confirmReceipt');
            Route::post('/tours/{tour}/retry', [TourController::class, 'retry'])->name('tours.retry');
            Route::put('/tours/{tour}/reassign', [TourController::class, 'reassign'])->name('tours.reassign');

            // Exports
            Route::get('/contributions/export/csv', [ExportController::class, 'contributionsCsv'])->name('contributions.export.csv');
            Route::get('/contributions/export/pdf', [ExportController::class, 'contributionsPdf'])->name('contributions.export.pdf');
            Route::get('/contributions/matrix/export/csv', [ExportController::class, 'matrixCsv'])->name('contributions.matrix.export.csv');
            Route::get('/finances/export/pdf', [ExportController::class, 'financesPdf'])->name('finances.export.pdf');
            Route::get('/export/members/csv', [ExportController::class, 'membersCsv'])->name('export.members.csv');
            Route::get('/export/full-report/csv', [ExportController::class, 'fullReportCsv'])->name('export.full-report.csv');

            // Contributions
            Route::get('/contributions', [ContributionController::class, 'index'])->name('contributions.index');
            Route::get('/contributions/matrix', [ContributionController::class, 'matrix'])->name('contributions.matrix');
            Route::post('/contributions/{contribution}/declare', [ContributionController::class, 'declare'])->middleware('throttle:sensitive')->name('contributions.declare');
            Route::post('/contributions/{contribution}/confirm', [ContributionController::class, 'confirm'])->middleware('throttle:sensitive')->name('contributions.confirm');
            Route::post('/contributions/{contribution}/reject', [ContributionController::class, 'reject'])->middleware('throttle:sensitive')->name('contributions.reject');

            // Swap de position
            Route::post('/swap', [SwapController::class, 'store'])->middleware('throttle:sensitive')->name('swap.store');
            Route::get('/swap/pending', [SwapController::class, 'pending'])->name('swap.pending');
            Route::post('/swap/{swap}/respond', [SwapController::class, 'respond'])->middleware('throttle:sensitive')->name('swap.respond');

            // Historique d'activité
            Route::get('/activity', [ActivityController::class, 'index'])->name('activity');
            Route::get('/activity/export', [ActivityController::class, 'export'])->name('activity.export');
        });

        // Requetes utilisateur
        Route::get('/requests', [UserRequestController::class, 'index'])->name('requests.index');
        Route::get('/requests/create', [UserRequestController::class, 'create'])->name('requests.create');
        Route::post('/requests', [UserRequestController::class, 'store'])->name('requests.store');
        Route::get('/requests/{userRequest}', [UserRequestController::class, 'show'])->name('requests.show');

        // Push notifications
        Route::post('/push/subscribe', [PushSubscriptionController::class, 'store'])->name('push.subscribe');
        Route::post('/push/unsubscribe', [PushSubscriptionController::class, 'destroy'])->name('push.unsubscribe');

        // Historique financier
        Route::get('/financial-history', [FinancialHistoryController::class, 'index'])->name('financial-history');

        // Parametres utilisateur
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
        Route::put('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile');
        Route::put('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password');
        Route::put('/settings/notifications', [SettingsController::class, 'updateNotifications'])->name('settings.notifications');
        Route::delete('/settings/avatar', [SettingsController::class, 'removeAvatar'])->name('settings.avatar.remove');
        Route::delete('/settings/sessions/{session}', [SettingsController::class, 'destroySession'])->name('settings.sessions.destroy');

        // Administration
        Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
            Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
            Route::get('/requests', [AdminRequestController::class, 'index'])->name('requests.index');
            Route::get('/requests/{userRequest}', [AdminRequestController::class, 'show'])->name('requests.show');
            Route::put('/requests/{userRequest}', [AdminRequestController::class, 'respond'])->name('requests.respond');

            Route::get('/users', [UserManagementController::class, 'index'])->name('users');
            Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
            Route::post('/users/bulk-action', [UserManagementController::class, 'bulkAction'])->name('users.bulk');
            Route::get('/users/{user}', [UserManagementController::class, 'show'])->name('users.show');
            Route::post('/users/{user}/suspend', [UserManagementController::class, 'suspend'])->name('users.suspend');
            Route::post('/users/{user}/activate', [UserManagementController::class, 'activate'])->name('users.activate');
            Route::post('/users/{user}/unlock', [UserManagementController::class, 'unlock'])->name('users.unlock');

            Route::get('/activity', [AdminActivityController::class, 'index'])->name('activity');

            Route::get('/settings', [AdminSettingsController::class, 'index'])->name('settings');
            Route::put('/settings', [AdminSettingsController::class, 'update'])->name('settings.update');

            Route::post('/users/{user}/impersonate', [ImpersonationController::class, 'start'])->name('users.impersonate');

            Route::post('/test-sms', [TestMessagingController::class, 'testSms'])->name('test.sms');
            Route::post('/test-whatsapp', [TestMessagingController::class, 'testWhatsapp'])->name('test.whatsapp');

            Route::get('/guide', [AdminGuideController::class, 'index'])->name('guide');
        });
    });
});
