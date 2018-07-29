<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\surat;
use Carbon\Carbon;
use Redirect;
use Response;
use DB;
use Validator;
use App\Http\Controllers\Controller;

class suratController extends Controller
{   

  //INDEX

  public function index(Request $request){
      
$mitra = DB::table('d_mitra')
      ->groupBy('m_name')
      ->get();

          $surat_list = surat::all();
          $surat = DB::table('d_surat') 
          ->leftJoin('d_mitra','d_mitra.m_id','=','d_surat.mitra')
         ->get();
     


    return view('surat.index',compact('surat','mitra'));
  }
   

  //CETAK PRINT

  public function cetak($id_surat){



    $surat_list   = surat::findOrfail($id_surat);
      $surat = DB::table('d_surat') 
          ->Join('d_mitra','d_mitra.m_id','=','d_surat.mitra')
          ->where('id_surat','=',$id_surat)
         ->get();
     foreach ($surat as $key => $value) {
            $m = $value->m_name;

          }
     foreach ($surat as $key => $value) {
            $tgl_b = $value->tgl_b;

          }
     foreach ($surat as $key => $value) {
            $tgl_m = $value->tgl_m;

          }
    foreach ($surat as $key => $value) {
            $n_pj = $value->n_pj;

          }
     foreach ($surat as $key => $value) {
            $a_pj = $value->a_pj;

          }
      foreach ($surat as $key => $value) {
            $j_pj = $value->j_pj;

          }
      foreach ($surat as $key => $value) {
            $alamat = $value->alamat;

          }
      foreach ($surat as $key => $value) {
            $nama = $value->nama;

          }
      foreach ($surat as $key => $value) {
            $no_surat = $value->no_surat;

          }
      foreach ($surat as $key => $value) {
            $jabatan = $value->jabatan;

          }
           foreach ($surat as $key => $value) {
            $i = $value->instansi;

          }


      return view('surat.cetak' , compact('i','surat','m','tgl_b','tgl_m','n_pj','j_pj','a_pj','alamat','nama','no_surat','jabatan'));

    }
     public function cetak2($id_surat){



    $surat_list   = surat::findOrfail($id_surat);
      $surat = DB::table('d_surat') 
          ->Join('d_mitra','d_mitra.m_id','=','d_surat.mitra')
          ->where('id_surat','=',$id_surat)
         ->get();
           foreach ($surat as $key => $value) {
            $m = $value->m_name;

          }
           foreach ($surat as $key => $value) {
            $tgl_b = $value->tgl_b;

          }
           foreach ($surat as $key => $value) {
            $tgl_m = $value->tgl_m;

          }
           foreach ($surat as $key => $value) {
            $n_pj = $value->n_pj;

          }
           foreach ($surat as $key => $value) {
            $a_pj = $value->a_pj;

          }
           foreach ($surat as $key => $value) {
            $j_pj = $value->j_pj;

          }
           foreach ($surat as $key => $value) {
            $alamat = $value->alamat;

          }
           foreach ($surat as $key => $value) {
            $nama = $value->nama;

          }
           foreach ($surat as $key => $value) {
            $no_surat = $value->no_surat;

          }
           foreach ($surat as $key => $value) {
            $jabatan = $value->jabatan;

          }
           foreach ($surat as $key => $value) {
            $i = $value->instansi;

          }


      return view('surat.cetak2' , compact('i','surat','m','tgl_b','tgl_m','n_pj','j_pj','a_pj','alamat','nama','no_surat','jabatan'));

    }

    //CETAK LAPORAN

     public function laporan($id_surat){

      
$surat_list = surat::findOrfail($id_surat);
  $surat = DB::table('d_surat') 
          ->Join('d_mitra','d_mitra.m_id','=','d_surat.mitra')
           ->where('id_surat','=',$id_surat)
          ->get();

            foreach ($surat as $key => $value) {
              $i = $value->instansi;

            }
            foreach ($surat as $key => $value) {
              $m = $value->m_name;

            }
            foreach ($surat as $key => $value) {
              $tgl_b = $value->tgl_b;

            }
            foreach ($surat as $key => $value) {
              $tgl_m = $value->tgl_m;

            }
            foreach ($surat as $key => $value) {
                $n_pj = $value->n_pj;

              }
            foreach ($surat as $key => $value) {
                $a_pj = $value->a_pj;

              }
            foreach ($surat as $key => $value) {
                $j_pj = $value->j_pj;

              }
            foreach ($surat as $key => $value) {
                $alamat = $value->alamat;

              }
            foreach ($surat as $key => $value) {
                $nama = $value->nama;

              }
            foreach ($surat as $key => $value) {
                $no_surat = $value->no_surat;

              }
            foreach ($surat as $key => $value) {
                $jabatan = $value->jabatan;

              }
            foreach ($surat as $key => $value) {
                $i = $value->instansi;

              }
            return view('surat.laporan' , compact('i','surat','m','tgl_b','tgl_m','n_pj','j_pj','a_pj','alamat','nama','no_surat','jabatan'));
    }
    public function delete($id_surat){

     DB::table('d_surat')->where('id_surat',$id_surat)->delete();
              
      return redirect('surat');

    }
    
