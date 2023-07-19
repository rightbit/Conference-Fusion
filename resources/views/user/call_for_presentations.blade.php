@extends('layouts.app')

@section('content')
    @php  /* @var $conference */  @endphp
    <call-for-presentations :conference-id="'{{ $conference }}'" />
@endsection
