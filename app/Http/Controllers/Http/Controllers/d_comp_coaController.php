<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\d_comp_coa;
use App\d_comp_trans;
use App\d_mem_comp;
use App\d_comp;
use DB;
use Auth;
use Validator;
use Session;

class d_comp_coaController extends Controller {
      public function __construct(){
        $this->middleware('auth');
    }
    public function index() {
        $comp = Session::get('mem_comp');
        $year = Session::get('mem_year');

        $harta = DB::select(DB::raw("select *,SUBSTRING(coa_code,1,1) as coa_kategori from d_comp_coa where coa_comp='$comp' and coa_year='$year' and SUBSTRING(coa_code,1,1)='1'order by coa_code")); //total             
        $kewajiban = DB::select(DB::raw("select *,SUBSTRING(coa_code,1,1) as coa_kategori from d_comp_coa where coa_comp='$comp' and coa_year='$year' and  SUBSTRING(coa_code,1,1)='2'order by coa_code")); //total                     
        $modals = DB::select(DB::raw("select *,SUBSTRING(coa_code,1,1) as coa_kategori from d_comp_coa where coa_comp='$comp' and coa_year='$year' and  SUBSTRING(coa_code,1,1)='3'order by coa_code")); //total                             

        return view('master_akun.index', compact('harta', 'kewajiban', 'modals'));
    }

    public function create() {        
        $comp = Session::get('mem_comp');
        $year = Session::get('mem_year');
        $coa_level1 = d_comp_coa::select('coa_code', 'coa_name')->where('coa_level', '=', '1')->where('coa_comp', '=', $comp)->where('coa_year', '=', $year)->get();
        return view('master_akun.create', compact('coa_level1'));
    }