       public function cetak3($id_surat){



    $surat_list   = surat::findOrfail($id_surat);
      $surat = DB::table('d_surat') 
          ->Join('d_mitra','d_mitra.m_id','=','d_surat.mitra')
          ->where('id_surat','=',$id_surat)
         ->get();
           foreach ($surat as $key => $value) {
            $m = $value->m_name;

          }
          foreach ($surat as $key => $value) {
            $no_rek = $value->no_rek;

          }
           foreach ($surat as $key => $value) {
            $tgl_b = $value->tgl_b;

          }
           foreach ($surat as $key => $value) {
            $tgl_m = $value->tgl_m;

          }
           foreach ($surat as $key => $value) {
            $tgl = $value->tgl;

          }
           foreach ($surat as $key => $value) {
            $n_pj = $value->n_pj;

          }
           foreach ($surat as $key => $value) {
            $a_pj = $value->a_pj;

          }
           foreach ($surat as $key => $value) {
            $j_pj = $value->j_pj;

          }
           foreach ($surat as $key => $value) {
            $alamat = $value->alamat;

          }
           foreach ($surat as $key => $value) {
            $nama = $value->nama;

          }
           foreach ($surat as $key => $value) {
            $no_surat = $value->no_surat;

          }
           foreach ($surat as $key => $value) {
            $jabatan = $value->jabatan;

          }
           foreach ($surat as $key => $value) {
            $i = $value->instansi;

          }


      return view('surat.cetak3' , compact('i','surat','no_rek','m','tgl','tgl_b','tgl_m','n_pj','j_pj','a_pj','alamat','nama','no_surat','jabatan'));

    }
     public function cetak4($id_surat){



    $surat_list   = surat::findOrfail($id_surat);
      $surat = DB::table('d_surat') 
          ->Join('d_mitra','d_mitra.m_id','=','d_surat.mitra')
          ->where('id_surat','=',$id_surat)
         ->get();
           foreach ($surat as $key => $value) {
            $m = $value->m_name;

          }
          foreach ($surat as $key => $value) {
            $ttl = $value->tgl_lahir;

          }
          foreach ($surat as $key => $value) {
            $tl = $value->tempat_lahir;

          }
          foreach ($surat as $key => $value) {
            $tgl = $value->tgl;

          }
           foreach ($surat as $key => $value) {
            $tgl_b = $value->tgl_b;

          }
           foreach ($surat as $key => $value) {
            $tgl_m = $value->tgl_m;

          }
           foreach ($surat as $key => $value) {
            $n_pj = $value->n_pj;

          }
           foreach ($surat as $key => $value) {
            $a_pj = $value->a_pj;

          }
           foreach ($surat as $key => $value) {
            $j_pj = $value->j_pj;

          }
           foreach ($surat as $key => $value) {
            $alamat = $value->alamat;

          }
           foreach ($surat as $key => $value) {
            $nama = $value->nama;

          }
           foreach ($surat as $key => $value) {
            $no_surat = $value->no_surat;

          }
           foreach ($surat as $key => $value) {
            $jabatan = $value->jabatan;

          }
           foreach ($surat as $key => $value) {
            $i = $value->instansi;

          }


      return view('surat.cetak4' , compact('tgl','tl','ttl','i','surat','m','tgl_b','tgl_m','n_pj','j_pj','a_pj','alamat','nama','no_surat','jabatan'));

    }
     public function cetak5($id_surat){



    $surat_list   = surat::findOrfail($id_surat);
      $surat = DB::table('d_surat') 
          ->Join('d_mitra','d_mitra.m_id','=','d_surat.mitra')
          ->Join('d_mitra_divisi','d_mitra_divisi.md_id','=','d_surat.divisi')
          ->where('id_surat','=',$id_surat)
         ->get();
           foreach ($surat as $key => $value) {
            $m = $value->m_name;

          }
          foreach ($surat as $key => $value) {
            $divisi = $value->md_name;

          }
          foreach ($surat as $key => $value) {
            $ttl = $value->tgl_lahir;

          }
          foreach ($surat as $key => $value) {
            $tl = $value->tempat_lahir;

          }
          foreach ($surat as $key => $value) {
            $tgl = $value->tgl;

          }
           foreach ($surat as $key => $value) {
            $tgl_b = $value->tgl_b;

          }
           foreach ($surat as $key => $value) {
            $tgl_m = $value->tgl_m;

          }
           foreach ($surat as $key => $value) {
            $n_pj = $value->n_pj;

          }
           foreach ($surat as $key => $value) {
            $a_pj = $value->a_pj;

          }
           foreach ($surat as $key => $value) {
            $j_pj = $value->j_pj;

          }
           foreach ($surat as $key => $value) {
            $alamat = $value->alamat;

          }
           foreach ($surat as $key => $value) {
            $nama = $value->nama;

          }
           foreach ($surat as $key => $value) {
            $no_surat = $value->no_surat;

          }
           foreach ($surat as $key => $value) {
            $jabatan = $value->jabatan;

          }
           foreach ($surat as $key => $value) {
            $i = $value->instansi;

          }
           


      return view('surat.cetak5', compact('d_mitra_divisi','tgl','tl','ttl','divisi','i','surat','m','tgl_b','tgl_m','n_pj','j_pj','a_pj','alamat','nama','no_surat','jabatan'));

    }
     public function cetak6($id_surat){



    $surat_list   = surat::findOrfail($id_surat);
      $surat = DB::table('d_surat') 
          ->Join('d_mitra','d_mitra.m_id','=','d_surat.mitra')
          ->where('id_surat','=',$id_surat)
         ->get();
           foreach ($surat as $key => $value) {
            $m = $value->m_name;

          }
          foreach ($surat as $key => $value) {
            $kpk = $value->p_kpk; 

          }
          foreach ($surat as $key => $value) {
            $bu = $value->p_bu;

          }
          foreach ($surat as $key => $value) {
            $tgl = $value->tgl;

          }
           foreach ($surat as $key => $value) {
            $tgl_b = $value->tgl_b;

          }
           foreach ($surat as $key => $value) {
            $tgl_m = $value->tgl_m;

          }
           foreach ($surat as $key => $value) {
            $n_pj = $value->n_pj;

          }
           foreach ($surat as $key => $value) {
            $a_pj = $value->a_pj;

          }
           foreach ($surat as $key => $value) {
            $j_pj = $value->j_pj;

          }
           foreach ($surat as $key => $value) {
            $alamat = $value->alamat;

          }
           foreach ($surat as $key => $value) {
            $nama = $value->nama;

          }
           foreach ($surat as $key => $value) {
            $no_surat = $value->no_surat;

          }
           foreach ($surat as $key => $value) {
            $jabatan = $value->jabatan;

          }
           foreach ($surat as $key => $value) {
            $i = $value->instansi;

          }


      return view('surat.cetak6' , compact('tgl','bu','kpk','i','surat','m','tgl_b','tgl_m','n_pj','j_pj','a_pj','alamat','nama','no_surat','jabatan'));

    }
     public function cetak7($id_surat){



    $surat_list   = surat::findOrfail($id_surat);
      $surat = DB::table('d_surat') 
          ->Join('d_mitra','d_mitra.m_id','=','d_surat.mitra')
          ->Join('d_mitra_divisi','d_mitra_divisi.md_id','=','d_surat.divisi')
          ->where('id_surat','=',$id_surat)
         ->get();
           foreach ($surat as $key => $value) {
            $m = $value->m_name;

          }
          foreach ($surat as $key => $value) {
            $ttl = $value->tgl_lahir;

          }
           foreach ($surat as $key => $value) {
            $divisi = $value->md_name;

          }
           foreach ($surat as $key => $value) {
            $gaji = $value->gaji;

          }
          foreach ($surat as $key => $value) {
            $tl = $value->tempat_lahir;

          }
          foreach ($surat as $key => $value) {
            $tgl = $value->tgl;

          }
           foreach ($surat as $key => $value) {
            $tgl_b = $value->tgl_b;

          }
           foreach ($surat as $key => $value) {
            $tgl_m = $value->tgl_m;

          }
           foreach ($surat as $key => $value) {
            $n_pj = $value->n_pj;

          }
           foreach ($surat as $key => $value) {
            $a_pj = $value->a_pj;

          }
           foreach ($surat as $key => $value) {
            $j_pj = $value->j_pj;

          }
           foreach ($surat as $key => $value) {
            $alamat = $value->alamat;

          }
           foreach ($surat as $key => $value) {
            $nama = $value->nama;

          }
           foreach ($surat as $key => $value) {
            $no_surat = $value->no_surat;

          }
           foreach ($surat as $key => $value) {
            $jabatan = $value->jabatan;

          }
           foreach ($surat as $key => $value) {
            $i = $value->instansi;

          }


      return view('surat.cetak7' , compact('tl','ttl','divisi','gaji','tgl','i','surat','m','tgl_b','tgl_m','n_pj','j_pj','a_pj','alamat','nama','no_surat','jabatan'));

    }




















