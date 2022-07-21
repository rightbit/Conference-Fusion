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
                                            <p class="card-text">Call opens: {{  $conference->call_start_date_display }}</p>
                                            <p class="card-text">{{ $conference->description }}</p>
                                        </div>
                                        <div class="card-footer">
                                            <a href="{{ route('call_for_presentations', ['conference' => $conference->id] ) }}" class="btn align-self-end m-1 {{ $conference->call_active ? 'btn-warning':'btn-outline-secondary disabled' }}" >
                                                {{ __('Call for presentations') }}
                                            </a>
                                            <a href="{{ route('call_for_panelists', ['conference' => $conference->id] ) }}" class="btn align-self-end m-1 {{ $conference->call_active ? ' btn-primary':'btn-outline-secondary disabled' }}">
                                                {{ __('Call for panelists') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <h2 class="mt-2">{{ __('My sessions') }}</h2>
                    <div class="row py-4 border-bottom">
                        <p class="text-center">{{ __('None found. Sessions you submit requests for will appear here.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
