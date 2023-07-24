@extends('layouts.app')

@section('content')
    @php  /* @var $conference */  @endphp
    @php  /* @var $singlesession */  @endphp
    <call-for-panels :conference-id="'{{ $conference }}'" :user-id="{{ Auth::user()->id }}" :single-session="{{ $singlesession }}" />
@endsection
