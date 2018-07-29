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

class PembelianController extends Controller
{
    public function index()
    {

        $data = DB::table('d_purchase')
            ->join('d_purchase_dt', 'pd_purchase', '=', 'p_id')
            ->join('d_supplier', 's_id', '=', 'p_supplier')
            ->select('*')
            ->where('pd_receivetime', null)
            ->whereRaw("p_isapproved = 'P' Or p_isapproved = 'Y'")
            ->take(20)
            ->groupBy('p_nota')
            ->get();

        return view('pembelian.index', compact('data'));
    }

    public function create()
    {

        $supplier = DB::table('d_supplier')
            ->select('s_id', 's_company')
            ->where('s_isactive', '=', 'Y')
            ->get();

        $angka = rand(10, 99);
        $tanggal = date('y/m/d/His');
        $nota = 'PO-' . $tanggal . '/'. $angka;

        return view('pembelian.create', compact('supplier', 'nota'));
    }

    public function getItem(Request $request)
    {
        $cari = $request->term;

        $data = DB::table('d_item')
            ->join('d_item_dt', 'id_item', '=', 'i_id')
            ->join('d_size', 's_id', '=', 'id_size')
            ->select('i_id', 'id_detailid', 'i_nama', 's_nama', 'id_price', 'i_warna', DB::raw('concat(i_nama, " ", i_warna, " ", coalesce(s_nama, ""), " ") as nama'))
            ->whereRaw('concat(i_nama, " ", i_warna, " ", coalesce(s_nama, ""), " ") like "%'.$cari.'%"')
            ->where('i_isactive', '=', 'Y')
            ->take(50)->get();

        if ($data == null) {
            $results[] = ['id' => null, 'label' => 'Tidak ditemukan data terkait'];
        } else {

            foreach ($data as $query) {
                $results[] = ['id' => $query->i_id, 'label' => $query->nama, 'harga' => $query->id_price, 'warna' => $query->i_warna, 'i_nama' => $query->i_nama, 'ukuran' => $query->s_nama, 'detailid' => $query->id_detailid ];
            }
        }

        return Response::json($results);
    }

    public function save(Request $request)
    {
        DB::beginTransaction();
        try {

            $id = DB::table('d_purchase')
                ->max('p_id');

            $id = $id + 1;
            $gross = 0;

            for ($i = 0; $i < count($request->qty); $i++){
                $qty = $request->qty[$i];
                $harga = str_replace(".", '', $request->harga[$i]);
                $gross = ($qty * $harga) + $gross;
            }
            $comp = Session::get('mem_comp');

            $data = array(
                'p_id' => $id,
                'p_comp' => $comp,
                'p_date' => Carbon::now('Asia/Jakarta'),
                'p_supplier' => $request->supplier,
                'p_nota' => $request->nota,
                'p_total_gross' => $gross,
                'p_disc_percent' => 0,
                'p_disc_value' => 0,
                'p_pajak' => 0,
                'p_total_net' => $gross,
                'p_jurnal' => '1234'
            );

            d_purchase::insert($data);

            $detailid = DB::table('d_purchase_dt')
                ->where('pd_purchase', '=', $id)
                ->max('pd_detailid');

            $detailid = $detailid + 1;
            $pd = [];

            for ($i = 0; $i < count($request->qty); $i++){
                $data_dt = array(
                    'pd_purchase' => $id,
                    'pd_detailid' => $detailid + $i,
                    'pd_comp' => $comp,
                    'pd_qty' => $request->qty[$i],
                    'pd_value' => str_replace(".", '', $request->harga[$i]),
                    'pd_item' => $request->id[$i],
                    'pd_item_dt' => $request->iddt[$i],
                    'pd_total_gross' => $request->qty[$i] * str_replace(".", '', $request->harga[$i]),
                    'pd_disc_percent' => 0,
                    'pd_disc_value' => str_replace(".",'',$request->disc[$i]),
                    'pd_total_net' => ((int)$request->qty[$i] * (int)str_replace(".", '', $request->harga[$i])) - (int)str_replace(".", '', $request->disc[$i]),
                    'pd_barang_masuk' => 0,
                    'pd_receivetime' => null
                );
                array_push($pd, $data_dt);
            }
            d_purchase_dt::insert($pd);

            DB::commit();
            return response()->json([
                'status' => 'sukses'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'gagal',
                'data' => $e
            ]);
        }

    }

    public function cari(){
      return view('pembelian.cari');
    }

    public function getnota(Request $request){
      $keyword = $request->term;

      $data = DB::table('d_purchase')
          ->join('d_purchase_dt', 'pd_purchase', '=', 'p_id')
          ->join('d_supplier', 's_id', '=', 'p_supplier')
          ->select('*')
          ->where('p_nota', 'LIKE', '%'.$keyword.'%')
          ->take(20)
          ->groupBy('p_nota')
          ->get();

      // dd($data);
      if ($data == null) {
          $results[] = ['id' => null, 'label' => 'Tidak ditemukan data terkait'];
      } else {

          foreach ($data as $query) {
              $results[] = ['id' => $query->p_id, 'label' => $query->p_nota . ' (' . $query->s_company . ')'];
          }
      }

      return response()->json($results);
    }

    public function getdata(Request $request){
      $id = $request->id;

      $data = DB::table('d_purchase')
          ->join('d_purchase_dt', 'pd_purchase', '=', 'p_id')
          ->join('d_supplier', 's_id', '=', 'p_supplier')
          ->select('p_id','s_company', 'p_nota', 'p_total_net', 'pd_receivetime', 'p_isapproved', DB::raw("DATE_FORMAT(p_date, '%d/%m/%Y %H:%i:%s') as p_date"))
          ->where('p_id', $id)
          ->groupBy('p_nota')
          ->get();

      return response()->json([
        'p_id' => $data[0]->p_id,
        'p_date' => $data[0]->p_date,
        's_company' => $data[0]->s_company,
        'p_nota' => $data[0]->p_nota,
        'p_total_net' => $data[0]->p_total_net,
        'pd_receivetime' => $data[0]->pd_receivetime,
        'p_isapproved' => $data[0]->p_isapproved
      ]);
    }

    public function filter(Request $request){

      $data = DB::table('d_purchase')
          ->join('d_purchase_dt', 'pd_purchase', '=', 'p_id')
          ->join('d_supplier', 's_id', '=', 'p_supplier')
          ->select('p_id','s_company', 'p_nota', 'p_total_net', 'pd_receivetime', 'p_isapproved', DB::raw("DATE_FORMAT(p_date, '%d/%m/%Y %H:%i:%s') as p_date"))
          ->whereRaw("date(p_date) >= '".$request->moustart."' AND date(p_date) <= '".$request->mouend."'")
          ->where('pd_receivetime', null)
          ->whereRaw("p_isapproved = 'P' Or p_isapproved = 'Y'")
          ->groupBy('p_nota')
          ->get();

      return response()->json($data);
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

    public function print(){
      $id = DB::table('d_purchase')
          ->max('p_id');

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
            's_nama',
            'p_nota',
            's_address',
            's_phone'
            )
          ->where('p_id', $id)
          ->get();

          $count = count($data);

          // dd($data);
          return view('pembelian.print', compact('data','count'));
    }
}