    //BUAT DATA PENGALAMAN KERJA



    public function create () {

 $array_bulan = array(1=>"I","II","III", "IV", "V","VI","VII","VIII","IX","X","XI","XII");
$bulan = $array_bulan[date('n')];

      $mitra = DB::table('d_mitra')
      ->groupBy('m_name')
      ->get();

    $itung = surat::select('no_surat')->orderBy('id_surat','DESC')->first();
      if ($itung != null){
         $a = explode('/',$itung);
     
       $itung = (int) abs( filter_var($a[0],FILTER_SANITIZE_NUMBER_INT));
      }else{
        $itung = 0;
      }
      $itung = $itung +1;

  $itung =  "". str_pad((string) $itung,0,"",STR_PAD_LEFT);

     
       $year = Carbon::parse()->now()->format('/Y');


      $k = surat::max('id_surat');
     $k = $k+1;

        return view('surat.create',compact('mitra','year','itung','k','bulan'));
    }



    // BUAT DATA TIDAK KERJA 

    public function create1 () {

   $array_bulan = array(1=>"I","II","III", "IV", "V","VI","VII","VIII","IX","X","XI","XII");
$bulan = $array_bulan[date('n')];

      $mitra = DB::table('d_mitra')
      ->groupBy('m_name')
      ->get();

     $itung = surat::select('no_surat')->orderBy('id_surat','DESC')->first();
      if ($itung != null){
         $a = explode('/',$itung);
     
       $itung = (int) abs( filter_var($a[0],FILTER_SANITIZE_NUMBER_INT));
      }else{
        $itung = 0;
      }
      $itung = $itung +1;

  $itung =  "". str_pad((string) $itung,0,"",STR_PAD_LEFT);

     
       $year = Carbon::parse()->now()->format('/Y');


      $k = surat::max('id_surat');
     $k = $k+1;

        return view('surat.create-tkerja',compact('mitra','year','itung','k','bulan'));
    }
     public function create2(Request $request){

        $array_bulan = array(1=>"I","II","III", "IV", "V","VI","VII","VIII","IX","X","XI","XII");
$bulan = $array_bulan[date('n')];

      $mitra = DB::table('d_mitra')
      ->groupBy('m_name')
      ->get();

     $itung = surat::select('no_surat')->orderBy('id_surat','DESC')->first();
      if ($itung != null){
         $a = explode('/',$itung);
     
       $itung = (int) abs( filter_var($a[0],FILTER_SANITIZE_NUMBER_INT));
      }else{
        $itung = 0;
      }
      $itung = $itung +1;

  $itung =  "". str_pad((string) $itung,0,"",STR_PAD_LEFT);

     
       $year = Carbon::parse()->now()->format('/Y');


      $k = surat::max('id_surat');
     $k = $k+1;

  $surat = DB::table('d_surat')
          ->where('id_surat')
          ->get();

          foreach ($surat as $key => $value) {
            $i = $value->instansi;

          }

        return view('surat.create-daupa',compact('mitra','year','itung','k','bulan','surat'));


      }
       public function create3(Request $request){

        $array_bulan = array(1=>"I","II","III", "IV", "V","VI","VII","VIII","IX","X","XI","XII");
$bulan = $array_bulan[date('n')];

      $mitra = DB::table('d_mitra')
      ->groupBy('m_name')
      ->get();

     $itung = surat::select('no_surat')->orderBy('id_surat','DESC')->first();
      if ($itung != null){
         $a = explode('/',$itung);
     
       $itung = (int) abs( filter_var($a[0],FILTER_SANITIZE_NUMBER_INT));
      }else{
        $itung = 0;
      }
      $itung = $itung +1;

  $itung =  "". str_pad((string) $itung,0,"",STR_PAD_LEFT);

     
       $year = Carbon::parse()->now()->format('/Y');


      $k = surat::max('id_surat');
     $k = $k+1;

        return view('surat.create-tibpjs',compact('mitra','year','itung','k','bulan'));


      }
       public function create4(Request $request){

        $array_bulan = array(1=>"I","II","III", "IV", "V","VI","VII","VIII","IX","X","XI","XII");
$bulan = $array_bulan[date('n')];

      $mitra = DB::table('d_mitra')
      ->groupBy('m_name')
      ->get();

     $itung = surat::select('no_surat')->orderBy('id_surat','DESC')->first();
      if ($itung != null){
         $a = explode('/',$itung);
     
       $itung = (int) abs( filter_var($a[0],FILTER_SANITIZE_NUMBER_INT));
      }else{
        $itung = 0;
      }
      $itung = $itung +1;

  $itung =  "". str_pad((string) $itung,0,"",STR_PAD_LEFT);

     
       $year = Carbon::parse()->now()->format('/Y');


      $k = surat::max('id_surat');
     $k = $k+1;

        return view('surat.create-resign',compact('mitra','year','itung','k','bulan'));


      }
       public function create5(Request $request){

        $array_bulan = array(1=>"I","II","III", "IV", "V","VI","VII","VIII","IX","X","XI","XII");
$bulan = $array_bulan[date('n')];

      $mitra = DB::table('d_mitra')
      ->groupBy('m_name')
      ->get();
      $divisi = DB::table('d_mitra_divisi')
      ->groupBy('md_name')
      ->get();

     $itung = surat::select('no_surat')->orderBy('id_surat','DESC')->first();
      if ($itung != null){
         $a = explode('/',$itung);
     
       $itung = (int) abs( filter_var($a[0],FILTER_SANITIZE_NUMBER_INT));
      }else{
        $itung = 0;
      }
      $itung = $itung +1;

  $itung =  "". str_pad((string) $itung,0,"",STR_PAD_LEFT);

     
       $year = Carbon::parse()->now()->format('/Y');


      $k = surat::max('id_surat');
     $k = $k+1;

        return view('surat.create-pibank',compact('mitra','year','itung','k','bulan','divisi'));


      }
       public function create6(Request $request){

        $array_bulan = array(1=>"I","II","III", "IV", "V","VI","VII","VIII","IX","X","XI","XII");
$bulan = $array_bulan[date('n')];

      $mitra = DB::table('d_mitra')
      ->groupBy('m_name')
      ->get();
   

     $itung = surat::select('no_surat')->orderBy('id_surat','DESC')->first();
      if ($itung != null){
         $a = explode('/',$itung);
     
       $itung = (int) abs( filter_var($a[0],FILTER_SANITIZE_NUMBER_INT));
      }else{
        $itung = 0;
      }
      $itung = $itung +1;

  $itung =  "". str_pad((string) $itung,0,"",STR_PAD_LEFT);

     
       $year = Carbon::parse()->now()->format('/Y');


      $k = surat::max('id_surat');
     $k = $k+1;

        return view('surat.create-pebpjs',compact('mitra','year','itung','k','bulan'));


      }
      public function create7(Request $request){

        $array_bulan = array(1=>"I","II","III", "IV", "V","VI","VII","VIII","IX","X","XI","XII");
$bulan = $array_bulan[date('n')];

      $mitra = DB::table('d_mitra')
      ->groupBy('m_name')
      ->get();
      $divisi = DB::table('d_mitra_divisi')
      ->groupBy('md_name')
      ->get();

     $itung = surat::select('no_surat')->orderBy('id_surat','DESC')->first();
      if ($itung != null){
         $a = explode('/',$itung);
     
       $itung = (int) abs( filter_var($a[0],FILTER_SANITIZE_NUMBER_INT));
      }else{
        $itung = 0;
      }
      $itung = $itung +1;

  $itung =  "". str_pad((string) $itung,0,"",STR_PAD_LEFT);

     
       $year = Carbon::parse()->now()->format('/Y');


      $k = surat::max('id_surat');
     $k = $k+1;

        return view('surat.create-pekpr',compact('mitra','year','itung','k','bulan','divisi'));


      }

