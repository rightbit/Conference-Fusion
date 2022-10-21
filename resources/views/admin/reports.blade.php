@extends('layouts.app')

@section('content')
    @php  /* @var $session_conference */  @endphp
    @php  /* @var $report_id */  @endphp

    <admin-reports
        :conference-id="{{ $session_conference['id'] }}"
        :conference-name="'{{ $session_conference['short_name'] }}'"
        :report-id="'{{ $report_id ?? null }}'"
    />
@endsection
