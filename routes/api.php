<?php

use App\Http\Controllers\Api\ConferenceController;
use App\Http\Controllers\Api\ConferenceScheduleController;
use App\Http\Controllers\Api\ConferenceSessionController;
use App\Http\Controllers\Api\SessionAlternateNameController;
use App\Http\Controllers\Api\SessionInterestController;
use App\Http\Controllers\Api\SessionStatusController;
use App\Http\Controllers\Api\SessionTypeController;
use App\Http\Controllers\Api\SiteConfigController;
use App\Http\Controllers\Api\TrackController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserInfoDataCategoryController;
use App\Http\Controllers\Api\UserPermissionController;
use App\Http\Controllers\Api\RoomController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::middleware(['auth:sanctum'])->group( function() {
    Route::get('track-list', [TrackController::class, 'index']);
    Route::get('type-list', [SessionTypeController::class, 'index']);
    Route::get('user-presentation-list/{conference_id}', [ConferenceSessionController::class, 'userPresentationIndex']);
    Route::get('user-panel-list/{conference_id}', [ConferenceSessionController::class, 'userPanelIndex']);
    Route::get('user-session-totals/{user_id}', [SessionInterestController::class, 'userSessionTotals']);

    Route::get('user/{user_id}/sessions/interests', [SessionInterestController::class, 'userSessionsInterests']);
    Route::get('user/{user_id}/sessions/panelist', [SessionInterestController::class, 'userSessionsPanelist']);
    Route::get('user/{user_id}/sessions/presenter', [SessionInterestController::class, 'userSessionsPresenter']);

    Route::post('profile-image/{user}', [UserController::class, 'uploadProfileImage']);
    Route::post('presentations/submit', [ConferenceSessionController::class, 'storePresentation']);
    Route::delete('presentations/{id}', [ConferenceSessionController::class, 'destroyPresentation']);
    Route::post('panels/interest/submit', [ConferenceSessionController::class, 'storePanelInterest']);
    Route::post('panels/interest/update/{session_interest}', [ConferenceSessionController::class, 'updatePanelInterest']);
    Route::delete('panels/interest/{id}', [ConferenceSessionController::class, 'destroyPanelInterest']);

    Route::group(['prefix' => 'profile'], function () {
        Route::apiResource('user', UserController::class);
        Route::apiResource('user-info-data', UserInfoDataCategoryController::class);
    });

});

Route::group(['prefix' => 'admin', 'middleware' => ['can:view_admin']], function () {
    Route::apiResource('conference', ConferenceController::class);
    Route::apiResource('conference-session', ConferenceSessionController::class);
    Route::apiResource('conference-schedule', ConferenceScheduleController::class);
    Route::apiResource('configuration', SiteConfigController::class);
    Route::apiResource('session-interest', SessionInterestController::class);
    Route::apiResource('session-status', SessionStatusController::class);
    Route::apiResource('session-type', SessionTypeController::class);
    Route::apiResource('room', RoomController::class);
    Route::apiResource('track', TrackController::class);
    Route::apiResource('permissions', UserPermissionController::class)->middleware('can:admin');
});