    public function store(Request $req) {
           return DB::transaction(function() use ($req) { 
       // dd($req);
        //dd(str_replace(['Rp', '\\', '/', '*','.',' '], '', $req->angka3));
               
        $comp = Session::get('mem_comp');
        $year = Session::get('mem_year');        
        $rules = array(
            'Level_COA' => 'required',
            'Kode' => 'required|max:4',
            'Nama_Akun' => 'required',
            'Tahun_Akun' => 'required',
            'Pembukaan_Akun' => 'required',
        );


        $chekKodeAkun = d_comp_coa::where('coa_code', '=', $req->Kode_Akun)->where('coa_comp', '=', $comp)->where('coa_year', '=', $year)->first();
        $chekNamaAkun = d_comp_coa::where('coa_name', '=', $req->Nama_Akun)->where('coa_comp', '=', $comp)->where('coa_year', '=', $year)->first();

      
        $req->Coa_Opening_Tgl = date('Y-m-d', strtotime($req->Coa_Opening_Tgl));
        $validator = Validator::make($req->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                        'status' => 'error',
                        'data' => $validator->errors()->toArray()
            ]);
        } else if ($chekKodeAkun) {
            $validator->getMessageBag()->add('login', 'Kode Akun Sudah Digunakan'); //manual validation                           
             return response()->json([
                'status' => 'error',
                'data'   => $validator->errors()->toArray()
                ]);
        }else if ($chekNamaAkun) {
            $validator->getMessageBag()->add('login', 'Nama Akun Sudah Ada'); //manual validation                           
             return response()->json([
                'status' => 'error',
                'data'   => $validator->errors()->toArray()
                ]);
        }
        else {
            if ($req->Pembukaan_Akun != '') {
                $akunInduk = 0;
                $punyaSaldo = 1;
            } else {
                $akunInduk = 0;
                $punyaSaldo = 0;
            }

            $coaParent = d_comp_coa::where('coa_code', '=', $req->Parent_COA)->where('coa_comp', '=', $comp)->where('coa_year', '=', $year);            
            $chekJUmlahCoaParent = d_comp_coa::where('coa_parent', '=', $req->Parent_COA)->where('coa_comp', '=', $comp)->where('coa_year', '=', $year)->get();               
            $req->Pembukaan_Akun=str_replace(['Rp', '\\','.',' '], '', $req->Pembukaan_Akun);            
            if (count($chekJUmlahCoaParent)==0) {               
                $d_comp_coa = new d_comp_coa;
                $d_comp_coa->coa_comp = $comp;
                $d_comp_coa->coa_year = $year;
                $d_comp_coa->coa_code = $req->Kode_Akun;
                $d_comp_coa->coa_name = $req->Nama_Akun;
                $d_comp_coa->coa_level = $req->Level_COA;
                $d_comp_coa->coa_parent = $req->Parent_COA;
                $d_comp_coa->coa_isparent = $akunInduk;
                $d_comp_coa->coa_isactive = $punyaSaldo;
                $d_comp_coa->coa_opening_tgl = $req->Coa_Opening_Tgl;
                $d_comp_coa->coa_opening = $req->Pembukaan_Akun+$coaParent->first()->coa_opening;
                $d_comp_coa->save();
                
                 $coaParent->update([
                    'coa_isparent' => 1,
                    'coa_isactive' => 0,
                    'coa_opening'  =>0
                ]);
                 
                 
                 if(substr($req->Kode_Akun,0,1)==1 && $req->Pembukaan_Akun!=0){
                    $coaBalence = d_comp_coa::where('coa_name', '=','Akumulasi Laba')->where('coa_comp', '=', $comp)->where('coa_year', '=', $year);                                 
                    $coaBalence->update([
                      'coa_opening' => $coaBalence->first()->coa_opening+$req->Pembukaan_Akun 
                    ]);
                 }
                 
                 else if(substr($req->Kode_Akun,0,1)==2 && $req->Pembukaan_Akun!=0 || substr($req->Kode_Akun,0,1)==3 && $req->Pembukaan_Akun!=0){
                    $coaBalence = d_comp_coa::where('coa_name', '=','Akumulasi Laba')->where('coa_comp', '=', $comp)->where('coa_year', '=', $year);                                 
                    $coaBalence->update([
                      'coa_opening' => $coaBalence->first()->coa_opening-$req->Pembukaan_Akun 
                    ]);
                 }

                $mem_comp = d_mem_comp::where('mc_mem', '=', Auth::user()->m_id)->where('mc_active', '=', 1);

                if ($mem_comp->first()->mc_step == 0) {
                    $mem_comp->update([
                        'mc_step' => 1
                    ]);
                }
            } else if(count($chekJUmlahCoaParent)>0) {                
                $d_comp_coa = new d_comp_coa;
                $d_comp_coa->coa_comp = $comp;
                $d_comp_coa->coa_year = $year;
                $d_comp_coa->coa_code = $req->Kode_Akun;
                $d_comp_coa->coa_name = $req->Nama_Akun;
                $d_comp_coa->coa_level = $req->Level_COA;
                $d_comp_coa->coa_parent = $req->Parent_COA;
                $d_comp_coa->coa_isparent = $akunInduk;
                $d_comp_coa->coa_isactive = $punyaSaldo;
                $d_comp_coa->coa_opening_tgl = $req->Coa_Opening_Tgl;
                $d_comp_coa->coa_opening = $req->Pembukaan_Akun;
                $d_comp_coa->save();                
                $coaParent->update([
                    'coa_isparent' => 1,
                    'coa_isactive' => 0
                ]);       
                
                if(substr($req->Kode_Akun,0,1)==1 && $req->Pembukaan_Akun!=0){
                    $coaBalence = d_comp_coa::where('coa_name', '=','Akumulasi Laba')->where('coa_comp', '=', $comp)->where('coa_year', '=', $year);                                 
                    $coaBalence->update([
                      'coa_opening' => $coaBalence->first()->coa_opening+$req->Pembukaan_Akun 
                    ]);
                 }
                 
                 else if(substr($req->Kode_Akun,0,1)==2 && $req->Pembukaan_Akun!=0 || substr($req->Kode_Akun,0,1)==3 && $req->Pembukaan_Akun!=0){
                    $coaBalence = d_comp_coa::where('coa_name', '=','Akumulasi Laba')->where('coa_comp', '=', $comp)->where('coa_year', '=', $year);                                 
                    $coaBalence->update([
                      'coa_opening' => $coaBalence->first()->coa_opening-$req->Pembukaan_Akun 
                    ]);
                 }
      
                
            }
        }

         return response()->json([
                'status' => 'berhasil'                
                ]);
           });
    }

    public function edit($id) {   
           return DB::transaction(function() use ($id) {                
        $comp = Session::get('mem_comp');
        $year= Session::get('mem_year');          
        $chekCoaParrent = d_comp_coa::where('coa_parent', '=', $id)->where('coa_year', '=', $year)->where('coa_comp', '=', $comp)->get();               
        $comp_coas = d_comp_coa::where('coa_comp', '=', $comp)->where('coa_year', '=', $year)->where('coa_code', '=', $id)->first();     
        return view('master_akun.edit', compact('comp_coas', 'coa_codes', 'chekCoaParrent'));
           });
    }

    public function update(Request $req, $id) {
        //dd($req);
        
        $year = Session::get('mem_year');
        $comp = Session::get('mem_comp');
        $comp_coas = d_comp_coa::where('coa_comp', '=', $comp)->where('coa_year', '=', $year)->where('coa_code', '=', $id);
        $rules = array(
            'Kode_Akun' => 'required', // make sure the email is an actual email
            'Nama_Akun' => 'required|min:2',
            'Coa_Opening_Tgl' => 'required',
        );
        $req->Coa_Opening_Tgl = date('Y-m-d', strtotime($req->Coa_Opening_Tgl));
        $validator = Validator::make($req->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                        'status' => 'error',
                        'data' => $validator->errors()->toArray()
            ]);
        } else {
            if ($req->Pembukaan_Akun != '') {
                $akunInduk = 0;
                $punyaSaldo = 1;
            } else {
                $akunInduk = 1;
                $punyaSaldo = 0;
            }           
            $hasil=0;
            $req->Pembukaan_Akun=str_replace(['Rp', '\\','.',' '], '', $req->Pembukaan_Akun);                        
            if(substr($req->Kode_Akun,0,1)==1){       
                    $coaBalence = d_comp_coa::where('coa_name', '=','Akumulasi Laba')->where('coa_comp', '=', $comp)->where('coa_year', '=', $year);                                 
                    $saldo=$coaBalence->first()->coa_opening-$comp_coas->first()->coa_opening;    
                    $hasil=$saldo+$req->Pembukaan_Akun ;
                    $coaBalence->update([
                      'coa_opening' =>$hasil
                    ]);                   
                 }
                 
                 else if(substr($req->Kode_Akun,0,1)==2 || substr($req->Kode_Akun,0,1)==3){                     
                    $coaBalence = d_comp_coa::where('coa_name', '=','Akumulasi Laba')->where('coa_comp', '=', $comp)->where('coa_year', '=', $year);                                 
                    $saldo=$coaBalence->first()->coa_opening+$comp_coas->first()->coa_opening;   
                   $hasil=$saldo-$req->Pembukaan_Akun ;
                    $coaBalence->update([
                      'coa_opening' =>$hasil
                    ]);
                 }            
                 
            $comp_coas->update([
                'coa_comp' => $comp,
                'coa_year' => $year,
                'coa_code' => $req->Kode_Akun,
                'coa_name' => $req->Nama_Akun,
                'coa_isparent' => $akunInduk,
                'coa_isactive' => $punyaSaldo,
                //'coa_opening_tgl' => $req->Coa_Opening_Tgl,
                'coa_opening' => $req->Pembukaan_Akun
            ]);
            
            
             
        }        
        return response()->json([
                        'status' => 'sukses',
                        'akumulasi' => number_format($hasil,2,',','.')
            ]);
    }

    public function delete($id) {        
        $comp = Session::get('mem_comp');        
        $year = Session::get('mem_year');
        $chek = d_comp_trans::where('tr_year', '=', $year)->where('tr_comp', '=', $comp)->where('tr_acc01', '=', $id)->orWhere('tr_acc02', $id)->first();

        if ($chek == null) {
            $hasil=0;
            $d_comp_coa=d_comp_coa::where('coa_year', '=', $year)->where('coa_comp', '=', $comp)->where('coa_code', '=', $id);
            
              
            if(substr($id,0,1)==1){   
                
                    $coaBalence = d_comp_coa::where('coa_name', '=','Akumulasi Laba')->where('coa_comp', '=',$comp)->where('coa_year', '=', $year);                                 
                    
                   
                    
                    
                    $saldo=$coaBalence->first()->coa_opening-$d_comp_coa->first()->coa_opening;                                        
                    $coaBalence->update([
                      'coa_opening' =>$saldo 
                    ]);  
                    $d_comp_coa->delete();
                     $hasil=$coaBalence->first()->coa_opening;                    
                 }
                 
                 else if(substr($id,0,1)==2||
                         substr($id,0,1)==3  ){                     
                    $coaBalence = d_comp_coa::where('coa_name', '=','Akumulasi Laba')->where('coa_comp', '=', $comp)->where('coa_year', '=', $year);                                 
                    $saldo=$coaBalence->first()->coa_opening+$d_comp_coa->first()->coa_opening;                    
                    $coaBalence->update([
                      'coa_opening' =>$saldo 
                    ]);   
                    $d_comp_coa->delete();
                     $hasil=$coaBalence->first()->coa_opening;
                 }
            return response()->json([
                        'success' => 'sukses',
                        'akumulasi' => number_format($hasil,2,',','.')
            ]);
        }if ($chek != null) {
            return 'gagal';
        }
    }

    public function coa_level($id) {
           return DB::transaction(function() use ($id) { 
        $comp = Session::get('mem_comp');
        $year = Session::get('mem_year');
        $coa_level;
        if ($id == 2) {
            $coa_level1 = d_comp_coa::select('coa_code', 'coa_name')->where('coa_level', '=', '1')->where('coa_comp', '=', $comp)->where('coa_year', '=', $year)->get();
            $coa_level = $coa_level1;
        } else if ($id == 3) {
            $coa_level2 = d_comp_coa::select('coa_code', 'coa_name')->where('coa_level', '=', '2')->where('coa_comp', '=', $comp)->where('coa_year', '=', $year)->get();
            $coa_level = $coa_level2;
        } else if ($id == 4) {
            $coa_level3 = d_comp_coa::select('coa_code', 'coa_name')->where('coa_level', '=', '3')->where('coa_comp', '=', $comp)->where('coa_year', '=', $year)->get();
            $coa_level = $coa_level3;
        }

        $htmlData = '<select class="form-control"  id="Parent_COA" name="Parent_COA" onchange="hapus()">';
        foreach ($coa_level as $coa_level) {

            //$htmlData = $htmlData . '<option value="' . $coa_level->coa_code .'" if(old("Parent_COA")=="'.$coa_level->coa_code.'" selected=selected)>' . $coa_level->coa_name . '</option>';
            $htmlData = $htmlData . '<option value="' . $coa_level->coa_code . '" selected>' . $coa_level->coa_name . '</option>';
        }
        $htmlData = $htmlData . '</select>';
        return $htmlData;
           });
    }

    public function create_sub_akun($id) {
   return DB::transaction(function() use ($id) { 
        $comp = Session::get('mem_comp');
        $year = Session::get('mem_year');


        $coa = d_comp_coa::select('coa_code', 'coa_name', 'coa_level')->where('coa_code', '=', $id)->where('coa_comp', '=', $comp)->where('coa_year', '=', $year)->first();

        // $coa_level = d_comp_coa::select('coa_code', 'coa_name', 'coa_level')->where('coa_year', '=', $year)->where('coa_comp', '=', $comp)->where('coa_code', '=', $id)->first();
        $code = $coa->coa_code;
        $level = $coa->coa_level + 1;
        $kode = $this->generateId($code, $level);
        
        //$this->generateId();
        return view('master_akun.create_sub_akun', compact('coa_level', 'coa', 'kode'));
   });
    }

    public function generate_akun() {
        return DB::transaction(function() {         
        $year= Session::get('mem_year');         
        $comp=Session::get('mem_comp'); 
        
        $aset = array(
            array(
                'coa_comp' => $comp,
                'coa_year' => $year,
                'coa_code' => '100000000',
                'coa_name' => 'Aset',
                'coa_level' => 1,
                'coa_parent' => '',
                'coa_isparent' => '1',
                'coa_isactive' => '0',
                'coa_opening_tgl' => date('Y-m-d'),
                'coa_opening' => 0,
                'coa_default' => 1
            ),
            array(
                'coa_comp' => $comp,
                'coa_year' => $year,
                'coa_code' => '101000000',
                'coa_name' => 'Aset Lancar',
                'coa_level' => 2,
                'coa_parent' => '100000000',
                'coa_isparent' => '1',
                'coa_isactive' => '0',
                'coa_opening_tgl' => date('Y-m-d'),
                'coa_opening' => 0,
                'coa_default' => 1
            ),
            array(
                'coa_comp' => $comp,
                'coa_year' => $year,
                'coa_code' => '101010000',
                'coa_name' => 'Kas Dan Setara Kas',
                'coa_level' => 3,
                'coa_parent' => '101000000',
                'coa_isparent' => '1',
                'coa_isactive' => '0',
                'coa_opening_tgl' => date('Y-m-d'),
                'coa_opening' => 0,
                'coa_default' => 1
                
            ), array(
                'coa_comp' => $comp,
                'coa_year' => $year,
                'coa_code' => '101010003',
                'coa_name' => 'Kas Pimpinan',
                'coa_level' => 4,
                'coa_parent' => '101010000',
                'coa_isparent' => '1',
                'coa_isactive' => '0',
                'coa_opening_tgl' => date('Y-m-d'),
                'coa_opening' => 0,
                'coa_default' => 1
                
            ), array(
                'coa_comp' => $comp,
                'coa_year' => $year,
                'coa_code' => '101010002',
                'coa_name' => 'Kas Kasir',
                'coa_level' => 4,
                'coa_parent' => '101010000',
                'coa_isparent' => '1',
                'coa_isactive' => '0',
                'coa_opening_tgl' => date('Y-m-d'),
                'coa_opening' => 0,
                'coa_default' => 1
                
            ), array(
                'coa_comp' => $comp,
                'coa_year' => $year,
                'coa_code' => '101010001',
                'coa_name' => 'Kas Pusat',
                'coa_level' => 4,
                'coa_parent' => '101010000',
                'coa_isparent' => '1',
                'coa_isactive' => '0',
                'coa_opening_tgl' => date('Y-m-d'),
                'coa_opening' => 0,
                'coa_default' => 1
                
            ), array(
                'coa_comp' => $comp,
                'coa_year' => $year,
                'coa_code' => '101030000',
                'coa_name' => 'Piutang Usaha',
                'coa_level' => 3,
                'coa_parent' => '101000000',
                'coa_isparent' => '1',
                'coa_isactive' => '0',
                'coa_opening_tgl' => date('Y-m-d'),
                'coa_opening' => 0,
                'coa_default' => 1
            ), array(
                'coa_comp' => $comp,
                'coa_year' => $year,
                'coa_code' => '101040000',
                'coa_name' => 'Piutang Non Usaha',
                'coa_level' => '3',
                'coa_parent' => '101000000',
                'coa_isparent' => '1',
                'coa_isactive' => '1',
                'coa_opening_tgl' => date('Y-m-d'),
                'coa_opening' => 0,
                'coa_default' => 0
            ), array(
                'coa_comp' => $comp,
                'coa_year' => $year,
                'coa_code' => '101050000',
                'coa_name' => 'Persediaan',
                'coa_level' => '3',
                'coa_parent' => '101000000',
                'coa_isparent' => '1',
                'coa_isactive' => '0',
                'coa_opening_tgl' => date('Y-m-d'),
                'coa_opening' => 0,
                'coa_default' => 1
            )
            , array(
                'coa_comp' => $comp,
                'coa_year' => $year,
                'coa_code' => '101020000',
                'coa_name' => 'Marketable Investment',
                'coa_level' => '3',
                'coa_parent' => '101000000',
                'coa_isparent' => '1',
                'coa_isactive' => '1',
                'coa_opening_tgl' => date('Y-m-d'),
                'coa_opening' => 0,
                'coa_default' => 0
            //aset tetap
            ), array(
                'coa_comp' => $comp,
                'coa_year' => $year,
                'coa_code' => '102000000',
                'coa_name' => 'Aset Tetap',
                'coa_level' => '2',
                'coa_parent' => '100000000',
                'coa_isparent' => '1',
                'coa_isactive' => '0',
                'coa_opening_tgl' => date('Y-m-d'),
                'coa_opening' => 0,
                'coa_default' => 1
            ), array(
                'coa_comp' => $comp,
                'coa_year' => $year,
                'coa_code' => '102010000',
                'coa_name' => 'Tanah Bangunan',
                'coa_level' => '3',
                'coa_parent' => '102000000',
                'coa_isparent' => '1',
                'coa_isactive' => '1',
                'coa_opening_tgl' => date('Y-m-d'),
                'coa_opening' => 0,
                'coa_default' => 0
            ), array(
                'coa_comp' => $comp,
                'coa_year' => $year,
                'coa_code' => '102020000',
                'coa_name' => 'Kendaraan',
                'coa_level' => '3',
                'coa_parent' => '102000000',
                'coa_isparent' => '1',
                'coa_isactive' => '1',
                'coa_opening_tgl' => date('Y-m-d'),
                'coa_opening' => 0,
                'coa_default' => 0
            ), array(
                'coa_comp' => $comp,
                'coa_year' => $year,
                'coa_code' => '102030000',
                'coa_name' => 'Mesin/Produksi',
                'coa_level' => '3',
                'coa_parent' => '102000000',
                'coa_isparent' => '1',
                'coa_isactive' => '1',
                'coa_opening_tgl' => date('Y-m-d'),
                'coa_opening' => 0,
                'coa_default' => 0                
            ), array(
                'coa_comp' => $comp,
                'coa_year' => $year,
                'coa_code' => '102040000',
                'coa_name' => 'Peralatan Kantor',
                'coa_level' => '3',
                'coa_parent' => '102000000',
                'coa_isparent' => '1',
                'coa_isactive' => '1',
                'coa_opening_tgl' => date('Y-m-d'),
                'coa_opening' => 0,
                'coa_default' => 0
            //aset tak berwujud
            ), array(
                'coa_comp' => $comp,
                'coa_year' => $year,
                'coa_code' => '103000000',
                'coa_name' => 'Aset Tak Berwujud',
                'coa_level' => '2',
                'coa_parent' => '100000000',
                'coa_isparent' => '1',
                'coa_isactive' => '0',
                'coa_opening_tgl' => date('Y-m-d'),
                'coa_opening' => 0,
                'coa_default' => 1
            )
        );
//
////kewajiban
        $kewajiban = array(
            array(
                'coa_comp' => $comp,
                'coa_year' => $year,
                'coa_code' => '200000000',
                'coa_name' => 'Kewajiban',
                'coa_level' => 1,
                'coa_parent' => '',
                'coa_isparent' => '1',
                'coa_isactive' => '0',
                'coa_opening_tgl' => date('Y-m-d'),
                'coa_opening' => 0,
                'coa_default' => 1
            ),
            array(
                'coa_comp' => $comp,
                'coa_year' => $year,
                'coa_code' => '201000000',
                'coa_name' => 'Hutang Lancar',
                'coa_level' => 2,
                'coa_parent' => '200000000',
                'coa_isparent' => '1',
                'coa_isactive' => '0',
                'coa_opening_tgl' => date('Y-m-d'),
                'coa_opening' => 0,
                'coa_default' => 1
            ),
            array(
                'coa_comp' => $comp,
                'coa_year' => $year,
                'coa_code' => '201010000',
                'coa_name' => 'Hutang Usaha',
                'coa_level' => 3,
                'coa_parent' => '201000000',
                'coa_isparent' => '1',
                'coa_isactive' => '1',
                'coa_opening_tgl' => date('Y-m-d'),
                'coa_opening' => 0,
                'coa_default' => 0
            ), array(
                'coa_comp' => $comp,
                'coa_year' => $year,
                'coa_code' => '201020000',
                'coa_name' => 'Hutang Non Usaha',
                'coa_level' => 3,
                'coa_parent' => '201000000',
                'coa_isparent' => '1',
                'coa_isactive' => '1',
                'coa_opening_tgl' => date('Y-m-d'),
                'coa_opening' => 0,
                'coa_default' => 0
            ), array(
                'coa_comp' => $comp,
                'coa_year' => $year,
                'coa_code' => '201030000',
                'coa_name' => 'Hutang Beban',
                'coa_level' => '3',
                'coa_parent' => '201000000',
                'coa_isparent' => '1',
                'coa_isactive' => '1',
                'coa_opening_tgl' => date('Y-m-d'),
                'coa_opening' => 0,
                'coa_default' => 0
            ), array(
                'coa_comp' => $comp,
                'coa_year' => $year,
                'coa_code' => '201040000',
                'coa_name' => 'Hutang Pajak',
                'coa_level' => '3',
                'coa_parent' => '201000000',
                'coa_isparent' => '1',
                'coa_isactive' => '1',
                'coa_opening_tgl' => date('Y-m-d'),
                'coa_opening' => 0,
                'coa_default' => 0
            ), array(
                'coa_comp' => $comp,
                'coa_year' => $year,
                'coa_code' => '202000000',
                'coa_name' => 'Hutang Jangka Panjang',
                'coa_level' => '2',
                'coa_parent' => '200000000',
                'coa_isparent' => '1',
                'coa_isactive' => '0',
                'coa_opening_tgl' => date('Y-m-d'),
                'coa_opening' => 0,
                'coa_default' => 1
            )
        );
        $modal = array(
            array(
                'coa_comp' => $comp,
                'coa_year' => $year,
                'coa_code' => '300000000',
                'coa_name' => 'Modal',
                'coa_level' => 1,
                'coa_parent' => '',
                'coa_isparent' => '1',
                'coa_isactive' => '0',
                'coa_opening_tgl' => date('Y-m-d'),
                'coa_opening' => 0,
                'coa_default' => 1
            ),
            array(
                'coa_comp' => $comp,
                'coa_year' => $year,
                'coa_code' => '301000000',
                'coa_name' => 'Modal Investor',
                'coa_level' => 2,
                'coa_parent' => '300000000',
                'coa_isparent' => '1',
                'coa_isactive' => '0',
                'coa_opening_tgl' => date('Y-m-d'),
                'coa_opening' => 0,
                'coa_default' => 1
            ), array(
                'coa_comp' => $comp,
                'coa_year' => $year,
                'coa_code' => '302000000',
                'coa_name' => 'Laba',
                'coa_level' => '2',
                'coa_parent' => '300000000',
                'coa_isparent' => '1',
                'coa_isactive' => '0',
                'coa_opening_tgl' => date('Y-m-d'),
                'coa_opening' => 0,
                'coa_default' => 1
            ), array(
                'coa_comp' => $comp,
                'coa_year' => $year,
                'coa_code' => '302010000',
                'coa_name' => 'Akumulasi Laba',
                'coa_level' => '3',
                'coa_parent' => '302000000',
                'coa_isparent' => '1',
                'coa_isactive' => '0',
                'coa_opening_tgl' => date('Y-m-d'),
                'coa_opening' => 0,
                'coa_default' => 1
            ), array(
                'coa_comp' => $comp,
                'coa_year' => $year,
                'coa_code' => '302020000',
                'coa_name' => 'Laba Berjalan',
                'coa_level' => '3',
                'coa_parent' => '302000000',
                'coa_isparent' => '1',
                'coa_isactive' => '0',
                'coa_opening_tgl' => date('Y-m-d'),
                'coa_opening' => 0,
                'coa_default' => 1
            )
        );
        d_comp_coa::insert($aset);
        d_comp_coa::insert($kewajiban);
        d_comp_coa::insert($modal);
    
                    
                    
          

        

            return redirect('/dashboard');
           });
        
    }

    public function update_saldo($id, Request $request) {        
        $comp = Session::get('mem_comp');
        $year = Session::get('mem_year');
        $comp_coas = d_comp_coa::where('coa_comp', '=', $comp)->where('coa_year', '=', $year)->where('coa_code', '=', $id);        
        $request->Pembukaan_Akun=str_replace(['Rp', '\\','.',' '], '', $request->Pembukaan_Akun);  
        
            $hasil=0;
            $request->Pembukaan_Akun=str_replace(['Rp', '\\','.',' '], '', $request->Pembukaan_Akun);                        
            if(substr($id,0,1)==1){       
                    $coaBalence = d_comp_coa::where('coa_name', '=','Akumulasi Laba')->where('coa_comp', '=', $comp)->where('coa_year', '=', $year);                                 
                    $saldo=$coaBalence->first()->coa_opening-$comp_coas->first()->coa_opening;    
                    $hasil=$saldo+$request->Pembukaan_Akun ;
                    $coaBalence->update([
                      'coa_opening' =>$hasil
                    ]);                   
                 }
                 
                 else if(substr($id,0,1)==2 || substr($id,0,1)==3){                     
                    $coaBalence = d_comp_coa::where('coa_name', '=','Akumulasi Laba')->where('coa_comp', '=', $comp)->where('coa_year', '=', $year);                                 
                    $saldo=$coaBalence->first()->coa_opening+$comp_coas->first()->coa_opening;   
                   $hasil=$saldo-$request->Pembukaan_Akun ;
                    $coaBalence->update([
                      'coa_opening' =>$hasil
                    ]);
                 }            
            $comp_coas->update([                
                'coa_opening' => $request->Pembukaan_Akun
            ]);
        
    }
    public function generateId($code, $level) {
        //kategori aset
        $comp = Session::get('mem_comp');
        $year = Session::get('mem_year');
        $kategori = substr($code, 0, 1);
        if ($kategori == '1' && $level == 2) {
            $aset_id = DB::select(DB::raw("select substr(coa_code,2,2) as coa from d_comp_coa "
                                    . "where substr(coa_code,4,9)=0 and  substr(coa_code,1,1)=1 and substr(coa_code,2,2)!=00"
                                    . " and coa_comp='$comp' and coa_year = '$year'"));
            $aset_min = DB::select(DB::raw("select min(substr(coa_code,2,2)) as min from d_comp_coa "
                                    . "where substr(coa_code,4,9)=0 and  substr(coa_code,1,1)=1 and substr(coa_code,2,2)!=00"
                                    . " and coa_comp='$comp' and coa_year = '$year'"));
            $aset_max = DB::select(DB::raw("select max(substr(coa_code,2,2)) as max from d_comp_coa "
                                    . "where substr(coa_code,4,9)=0 and  substr(coa_code,1,1)=1 and substr(coa_code,2,2)!=00"
                                    . " and coa_comp='$comp' and coa_year = '$year'"));
            $aset_max = $aset_max[0]->max;
            $aset_min = $aset_min[0]->min;
            $dataId = [];
            foreach ($aset_id as $index => $aset_id) {
                $dataId[$index] = $aset_id->coa;
            }
            $range = range($aset_min, $aset_max);
            foreach ($range as $key) {
                if (!in_array($key, $dataId)) {
                    return $key;
                    break;
                }
            }
            return $aset_max + 1;
        } else if ($kategori == '1' && $level == 3) {
            $idAwal = substr($code, 0, 3);
            //dd($idAwal);
            $aset_id = DB::select(DB::raw("select substr(coa_code,4,2) as coa from d_comp_coa "
                                    . "where substr(coa_code,6,9)=0 and  substr(coa_code,1,1)=1 and substr(coa_code,4,2)!=00"
                                    . " and substr(coa_code,1,3)='$idAwal' and coa_comp='$comp' and coa_year = '$year'"));
            $aset_min = DB::select(DB::raw("select min(substr(coa_code,4,2)) as min from d_comp_coa "
                                    . "where substr(coa_code,6,9)=0 and  substr(coa_code,1,1)=1 and substr(coa_code,4,2)!=00"
                                    . " and substr(coa_code,1,3)='$idAwal' and coa_comp='$comp' and coa_year = '$year'"));
            $aset_max = DB::select(DB::raw("select max(substr(coa_code,4,2)) as max from d_comp_coa"
                                    . " where substr(coa_code,6,9)=0 and  substr(coa_code,1,1)=1 and substr(coa_code,4,2)!=00"
                                    . " and substr(coa_code,1,3)='$idAwal' and coa_comp='$comp' and coa_year = '$year'"));
            $aset_max = $aset_max[0]->max;
            $aset_min = $aset_min[0]->min;
            if (count($aset_id) != 0) {
                $dataId = [];
                foreach ($aset_id as $index => $aset_id) {
                    $dataId[$index] = $aset_id->coa;
                }
                $range = range($aset_min, $aset_max);
                foreach ($range as $key) {
                    if (!in_array($key, $dataId)) {
                        return $key;
                        break;
                    }
                }
                return $aset_max + 1;
            } else {
                return 1;
            }
        } else if ($kategori == '1' && $level == 4) {
            $idAwal = substr($code, 0, 5);
            $aset_id = DB::select(DB::raw("select substr(coa_code,6,4) as coa from d_comp_coa "
                                    . "where substr(coa_code,1,1)=1 and substr(coa_code,1,5)='$idAwal'"
                                    . " and coa_comp='$comp' and coa_year = '$year'"));
            $aset_min = DB::select(DB::raw("select min(substr(coa_code,6,4)) as min from d_comp_coa "
                                    . "where substr(coa_code,1,1)=1 and substr(coa_code,1,5)='$idAwal'"
                                    . " and coa_comp='$comp' and coa_year = '$year'"));
            $aset_max = DB::select(DB::raw("select max(substr(coa_code,6,4)) as max from d_comp_coa "
                                    . "where substr(coa_code,1,1)=1 and substr(coa_code,1,5)='$idAwal'"
                                    . " and coa_comp='$comp' and coa_year = '$year'"));
            $aset_max = $aset_max[0]->max;
            $aset_min = $aset_min[0]->min;
            if (count($aset_id) != 0) {
                $dataId = [];
                foreach ($aset_id as $index => $aset_id) {
                    $dataId[$index] = $aset_id->coa;
                }
                $range = range($aset_min, $aset_max);
                foreach ($range as $key) {
                    if (!in_array($key, $dataId)) {
                        return $key;
                        break;
                    }
                }
                return $aset_max + 1;
            } else {
                return 1;
            }
        }
        //kategori kewajiban
        if ($kategori == '2' && $level == 2) {
            $kewajiban_id = DB::select(DB::raw("select substr(coa_code,2,2) as coa from d_comp_coa where substr(coa_code,4,9)=0 and  substr(coa_code,1,1)=2 and substr(coa_code,2,2)!=00"
                                    . " and coa_comp='$comp' and coa_year = '$year'"));
            $kewajiban_min = DB::select(DB::raw("select min(substr(coa_code,2,2)) as min from d_comp_coa where substr(coa_code,4,9)=0 and  substr(coa_code,1,1)=2 and substr(coa_code,2,2)!=00"
                                    . " and coa_comp='$comp' and coa_year = '$year'"));
            $kewajiban_max = DB::select(DB::raw("select max(substr(coa_code,2,2)) as max from d_comp_coa where substr(coa_code,4,9)=0 and  substr(coa_code,1,1)=2 and substr(coa_code,2,2)!=00"
                                    . " and coa_comp='$comp' and coa_year = '$year'"));
            $kewajiban_max = $kewajiban_max[0]->max;
            $kewajiban_min = $kewajiban_min[0]->min;
            $dataId = [];
            foreach ($kewajiban_id as $index => $kewajiban_id) {
                $dataId[$index] = $kewajiban_id->coa;
            }
            $range = range($kewajiban_min, $kewajiban_max);
            foreach ($range as $key) {
                if (!in_array($key, $dataId)) {
                    return $key;
                    break;
                }
            }
            return $kewajiban_max + 1;
        } else if ($kategori == '2' && $level == 3) {
            $idAwal = substr($code, 0, 3);
            //dd($idAwal);
            $kewajiban_id = DB::select(DB::raw("select substr(coa_code,4,2) as coa from d_comp_coa "
                                    . "where substr(coa_code,6,9)=0 and  substr(coa_code,1,1)=2 and substr(coa_code,4,2)!=00 and substr(coa_code,1,3)='$idAwal'"
                                    . " and coa_comp='$comp' and coa_year = '$year'"));
            $kewajiban_min = DB::select(DB::raw("select min(substr(coa_code,4,2)) as min from d_comp_coa "
                                    . "where substr(coa_code,6,9)=0 and  substr(coa_code,1,1)=2 and substr(coa_code,4,2)!=00 and substr(coa_code,1,3)='$idAwal'"
                                    . " and coa_comp='$comp' and coa_year = '$year'"));
            $kewajiban_max = DB::select(DB::raw("select max(substr(coa_code,4,2)) as max from d_comp_coa "
                                    . "where substr(coa_code,6,9)=0 and  substr(coa_code,1,1)=2 and substr(coa_code,4,2)!=00 and substr(coa_code,1,3)='$idAwal'"
                                    . " and coa_comp='$comp' and coa_year = '$year'"));
            $kewajiban_max = $kewajiban_max[0]->max;
            $kewajiban_min = $kewajiban_min[0]->min;
            if (count($kewajiban_id) != 0) {
                $dataId = [];
                foreach ($kewajiban_id as $index => $kewajiban_id) {
                    $dataId[$index] = $kewajiban_id->coa;
                }
                $range = range($kewajiban_min, $kewajiban_max);
                foreach ($range as $key) {
                    if (!in_array($key, $dataId)) {
                        return $key;
                        break;
                    }
                }
                return $kewajiban_max + 1;
            } else {
                return 1;
            }
        } else if ($kategori == '2' && $level == 4) {
            $idAwal = substr($code, 0, 5);
            $kewajiban_id = DB::select(DB::raw("select substr(coa_code,6,4) as coa from d_comp_coa "
                                    . "where substr(coa_code,1,1)=2 and substr(coa_code,1,5)='$idAwal'"
                                    . " and coa_comp='$comp' and coa_year = '$year'"));
            $kewajiban_min = DB::select(DB::raw("select min(substr(coa_code,6,4)) as min from d_comp_coa "
                                    . "where substr(coa_code,1,1)=2 and substr(coa_code,1,5)='$idAwal'"
                                    . " and coa_comp='$comp' and coa_year = '$year'"));
            $kewajiban_max = DB::select(DB::raw("select max(substr(coa_code,6,4)) as max from d_comp_coa "
                                    . "where substr(coa_code,1,1)=2 and substr(coa_code,1,5)='$idAwal'"
                                    . " and coa_comp='$comp' and coa_year = '$year'"));
            $kewajiban_max = $kewajiban_max[0]->max;
            $kewajiban_min = $kewajiban_min[0]->min;
            if (count($kewajiban_id) != 0) {
                $dataId = [];
                foreach ($kewajiban_id as $index => $kewajiban_id) {
                    $dataId[$index] = $kewajiban_id->coa;
                }
                $range = range($kewajiban_min, $kewajiban_max);
                foreach ($range as $key) {
                    if (!in_array($key, $dataId)) {
                        return $key;
                        break;
                    }
                }
                return $kewajiban_max + 1;
            } else {
                return 1;
            }
        }

        //kategori modal
        if ($kategori == '3' && $level == 2) {
            $modal_id = DB::select(DB::raw("select substr(coa_code,2,2) as coa from d_comp_coa where substr(coa_code,4,9)=0 and  substr(coa_code,1,1)=3 and substr(coa_code,2,2)!=00"
                                    . " and coa_comp='$comp' and coa_year = '$year'"));
            $modal_min = DB::select(DB::raw("select min(substr(coa_code,2,2)) as min from d_comp_coa where substr(coa_code,4,9)=0 and  substr(coa_code,1,1)=3 and substr(coa_code,2,2)!=00"
                                    . " and coa_comp='$comp' and coa_year = '$year'"));
            $modal_max = DB::select(DB::raw("select max(substr(coa_code,2,2)) as max from d_comp_coa where substr(coa_code,4,9)=0 and  substr(coa_code,1,1)=3 and substr(coa_code,2,2)!=00"
                                    . " and coa_comp='$comp' and coa_year = '$year'"));
            $modal_max = $modal_max[0]->max;
            $modal_min = $modal_min[0]->min;
            $dataId = [];
            foreach ($modal_id as $index => $modal_id) {
                $dataId[$index] = $modal_id->coa;
            }
            $range = range($modal_min, $modal_max);
            foreach ($range as $key) {
                if (!in_array($key, $dataId)) {
                    return $key;
                    break;
                }
            }
            return $modal_max + 1;
        } else if ($kategori == '3' && $level == 3) {
            $idAwal = substr($code, 0, 3);
            //dd($idAwal);
            $modal_id = DB::select(DB::raw("select substr(coa_code,4,2) as coa from d_comp_coa where substr(coa_code,6,9)=0 and  substr(coa_code,1,1)=3 and substr(coa_code,4,2)!=00 and substr(coa_code,1,3)='$idAwal'"
                                    . " and coa_comp='$comp' and coa_year = '$year'"));
            $modal_min = DB::select(DB::raw("select min(substr(coa_code,4,2)) as min from d_comp_coa where substr(coa_code,6,9)=0 and  substr(coa_code,1,1)=3 and substr(coa_code,4,2)!=00 and substr(coa_code,1,3)='$idAwal'"
                                    . " and coa_comp='$comp' and coa_year = '$year'"));
            $modal_max = DB::select(DB::raw("select max(substr(coa_code,4,2)) as max from d_comp_coa where substr(coa_code,6,9)=0 and  substr(coa_code,1,1)=3 and substr(coa_code,4,2)!=00 and substr(coa_code,1,3)='$idAwal'"
                                    . " and coa_comp='$comp' and coa_year = '$year'"));
            $modal_max = $modal_max[0]->max;
            $modal_min = $modal_min[0]->min;
            if (count($modal_id) != 0) {
                $dataId = [];
                foreach ($modal_id as $index => $modal_id) {
                    $dataId[$index] = $modal_id->coa;
                }
                $range = range($modal_min, $modal_max);
                foreach ($range as $key) {
                    if (!in_array($key, $dataId)) {
                        return $key;
                        break;
                    }
                }
                return $modal_max + 1;
            } else {
                return 1;
            }
        } else if ($kategori == '3' && $level == 4) {

            $idAwal = substr($code, 0, 5);
            $modal_id = DB::select(DB::raw("select substr(coa_code,6,4) as coa from d_comp_coa "
                                    . "where substr(coa_code,1,1)=3 and substr(coa_code,1,5)='$idAwal'"
                                    . " and coa_comp='$comp' and coa_year = '$year'"));
            $modal_min = DB::select(DB::raw("select min(substr(coa_code,6,4)) as min from d_comp_coa "
                                    . "where substr(coa_code,1,1)=3 and substr(coa_code,1,5)='$idAwal'"
                                    . " and coa_comp='$comp' and coa_year = '$year'"));
            $modal_max = DB::select(DB::raw("select max(substr(coa_code,6,4)) as max from d_comp_coa "
                                    . "where substr(coa_code,1,1)=3 and substr(coa_code,1,5)='$idAwal'"
                                    . " and coa_comp='$comp' and coa_year = '$year'"));
            $modal_max = $modal_max[0]->max;
            $modal_min = $modal_min[0]->min;
            if (count($modal_id) != 0) {
                $dataId = [];
                foreach ($modal_id as $index => $modal_id) {
                    $dataId[$index] = $modal_id->coa;
                }
                $range = range($modal_min, $modal_max);
                foreach ($range as $key) {
                    if (!in_array($key, $dataId)) {
                        return $key;
                        break;
                    }
                }
                return $modal_max + 1;
            } else {
                return 1;
            }
        }
    }

}
