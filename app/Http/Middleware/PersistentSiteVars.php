<?php

namespace App\Http\Middleware;


use App\Models\Conference;
use App\Models\SiteConfig;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Closure;
use Illuminate\Http\Request;

class PersistentSiteVars
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Get some configuration info. All the schema checks are required to migrate and seed

        // Get the site name
        $config_site_name = 'Scheduler';
        $config_contact_email = null;
        $default_conference = 0;
        if(Schema::hasTable('site_configs')) {
            $site_name_config = SiteConfig::where('key', 'site_name')->first();
            $config_site_name = $site_name_config->value ?? $config_site_name;

            $contact_email_config = SiteConfig::where('key', 'contact_email')->first();
            $config_contact_email = $contact_email_config->value ?? $config_contact_email;

            $site_config = SiteConfig::where('key', 'default_conference_id')->first();
            $default_conference = $site_config->value ?? 0;
        }
        View::share('config_sitename', $config_site_name);
        View::share('config_contact_email', $config_contact_email);

        // Has the default user login been changed?
        $finish_setup = true;
        if(Schema::hasTable('users')) {
            $finish_setup = !empty(User::where('email', 'admin@example.com')->first());
        }
        View::share('finish_setup', $finish_setup);

        // What session is the user on and if an Admin, how to choose a new one
        $selected_conference = session('selected_conference', $default_conference);
        $session_conference = ['id' =>  0, 'name' => '', 'short_name' => ''];
        $conference_list = [];
        if(Schema::hasTable('conferences')) {
            if($selected_conference == 0) {
                $selected_conference_info = Conference::where('end_date', '>', now())->orderBy('start_date')->first();
            } else {
                $selected_conference_info = Conference::find($selected_conference);
            }
            session(['selected_conference' => $selected_conference_info->id]);
            $session_conference = [
                'id' =>  $selected_conference_info->id,
                'name' => $selected_conference_info->name,
                'short_name' => $selected_conference_info->short_name,
                'start_date' =>  $selected_conference_info->start_date,
                'end_date' =>  $selected_conference_info->end_date
            ];
            $conferences = Conference::orderBy('start_date')->take(10)->get();
            foreach($conferences as $conference){
                $conference_list[] = [
                    'id' =>  $conference->id,
                    'short_name' => $conference->short_name,
                    'start_date' =>  $conference->start_date,
                    'end_date' =>  $conference->end_date
                ];
            }
        }
        View::share('session_conference', $session_conference);
        View::share('conference_short_list', $conference_list);

        return $next($request);
    }
}
