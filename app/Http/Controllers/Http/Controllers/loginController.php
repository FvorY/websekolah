<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\d_mem;
use App\d_comp;
use App\d_mem_comp;
use App\d_mem_email;
use App\d_comp_year;
use App\d_comp_coa;
use App\d_mem_log;
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
            $request->username = nama($request->username);
            
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

                return json_encode($response);
            } else {
                $username = $request->username;
                $password = $request->password;

                $user = d_mem::where(DB::raw('BINARY m_username'), $request->username)->first();
                if ($user && $user->m_passwd == sha1(md5('passwordAllah') . $request->password)) {
                    $userCompany = $user->company($user->m_id);

                    if ($userCompany == null) {
                        $response = [
                            'status' => 'gagal',
                            'content' => 'Perusahaan'
                        ];
                        return json_encode($response);
                    }
                    Session::set('mem', $userCompany->mc_mem);
                    $getJabatan = DB::table('d_mem')
                        ->leftJoin('d_jabatan', 'm_jabatan', '=', 'j_id')
                        ->select('m_jabatan', 'j_name')
                        ->where('m_id', '=', Session::get('mem'))
                        ->first();
                    Session::set('mem_comp', $userCompany->c_id);
                    Session::set('mem_year', $userCompany->y_year);
                    Session::set('jabatan', $getJabatan->m_jabatan);
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