     //STORE MAIN

       public function store (Request $request){

      if($request->instansi=="PT-PN/MJI") {
        $a ="PT PERWITA NUSANTARA MJI";
      }
       elseif ($request->instansi=="PT-AWKB") {
         $a = "PT AMERTA WIDIYA KARYA BHAKTI";
       }

      $rules = [
                  "no_surat" => "required",
                  "tgl" => "required",
                  "n_pj" => "required",
                  "a_pj" => "required",
                  "j_pj" => "required",
                  "nama" => "required",
                  "jabatan" => "required",
                  "alamat" => "required",              
                  "mitra" => "required", 
                  "tgl_m" => "required",
                  "tgl_b" => "required", 
                  "instansi" => "required"
            ];
   
     $validator = Validator::make($request->all(), $rules);
   /*  dd($request);*/
        if ($validator->fails()){
          if ($request->kop_surat=='Tidak Lagi Bekerja'){
            return redirect ('surat/create-tkerja')
            ->witherrors($validator)
            ->withinput();
          }
          elseif ($request->kop_surat == "Keterangan Resign"){
            return redirect ('surat/create-resign')
          ->witherrors($validator)
          ->withinput();
          }
        
          elseif ($request->kop_surat == "Tidak Aktif BPJS"){
            return redirect ('surat/create-tibpjs')
          ->witherrors($validator)
          ->withinput();
          }
          }
/*          
   dd($request);
          */
      $surat = new surat;
       $surat->kop_surat=$request->kop_surat;
       $surat->id_surat=$request->id;
       $surat->no_surat=$request->no_surat;
       $surat->tgl=date('Y-m-d',strtotime($request->tgl));
       $surat->nama=$request->nama;
       $surat->jabatan=$request->jabatan;
       $surat->alamat=$request->alamat;
       $surat->mitra=$request->mitra;
       $surat->tgl_m=date('Y-m-d',strtotime($request->tgl_m));
       $surat->tgl_b=date('Y-m-d',strtotime($request->tgl_b));
       $surat->tempat_lahir=$request->tl;
       $surat->tgl_lahir=$request->ttl;
       $surat->n_pj=$request->n_pj;
       $surat->j_pj=$request->j_pj;
       $surat->a_pj=$request->a_pj;
       $surat->p_kpk=$request->kpk;
       $surat->p_kpj=$request->kpj;
       $surat->p_bu=$request->bu;
       $surat->no_rek=$request->no_rek;
       $surat->instansi=$a;
       $surat->divisi=$request->divisi;
       $surat->save();

       return redirect('surat');
        
      }

       public function store2 (Request $request){

      if($request->instansi=="PT-PN/MJI") {
        $a ="PT PERWITA NUSANTARA MJI";
      }
       elseif ($request->instansi=="PT-AWKB") {
         $a = "PT AMERTA WIDIYA KARYA BHAKTI";
       }

      $rules = [
                  "no_surat" => "required",
                  "tgl" => "required",
                  "n_pj" => "required",
                  "a_pj" => "required",
                  "j_pj" => "required",
                  "nama" => "required",
                  "jabatan" => "required",
                  "no_rek" =>"required",
                  "alamat" => "required",              
                  "mitra" => "required", 
                  "tgl_m" => "required",
                  "tgl_b" => "required", 
                  "instansi" => "required"
            ];
   
     $validator = Validator::make($request->all(), $rules);
   /*  dd($request);*/
        if ($validator->fails()){
           if ($request->kop_surat == "Data Upah"){
            return redirect ('surat/create-daupa')
          ->witherrors($validator)
          ->withinput();
          }
          }
/*          
   dd($request);
          */
      $surat = new surat;
       $surat->kop_surat=$request->kop_surat;
       $surat->id_surat=$request->id;
       $surat->no_surat=$request->no_surat;
       $surat->tgl=date('Y-m-d',strtotime($request->tgl));
       $surat->nama=$request->nama;
       $surat->jabatan=$request->jabatan;
       $surat->alamat=$request->alamat;
       $surat->mitra=$request->mitra;
       $surat->tgl_m=date('Y-m-d',strtotime($request->tgl_m));
       $surat->tgl_b=date('Y-m-d',strtotime($request->tgl_b));
       $surat->tempat_lahir=$request->tl;
       $surat->tgl_lahir=$request->ttl;
       $surat->n_pj=$request->n_pj;
       $surat->j_pj=$request->j_pj;
       $surat->a_pj=$request->a_pj;
       $surat->p_kpk=$request->kpk;
       $surat->p_kpj=$request->kpj;
       $surat->p_bu=$request->bu;
       $surat->no_rek=$request->no_rek;
       $surat->instansi=$a;
       $surat->divisi=$request->divisi;
       $surat->save();

       return redirect('surat');
        
      }
      public function store3 (Request $request){

      if($request->instansi=="PT-PN/MJI") {
        $a ="PT PERWITA NUSANTARA MJI";
      }
       elseif ($request->instansi=="PT-AWKB") {
         $a = "PT AMERTA WIDIYA KARYA BHAKTI";
       }

      $rules = [
                  "no_surat" => "required",
                  "tgl" => "required",
                  "n_pj" => "required",
                  "a_pj" => "required",
                  "j_pj" => "required",
                  "nama" => "required",
                  "jabatan" => "required",
                  "no_rek" =>"required",
                  "alamat" => "required",              
                  "mitra" => "required", 
                  "tgl_m" => "required",
                  "tgl_b" => "required", 
                  "tl" => "required",
                  "ttl" => "required", 
                  "divisi" => "required", 
                  "instansi" => "required"
            ];
   
     $validator = Validator::make($request->all(), $rules);
   /*  dd($request);*/
        if ($validator->fails()){
            if ($request->kop_surat == "Peminjaman Bank"){
            return redirect ('surat/create-pibank')
          ->witherrors($validator)
          ->withinput();

         }
          }
/*          
   dd($request);
          */
      $surat = new surat;
       $surat->kop_surat=$request->kop_surat;
       $surat->id_surat=$request->id;
       $surat->no_surat=$request->no_surat;
       $surat->tgl=date('Y-m-d',strtotime($request->tgl));
       $surat->nama=$request->nama;
       $surat->jabatan=$request->jabatan;
       $surat->alamat=$request->alamat;
       $surat->mitra=$request->mitra;
       $surat->tgl_m=date('Y-m-d',strtotime($request->tgl_m));
       $surat->tgl_b=date('Y-m-d',strtotime($request->tgl_b));
       $surat->tempat_lahir=$request->tl;
       $surat->tgl_lahir=$request->ttl;
       $surat->n_pj=$request->n_pj;
       $surat->j_pj=$request->j_pj;
       $surat->a_pj=$request->a_pj;
       $surat->p_kpk=$request->kpk;
       $surat->p_kpj=$request->kpj;
       $surat->p_bu=$request->bu;
       $surat->no_rek=$request->no_rek;
       $surat->instansi=$a;
       $surat->divisi=$request->divisi;
       $surat->save();

       return redirect('surat');
        
      }
//STORE 1

