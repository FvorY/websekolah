<?php
namespace App\Http\Controllers;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PDF;
use DB;
use Response;
Use Carbon\Carbon;
use Session;
use Mail;
use Illuminate\Support\Facades\Input;
use Dompdf\Dompdf;


class mitraDivisiController extends Controller
{

  public function index(){
    return view ('mitra_divisi.index');
  }

  public function tabel(){
    $list = DB::table('d_mitra')
                  ->select('d_mitra.*')
                  ->where('m_status', '=', 'Aktif')
                  ->ORwhere('m_status_approval', '=', 'Y')
                  ->get();
        $data = array();
        foreach ($list as $r) {
            $data[] = (array) $r;
        }
        $i=0;
        foreach ($data as $key) {
            // add new button
            $data[$i]['button'] = '<div class="text-center" style="width: 100%">
                                   <button id="'.$data[$i]['m_id'].'" data-toggle="tooltip" title="Tambah" style="margin-left:5px;" class="btn btn-success btn-xs tambah" ><i class="fa fa-plus"></i></button>
                                   <button id="'.$data[$i]['m_id'].'" data-toggle="tooltip" title="Detail" style="margin-left:5px;" class="btn btn-info btn-xs detail" ><i class="fa fa-folder-open"></i></button>
                                   <button id="'.$data[$i]['m_id'].'" data-toggle="tooltip" title="Edit" style="margin-left:5px;" class="btn btn-warning btn-xs edit" ><i class="fa fa-pencil-square-o"></i></button>
                                   </div> ';
            $i++;
        }
        $datax = array('data' => $data);
        echo json_encode($datax);
  }

  public function get_mitra(Request $request){
        $data = DB::table('d_mitra')
                  ->select('d_mitra.*')
                  ->where('m_id', $request->id)
                  ->get();
        return $data;
  }

  public function tambah(Request $request){

        for ($j=0; $j <count($request->nama_divisi) ; $j++) {
        if($request->nama_divisi[$j] == null){
          return response()->json(['status' => 0]);
         }
        }

        for ($i=0; $i < count($request->nama_divisi); $i++) {
          $cari_max_md_id = DB::table('d_mitra_divisi')
                            ->max('md_id');

          if ($cari_max_md_id != null) {
            $cari_max_md_id += 1;
          }else{
            $cari_max_md_id = 1;
          }
        $save_mitra_divisi = DB::table('d_mitra_divisi')
                 ->insert([
                  'md_mitra'  => $request->id_mitra,
                  'md_id'     => $cari_max_md_id,
                  'md_name'   => strtoupper($request->nama_divisi[$i])
              ]);
       }
        return response()->json(['status' => 1]);
  }

  public function detail(Request $request){

    $id =$request->id;
    $list = DB::table('d_mitra')
                ->leftjoin('d_mitra_divisi', 'd_mitra_divisi.md_mitra', '=', 'd_mitra.m_id')
                ->select('d_mitra_divisi.*','d_mitra.*')
                ->where('m_id', $id)
                ->get();

    $data = array();
        foreach ($list as $r) {
            $data[] = (array) $r;
        }
        $i=0;
        foreach ($data as $key) {
            // add new button
            if ($data[$i]['md_name'] == null){
              $data[$i]['nomer'] = "data divisi kosong";
              $data[$i]['md_name'] = "data divisi kosong";
            }else{
            $data[$i]['nomer']= $i+1;
            }
            $i++;
        }
        echo json_encode($data);
  }

  public function get_data_edit(Request $request){
    $id =$request->id;
    $list = DB::table('d_mitra')
                ->leftjoin('d_mitra_divisi', 'd_mitra_divisi.md_mitra', '=', 'd_mitra.m_id')
                ->select('d_mitra_divisi.*','d_mitra.*')
                ->where('m_id', $id)
                ->get();

    $data = array();
        foreach ($list as $r) {
            $data[] = (array) $r;
        }
        $i=0;
        foreach ($data as $key) {
            // add new button
            if ($data[$i]['md_name'] == null){
              $data[$i]['nomer'] = "data divisi kosong";
              $data[$i]['md_name'] = "data divisi kosong";
            }else{
            $data[$i]['nomer']= $i+1;
            }
            $i++;
        }
        echo json_encode($data);
  }

  public function edit(Request $request){
      for ($j=0; $j <count($request->nama_divisi) ; $j++) {
        if($request->nama_divisi[$j] == null){
          return response()->json(['status' => 0]);
         }
        }

      for ($i=0; $i < count($request->nama_divisi); $i++) {

        $save_mitra_divisi = DB::table('d_mitra_divisi')
                 ->where('md_mitra', $request->id_mitra[$i])
                 ->where('md_id', $request->id_divisi[$i])
                 ->update([
                  'md_name'   => strtoupper($request->nama_divisi[$i])
              ]);
       }

        return response()->json(['status' => 1]);
  }

}
