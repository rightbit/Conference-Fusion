@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ __('Import Sessions') }}</h4>
                    <span><a href="{{ route('admin_conference_session_list') }}">Back to sessions</a></span>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('import_errors'))
                        <div class="alert alert-warning mt-3">
                            <strong>Import Warnings:</strong>
                            <ul>
                                @foreach (session()->pull('import_errors') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin_post_import_session') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row mb-3">
                            <label for="track_id" class="col-md-4 col-form-label text-md-end">{{ __('Track') }}</label>

                            <div class="col-md-6">
                                <select id="track_id" class="form-control @error('track_id') is-invalid @enderror" name="track_id" required>
                                    <option value="">-- Select a Track --</option>
                                    @foreach($tracks as $track)
                                        <option value="{{ $track->id }}" {{ old('track_id') == $track->id ? 'selected' : '' }}>
                                            {{ $track->name }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('track_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="csv_file" class="col-md-4 col-form-label text-md-end">{{ __('CSV File') }}</label>

                            <div class="col-md-6">
                                <input id="csv_file" type="file" class="form-control @error('csv_file') is-invalid @enderror" name="csv_file" accept=".csv,.txt" required>

                                @error('csv_file')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror

                                <small class="form-text text-muted d-block mt-2">
                                    <strong>CSV Format Requirements:</strong><br>
                                    - First row should be skipped (header row)<br>
                                    - Column 1: Session Name (required)<br>
                                    - Column 2: Description<br>
                                    - Column 3: Session Type ID<br>
                                    - Column 4: Staff Notes<br>
                                    - Column 5: Seed Questions
                                </small>
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="bi bi-upload"></i>  {{ __('Import Sessions') }}
                                </button>
                                <a href="{{ route('admin_conference_session_list') }}" class="btn btn-secondary">
                                    {{ __('Cancel') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

