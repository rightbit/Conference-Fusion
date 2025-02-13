@extends('layouts.no_header')

@section('content')
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h2>{{ $conference_schedule->session->name }}</h2>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('save-rating') }}" method="POST">
                            @csrf
                            <input type="hidden" name="conference_schedule_id" value="{{ $conference_schedule->id }}">
                            <input type="hidden" name="uuid" value="{{ $uuid }}">

                            <div class="form-group">
                                <label for="overall_rating"><b>Overall Review</b></label>
                                <select class="form-control" id="overall_rating" name="overall_rating" required oninvalid="this.setCustomValidity('Please leave a rating')" oninput="setCustomValidity('')">
                                    <option value="" class="disabled" disabled selected>What did you think of the session?</option>
                                    <option value="5">5 - Amazing!</option>
                                    <option value="4">4 - Great!</option>
                                    <option value="3">3 - Good</option>
                                    <option value="2">2 - Fair</option>
                                    <option value="1">1 - Poor</option>
                                </select>
                            </div>

                            @if (count($conference_schedule->participants) > 0 && count($conference_schedule->participants) <=6)
                                <div class="mt-3"><b>Participants</b></div>
                                @foreach($conference_schedule->participants as $participant)
                                    <div class="form-group mt-1">
                                        <label for="presenter_rating">{{ $participant->badge_name }}</label>
                                        <select class="form-control" id="participant_ratings_{{ $participant->user_id }}" name="participant_ratings[{{ $participant->user_id }}]" optional>
                                            <option value="" class="disabled" disabled selected>How well did this participant do?</option>
                                            <option value="5">5 - Amazing!</option>
                                            <option value="4">4 - Great!</option>
                                            <option value="3">3 - Good</option>
                                            <option value="2">2 - Fair</option>
                                            <option value="1">1 - Poor</option>
                                        </select>
                                    </div>
                                @endforeach
                           @elseif (count($conference_schedule->participants) > 0)
                            <div class="form-group mt-3">
                                <label for="presenter_rating"><b>Participants Review</b></label>
                                <div><small class="form-text text-muted">You can leave a comment below about any specific participants</small></div>
                                <select class="form-control" id="participant_ratings" name="participant_ratings[group]" optional>
                                    <option value="" class="disabled" disabled selected>How well did this participant do?</option>
                                    <option value="5">5 - Amazing!</option>
                                    <option value="4">4 - Great!</option>
                                    <option value="3">3 - Good</option>
                                    <option value="2">2 - Fair</option>
                                    <option value="1">1 - Poor</option>
                                </select>
                            </div>
                            @endif


                            <div class="form-group mt-3">
                                <label for="comments"><b>Comments</b></label>
                                <div><small class="form-text text-muted">Please leave comments or explanations for your ratings</small></div>
                                <textarea class="form-control" id="comments" name="comments" rows="3" optional></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary mt-3">Submit Review</button>
                        </form>
                    </div>
            </div>
            </div>
        </div>
    </div>

@endsection