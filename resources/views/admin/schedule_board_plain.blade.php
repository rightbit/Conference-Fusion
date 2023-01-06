@extends('layouts.app')

@section('header_scripts')

@endsection

@section('styles')

@endsection

@section('content')
    @php  /* @var $session_conference */  @endphp
    <admin-schedule-board-plain
        :conference-id="{{ $session_conference['id'] }}"
        :conference-name="'{{ $session_conference['short_name'] }}'"
        :conference-start-date="'{{ $session_conference['start_date'] }}'"
        :conference-end-date="'{{ $session_conference['end_date'] }}'"
    />
@endsection