       public function store1 (Request $request){

      if($request->instansi=="PT-PN/MJI") {
        $a ="PT PERWITA NUSANTARA MJI";
      }
       elseif ($request->instansi=="PT-AWKB") {
         $a = "PT AMERTA WIDIYA KARYA BHAKTI";
       }

      $rules = [
                  "no_surat" => "required",
                  "tgl" => "required",
                  "n_pj" => "required",
                  "a_pj" => "required",
                  "j_pj" => "required",
                  "nama" => "required",
                /*  "no_rek" => "required",*/
                  "jabatan" => "required",
              /*    "divisi" => "required",*/
                  "alamat" => "required",              
                  "mitra" => "required", 
                  "tgl_m" => "required",
                  "tgl_b" => "required", 
               /*   "a" => "required",*/
                  "instansi" => "required", 
               /*   "gaji" => "required", */
                  "tl" => "required", 
                  "ttl" => "required",
                 /* "kpk" => "required", 
                  "kpj" => "required", 
                  "bu" => "required"*/
            ];
   
     $validator = Validator::make($request->all(), $rules);
    /* dd($request);*/
        if ($validator->fails()){
          if ($request->kop_surat=='Tidak Lagi Bekerja'){
            return redirect ('surat/create-tkerja')
            ->witherrors($validator)
            ->withinput();
          
          }
          elseif ($request->kop_surat == "Peminjaman Bank"){
            return redirect ('surat/create-pibank')
          ->witherrors($validator)
          ->withinput();

         }
          elseif ($request->kop_surat == "Data Upah"){
            return redirect ('surat/create-daupa')
          ->witherrors($validator)
          ->withinput();
          }
          elseif ($request->kop_surat == "Keterangan Resign"){
            return redirect ('surat/create-resign')
          ->witherrors($validator)
          ->withinput();
          }
         elseif ($request->kop_surat == "Pengalaman Kerja"){
          return redirect ('surat/create')
          ->witherrors($validator)
          ->withinput();
          }
          elseif ($request->kop_surat == "Tidak Aktif BPJS"){
            return redirect ('surat/create-tibpjs')
          ->witherrors($validator)
          ->withinput();
          }

          elseif ($request->kop_surat == "Tidak Lagi Bekerja"){
            return redirect ('surat/create-tkerja')
          ->witherrors($validator)
          ->withinput();
          }
          elseif ($request->kop_surat == "Pendaftaran BPJS"){
            return redirect ('surat/create-pebpjs')
          ->witherrors($validator)
          ->withinput();
          }
         elseif ($request->kop_surat == "Pengajuan KPR"){
            return redirect ('surat/create-pekpr')
          ->witherrors($validator)
          ->withinput();
          }  
          }
/*          
   dd($request);
          */
      $surat = new surat;
       $surat->kop_surat=$request->kop_surat;
       $surat->id_surat=$request->id;
       $surat->no_surat=$request->no_surat;
       $surat->tgl=date('Y-m-d',strtotime($request->tgl));
       $surat->nama=$request->nama;
       $surat->jabatan=$request->jabatan;
       $surat->alamat=$request->alamat;
       $surat->mitra=$request->mitra;
       $surat->tgl_m=date('Y-m-d',strtotime($request->tgl_m));
       $surat->tgl_b=date('Y-m-d',strtotime($request->tgl_b));
       $surat->tempat_lahir=$request->tl;
       $surat->tgl_lahir=$request->ttl;
       $surat->n_pj=$request->n_pj;
       $surat->j_pj=$request->j_pj;
       $surat->a_pj=$request->a_pj;
       $surat->p_kpk=$request->kpk;
       $surat->p_kpj=$request->kpj;
       $surat->p_bu=$request->bu;
       $surat->no_rek=$request->no_rek;
       $surat->instansi=$a;
       $surat->divisi=$request->divisi;
       $surat->save();

       return redirect('surat');
        
      }

