@extends('layouts.app')

@section('content')
    @php  /* @var $session_conference */  @endphp
    @php $canEdit = 0; @endphp
    @can('edit_conference', 'user')
        @php $canEdit = 1; @endphp
    @endcan
    <admin-announcement-list
            :conference-id="{{ $session_conference['id'] }}"
            :conference-name="'{{ $session_conference['short_name'] }}'"
            :can-edit="{{ $canEdit }}"
    />
@endsection
