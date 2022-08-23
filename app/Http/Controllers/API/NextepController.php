<?php

namespace App\Http\Controllers\API;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
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

    public function is2fa(Request $request)
    {
        $user = User::findOrFail(Auth::user()->user_id);
        return $user->two_factor_auth;
    }

    public function tfa(){
        $user = User::findOrFail(Auth::user()->user_id);

        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->Mailer = "smtp";

        $mail->SMTPDebug  = 1;
        $mail->SMTPAuth   = TRUE;
        $mail->SMTPSecure = "tls";
        $mail->Port       = 587;
        $mail->Host       = "smtp.gmail.com";
        $mail->Username   = env("MAIL_USERNAME");
        $mail->Password   = env("MAIL_PASSWORD");
        $mail->IsHTML(true);
        $mail->AddAddress($user->email, "recipient-name");

        $code = rand(000000, 999999);

        $mail->Subject = "Nextep's 2Fa Code";
        $content = "bonjour " . $user->email . "<br><br>Voici votre code à usage unique : <b>" . $code . "</b><br><br> Si vous n’avez demandé aucun code, vous pouvez ignorer cet e-mail. Un autre utilisateur a peut-être indiqué votre adresse e-mail par erreur.<br><br>Merci,<br>L’équipe Nextep";

        $mail->MsgHTML($content);
        $mail->send();

        return $code;
    }

    public function tfa_check(Request $request){
        if($request->input("mail_code") == $request->input("code")){
            return response('correct', 200);
        }
        return response('bad code', 400);
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

            $user->two_factor_auth = $request->tfa;
            $user->save();

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
