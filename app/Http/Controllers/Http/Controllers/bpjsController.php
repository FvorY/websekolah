<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\bpjs;

use Redirect;
use Response;
use DB;
use Validator;
use App\Http\Controllers\Controller;

class bpjsController extends Controller
{   

    public function index( Request $request)
    {
      $mitra = DB::table('d_mitra')
      ->groupBy('m_name')
      ->get();
      $d_mitra_divisi = DB::table('d_mitra_divisi')
      ->groupBy('md_name')
      ->get();
      
          $bpjs_list = bpjs::all();
          $bpjs = DB::table('d_bpjs') 
          ->leftJoin('d_mitra','d_mitra.m_id','=','d_bpjs.m_id')
          ->leftJoin('d_mitra_divisi','d_mitra_divisi.md_id','=','d_bpjs.md_id') 
       
        ->get();

        return view('bpjs.index' , compact('bpjs','mitra','d_mitra_divisi'));
    }
    public function delete($no_kartu)
    {
      DB::table('d_bpjs')->where('no_kartu',$no_kartu)->delete();
              
    	return redirect('bpjs');
    }

    public function create()
    {
      

      $mitra = DB::table('d_mitra')
      ->groupBy('m_name')
      ->get();
       $d_mitra_divisi = DB::table('d_mitra_divisi')
      ->groupBy('md_name')
      ->get();
       
        return view('bpjs.create',compact('mitra','d_mitra_divisi'));

    }

    public function store(Request $request)
    {
     

    $rules = [
                  "no_kartu" => "required|unique:d_bpjs,no_kartu",
                  "p_nik" => "required",
                  "npp" => "required",
                  "p_name" => "required",
                  "p_birthdate" => "required",
                  "h_keluarga" => "required",
                  "TMT" => "required",
                  "nf_1" => "required",                
                  "kelas" => "required", 
                  "m_id_1" => "required",
                  "md_id_1" => "required",  

            ];
      $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
          return redirect ('bpjs/create')
          ->witherrors($validator)
          ->withinput();
        }

       $bpjs = new bpjs;
       $bpjs->no_kartu=$request->no_kartu;
       $bpjs->p_nik=$request->p_nik;
       $bpjs->npp=$request->npp;
       $bpjs->p_name=$request->p_name;
       $bpjs->p_birthdate=date('Y-m-d',strtotime($request->p_birthdate));
       $bpjs->h_keluarga=$request->h_keluarga;
       $bpjs->TMT=date('Y-m-d',strtotime($request->TMT));
       $bpjs->nf_1=$request->nf_1;
       $bpjs->p_state=$request->p_state;
       $bpjs->kelas=$request->kelas;
       $bpjs->m_id=$request->m_id_1;
       $bpjs->md_id=$request->md_id_1;
       $bpjs->save();


       return redirect('bpjs');




    }
    public function edit($nik)
    {

      $mitra = DB::table('d_mitra')
      ->groupBy('m_name')
      ->get();
       $d_mitra_divisi = DB::table('d_mitra_divisi')
      ->groupBy('md_name')
      ->get();

      $bpjs = bpjs::findOrfail($nik);
      return view('bpjs.edit' , ['bpjs' => $bpjs],compact('mitra','d_mitra_divisi'));

    }
    public function update(Request $request,$nik)
    {

        
        $rules = [
                 
                  "p_nik" => "required",
                  "npp" => "required",
                  "p_name" => "required",
                  "p_birthdate" => "required",
                  "h_keluarga" => "required",
                  "TMT" => "required",
                  "nf_1" => "required",       
                  "kelas" => "required",
                   "m_id_1" => "required",
                  "md_id_1" => "required", 
                                  
            ];
      $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
          return Redirect::back()
          ->witherrors($validator)
          ->withinput();
        }
        else
        {
         
      $bpjs = bpjs::findOrfail($nik);
       $bpjs->no_kartu=$request->no_kartu;
       $bpjs->p_nik=$request->p_nik;
       $bpjs->npp=$request->npp;
       $bpjs->p_name=$request->p_name;
       $bpjs->p_birthdate=date('Y-m-d',strtotime($request->p_birthdate));
       $bpjs->h_keluarga=$request->h_keluarga;
       $bpjs->TMT=date('Y-m-d',strtotime($request->TMT));
       $bpjs->nf_1=$request->nf_1;
       $bpjs->p_state=$request->p_state;
       $bpjs->kelas=$request->kelas;
       $bpjs->m_id=$request->m_id_1;
       $bpjs->md_id=$request->md_id_1;
      $bpjs->save();
      
      return redirect('bpjs');
        }
        


    }
    public function autocomplete(Request $request){
  
        $term = $request->term;
        
        $results = array();
        $queries = DB::table('d_pekerja')
            ->where('d_pekerja.p_nik', 'like', '%'.$term.'%')
            ->take(100)->get();

        if ($queries == null){
            $results[] = [ 'id' => null, 'label' => 'Tidak ditemukan data terkait'];
        } else {

            foreach ($queries as $query)
            {
                $results[] = [ 'id' => $query->p_nik, 'label' => $query->p_nik];
            }
        }

        return Response::json($results);
    }   
    public function setnama($nik){   

        $setnama=DB::table('d_pekerja')->where('d_pekerja.p_nik',$nik)->first();
      return Response::json($setnama);
    }




}
