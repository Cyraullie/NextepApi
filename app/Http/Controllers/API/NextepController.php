<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Address_wallet;
use App\Models\ApiClient;
use App\Models\User;
use App\Models\Vote;
use App\Models\VotingTopic;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use mysql_xdevapi\Exception;
use phpDocumentor\Reflection\Types\Integer;
use function Symfony\Component\String\length;

class NextepController extends Controller
{
    public function mytoken(Request $request)
    {
        if (Auth::attempt(['email' => $request->input('username'), 'password' => $request->input('password')])) {
            return Auth::user()->apiClient->api_token;
        } else {
            return response('bad credentials', 401);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        if($request->password == $request->password_confirmation){
            $parts = explode('@', $request->email);
            $parts = explode('.',$parts[0]);
            $fname = isset($request->firstname) ? $request->firstname : $parts[0];
            $lname = isset($request->lastname) ? $request->lastname : $parts[1];
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'firstname' => $fname,
                'lastname' => $lname,
                'phone' => '07'.rand(70000000, 99999999),
                'picture' => 'nextep.png',
                'password' => Hash::make($request->password),
                'role_id' => 1,
            ]);

            $api = new ApiClient();
            $api->api_token = Str::random(60);
            $api->user()->associate($user);
            $api->save();

            Auth::login($user);

            return Auth::user()->apiClient->api_token;
        }

        return response('bad credentials', 401);

        //event(new Registered($user));

        //Auth::login($user);


    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


}
