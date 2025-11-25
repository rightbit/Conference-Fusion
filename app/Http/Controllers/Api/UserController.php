<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\UserInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!Gate::allows('view_admin', Auth::user())){
            abort(403, 'Not authorized');
        }

        if($request->keyword){
            return UserResource::collection(
              User::where('first_name', 'LIKE',"%$request->keyword%")
                  ->orWhere('last_name', 'LIKE',"%$request->keyword%")
                  ->orWhere('email', 'LIKE', "%$request->keyword%")
                  ->orWhereHas('info', function($query) use($request) {
                      $query->where('badge_name', 'LIKE',"%$request->keyword%");
                  })
                  ->orderby('last_name')->paginate(25)
            );
        }

        return UserResource::collection(User::orderby('last_name')->paginate(25));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\UserRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        abort(403, "Not Authorized");

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User     $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        if(!Gate::allows('view_admin', Auth::user()) && !Gate::allows('edit_users', $user->id)){
            abort(403, 'Not authorized');
        }
        $userinfo =  new UserResource($user);
        return $userinfo;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\User $user
     * @param  \App\Http\Requests\UserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function update(User $user, UserRequest $request)
    {
        if($request->id == $user->id) {
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->save();

            $user->info->badge_name = $request->info['badge_name'];
            $user->info->contact_email = $request->info['contact_email'];
            $user->info->biography = $request->info['biography'];
            $user->info->notes = $request->info['notes'];
            $user->info->staff_notes = $request->info['staff_notes'];
            $user->info->website = $request->info['website'];
            $user->info->share_email_permission = $request->info['share_email_permission'] ?? 0;
            $user->info->agree_to_terms = $request->info['agree_to_terms'] ?? 0;
            $user->info->personal_data = !empty($request->info['personal_data']) ? json_encode($request->info['personal_data']) : NULL;
            $user->info->social_data = !empty($request->info['social_data']) ? json_encode($request->info['social_data']) : NULL;
            $user->info->participant_data = !empty($request->info['participant_data']) ? json_encode($request->info['participant_data']) : NULL;
            $user->info->save();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    /**
     * Update the user password.
     *
     * @param  \App\Models\User     $user
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(User $user, Request $request)
    {
        Log::debug('user', [$request]);
        if(!Gate::allows('edit_users', $user->id)){
            abort(403, 'Not authorized');
        }

        $this->validate($request, [
            'password' => 'required|string|min:6',
        ]);

        $user->password = Hash::make($request->password);
        $user->save();

    }

    /**
     * Update the user profile image.
     *
     * @param  \App\Models\User     $user
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function uploadProfileImage(User $user, Request $request)
    {

        if(!Gate::allows('view_admin', Auth::user()) && !Gate::allows('edit_users', $user->id)){
            abort(403, 'Not authorized');
        }

        $this->validate($request, [
            'file' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);
        $folder = intval($user->id / 100); //create a new folder every 100 users
        $file = $request->file('file');
        $image_name = $user->id . substr(sha1($user->id), 0, 10);

        $filePath = public_path('/images/profile/' . $folder);
        if (!file_exists($filePath)) {
            mkdir($filePath, 0777, true);
        }
        $img = Image::make($file->path());
        $img->save($filePath . '/' . $image_name . '.' . $file->extension(), 80);
        $img->fit(150)->save($filePath . '/' . $image_name . '-thumb.' . $file->extension());
        $user->info->profile_image = '/images/profile/' . $folder . '/' . $image_name . '-thumb.' . $file->extension();
        try {
            $user->info->save();
            $user_res = new UserResource($user);
            return $user_res;

        } catch (\Exception $e) {
            abort(500, 'Please complete your profile first.');
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User     $user
     * @return \Illuminate\Http\Response
     */
    public function createToken(User $user)
    {
        if(!Gate::allows('view_admin', Auth::user()) && !Gate::allows('edit_users', $user->id)){
            abort(403, 'Not authorized');
        }

        $authToken = $user->tokens()->first();

        //TODO - this is to test an app conection and needs a better management system
        if($authToken) {
            return response()->json(['accessToken' => 'A Token was already created and cannot be created again. Contact an administrator.']);
        }
        $authToken = $user->createToken('auth-token')->plainTextToken;

        return response()->json(['accessToken' => $authToken]);

    }

    //DELETE ME
    public function importUsers(Request $request)
    {
        set_time_limit(1000);
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt'
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getPathname(), 'r');
        fgetcsv($handle, 1000, ',');
        while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {


            if (count($data) < 13) {
                // Skip or handle incomplete row
                dd($data);
            }
            $user = User::create([
                'first_name' => $data[0],
                'last_name' => $data[1],
                'email' => $data[2] ?: trim($data[0]).trim($data[1]).'@example.com',
                'password' => Hash::make($data[0].$data[1].'password'),
            ]);

            $participant_array = [];
            $participant_array['presenter_type'] = $data[8] ?: '';
            $participant_array['featured_book'] = $data[9] ?: '';
            $participant_array['featured_book_cover'] = $data[10] ?: '';
            $participant_array['additional_books'] = $data[11] ?: '';

            $social_array = [];
            $social_array['social_followers'] = $data[12] ?: '';

            UserInfo::create([
                'user_id' => $user->id,
                'badge_name' => $data[3],
                'contact_email' => $data[2] ?: trim($data[0]).trim($data[1]).'@example.com',
                'website' => $data[4] ?: '',
                'biography' => $data[5] ?: '',
                'notes' => $data[6] ?: '',
                'participant_data' => !empty($participant_array['presenter_type'] || $participant_array['featured_book'] || $participant_array['featured_book_cover'] || $participant_array['additional_books']) ? json_encode($participant_array) : NULL,
                'social_data' => !empty($social_array['social_followers']) ? json_encode($social_array) : NULL,
                'agree_to_terms' => 1,
            ]);

            // Download image from URL

            $imageUrl = $data[7] ?? null;
            if ($imageUrl && filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                preg_match('/\/d\/([^\/]+)\//', $imageUrl, $matches);
                if (isset($matches[1])) {
                    $fileId = $matches[1];
                    $imageUrl = "https://drive.google.com/uc?export=download&id={$fileId}";
                }



                $options = [
                    "http" => [
                        "header" => "User-Agent: Mozilla/5.0\r\n"
                    ]
                ];
                $context = stream_context_create($options);

                $imageContents = @file_get_contents($imageUrl, false, $context);


//                if ($imageContents === false) {
//                    // Handle failed download (skip or log)
//                    continue;
//                }
//                if (strlen($imageContents) > 2048 * 1024) {
//                    // Skip or handle file too large
//                    continue;
//                }
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
                $finfo = new \finfo(FILEINFO_MIME_TYPE);
                $mimeType = $finfo->buffer($imageContents);

// Map MIME type to extension
                $mimeToExt = [
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/gif' => 'gif',
                    'image/bmp' => 'bmp',
                    'image/webp' => 'webp',
                ];
                $extension = $mimeToExt[$mimeType] ?? null;

                if (!$extension || !in_array($extension, $allowedExtensions)) {
                    // Skip or handle invalid extension
                    continue;
                }

                $tmpImagePath = storage_path('app/tmp_profile_' . uniqid() . '.' . $extension);
                file_put_contents($tmpImagePath, $imageContents);

                $folder = intval($user->id / 100);
                $image_name = $user->id . substr(sha1($user->id), 0, 10);
                $filePath = public_path('/images/profile/' . $folder);
                if (!file_exists($filePath)) {
                    mkdir($filePath, 0777, true);
                }

                try {
                    $img = Image::make($tmpImagePath);
                    $img->save($filePath . '/' . $image_name . '.' . $extension, 80);
                    $img->fit(150)->save($filePath . '/' . $image_name . '-thumb.' . $extension);
                    $user->info->profile_image = '/images/profile/' . $folder . '/' . $image_name . '-thumb.' . $extension;
                    $user->info->save();
                    if (file_exists($tmpImagePath)) {
                        unlink($tmpImagePath);
                    }
                } catch (\Exception $e) {
                    \Log::error('Profile image import failed', [
                        'user_id' => $user->id,
                        'user_email' => $user->email,
                        'image_url' => $imageUrl,
                        'error' => $e->getMessage()
                    ]);
                }

                if (file_exists($tmpImagePath)) {
                    unlink($tmpImagePath);
                }
            }




        }
        fclose($handle);

        return redirect()->back()->with('success', 'Users imported successfully!');
    }

}