  public function store4 (Request $request){

      if($request->instansi=="PT-PN/MJI") {
        $a ="PT PERWITA NUSANTARA MJI";
      }
       elseif ($request->instansi=="PT-AWKB") {
         $a = "PT AMERTA WIDIYA KARYA BHAKTI";
       }

      $rules = [
                  "no_surat" => "required",
                  "tgl" => "required",
                  "n_pj" => "required",
                  "a_pj" => "required",
                  "j_pj" => "required",
                  "nama" => "required",
                  "jabatan" => "required",
                  "alamat" => "required",              
                  "mitra" => "required", 
                  "tgl_m" => "required",
                  "tgl_b" => "required", 
                  "instansi" => "required", 
                  "kpk" => "required", 
                  "kpj" => "required", 
                  "bu" => "required"
            ];
   
     $validator = Validator::make($request->all(), $rules);
    /* dd($request);*/
        if ($validator->fails()){
          
          if ($request->kop_surat == "Pendaftaran BPJS"){
            return redirect ('surat/create-pebpjs')
          ->witherrors($validator)
          ->withinput();
          }
         
          }
/*          
   dd($request);
          */
      $surat = new surat;
       $surat->kop_surat=$request->kop_surat;
       $surat->id_surat=$request->id;
       $surat->no_surat=$request->no_surat;
       $surat->tgl=date('Y-m-d',strtotime($request->tgl));
       $surat->nama=$request->nama;
       $surat->jabatan=$request->jabatan;
       $surat->alamat=$request->alamat;
       $surat->mitra=$request->mitra;
       $surat->tgl_m=date('Y-m-d',strtotime($request->tgl_m));
       $surat->tgl_b=date('Y-m-d',strtotime($request->tgl_b));
       $surat->tempat_lahir=$request->tl;
       $surat->tgl_lahir=$request->ttl;
       $surat->n_pj=$request->n_pj;
       $surat->j_pj=$request->j_pj;
       $surat->a_pj=$request->a_pj;
       $surat->p_kpk=$request->kpk;
       $surat->p_kpj=$request->kpj;
       $surat->p_bu=$request->bu;
       $surat->no_rek=$request->no_rek;
       $surat->instansi=$a;
       $surat->divisi=$request->divisi;
       $surat->save();

       return redirect('surat');
        
      }
 public function store5 (Request $request){

      if($request->instansi=="PT-PN/MJI") {
        $a ="PT PERWITA NUSANTARA MJI";
      }
       elseif ($request->instansi=="PT-AWKB") {
         $a = "PT AMERTA WIDIYA KARYA BHAKTI";
       }

      $rules = [
                  "no_surat" => "required",
                  "tgl" => "required",
                  "n_pj" => "required",
                  "a_pj" => "required",
                  "j_pj" => "required",
                  "nama" => "required",     
                  "jabatan" => "required",
                  "alamat" => "required",              
                  "mitra" => "required",
                  "divisi" => "required", 
                  "tgl_m" => "required",
                  "tgl_b" => "required",
                  "instansi" => "required", 
                  "gaji" => "required", 
                  "tl" => "required", 
                  "ttl" => "required",
                
            ];
   
     $validator = Validator::make($request->all(), $rules);
    /* dd($request);*/
        if ($validator->fails()){
         if ($request->kop_surat == "Pengajuan KPR"){
            return redirect ('surat/create-pekpr')
          ->witherrors($validator)
          ->withinput();
          }  
          }
/*          
   dd($request);
          */
      $surat = new surat;
       $surat->kop_surat=$request->kop_surat;
       $surat->id_surat=$request->id;
       $surat->no_surat=$request->no_surat;
       $surat->tgl=date('Y-m-d',strtotime($request->tgl));
       $surat->nama=$request->nama;
       $surat->jabatan=$request->jabatan;
       $surat->alamat=$request->alamat;
       $surat->mitra=$request->mitra;
       $surat->tgl_m=date('Y-m-d',strtotime($request->tgl_m));
       $surat->tgl_b=date('Y-m-d',strtotime($request->tgl_b));
       $surat->tempat_lahir=$request->tl;
       $surat->tgl_lahir=$request->ttl;
       $surat->n_pj=$request->n_pj;
       $surat->j_pj=$request->j_pj;
       $surat->a_pj=$request->a_pj;
       $surat->p_kpk=$request->kpk;
       $surat->p_kpj=$request->kpj;
       $surat->p_bu=$request->bu;
       $surat->no_rek=$request->no_rek;
       $surat->instansi=$a;
       $surat->divisi=$request->divisi;
       $surat->save();

       return redirect('surat');
        
      }


                //EDIT DATA
      public function edit($id_surat){

            $array_bulan = array(1=>"I","II","III", "IV", "V","VI","VII","VIII","IX","X","XI","XII");
            $bulan = $array_bulan[date('n')];

            $mitra = DB::table('d_mitra')
                ->groupBy('m_name')
                ->get();      
            $itung = surat::select('no_surat')
               ->where('id_surat','=',$id_surat)
               ->orderBy('id_surat','DESC')
               ->get();
                if ($itung != null){
                $a = explode('/',$itung);
                 $itung = (int) abs( filter_var($a[0],FILTER_SANITIZE_NUMBER_INT));
                }
                 $year = Carbon::parse()->now()->format('/Y');
                
            $surat = surat::findOrfail($id_surat);
                  return view('surat.edit',['surat' => $surat],compact('surat','mitra','year','itung','bulan'));
              }
              
