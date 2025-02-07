@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($home_page_message)
                        <div class="alert alert-info" role="alert">
                            <i class="bi bi-exclamation-circle-fill"></i>
                            {!! $home_page_message !!}
                        </div>
                    @endif

                    <h2>{{ __('Upcoming conference submissions') }}</h2>
                    <div class="row py-4 border-bottom">
                        @if(count($conferences) < 1)
                            <p class="text-center">{{ __('None found. No upcoming conferences ready to be scheduled.') }}</p>
                        @else
                            @foreach($conferences as $conference)
                                <div class="col-md-6 my-2">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <span class="position-absolute top-0 end-0 translate-middle-y badge rounded-pill {{ $conference->call_active ? 'bg-success':'bg-secondary' }}">
                                                    {{ $conference->start_date_display }}
                                                    -
                                                    {{ $conference->end_date_display }}
                                            </span>
                                            <h5 class="card-title">{{ $conference->name }}</h5>
                                            <p class="card-text">
                                                Call opens: {{  $conference->call_start_date_display }} <br />
                                                Call closes: {{  $conference->call_end_date_display }}
                                            </p>
                                            <p class="card-text">{{ $conference->description }}</p>
                                        </div>
                                        <div class="card-footer">
                                            @if($conference->call_active)
                                                <a href="{{ route('call_for_presentations', ['conference' => $conference->slug] ) }}" class="btn align-self-end m-1 {{ $conference->call_active ? 'btn-warning':'btn-outline-secondary disabled' }}" >
                                                    {{ __('Call for presentations') }}
                                                </a>
                                                <a href="{{ route('call_for_panelists', ['conference' => $conference->slug] ) }}" class="btn align-self-end m-1 {{ $conference->call_active ? ' btn-primary':'btn-outline-secondary disabled' }}">
                                                    {{ __('Call for panelists') }}
                                                </a>
                                                @if($mass_signing_enabled)
                                                    <a href="{{ route('call_for_signing', ['conference' => $conference->slug] ) }}" class="btn align-self-end m-1 {{ $conference->call_active ? ' btn-info':'btn-outline-secondary disabled' }}">
                                                        {{ __('Mass signing') }}
                                                    </a>
                                                @endif

                                            @else
                                                {{ __('The call is currently closed.') }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    @if (!empty($user_sessions))
                        <h2 class="mt-2">{{ __('Your schedule') }}</h2>
                        <div class="py-4 border-bottom">
                            @foreach($user_sessions as $session)
                                <h4>{{ $session['conference_info']->name }}</h4>
                                @foreach($session['session_info'] as $session_info)
                                    <div class="d-flex border">
                                        <div class="p-2 flex-grow-1">
                                            <a href="{{ route('user-session-view', ['conference_slug' => $session['conference_info']->short_name, 'session_id' => $session_info->session_id] ) }}"><i class="bi bi-calendar-check-fill me-2"></i>{{ $session_info->session_name }}</a>
                                            <span class="text-secondary"> | {{ $session_info->track_name }} {{ $session_info->session_type }}</span>
                                            {!! $session_info->is_moderator ? ' <b class="bg-warning  bg-opacity-75 px-1 ms-1"><i class="bi bi-mic-fill small"></i> Moderator</b>':'' !!}</div>
                                        <div class="p-2 text-nowrap">
                                            {{ date("D M j, g:i a", strtotime($session_info->date . ' '. $session_info->time)) }}
                                        </div>
                                    </div>
                                @endforeach
                            @endforeach
                            <partials-presenter-promo
                                    :badge-name="'{{ addslashes($user->info->badge_name) }}'"
                                    :profile-image="'{{ str_replace('-thumb', '', $user->info->profile_image) }}'"
                                    :conference-image="'{{ asset('/images/presenter-promos/' . $session_conference['short_name'] . '-square.jpg') }}'"
                            />
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
