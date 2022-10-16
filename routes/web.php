<?php

use App\Http\Controllers\CreateUserController;
use App\Models\SiteConfig;
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
        return view('user.call_for_panelists', ['conference' => $conference]);
    })->name('call_for_panelists');

    Route::get('/user/profile', function() {
        return view('user.profile', ['id' => Auth::id()]);
    })->name('profile');
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

    Route::view('room-list', 'admin.room_list')->name('admin_room_list');
    Route::view('schedule-board', 'admin.schedule_board')->name('admin_schedule_board');
    Route::view('track-list', 'admin.track_list')->name('admin_track_list');
    Route::view('user-list', 'admin.user_list')->name('admin_user_list');
    Route::get('user-profile/{id}', function($id) {
        return view('user.profile', ['id' => $id]);
    })->name('user_profile');
    // More Admin routes

});
