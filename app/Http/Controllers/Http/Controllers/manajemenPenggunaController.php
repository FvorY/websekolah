<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\d_mem;

use App\d_access;

class manajemenPenggunaController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }
     public function index() {
        $member=d_mem::paginate(8);        
        return view('manajemen-pengguna.index',compact('member'));
    }
    public function edit($id) {
        $member     =d_mem::where('m_id',$id)->first();        
        $acces      =d_access::select('a_id','a_type')->get();              
        return view('manajemen-pengguna.edit',compact('acces','member'));
    }
}
