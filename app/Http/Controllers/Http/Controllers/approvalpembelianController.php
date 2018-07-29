<?php

namespace App\Http\Controllers;

use App\d_purchase;
use App\d_purchase_dt;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;
use App\Http\Requests;
use Illuminate\Support\Facades\Session;
use Response;

class approvalpembelianController extends Controller
{
    public function index(){
      $data = DB::table('d_purchase')
          ->join('d_purchase_dt', 'pd_purchase', '=', 'p_id')
          ->join('d_supplier', 's_id', '=', 'p_supplier')
          ->select('*')
          ->where('pd_receivetime', null)
          ->where('p_isapproved', 'P')
          ->groupBy('p_nota')
          ->get();

      return view('approvalpembelian.index', compact('data'));
    }

    public function setujui(Request $request){
      $id = $request->id;
      try {
        d_purchase::where('p_id',$id)
                  ->update([
                    'p_isapproved' => 'Y'
                  ]);
        return response()->json([
            'status' => 'berhasil'
        ]);
      } catch (\Exception $e) {
        return response()->json([
            'status' => 'gagal'
        ]);
      }


    }

    public function tolak(Request $request){
      $id = $request->id;
      try {
        d_purchase::where('p_id',$id)
                  ->update([
                    'p_isapproved' => 'N'
                  ]);
        return response()->json([
            'status' => 'berhasil'
        ]);
      } catch (\Exception $e) {
        return response()->json([
            'status' => 'gagal'
        ]);
      }


    }

    public function detail(Request $request){
      $id = $request->id;
      $count = 0;

      $data = DB::table('d_purchase')
      ->join('d_purchase_dt', 'pd_purchase', '=', 'p_id')
      ->join('d_supplier', 'd_supplier.s_id', '=', 'p_supplier')
      ->join('d_item', 'i_id', '=', 'pd_item')
      ->join('d_item_dt', function($e){
          $e->on('id_detailid', '=', 'pd_item_dt');
          $e->on('id_item', '=', 'i_id');
      })
      ->join('d_size', 'd_size.s_id', '=', 'id_size')
      ->join('d_kategori', 'k_id', '=', 'i_kategori')
      ->select(
        'p_date',
        'p_total_net',
        'pd_value',
        'pd_qty',
        'i_nama',
        'pd_total_gross',
        'pd_disc_value',
        'pd_disc_percent',
        'pd_total_net',
        'i_nama',
        'p_total_gross',
        'k_nama',
        'i_warna',
        'p_pajak',
        's_company',
        's_nama'
        )
      ->where('p_id', $id)
      ->get();

      $count = count($data);

      return response()->json([
        $data,
        'count' => $count
      ]);
    }

    public function setujuilist(Request $request){
      DB::beginTransaction();
      try {
        for ($i=0; $i < count($request->pilih); $i++) {
          d_purchase::where('p_id',$request->pilih[$i])
                    ->update([
                      'p_isapproved' => 'Y'
                    ]);
        }

        DB::commit();
        return response()->json([
            'status' => 'berhasil'
        ]);
      } catch (\Exception $e) {
        DB::rollback();
        return response()->json([
            'status' => 'gagal'
        ]);
      }
    }

    public function tolaklist(Request $request){
      DB::beginTransaction();
      try {
        for ($i=0; $i < count($request->pilih); $i++) {
          d_purchase::where('p_id',$request->pilih[$i])
                    ->update([
                      'p_isapproved' => 'N'
                    ]);
        }

        DB::commit();
        return response()->json([
            'status' => 'berhasil'
        ]);
      } catch (\Exception $e) {
        DB::rollback();
        return response()->json([
            'status' => 'gagal'
        ]);
      }
    }

}
