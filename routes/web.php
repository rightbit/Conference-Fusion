<?php

use App\Http\Controllers\CreateUserController;
use App\Http\Controllers\ExportController;
use App\Models\SiteConfig;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [App\Http\Controllers\WelcomeController::class, 'welcome'])->name('welcome');
Route::get('/terms-and-policies', function() {
        return (view()->exists('custom.terms')) ? view('custom.terms') : view('terms');
})->name('terms');

Auth::routes();

//Logged in group
Route::middleware(['auth'])->group( function() {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::get('/select-conference/{id}', [App\Http\Controllers\HomeController::class, 'conferenceSelect'])->name('select-conference');

    Route::get('/call-for-presentations/{conference}',  function($conference) {
        return view('user.call_for_presentations', ['conference' => $conference]);
    })->name('call_for_presentations');

    Route::get('/call-for-panelists/{conference}',  function($conference) {
        return view('user.call_for_panelists', ['conference' => $conference, 'singlesession' => 0]);
    })->name('call_for_panelists');

    Route::get('/call-for-panelists/{conference}/panel/{singlesession}',  function($conference, $singlesession) {
        return view('user.call_for_panelists', ['conference' => $conference, 'singlesession' => $singlesession]);
    })->name('call_for_panelists_single_panel');

    Route::get('/c/{conference_slug}/session/{session_id}', function($conference_slug, $session_id) {
        return view('user.session_view', ['conference_slug' => $conference_slug, 'session_id' => $session_id]);
    })->name('user-session-view');

    Route::get('/user/profile', function() {
        return view('user.profile', ['id' => Auth::id()]);
    })->name('profile');

    Route::get('/mass-signing/{conference}',  function($conference) {
        return view('user.call_for_signing', ['conference' => $conference]);
    })->name('call_for_signing');

});


//Admin Group
Route::group(['prefix' => 'admin', 'middleware' => ['auth','can:view_admin']], function () {
    //Add a user
    Route::view('user-create', 'admin.create_user')
        ->middleware('can:edit_users')
        ->name('admin_user_create');
    Route::post('user-create', [CreateUserController::class, 'create'])
        ->middleware('can:edit_users')
        ->name('admin_post_user_create');

    //Vue Only
    Route::view('conference-session-list', 'admin.conference_session_list')->name('admin_conference_session_list');
    Route::get('conference-session/{id}', function($id) {
                $default_session_duration = SiteConfig::where('key', 'default_session_duration')->first()->value ?? 0;
                return view('admin.conference_session', ['id' => $id, 'default_session_duration' => $default_session_duration]);
            })->name('admin_conference_session');

    Route::view('conference-list', 'admin.conference_list')->name('admin_conference_list');
    Route::get('conference-info/{id}', function($id) {
                return view('admin.conference_info', ['id' => $id]);
            })->name('admin_conference_info');

    Route::view('configuration', 'admin.configuration')->name('admin_configuration');

    Route::group(['prefix' => 'export'], function () {
        Route::get('participant-list/{conference_id}',  [ExportController::class, 'exportParticipantList']);
        Route::get('non-participant-list/{conference_id}',  [ExportController::class, 'exportNonParticipantList']);
        Route::get('schedule-list/{conference_id}',  [ExportController::class, 'exportScheduleList']);
    });

    Route::view('reports', 'admin.reports')->name('admin_reports');
    Route::get('reports/{report_id}', function($report_id) {
                return view('admin.reports', ['report_id' => $report_id]);
            })->name('admin_report_id');
    Route::view('room-list', 'admin.room_list')->name('admin_room_list');
    Route::view('schedule-board', 'admin.schedule_board')->name('admin_schedule_board');
    Route::view('schedule-board-plain', 'admin.schedule_board_plain')->name('admin_schedule_board_plain');
    Route::view('track-list', 'admin.track_list')->name('admin_track_list');
    Route::view('announcement-list', 'admin.announcement_list')->name('admin_announcement_list');
    Route::view('sponsor-list', 'admin.sponsor_list')->name('admin_sponsor_list');
    Route::view('user-list', 'admin.user_list')->name('admin_user_list');
    Route::view('volunteer-list', 'admin.volunteer_list')->name('admin_volunteer_list');
    Route::get('user-profile/{id}', function($id) {
        return view('user.profile', ['id' => $id]);
    })->name('user_profile');
    // More Admin routes

    // Clear application cache:
    Route::get('/clear-cache', function() {
        Artisan::call('cache:clear');
        return 'Cache has been cleared';
    });
    //Clear route cache:
    Route::get('/route-cache', function() {
        Artisan::call('route:cache');
        return 'Routes cache has been cleared';
    });

    //Clear config cache:
    Route::get('/config-cache', function() {
        Artisan::call('config:cache');
        return 'Config cache has been cleared';
    });

    // Clear view cache:
    Route::get('/view-clear', function() {
        Artisan::call('view:clear');
        return 'View cache has been cleared';
    });
});