               public function edit1($id_surat){

                $array_bulan = array(1=>"I","II","III", "IV", "V","VI","VII","VIII","IX","X","XI","XII");
          $bulan = $array_bulan[date('n')];

                $mitra = DB::table('d_mitra')
                ->groupBy('m_name')
                ->get();
              $itung = surat::select('no_surat')
               ->where('id_surat','=',$id_surat)
               ->orderBy('id_surat','DESC')
               ->get();
                if ($itung != null){
                $a = explode('/',$itung);
                 $itung = (int) abs( filter_var($a[0],FILTER_SANITIZE_NUMBER_INT));
                }
                 $year = Carbon::parse()->now()->format('/Y');
                
               $surat = surat::findOrfail($id_surat);
                  return view('surat.edit-tkerja',['surat' => $surat],compact('mitra','year','itung','bulan'));
              }
              public function edit2($id_surat){

                $array_bulan = array(1=>"I","II","III", "IV", "V","VI","VII","VIII","IX","X","XI","XII");
          $bulan = $array_bulan[date('n')];

                $mitra = DB::table('d_mitra')
                ->groupBy('m_name')
                ->get();
             $itung = surat::select('no_surat')
               ->where('id_surat','=',$id_surat)
               ->orderBy('id_surat','DESC')
               ->get();
                if ($itung != null){
                $a = explode('/',$itung);
                 $itung = (int) abs( filter_var($a[0],FILTER_SANITIZE_NUMBER_INT));
                }
                 $year = Carbon::parse()->now()->format('/Y');
                
               $surat = surat::findOrfail($id_surat);
                  return view('surat.edit-daupa',['surat' => $surat],compact('mitra','year','itung','bulan'));
              }
              public function edit3($id_surat){

                $array_bulan = array(1=>"I","II","III", "IV", "V","VI","VII","VIII","IX","X","XI","XII");
          $bulan = $array_bulan[date('n')];

                $mitra = DB::table('d_mitra')
                ->groupBy('m_name')
                ->get();



                
             $itung = surat::select('no_surat')
               ->where('id_surat','=',$id_surat)
               ->orderBy('id_surat','DESC')
               ->get();
                if ($itung != null){
                $a = explode('/',$itung);
                 $itung = (int) abs( filter_var($a[0],FILTER_SANITIZE_NUMBER_INT));
                }
                 $year = Carbon::parse()->now()->format('/Y');
                
               $surat = surat::findOrfail($id_surat);
                  return view('surat.edit-tibpjs',['surat' => $surat],compact('mitra','year','itung','bulan'));
              }
              public function edit4($id_surat){

                $array_bulan = array(1=>"I","II","III", "IV", "V","VI","VII","VIII","IX","X","XI","XII");
          $bulan = $array_bulan[date('n')];

                $mitra = DB::table('d_mitra')
                ->groupBy('m_name')
                ->get();



                
             $itung = surat::select('no_surat')
               ->where('id_surat','=',$id_surat)
               ->orderBy('id_surat','DESC')
               ->get();
                if ($itung != null){
                $a = explode('/',$itung);
                 $itung = (int) abs( filter_var($a[0],FILTER_SANITIZE_NUMBER_INT));
                }
                 $year = Carbon::parse()->now()->format('/Y');
                
               $surat = surat::findOrfail($id_surat);
                  return view('surat.edit-resign',['surat' => $surat],compact('mitra','year','itung','bulan'));
              }
              public function edit5($id_surat){

                $array_bulan = array(1=>"I","II","III", "IV", "V","VI","VII","VIII","IX","X","XI","XII");
          $bulan = $array_bulan[date('n')];

                $mitra = DB::table('d_mitra')
                ->groupBy('m_name')
                ->get();

                $divisi = DB::table('d_mitra_divisi')
                ->groupBy('md_name')
                ->get();


                
             $itung = surat::select('no_surat')
               ->where('id_surat','=',$id_surat)
               ->orderBy('id_surat','DESC')
               ->get();
                if ($itung != null){
                $a = explode('/',$itung);
                 $itung = (int) abs( filter_var($a[0],FILTER_SANITIZE_NUMBER_INT));
                }
                 $year = Carbon::parse()->now()->format('/Y');
                
               $surat = surat::findOrfail($id_surat);
                  return view('surat.edit-pibank',['surat' => $surat],compact('mitra','year','itung','bulan','divisi'));
              }
              public function edit6($id_surat){

                $array_bulan = array(1=>"I","II","III", "IV", "V","VI","VII","VIII","IX","X","XI","XII");
          $bulan = $array_bulan[date('n')];

                $mitra = DB::table('d_mitra')
                ->groupBy('m_name')
                ->get();



                
             $itung = surat::select('no_surat')
               ->where('id_surat','=',$id_surat)
               ->orderBy('id_surat','DESC')
               ->get();
                if ($itung != null){
                $a = explode('/',$itung);
                 $itung = (int) abs( filter_var($a[0],FILTER_SANITIZE_NUMBER_INT));
                }
                 $year = Carbon::parse()->now()->format('/Y');
                
               $surat = surat::findOrfail($id_surat);
                  return view('surat.edit-pebpjs',['surat' => $surat],compact('mitra','year','itung','bulan'));
              }
              public function edit7($id_surat){

                $array_bulan = array(1=>"I","II","III", "IV", "V","VI","VII","VIII","IX","X","XI","XII");
          $bulan = $array_bulan[date('n')];

                $mitra = DB::table('d_mitra')
                ->groupBy('m_name')
                ->get();
                $d_mitra_divisi = DB::table('d_mitra_divisi')
                ->groupBy('md_name')
                ->get();



                
             $itung = surat::select('no_surat')
               ->where('id_surat','=',$id_surat)
               ->orderBy('id_surat','DESC')
               ->get();
                if ($itung != null){
                $a = explode('/',$itung);
                 $itung = (int) abs( filter_var($a[0],FILTER_SANITIZE_NUMBER_INT));
                }
                 $year = Carbon::parse()->now()->format('/Y');
                
               $surat = surat::findOrfail($id_surat);
                  return view('surat.edit-pekpr',['surat' => $surat],compact('d_mitra_divisi','mitra','year','itung','bulan'));
              }


    //FUNCTION EDIT DATA
     public function update1(Request $request,$id_surat)
    {

    if($request->instansi=="PT-PN/MJI") {
        $a ="PT PERWITA NUSANTARA MJI";
      }
       elseif ($request->instansi=="PT-AWKB") {
         $a = "PT AMERTA WIDIYA KARYA BHAKTI";
       }


       $rules = [
                  "no_surat" => "required",
                  "tgl" => "required",
                  "n_pj" => "required",
                  "a_pj" => "required",
                  "j_pj" => "required",
                  "nama" => "required",
                  "jabatan" => "required",
                  "no_rek" => "required",
                  "divisi" => "required",
                  "alamat" => "required",              
                  "mitra" => "required", 
                  "tgl_m" => "required",
                  "tgl_b" => "required", 
                  "instansi" => "required", 
                  "gaji" => "required", 
                  "tl" => "required", 
                  "ttl" => "required",
                  "kpk" => "required", 
                  "kpj" => "required", 
                  "bu" => "required",   

            ];
       $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
          return Redirect::back()
          ->witherrors($validator)
          ->withinput();
        }
        else
        {


       $surat = surat::findOrfail($id_surat);
       $surat->tgl=date('Y-m-d',strtotime($request->tgl));
       $surat->nama=$request->nama;
       $surat->jabatan=$request->jabatan;
       $surat->alamat=$request->alamat;
       $surat->mitra=$request->mitra;
       $surat->tgl_m=date('Y-m-d',strtotime($request->tgl_m));
       $surat->tgl_b=date('Y-m-d',strtotime($request->tgl_b));
       $surat->n_pj=$request->n_pj;
       $surat->j_pj=$request->j_pj;
       $surat->p_bu=$request->bu;
       $surat->p_kpj=$request->kpj;
       $surat->p_kpk=$request->kpk;
       $surat->no_rek=$request->no_rek;
       $surat->kop_surat=$request->kop_surat; 
       $surat->tempat_lahir=$request->tl;
       $surat->tgl_lahir=$request->ttl;
       $surat->a_pj=$request->a_pj;
       $surat->instansi=$a;
       $surat->gaji=$request->gaji;
       $surat->divisi=$request->divisi;
       $surat->save();
       return redirect('surat');
      }
      
    }
    





/*AUTO COMPLETE*/








      public function auto(Request $request){
        $term = $request->term;
        
        $results = array();
        $queries = DB::table('d_mitra_pekerja')
            ->join('d_pekerja','d_mitra_pekerja.mp_id','=','d_pekerja.p_id')
            ->where('d_pekerja.p_name', 'like', '%'.$term.'%')
            ->take(100)->get();

        if ($queries == null){
            $results[] = [ 'id' => null, 'label' => 'Tidak ditemukan data terkait'];
        } else {

            foreach ($queries as $query)
            {
                $results[] = [ 'id' => $query->p_id, 'label' => $query->p_name];
            }
        }

        return Response::json($results);
    }
        public function autocomplete(Request $request){
        $term = $request->term;
        
        $results = array();
        $queries = DB::table('d_pegawai')
            ->where('d_pegawai.p_nama_lengkap', 'like', '%'.$term.'%')
            ->take(100)->get();

        if ($queries == null){
            $results[] = [ 'id' => null, 'label' => 'Tidak ditemukan data terkait'];
        } else {

            foreach ($queries as $query)
            {
                $results[] = [ 'id' => $query->p_id, 'label' => $query->p_nama_lengkap];
            }
        }

        return Response::json($results);
    }

   public function getData($id){         
   
     $getData = DB::table('d_pegawai')->select('*')->where('p_id',$id)->get();  
        
       return response()->json([
          'alamat' => $getData[0]->p_alamat
       ]);
    }
    public function getDatanama($id){         
   
     $getData = DB::table('d_pegawai')->select('*')->where('p_id',$id)->get(); 

        $getDatanama = DB::table('d_mitra_pekerja')
            ->select('*')
            ->join('d_pekerja','d_mitra_pekerja.mp_id','=','d_pekerja.p_id')
            ->where('p_id',$id)
            ->take(100)->get();

       return response()->json([
          'address' => $getDatanama[0]->p_address,
          'tl' => $getDatanama[0]->p_birthplace,
          'ttl' => $getDatanama[0]->p_birthdate,
          'kpk'=> $getDatanama[0]->p_kpk,
          'kpj'=> $getDatanama[0]->p_kpj_no,
          'bu'=> $getDatanama[0]->p_bu
       ]);
    }
    









