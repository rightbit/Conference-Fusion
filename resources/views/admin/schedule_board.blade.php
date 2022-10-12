@extends('layouts.app')

@section('header_scripts')

@endsection

@section('styles')

@endsection

@section('content')
    @php  /* @var $session_conference */  @endphp
    @php $canEdit = 0; @endphp
    @can('edit_schedules', 'user')
        @php $canEdit = 1; @endphp
    @endcan
    <admin-schedule-board
        :conference-id="{{ $session_conference['id'] }}"
        :conference-name="'{{ $session_conference['short_name'] }}'"
        :conference-start-date="'{{ $session_conference['start_date'] }}'"
        :conference-end-date="'{{ $session_conference['end_date'] }}'"
        :can-edit="{{ $canEdit }}"
    />
@endsection



