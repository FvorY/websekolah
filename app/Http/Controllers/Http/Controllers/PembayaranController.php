<?php

namespace App\Http\Controllers;

use App\d_seragam_pekerja;
use App\d_seragam_pekerja_dt;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;
use App\Http\Requests;

class PembayaranController extends Controller
{
    public function index()
    {
        $data = DB::table('d_sales')
            ->join('d_seragam_pekerja', 'sp_sales', '=', 'd_sales.s_id')
            ->join('d_mitra', 'm_id', '=', 's_member')
            ->join('d_item', 'i_id', '=', 'sp_item')
            ->select('s_nota', 'd_sales.s_id', 's_date', 'sp_item', 's_member', 'm_name', 'i_nama', DB::raw('count(sp_id) as jumlah'))
            ->where('sp_status', '=', 'Belum')
            ->groupBy('s_id')
            ->get();

        return view('pembayaran.index', compact('data'));
    }

    public function bayar(Request $request)
    {
        $nota = $request->nota;
        $idSales = DB::table('d_sales')
            ->select('s_id')
            ->where('s_nota', '=', $nota)
            ->get();
        $pekerja = DB::table('d_seragam_pekerja')
            ->join('d_pekerja', 'p_id', '=', 'sp_pekerja')
            ->join('d_size', 's_id', '=', 'sp_item_size')
            ->select('p_name', 'p_hp', 'p_id', 's_nama', DB::raw('round(sp_value - sp_pay_value) as tagihan'), 'sp_value', 'sp_pay_value', 'sp_sales')
            ->where('sp_sales', '=', $idSales[0]->s_id)
            ->get();

        return view('pembayaran.bayar', compact('pekerja', 'nota'));
    }

    public function getPekerja(Request $request)
    {
        $pekerja = DB::table('d_seragam_pekerja')
            ->join('d_pekerja', 'p_id', '=', 'sp_pekerja')
            ->join('d_size', 's_id', '=', 'sp_item_size')
            ->select('p_name', 'p_hp', 'p_id', 's_nama', DB::raw('(sp_value - sp_pay_value) as tagihan'))
            ->where('sp_sales', '=', $request->idSales)
            ->get();

        return response()->json([
            'data' => $pekerja
        ]);
    }

    public function save(Request $request)
    {
        DB::beginTransaction();
        try {

            $bayar = $request->bayar;
            $pekerja = $request->pekerja;
            $nota = $request->nota;

            $idSales = DB::table('d_sales')
                ->select('s_id')
                ->where('s_nota', '=', $nota)
                ->first();

            $idSales = $idSales->s_id;
            $sekarang = Carbon::now('Asia/Jakarta');

            for ($i = 0; $i < count($bayar); $i++) {
                $bayar[$i] = str_replace("Rp. ", '', $bayar[$i]);
                $bayar[$i] = str_replace(".", '', $bayar[$i]);
            }

            for ($i = 0; $i < count($bayar); $i++){
                if ($bayar[$i] != null || $bayar[$i] != ''){
                    d_seragam_pekerja::where('sp_sales', '=', $idSales)
                        ->where('sp_pekerja', '=', $pekerja[$i])
                        ->update(array(
                            'sp_pay_value' => DB::raw('sp_pay_value +'. $bayar[$i])
                        ));

                    $getDetailid = DB::table('d_seragam_pekerja_dt')
                        ->where('spd_sales', '=', $idSales)
                        ->where('spd_pekerja', '=', $pekerja[$i])
                        ->max('spd_detailid');

                    $detailid = $getDetailid + 1;

                    d_seragam_pekerja_dt::insert(array(
                        'spd_sales' => $idSales,
                        'spd_detailid' => $detailid,
                        'spd_pekerja' => $pekerja[$i],
                        'spd_installments' => $bayar[$i],
                        'spd_date' => $sekarang
                    ));
                }
            }

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

    public function getInfoPembayaran(Request $request)
    {
        $pekerja = $request->pekerja;
        $sales = $request->sales;

        $data = DB::table('d_seragam_pekerja_dt')
            ->join('d_pekerja', 'spd_pekerja', '=', 'p_id')
            ->select(DB::raw('round(spd_installments) as spd_installments'), 'p_name', 'spd_date')
            ->where('spd_sales', '=', $sales)
            ->where('spd_pekerja', '=', $pekerja)
            ->get();

        for ($i = 0; $i < count($data); $i++){
            $data[$i]->spd_date = Carbon::parse($data[$i]->spd_date)->format('d/M/Y H:i:s');
        }

        return response()->json([
            'data' => $data
        ]);
    }
}
