<?php

namespace App\Http\Controllers;

use App\Models\ConferenceSession;
use App\Models\SessionType;
use App\Models\Track;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class ImportSessionController extends Controller
{
    /**
     * Show the form for importing sessions.
     *
     * @return \Illuminate\View\View
     */
    public function form()
    {
        if (!Gate::allows('edit_sessions')) {
            abort(403, 'Not authorized');
        }

        $conference_id = session('selected_conference');
        $tracks = Track::get();
        $session_types = SessionType::all();

        return view('admin.import_session', compact('tracks', 'session_types', 'conference_id'));
    }

    /**
     * Store imported sessions from CSV.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Gate::allows('edit_sessions')) {
            abort(403, 'Not authorized');
        }

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120',
            'track_id' => 'required|exists:tracks,id',
        ]);

        $conference_id = session('selected_conference');
        $track_id = $request->track_id;
        $imported_count = 0;
        $errors = [];

        try {
            // Load all session types once for lookup
            $session_types = SessionType::all();
            $type_by_id = $session_types->keyBy('id');
            $type_by_name = $session_types->keyBy('name');

            $file = $request->file('csv_file');
            $stream = fopen($file->getRealPath(), 'r');

            // Skip the first line (header)
            fgetcsv($stream);

            while (($row = fgetcsv($stream)) !== false) {
                try {
                    // Validate that we have all required fields
                    if (count($row) < 5) {
                        $errors[] = "Row skipped: insufficient columns. Expected at least 5 columns.";
                        continue;
                    }

                    // Map CSV columns to session fields
                    $name = trim($row[0] ?? '');
                    $description = trim($row[1] ?? '');
                    $type_value = trim($row[2] ?? '');
                    $staff_notes = trim($row[3] ?? '');
                    $seed_questions = trim($row[4] ?? '');

                    // Validate required fields
                    if (empty($name)) {
                        $errors[] = "Row skipped: session name is required.";
                        continue;
                    }

                    // Resolve session type: match by ID if numeric, or by name if string
                    $resolved_type_id = 1;
                    if (!empty($type_value)) {
                        if (is_numeric($type_value)) {
                            if ($type_by_id->has($type_value)) {
                                $resolved_type_id = $type_value;
                            } else {
                                $errors[] = "Row for '{$name}' skipped: session type ID {$type_value} not found.";
                                continue;
                            }
                        } else {
                            if ($type_by_name->has($type_value)) {
                                $resolved_type_id = $type_by_name->get($type_value)->id;
                            } else {
                                $errors[] = "Row for '{$name}' skipped: session type '{$type_value}' not found.";
                                continue;
                            }
                        }
                    }

                    // Create the session
                    $session = ConferenceSession::create([
                        'name' => $name,
                        'description' => $description,
                        'type_id' => $resolved_type_id,
                        'staff_notes' => $staff_notes,
                        'seed_questions' => $seed_questions,
                        'track_id' => $track_id,
                        'conference_id' => $conference_id,
                        'session_status_id' => 1, // Default to "Planning" status
                        'duration_minutes' => 45, // Default duration
                    ]);

                    $imported_count++;
                    Log::info("Imported session: {$name} (ID: {$session->id}) by user " . Auth::id());

                } catch (\Exception $e) {
                    $errors[] = "Error processing row: " . $e->getMessage();
                    Log::error("Error importing session row: " . $e->getMessage());
                }
            }

            fclose($stream);

            $message = "Successfully imported {$imported_count} session(s).";
            if (!empty($errors)) {
                session(['import_errors' => $errors]);
                $message .= " There were " . count($errors) . " error(s) - see details below.";
            }

            return redirect()->route('admin_conference_session_list')
                ->with('success', $message);

        } catch (\Exception $e) {
            Log::error("Session import error: " . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while importing sessions: ' . $e->getMessage());
        }
    }
}

