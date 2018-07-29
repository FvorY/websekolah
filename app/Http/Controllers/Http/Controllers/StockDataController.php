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


class StockDataController extends Controller
{
    public function tabel(){


     $pp = DB::table('d_stock')
          ->join('d_comp', 'd_stock.s_comp', '=', 'd_comp.c_id')
          ->join('d_item', 'd_stock.s_item', '=', 'd_item.i_id')
          ->join('d_item_dt', function ($join) {
                $join->on('d_stock.s_item_dt', '=', 'd_item_dt.id_detailid')
                     ->on('d_item.i_id', '=', 'd_item_dt.id_item');
                })
          ->join('d_size', 'd_size.s_id', '=', 'd_item_dt.id_size')
          ->join('d_kategori', 'k_id', '=', 'i_kategori')
          ->select('d_stock.*','d_comp.c_name','d_size.s_nama','d_item.i_nama', 'd_item_dt.id_price', 'd_item.i_note')
          ->orderBy('d_item.i_nama', 'asc')
          ->get();

          for ($i=0; $i < count($pp); $i++) {
            $pp[$i]->id_price = 'Rp. ' . number_format($pp[$i]->id_price, 0, ',', '.');
          }

                // ->join('d_comp', 'd_stock.s_comp', '=', 'd_comp.c_id')
                // ->join('d_item', 'd_stock.s_item', '=', 'd_item.i_id')
                // ->join('d_item_dt', function ($join) {
                //       $join->on('d_stock.s_item_dt', '=', 'd_item_dt.id_detailid')
                //            ->on('d_item.i_id', '=', 'd_item_dt.id_item');
                //       })
                // ->join('d_mitra_item', function ($join) {
                //       $join->on('d_item.i_id', '=', 'd_mitra_item.mi_item');
                //       })
                // ->join('d_mitra','d_mitra.m_id', '=', 'd_mitra_item.mi_mitra')
                // ->join('d_size', 'd_item_dt.id_size', '=', 'd_size.s_id')
                // ->select('d_stock.*','d_comp.c_name','d_size.s_nama','d_item.i_nama','d_mitra.m_name')
                // ->orderBy('d_item.i_nama', 'asc')
                // ->get();

    $datax = $this->setData($pp);
    //dd(($datax));

    echo json_encode($datax);



  }

