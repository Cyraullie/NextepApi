<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Address_wallet;
use App\Models\User;
use App\Models\VotingTopic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use mysql_xdevapi\Exception;
use phpDocumentor\Reflection\Types\Integer;

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

    public function profile()
    {
        $user = User::findOrFail(Auth::user()->user_id);
        return [
            'id' => $user->id,
            'email' => $user->email,
            'username' => $user->name,
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'picture' => $user->picture,
            'creation_date' => $user->created_at,
            'last_logged_date' => $user->last_login,
            'two_factor_auth' => $user->two_factor_auth,
            'description' => $user->description,
            'address_wallets' => $user->address_wallets,
            'votes' => $user->votes,
        ];
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
        //
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
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        if ($request->input('_method') == 'PATCH') {
            try {
                $user = User::find(Auth::user()->user_id);
                $user->name = $request->has('username') ? $request->input('username') : $user->name;
                $user->email = $request->has('email') ? $request->input('email') : $user->email;
                $user->firstname = $request->has('firstname') ? $request->input('firstname') : $user->firstname;
                $user->lastname = $request->has('lastname') ? $request->input('lastname') : $user->lastname;
                //$user->wallet_address = $request->has('wallet_address') ? $request->input('wallet_address') : $user->wallet_address;
                $user->two_factor_auth = $request->has('two_factor_auth') ? $request->input('two_factor_auth') : $user->two_factor_auth;
                $user->description = $request->has('description') ? $request->input('description') : $user->description;
                $user->save();

                Address_wallet::create([
                    'address' => $request->input('wallet_address'),
                    'address_type' => 0,
                    'user_id' => $user->id,
                ]);
                return response("Ok",200);
            } catch (Exception $e) {
                return response('bad request',400);
            }
        } else {
            return response('Only PATCH method allowed', 405);
        }
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

    public function uploadPhoto(Request $request)
    {
        if ($file = $request->file('photo')) {
            $file->store('public/pics');
            $user = User::find(Auth::user()->user_id);
            $user->picture = $file->hashName();
            $user->save();
            return response('Ok',200);
        } else {
            return response('Bad request',400);
        }
    }

    public function deleteWallet(Request $request, $id)
    {
        try {
            Address_wallet::find($id)->delete();
            return response( "Ok",200);
        } catch (\Exception $e) {
            return response('Bad request:' . $e->getMessage(), 400);
        }
    }

    public function changePassword(Request $request)
    {
        if ($request->input('_method') == 'PATCH') {
            try {
                $user = User::find(Auth::user()->user_id);
                $old_password = $request->input("old_password");
                $new_password = $request->input("new_password");
                $new_ConfirmPassword = $request->input("new_ConfirmPassword");

                if(Hash::check($old_password, $user->password)){
                    if($new_password == $new_ConfirmPassword){
                        if($old_password != $new_password) {
                            if (strlen($new_password) >= 8) {
                                if (preg_match('/[A-Z]/', $new_password) && preg_match('/[a-z]/', $new_password) && preg_match('/[0-9]/', $new_password)) {
                                    $user->password = Hash::make($request->input("new_password"));
                                    $user->save();
                                    return response("Ok", 200);
                                }
                                return response('Bad request: bad password schema', 404);
                            }
                            return response('Bad request: not enough character', 403);
                        }
                        return response('Bad request: same password', 405);
                    }
                    return response('Bad request: bad confirm password', 402);
                }
                return response('Bad request: bad old password', 401);
            } catch (\Exception $e) {
                return response('Bad request:' . $e->getMessage(), 400);
            }
        } else {
            return response('Only PATCH method allowed', 405);
        }
    }




    public function votingTopics()
    {
        return VotingTopic::all()->where("enable", "=", 1);
    }
}