//CETAK UPAH DI FORM TAMBAH
    public function gege(Request $request){

if($request->instansi=="PT-PN/MJI") {
        $a ="PT PERWITA NUSANTARA MJI";
      }
       elseif ($request->instansi=="PT-AWKB") {
         $a = "PT AMERTA WIDIYA KARYA BHAKTI";
       } 

       $surat = $request;
       $request->no_surat;
       $request->ttl;
       $request->tl;
       $request->tgl;
       $request->nama;
       $request->jabatan;
       $request->alamat;
       $request->mitra;
       $request->tgl_m;
       $request->tgl_b;
       $request->n_pj;
       $request->j_pj;
       $request->a_pj;
       $request->no_rek;
       $request->instansi;

      return view('surat.laporan-legalisir-data-upah',compact('surat','request','a'));
    }   

//CETAK PENGALAMAN DI FORM TAMBAH
     public function gege1(Request $request){

if($request->instansi=="PT-PN/MJI") {
        $a ="PT PERWITA NUSANTARA MJI";
      }
       elseif ($request->instansi=="PT-AWKB") {
         $a = "PT AMERTA WIDIYA KARYA BHAKTI";
       } 
    

       $surat = $request;
       $request->no_surat;
       $request->tgl;
       $request->ttl;
       $request->tl;
       $request->nama;
       $request->jabatan;
       $request->alamat;
       $request->mitra;
       $request->tgl_m;
       $request->tgl_b;
       $request->n_pj;
       $request->j_pj;
       $request->a_pj;
       $request->instansi;

      return view('surat.laporan-pengalaman-kerja',compact('surat','request','a'));
    }   
    //CETAK RESIGN DI FORM TAMBAH 
     public function gege2(Request $request){


       $surat = $request;
       $request->no_surat;
       $request->tgl;
        $request->ttl;
       $request->nama;
       $request->jabatan;
       $request->alamat;
       $request->mitra;
       $request->tgl_m;
       $request->tgl_b;
       $request->n_pj;
       $request->j_pj;
       $request->a_pj;
       $request->instansi;

      return view('surat.laporan-pekerja-resign',compact('surat','request'));
    }    
    public function gege3(Request $request){


       if($request->instansi=="PT-PN/MJI") {
        $a ="PT PERWITA NUSANTARA MJI";
      }
       elseif ($request->instansi=="PT-AWKB") {
         $a = "PT AMERTA WIDIYA KARYA BHAKTI";
       } 
    

       $surat = $request;
       $request->no_surat;
       $request->tgl;
       $request->nama;
       $request->ttl;
       $request->tl;
       $request->jabatan;
       $request->alamat;
       $request->mitra;
       $request->tgl_m;
       $request->tgl_b;
       $request->n_pj;
       $request->j_pj;
       $request->a_pj;
       $request->instansi;

      return view('surat.laporan-pinjam-bank',compact('surat','request','a','b','mitra','divisi','i'));
    }    
    public function gege4(Request $request){


       if($request->instansi=="PT-PN/MJI") {
        $a ="PT PERWITA NUSANTARA MJI";
      }
       elseif ($request->instansi=="PT-AWKB") {
         $a = "PT AMERTA WIDIYA KARYA BHAKTI";
       } 
    

       $surat = $request;
       $request->no_surat;
       $request->tgl;
       $request->nama;
       $request->jabatan;
       $request->alamat;
       $request->mitra;
       $request->tgl_m;
       $request->tgl_b;
       $request->n_pj;
       $request->ttl;
       $request->tl;
       $request->j_pj;
       $request->a_pj;
       $request->instansi;

      return view('surat.laporan-pengajuan-kpr',compact('surat','request','a','b'));
    }    
     public function gege5(Request $request){


       if($request->instansi=="PT-PN/MJI") {
        $a ="PT PERWITA NUSANTARA MJI";
      }
       elseif ($request->instansi=="PT-AWKB") {
         $a = "PT AMERTA WIDIYA KARYA BHAKTI";
       } 
    

       $surat = $request;
       $request->no_surat;
       $request->tgl;
       $request->nama;
       $request->ttl;
       $request->tl;
       $request->jabatan;
       $request->alamat;
       $request->mitra;
       $request->tgl_m;
       $request->tgl_b;
       $request->n_pj;
       $request->j_pj;
       $request->a_pj;
       $request->instansi;

      return view('surat.laporan-pendaftaran-bpjs',compact('surat','request','a','b'));
    }    
       public function gege6(Request $request){


       if($request->instansi=="PT-PN/MJI") {
        $a ="PT PERWITA NUSANTARA MJI";
      }
       elseif ($request->instansi=="PT-AWKB") {
         $a = "PT AMERTA WIDIYA KARYA BHAKTI";
       } 
    

       $surat = $request;
       $request->no_surat;
       $request->tgl;
       $request->nama;
       $request->jabatan;
       $request->alamat;
       $request->mitra;
       $request->ttl;
       $request->tl;
       $request->tgl_m;
       $request->tgl_b;
       $request->n_pj;
       $request->j_pj;
       $request->a_pj;
       $request->instansi;

      return view('surat.laporan-tidak-aktif-bpjs',compact('surat','request','a','b'));
    }    
     public function gege7(Request $request){


       if($request->instansi=="PT-PN/MJI") {
        $a ="PT PERWITA NUSANTARA MJI";
      }
       elseif ($request->instansi=="PT-AWKB") {
         $a = "PT AMERTA WIDIYA KARYA BHAKTI";
       } 
    

       $surat = $request;
       $request->no_surat;
       $request->tgl;
       $request->nama;
       $request->jabatan;
       $request->alamat;
       $request->mitra;
       $request->ttl;
       $request->tl;
       $request->tgl_m;
       $request->tgl_b;
       $request->n_pj;
       $request->j_pj;
       $request->a_pj;
       $request->instansi;

      return view('surat.laporan-tidak-lagi-bekerja',compact('surat','request','a','b'));
    }   




}