<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\d_group;

use App\d_group_access;

use App\d_access;

use DB;

class aksesGroupController extends Controller
{
     public function __construct(){
        $this->middleware('auth');
    }
    public function index() {
        $group=d_group::get();
        return view('manajemen-akses-group.index',compact('group'));
    }
    public function detailGroup() { 
        $aksesGroup=DB::table('d_group')->join('d_group_access','d_group_access.ga_group','=','d_group.g_id')
                ->join('d_access','d_access.a_id','=','d_group_access.ga_access')
                ->select(DB::raw('group_concat(d_access.a_type) as akses'),'g_id','g_name')
                ->groupBy('g_id')->orderBy('g_name','ASC')
                ->get(); 
        //dd($aksesGroup);
         return view('manajemen-akses-group.detail_group',compact('aksesGroup'));        
    }
    public function tambah() {
        $access=d_access::select('a_id','a_type')->get();
        return view('manajemen-akses-group.formTambah',compact('access'));    
    }
    public function simpan(Request $request) { 
        
         return DB::transaction(function() use ($request) {
             
                $id_group=d_group::max('g_id')+1;        
                d_group::create([
                    'g_id'      =>$id_group,
                    'g_name'    =>$request->Nama_Group
                ]);
                for ($index = 0; $index < count($request->jumlah); $index++) {            
                    $menu="menu".$index;
                    $level="level".$index;
                      d_group_access::create([
                        'ga_group'=>$id_group,
                        'ga_access'=>$request->$menu,
                        'ga_level'=>$request->$level,
                      ]);
                }   
             
        });
       
    }
    public function edit($id) {
          $aksesGroup=DB::table('d_group')->join('d_group_access','d_group_access.ga_group','=','d_group.g_id')
                ->join('d_access','d_access.a_id','=','d_group_access.ga_access')
                ->select('a_id','d_access.a_type','g_id','g_name','d_group_access.ga_level')
                ->where('g_id',$id)                
                ->get();
          
        return view('manajemen-akses-group.formEdit',compact('aksesGroup'));
    }
}
