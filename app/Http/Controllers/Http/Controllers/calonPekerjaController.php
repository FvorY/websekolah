<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\d_calon_pekerja;

use Yajra\Datatables\Datatables;

use Validator;

use DB;

use Carbon\carbon;

class calonPekerjaController extends Controller
{
    
    public function __construct() {
        $this->middleware('auth');
    }
    public function index() {
        return view('calon_pekerja.index');
    } 
    public function data() {    
        DB::statement(DB::raw('set @rownum=0'));
        
        $pekerja = DB::table('d_calon_pekerja')->                
                select(DB::raw('@rownum  := @rownum  + 1 AS number'),
                        'd_calon_pekerja.*')->get();
         $pekerja= collect($pekerja);
//         $tgl=Carbon::createFromFormat('Y-m-d',$pekerja->p_tgl_lahir);
// return $tgl->age; 
         
        return Datatables::of($pekerja)
                ->editColumn('cp_tgl_lahir', function ($pekerja) {
                    if ($pekerja->cp_tgl_lahir != '')
                            return $pekerja->cp_tgl_lahir ? with(new Carbon($pekerja->cp_tgl_lahir))->format('d-M-Y') : '';
                    if ($pekerja->cp_tgl_lahir == '')
                            return '-';
                        
                    })
                ->editColumn('cp_expired_ktp', function ($pekerja) {
                    if ($pekerja->cp_expired_ktp != '')
                            return $pekerja->cp_expired_ktp ? with(new Carbon($pekerja->cp_expired_ktp))->format('d-M-Y') : '';
                    if ($pekerja->cp_expired_ktp == '')
                            return '-';
                    })
                 ->editColumn('cp_jenis_kelamin', function ($pekerja) {
                            if ($pekerja->cp_jenis_kelamin == 'L')
                                return 'Laki-Laki';
                            if ($pekerja->cp_jenis_kelamin == 'P')
                                return 'Perempuan';
                        })
                       ->addColumn('action', function ($pekerja) {
                            return' <div class="dropdown">                                
                                            <button class="btn btn-primary btn-flat btn-xs dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                Kelola
                                                <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                                <li><a href="data-calon-pekerja/' . $pekerja->cp_id .'/edit" ><i class="fa fa-pencil" aria-hidden="true"></i>Edit Data</a></li>
                                                <li role="separator" class="divider"></li>                                                                                                                                                                         
                                                <li><a class="btn-delete" onclick="hapus('.$pekerja->cp_id.')"></i>Hapus Data</a></li>                                                
                                            </ul>
                                        </div>';
                        })
                        ->make(true);
    }
    public function tambah() {        
        return view('calon_pekerja.formTambah');
        
    }
    public function simpan(Request $request) {   
        dd($request);
      return DB::transaction(function() use ($request) {           
           $request->Tanggal_Lahir=date('Y-m-d', strtotime($request->Tanggal_Lahir)); 
           $request->Tanggal_Masuk_Kerja=date('Y-m-d', strtotime($request->Tanggal_Masuk_Kerja)); 
           $request->Expired_KTP=date('Y-m-d', strtotime($request->Expired_KTP)); 
           
           
           $rules = [
                'No_KTP' => 'required|numeric',
                'Nama_Lengkap' => 'required',
                'Jenis_Kelamin' => 'required',                               
                'Tanggal_Lahir' => 'required|date',
                'Alamat' => 'required',
                'No_Telp' => 'required',
                'Nama_Ibu' => 'required',
                'Pendidikan' => 'required',
                'Tanggal_Masuk_Kerja' => 'required|date',
                'No_KTP' => 'required',
                'Expired_KTP' => 'required|date',
                'No_KPJ' => 'required',                
                'NIK' => 'required',                
                'Mitra' => 'required',                
            ];
           
      $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                        'status' => 'gagal',
                        'data' => $validator->errors()->toArray()
            ]);
        }
        	
	
      $id=d_calon_pekerja::max('cp_id')+1;  
      d_calon_pekerja::create([               
        "p_id"=>$id,        
        "p_mitra"=>$request->Mitra,
        "p_no_ktp"=>$request->No_KTP,
        "p_nama"=>$request->Nama_Lengkap,
        "p_jenis_kelamin"=>$request->Jenis_Kelamin,          
        "p_tgl_lahir"=>$request->Tanggal_Lahir,
        "p_alamat"=>$request->Alamat,
        "p_no_hp"=>$request->No_Telp,
        "p_expired_ktp"=>$request->Expired_KTP,
        "p_pendidikan"=>$request->Pendidikan,
        "p_nama_ibu"=>$request->Nama_Ibu,
        "p_nik"=>$request->NIK,
        "p_tgl_masuk_kerja"=>$request->Tanggal_Masuk_Kerja,
        "p_no_kpj"=>$request->No_KPJ,                      
      ]); 
      
       return response()->json([
                        'status' => 'berhasil',                        
            ]);
      
    });
    }
    public function edit($id) {        
        $pekerja=d_calon_pekerja::where('cp_id',$id)->first();          
        return view('calon_pekerja.formEdit',compact('pekerja'));
    }
    public function perbarui(Request $request,$id) {        
        return DB::transaction(function() use ($request,$id) { 
           $request->Tanggal_Lahir=date('Y-m-d', strtotime($request->Tanggal_Lahir));            
           $request->Expired_KTP=date('Y-m-d', strtotime($request->Expired_KTP)); 
           
            
        $pekerja=d_calon_pekerja::where('cp_id',$id);
        
           $rules = [
                'No_KTP' => 'required',
                'Nama_Lengkap' => 'required',
                'Jenis_Kelamin' => 'required',                               
                'Tanggal_Lahir' => 'required|date',
                'Alamat' => 'required',
                'No_Telp' => 'required',
                'Nama_Ibu' => 'required',
                'Pendidikan' => 'required',                
                'No_KTP' => 'required',
                'Expired_KTP' => 'required|date',
                'No_KPJ' => 'required',                
                'NIK' => 'required',                                               
            ];
              $validator = Validator::make($request->all(), $rules);

                if ($validator->fails()) {
                    return response()->json([
                                'status' => 'gagal',
                                'data' => $validator->errors()->toArray()
                    ]);
                }     
                
                 $pekerja->update([                                                                                               
                    "cp_no_ktp"=>$request->No_KTP,
                    "cp_nama"=>$request->Nama_Lengkap,
                    "cp_jenis_kelamin"=>$request->Jenis_Kelamin,          
                    "cp_tgl_lahir"=>$request->Tanggal_Lahir,
                    "cp_alamat"=>$request->Alamat,
                    "cp_no_hp"=>$request->No_Telp,
                    "cp_expired_ktp"=>$request->Expired_KTP,
                    "cp_pendidikan"=>$request->Pendidikan,
                    "cp_nama_ibu"=>$request->Nama_Ibu,
                    "cp_nik"=>$request->NIK,                    
                    "cp_no_kpj"=>$request->No_KPJ,                              
                  ]); 
                 
                  return response()->json([
                        'status' => 'berhasil',                        
            ]);
        });
    }
    public function hapus($id) {
        return DB::transaction(function() use ($id) { 
            $pekerja=d_calon_pekerja::where('p_id',$id);
            if($pekerja->delete()){
                   return response()->json([
                        'status' => 'berhasil',                        
                    ]);
            }
        });
        
    }
}