  // untuk filter
  public function tabel2(Request $request){

       $req_gudang = $request->gudang;
       $req_barang = $request->barang;

       $pp;

       if($req_gudang == "null" && $req_barang == "null"){
         $pp = DB::table('d_stock')
              ->join('d_comp', 'd_stock.s_comp', '=', 'd_comp.c_id')
              ->join('d_item', 'd_stock.s_item', '=', 'd_item.i_id')
              ->join('d_item_dt', function ($join) {
                    $join->on('d_stock.s_item_dt', '=', 'd_item_dt.id_detailid')
                         ->on('d_item.i_id', '=', 'd_item_dt.id_item');
                    })
              ->join('d_size', 'd_size.s_id', '=', 'd_item_dt.id_size')
              ->join('d_kategori', 'k_id', '=', 'i_kategori')
              ->select('d_stock.*','d_comp.c_name','d_size.s_nama','d_item.i_nama', 'd_item_dt.id_price', 'd_item.i_note')
              ->orderBy('d_item.i_nama', 'asc')
              ->get();
        }

        else if ($req_gudang != "null" && $req_barang != "null"){
          $pp = DB::table('d_stock')
               ->join('d_comp', 'd_stock.s_comp', '=', 'd_comp.c_id')
               ->join('d_item', 'd_stock.s_item', '=', 'd_item.i_id')
               ->join('d_item_dt', function ($join) {
                     $join->on('d_stock.s_item_dt', '=', 'd_item_dt.id_detailid')
                          ->on('d_item.i_id', '=', 'd_item_dt.id_item');
                     })
               ->join('d_size', 'd_size.s_id', '=', 'd_item_dt.id_size')
               ->join('d_kategori', 'k_id', '=', 'i_kategori')
               ->select('d_stock.*','d_comp.c_name','d_size.s_nama','d_item.i_nama', 'd_item_dt.id_price', 'd_item.i_note')
               ->where('d_stock.s_item', '=', $req_barang)
               ->where('d_stock.s_comp', '=', $req_gudang)
               ->orderBy('d_item.i_nama', 'asc')
               ->get();
          // $pp = DB::table('d_stock')
          //       ->join('d_comp', 'd_stock.s_comp', '=', 'd_comp.c_id')
          //       ->join('d_item', 'd_stock.s_item', '=', 'd_item.i_id')
          //       ->join('d_item_dt', function ($join) {
          //             $join->on('d_stock.s_item_dt', '=', 'd_item_dt.id_detailid')
          //                  ->on('d_item.i_id', '=', 'd_item_dt.id_item');
          //             })
          //       ->join('d_mitra_item', function ($join) {
          //             $join->on('d_item.i_id', '=', 'd_mitra_item.mi_item');
          //             })
          //       ->join('d_mitra','d_mitra.m_id', '=', 'd_mitra_item.mi_mitra')
          //       ->join('d_size', 'd_item_dt.id_size', '=', 'd_size.s_id')
          //       ->select('d_stock.*','d_comp.c_name','d_size.s_nama','d_item.i_nama','d_mitra.m_name')
          //       ->where('d_stock.s_item', '=', $req_barang)
          //       ->where('d_stock.s_comp', '=', $req_gudang)
          //       ->orderBy('d_item.i_nama', 'asc')
          //       ->get();
        }

        else if ($req_gudang != "null" && $req_barang == "null"){
          $pp = DB::table('d_stock')
               ->join('d_comp', 'd_stock.s_comp', '=', 'd_comp.c_id')
               ->join('d_item', 'd_stock.s_item', '=', 'd_item.i_id')
               ->join('d_item_dt', function ($join) {
                     $join->on('d_stock.s_item_dt', '=', 'd_item_dt.id_detailid')
                          ->on('d_item.i_id', '=', 'd_item_dt.id_item');
                     })
               ->join('d_size', 'd_size.s_id', '=', 'd_item_dt.id_size')
               ->join('d_kategori', 'k_id', '=', 'i_kategori')
               ->select('d_stock.*','d_comp.c_name','d_size.s_nama','d_item.i_nama', 'd_item_dt.id_price', 'd_item.i_note')
               ->where('d_stock.s_comp', '=', $req_gudang)
               ->orderBy('d_item.i_nama', 'asc')
               ->get();
          // $pp = DB::table('d_stock')
          //       ->join('d_comp', 'd_stock.s_comp', '=', 'd_comp.c_id')
          //       ->join('d_item', 'd_stock.s_item', '=', 'd_item.i_id')
          //       ->join('d_item_dt', function ($join) {
          //             $join->on('d_stock.s_item_dt', '=', 'd_item_dt.id_detailid')
          //                  ->on('d_item.i_id', '=', 'd_item_dt.id_item');
          //             })
          //       ->join('d_mitra_item', function ($join) {
          //             $join->on('d_item.i_id', '=', 'd_mitra_item.mi_item');
          //             })
          //       ->join('d_mitra','d_mitra.m_id', '=', 'd_mitra_item.mi_mitra')
          //       ->join('d_size', 'd_item_dt.id_size', '=', 'd_size.s_id')
          //       ->select('d_stock.*','d_comp.c_name','d_size.s_nama','d_item.i_nama','d_mitra.m_name')
          //       ->where('d_stock.s_comp', '=', $req_gudang)
          //       ->orderBy('d_item.i_nama', 'asc')
          //       ->get();
        }

        else if ($req_gudang == "null" && $req_barang != "null"){
          $pp = DB::table('d_stock')
               ->join('d_comp', 'd_stock.s_comp', '=', 'd_comp.c_id')
               ->join('d_item', 'd_stock.s_item', '=', 'd_item.i_id')
               ->join('d_item_dt', function ($join) {
                     $join->on('d_stock.s_item_dt', '=', 'd_item_dt.id_detailid')
                          ->on('d_item.i_id', '=', 'd_item_dt.id_item');
                     })
               ->join('d_size', 'd_size.s_id', '=', 'd_item_dt.id_size')
               ->join('d_kategori', 'k_id', '=', 'i_kategori')
               ->select('d_stock.*','d_comp.c_name','d_size.s_nama','d_item.i_nama', 'd_item_dt.id_price', 'd_item.i_note')
               ->where('d_stock.s_item', '=', $req_barang)
               ->orderBy('d_item.i_nama', 'asc')
               ->get();
          // $pp = DB::table('d_stock')
          //       ->join('d_comp', 'd_stock.s_comp', '=', 'd_comp.c_id')
          //       ->join('d_item', 'd_stock.s_item', '=', 'd_item.i_id')
          //       ->join('d_item_dt', function ($join) {
          //             $join->on('d_stock.s_item_dt', '=', 'd_item_dt.id_detailid')
          //                  ->on('d_item.i_id', '=', 'd_item_dt.id_item');
          //             })
          //       ->join('d_mitra_item', function ($join) {
          //             $join->on('d_item.i_id', '=', 'd_mitra_item.mi_item');
          //             })
          //       ->join('d_mitra','d_mitra.m_id', '=', 'd_mitra_item.mi_mitra')
          //       ->join('d_size', 'd_item_dt.id_size', '=', 'd_size.s_id')
          //       ->select('d_stock.*','d_comp.c_name','d_size.s_nama','d_item.i_nama','d_mitra.m_name')
          //       ->where('d_stock.s_item', '=', $req_barang)
          //       ->orderBy('d_item.i_nama', 'asc')
          //       ->get();
        }

        for ($i=0; $i < count($pp); $i++) {
          $pp[$i]->id_price = 'Rp. ' . number_format($pp[$i]->id_price, 0, ',', '.');
        }

        // dd($req);
        $datax = $this->setData($pp);

        echo json_encode($datax);



  }




