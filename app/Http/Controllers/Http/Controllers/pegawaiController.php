<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\d_pegawai;

use Yajra\Datatables\Datatables;

use Validator;

use DB;

class pegawaiController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }
    public function index() {
        return view('pegawai.index');
    } 
    public function data() {    
        DB::statement(DB::raw('set @rownum=0'));
        $pegawai = d_pegawai::select(DB::raw('@rownum  := @rownum  + 1 AS number'),'d_pegawai.*')->get();
        return Datatables::of($pegawai)
                       ->addColumn('action', function ($pegawai) {
                            return' <div class="dropdown">                                
                                            <button class="btn btn-primary btn-flat btn-xs dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                Kelola
                                                <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                                <li><a href="data-pegawai/' . $pegawai->p_id .'/edit" ><i class="fa fa-pencil" aria-hidden="true"></i>Edit Data</a></li>
                                                <li role="separator" class="divider"></li>                                                                                                                                                                         
                                                <li><a class="btn-delete" onclick="hapus('.$pegawai->p_id.')"></i>Hapus Data</a></li>                                                
                                            </ul>
                                        </div>';
                        })
                        ->make(true);
    }
    public function tambah() {
        return view('pegawai.formTambah');
    }
    public function simpan(Request $request) {   
      return DB::transaction(function() use ($request) { 
           $request->Tanggal_Lahir=date('Y-m-d', strtotime($request->Tanggal_Lahir)); 
           $request->Tanggal_Masuk_Kerja=date('Y-m-d', strtotime($request->Tanggal_Masuk_Kerja)); 
          
           $rules = [
                'NIK' => 'required',
                'Nama_Lengkap' => 'required',
                'Jenis_Kelamin' => 'required',               
                'Tempat_Lahir' => 'required',               
                'Tanggal_Lahir' => 'required|date',
                'Alamat' => 'required',
                'No_Telp' => 'required',
                'Nama_Ibu' => 'required',
                'Pendidikan' => 'required',
                'Tanggal_Masuk_Kerja' => 'required|date',
                'No_KTP' => 'required',
                'No_Rekening' => 'required',
                'No_KPK' => 'required',
                'No_JP' => 'required',                
                'No_KPJ' => 'required',                
            ];
           
      $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                        'status' => 'gagal',
                        'data' => $validator->errors()->toArray()
            ]);
        }
        	
	
      $id=d_pegawai::max('p_id')+1;      
      d_pegawai::create([          
        "p_id"=>$id,
        "p_nik"=>$request->NIK,
        "p_nama_lengkap"=>$request->Nama_Lengkap,
        "p_jenis_kelamin"=>$request->Jenis_Kelamin,
        "p_tempat_lahir"=>$request->Tempat_Lahir,
        "p_tgl_lahir"=>$request->Tanggal_Lahir,
        "p_alamat"=>$request->Alamat,
        "p_notelp"=>$request->No_Telp,
        "p_nama_ibu"=>$request->Nama_Ibu,
        "p_pendidikan"=>$request->Pendidikan,
        "p_tgl_masuk_kerja"=>$request->Tanggal_Masuk_Kerja,
        "p_no_ktp"=>$request->No_KTP,
        "p_no_rekening"=>$request->No_Rekening,
        "p_no_kpk"=>$request->No_KPK,
        "p_no_jp"=>$request->No_JP,
        "p_no_kpj"=>$request->No_KPJ,        
      ]); 
      
       return response()->json([
                        'status' => 'berhasil',                        
            ]);
      
    });
    }
    public function edit($id) {        
        $pegawai=d_pegawai::where('p_id',$id)->first();        
        return view('pegawai.formEdit',compact('pegawai'));
    }
    public function perbarui(Request $request,$id) {        
        return DB::transaction(function() use ($request,$id) { 
            
        $pegawai=d_pegawai::where('p_id',$id);
           $rules = [
                'NIK' => 'required',
                'Nama_Lengkap' => 'required',
                'Jenis_Kelamin' => 'required',               
                'Tempat_Lahir' => 'required',               
                'Tanggal_Lahir' => 'required',
                'Alamat' => 'required',
                'No_Telp' => 'required',
                'Nama_Ibu' => 'required',
                'Pendidikan' => 'required',
                'Tanggal_Masuk_Kerja' => 'required',
                'No_KTP' => 'required',
                'No_Rekening' => 'required',
                'No_KPK' => 'required',
                'No_JP' => 'required',                
                'No_KPJ' => 'required',                
            ];
              $validator = Validator::make($request->all(), $rules);

                if ($validator->fails()) {
                    return response()->json([
                                'status' => 'gagal',
                                'data' => $validator->errors()->toArray()
                    ]);
                }                
                 $pegawai->update([                                                 
                    "p_nik"=>$request->NIK,
                    "p_nama_lengkap"=>$request->Nama_Lengkap,
                    "p_jenis_kelamin"=>$request->Jenis_Kelamin,
                    "p_tempat_lahir"=>$request->Tempat_Lahir,
                    "p_tgl_lahir"=>$request->Tanggal_Lahir,
                    "p_alamat"=>$request->Alamat,
                    "p_notelp"=>$request->No_Telp,
                    "p_nama_ibu"=>$request->Nama_Ibu,
                    "p_pendidikan"=>$request->Pendidikan,
                    "p_tgl_masuk_kerja"=>$request->Tanggal_Masuk_Kerja,
                    "p_no_ktp"=>$request->No_KTP,
                    "p_no_rekening"=>$request->No_Rekening,
                    "p_no_kpk"=>$request->No_KPK,
                    "p_no_jp"=>$request->No_JP,
                    "p_no_kpj"=>$request->No_KPJ,                           
                  ]); 
                 
                  return response()->json([
                        'status' => 'berhasil',                        
            ]);
        });
    }
    public function hapus($id) {
        return DB::transaction(function() use ($id) { 
            $pegawai=d_pegawai::where('p_id',$id);
            if($pegawai->delete()){
                   return response()->json([
                        'status' => 'berhasil',                        
                    ]);
            }
        });
        
    }
}
