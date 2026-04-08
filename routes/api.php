<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\TestController;
use App\Http\Controllers\Api\Admin\QuestionController;
use App\Http\Controllers\Api\Admin\AssessmentController;
use App\Http\Controllers\Api\Admin\AssessmentLinkController;
use App\Http\Controllers\Api\Admin\ResultController;
use App\Http\Controllers\Api\Admin\ExportController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Admin\ProfileController;
use App\Http\Controllers\Api\Admin\ParticipantManagementController;
use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\Participant\ParticipationController;
use App\Http\Controllers\Api\Participant\ParticipantPortalController;

// Health check (no auth, no throttle — for load balancers)
Route::get('health', [HealthController::class, 'check']);

// Public contact form
Route::middleware('throttle:6,1')->post('contact', [ContactController::class, 'send']);

// Auth
Route::middleware('throttle:auth')->group(function () {
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::post('auth/forgot-password', [PasswordResetController::class, 'forgotPassword']);
    Route::post('auth/reset-password', [PasswordResetController::class, 'resetPassword']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::post('auth/refresh', [AuthController::class, 'refresh']);
    Route::get('auth/me', [AuthController::class, 'me']);

    Route::get('profile', [ProfileController::class, 'show']);
    Route::put('profile', [ProfileController::class, 'update']);
    Route::put('profile/password', [ProfileController::class, 'updatePassword']);
});

// Admin API
Route::middleware(['auth:sanctum', 'throttle:api'])->prefix('admin')->group(function () {
    Route::get('dashboard/stats', [DashboardController::class, 'stats']);
    Route::get('dashboard/recent-activity', [DashboardController::class, 'recentActivity']);
    Route::get('dashboard/charts', [DashboardController::class, 'charts']);

    Route::apiResource('tests', TestController::class)
        ->parameters(['tests' => 'test:uuid']);
    Route::post('tests/{test:uuid}/questions/reorder', [QuestionController::class, 'reorder']);
    Route::apiResource('tests.questions', QuestionController::class)
        ->except(['show'])
        ->parameters(['tests' => 'test:uuid']);

    Route::apiResource('assessments', AssessmentController::class)
        ->parameters(['assessments' => 'assessment:uuid']);
    Route::post('assessments/{assessment:uuid}/tests', [AssessmentController::class, 'attachTests']);
    Route::delete('assessments/{assessment:uuid}/tests/{test:uuid}', [AssessmentController::class, 'detachTest']);

    Route::apiResource('assessments.links', AssessmentLinkController::class)
        ->parameters(['assessments' => 'assessment:uuid', 'links' => 'link:uuid']);

    Route::get('assessments/{assessment:uuid}/analytics', [ResultController::class, 'assessmentAnalytics']);
    Route::get('assessments/{assessment:uuid}/results', [ResultController::class, 'assessmentResults']);
    Route::get('assessments/{assessment:uuid}/links/{link:uuid}/results', [ResultController::class, 'linkResults']);
    Route::get('attempts/{attempt:uuid}', [ResultController::class, 'attemptDetail']);
    Route::get('attempts/{attempt:uuid}/responses', [ResultController::class, 'attemptResponses']);

    Route::apiResource('users', UserController::class)
        ->parameters(['users' => 'user:uuid']);
    Route::post('users/{user:uuid}/reset-password', [UserController::class, 'resetPassword']);

    Route::get('assessments/{assessment:uuid}/export/summary', [ExportController::class, 'assessmentSummary']);
    Route::get('assessments/{assessment:uuid}/export/detailed', [ExportController::class, 'assessmentDetailed']);
    Route::get('assessments/{assessment:uuid}/links/{link:uuid}/export/summary', [ExportController::class, 'linkSummary']);
    Route::get('assessments/{assessment:uuid}/links/{link:uuid}/export/detailed', [ExportController::class, 'linkDetailed']);

    Route::get('assessments/{assessment:uuid}/export/summary-pdf', [ExportController::class, 'assessmentSummaryPdf']);
    Route::get('assessments/{assessment:uuid}/export/detailed-pdf', [ExportController::class, 'assessmentDetailedPdf']);
    Route::get('assessments/{assessment:uuid}/links/{link:uuid}/export/summary-pdf', [ExportController::class, 'linkSummaryPdf']);
    Route::get('assessments/{assessment:uuid}/links/{link:uuid}/export/detailed-pdf', [ExportController::class, 'linkDetailedPdf']);
    Route::get('attempts/{attempt:uuid}/export/pdf', [ExportController::class, 'attemptPdf']);
    Route::get('participants', [ParticipantManagementController::class, 'index']);
    Route::get('participants/combined-report/pdf', [ExportController::class, 'participantCombinedPdf']);
    Route::get('participants/{participantAccount:uuid}', [ParticipantManagementController::class, 'show']);
    Route::post('participants/{participantAccount:uuid}/grant-retake', [ParticipantManagementController::class, 'grantRetake']);
    Route::get('participants/{participantAccount:uuid}/retake-history', [ParticipantManagementController::class, 'retakeHistory']);
    Route::get('participants/{participantAccount:uuid}/combined-results', [ParticipantManagementController::class, 'combinedResults']);
    Route::get('participants/{participantAccount:uuid}/psycho-profile/pdf', [ParticipantManagementController::class, 'downloadProfile']);
});

// Participant Portal
Route::prefix('portal')->group(function () {
    Route::middleware('throttle:portal-auth')->group(function () {
        Route::post('register', [ParticipantPortalController::class, 'register']);
        Route::post('login', [ParticipantPortalController::class, 'login']);
        Route::post('forgot-password', [ParticipantPortalController::class, 'forgotPassword']);
        Route::post('reset-password', [ParticipantPortalController::class, 'resetPassword']);
    });

    Route::middleware('auth:participant')->group(function () {
        Route::post('logout', [ParticipantPortalController::class, 'logout']);
        Route::get('me', [ParticipantPortalController::class, 'me']);
        Route::get('assessments', [ParticipantPortalController::class, 'assessments']);
        Route::post('assign-link/{token}', [ParticipantPortalController::class, 'assignLink']);
        Route::get('combined-results', [ParticipantPortalController::class, 'combinedResults']);
        Route::get('psycho-profile/pdf', [ParticipantPortalController::class, 'downloadProfile']);
    });
});

// Participant API (public)
Route::middleware('throttle:participate')->prefix('participate')->group(function () {
    Route::get('{token}', [ParticipationController::class, 'showLink']);
    Route::post('{token}/verify-password', [ParticipationController::class, 'verifyPassword']);
    Route::post('{token}/register', [ParticipationController::class, 'register']);

    Route::prefix('session/{participant:uuid}')->withoutScopedBindings()->group(function () {
        Route::get('/', [ParticipationController::class, 'getSession']);
        Route::get('test/{test:uuid}', [ParticipationController::class, 'startTest']);
        Route::post('test/{test:uuid}/responses', [ParticipationController::class, 'submitResponses']);
        Route::post('test/{test:uuid}/complete', [ParticipationController::class, 'completeTest']);
        Route::get('results', [ParticipationController::class, 'getResults']);
    });
});