  public function index(){

    $databarang = DB::select(DB::raw("SELECT * FROM d_item"));
    $datagudang = DB::select(DB::raw("SELECT * FROM d_comp"));

    return view('manajemen-stock.data-stock.index', compact('databarang','datagudang'));
  }

  public function setData($pp){
    $data = array();
    foreach ($pp as $r) {
      $data[] = (array) $r;
     }
        $datax = array('data' => $data);
        return $datax;
    }

    public function printall(){
      $pp = DB::table('d_stock')
           ->join('d_comp', 'd_stock.s_comp', '=', 'd_comp.c_id')
           ->join('d_item', 'd_stock.s_item', '=', 'd_item.i_id')
           ->join('d_item_dt', function ($join) {
                 $join->on('d_stock.s_item_dt', '=', 'd_item_dt.id_detailid')
                      ->on('d_item.i_id', '=', 'd_item_dt.id_item');
                 })
           ->join('d_size', 'd_size.s_id', '=', 'd_item_dt.id_size')
           ->join('d_kategori', 'k_id', '=', 'i_kategori')
           ->select('d_stock.*','d_comp.c_name','d_size.s_nama','d_item.i_nama', 'd_item_dt.id_price', 'd_item.i_note')
           ->orderBy('d_item.i_nama', 'asc')
           ->get();

           for ($i=0; $i < count($pp); $i++) {
             $pp[$i]->id_price = 'Rp. ' . number_format($pp[$i]->id_price, 0, ',', '.');
           }

      // dd($pp);
      return view('manajemen-stock.data-stock.printall', compact('pp'));
    }

    public function print(){
      return view('manajemen-stock.data-stock.print');
    }

    public function getpilih(){
      $data = DB::table('d_item')
            ->select('i_id', 'i_nama')
            ->get();

      return response()->json($data);
    }

    public function getprint(Request $request){
      $id = $request->id;

      $pp = DB::table('d_stock')
           ->join('d_comp', 'd_stock.s_comp', '=', 'd_comp.c_id')
           ->join('d_item', 'd_stock.s_item', '=', 'd_item.i_id')
           ->join('d_item_dt', function ($join) {
                 $join->on('d_stock.s_item_dt', '=', 'd_item_dt.id_detailid')
                      ->on('d_item.i_id', '=', 'd_item_dt.id_item');
                 })
           ->join('d_size', 'd_size.s_id', '=', 'd_item_dt.id_size')
           ->join('d_kategori', 'k_id', '=', 'i_kategori')
           ->select('d_stock.*','d_comp.c_name','d_size.s_nama','d_item.i_nama', 'd_item_dt.id_price', 'd_item.i_note')
           ->where('s_item',$id)
           ->orderBy('d_item.i_nama', 'asc')
           ->get();

           for ($i=0; $i < count($pp); $i++) {
             $pp[$i]->id_price = 'Rp. ' . number_format($pp[$i]->id_price, 0, ',', '.');
           }

        return view('manajemen-stock.data-stock.print', compact('pp'));
    }


}
