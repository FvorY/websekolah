<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;

use App\d_user;

use Validator;
use Session;
use DB;
use Mail;
use Auth;

class loginController extends Controller
{

    public function __construct()
    {
        $this->middleware('guest')->except(['logout']);

        $this->middleware('auth')->only(['logout']);
    }

    public function index()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        return DB::transaction(function () use ($request) {

            $rules = array(
                'username' => 'required', // make sure the email is an actual email
                'password' => 'required' // password can only be alphanumeric and has to be greater than 3 characters
            );

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {

                $response = [
                    'status' => 'gagal',
                    'content' => $validator->errors()->all()
                ];

                return redirect('/');

            } else {
                $username = $request->username;
                $password = $request->password;

                $user = d_user::where(DB::raw('BINARY u_username'), $request->username)->first();
                if ($user && $user->u_username == $request->password) {

                    Session::set('username', $user->u_username);

                    Auth::login($user); //set login

                    $response = [
                        'status' => 'sukses',
                        'content' => 'authenticate'
                    ];

                    return json_encode($response);

                } else {
                    $response = [
                        'status' => 'gagal',
                        'content' => 'Inputan Nama dan Password Tidak Sesuai !'
                    ];

                    return json_encode($response);
                }
            }
        });
    }


    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }

}
