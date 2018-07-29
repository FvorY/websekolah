<?php

namespace App\Http\Controllers;

use App\d_sales;
use App\d_sales_dt;
use App\d_seragam_pekerja;
use App\d_stock;
use App\d_stock_mutation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;
use App\Http\Requests;
use Illuminate\Support\Facades\Session;

class PenjualanController extends Controller
{
    public function index()
    {
        return view('pengeluaran.index');
    }

    public function create()
    {
        $mitra = DB::table('d_mitra')
            ->select('m_id', 'm_name')
            ->orderBy('m_name')
            ->get();

        $angka = rand(10, 99);
        $tanggal = date('y/m/d/His');
        $nota = 'PB-' . $tanggal . '/'. $angka;

        return view('pengeluaran.create', compact('nota', 'mitra'));
    }

    public function getItem(Request $request)
    {
        $mitra = $request->mitra;
        $item = DB::table('d_mitra_item')
            ->join('d_item', 'i_id', '=', 'mi_item')
            ->select('i_nama', 'i_id')
            ->where('mi_mitra', '=', $mitra)
            ->get();

        $info = DB::table('d_mitra')
            ->select('m_phone', 'm_id', 'm_name')
            ->where('m_id', '=', $mitra)
            ->first();

        return response()->json([
            'data' => $item,
            'info' => $info
        ]);
    }

    public function getPekerja(Request $request)
    {
        $mitra = $request->mitra;
        $item = $request->item;

        $pekerja = DB::table('d_mitra_pekerja')
            ->join('d_pekerja', 'p_id', '=', 'mp_pekerja')
            ->select('mp_pekerja', 'p_name', 'p_hp')
            ->where('mp_mitra', '=', $mitra)
            ->get();

        $seragam = DB::table('d_item_dt')
            ->join('d_size', 'id_size', '=', 's_id')
            ->select('s_nama', 's_id', 'id_price')
            ->where('id_item', '=', $item)
            ->get();

        return response()->json([
            'pekerja' => $pekerja,
            'seragam' => $seragam
        ]);
    }

    public function save(Request $request)
    {
        DB::beginTransaction();
        try {

            $seragam = $request->seragam;
            $mitra = $request->mitra;
            $ukuran = $request->ukuran;
            $pekerja = $request->masuk;
            $comp = Session::get('mem_comp');
            $sekarang = Carbon::now('Asia/Jakarta');
            $nota = $request->nota;

            $getItem_dt = DB::table('d_item_dt')
                ->select('id_detailid')
                ->where('id_item', '=', $seragam)
                ->whereIn('id_size', $ukuran)
                ->get();

            $item_dt = [];
            for ($i = 0; $i < count($getItem_dt); $i++){
                $item_dt[$i] = $getItem_dt[$i]->id_detailid;
            }

            $getStock = DB::table('d_stock_mutation')
                ->join('d_stock', 's_id', '=', 'sm_stock')
                ->join('d_item_dt', function ($q) {
                    $q->on('s_item', '=', 'id_item')
                        ->on('s_item_dt', '=', 'id_detailid');
                })
                ->select('sm_stock', 'sm_detailid', 'sm_qty', 'sm_use', 'sm_item_dt', DB::raw('(sm_qty - sm_use) as sisa'), 'sm_hpp', 'sm_sell', 'id_size', 'id_price')
                ->where('s_item', '=', $seragam)
                ->whereIn('s_item_dt', $item_dt)
                ->where('s_comp', '=', $comp)
                ->where('s_position', '=', $comp)
                ->where(DB::raw('(sm_qty - sm_use)'), '!=', 0)
                ->orderBy('sm_stock', 'asc')
                ->orderBy('sm_detailid', 'asc')
                ->orderBy('sm_date', 'asc')
                ->get();

//========== pengurangan stock
            $jumlahUkuran = array_count_values($ukuran);
            $ukuranUnik = array_unique($ukuran);
            $ukuranUnik = array_values($ukuranUnik);
            $jumlahSimpan = $jumlahUkuran;
            $ukuranSudah = [];
            $temp_sisa['sisa'] = 0;
            $temp_sisa['size'] = null;
            $totalGross = 0;

            $idSales = DB::table('d_sales')
                ->max('s_id');
            $idSales = $idSales + 1;
            $detailSales = DB::table('d_sales_dt')
                ->where('sd_sales', '=', $idSales)
                ->max('sd_sales');
            $detailSales = $detailSales + 1;

            $sales = array(
                's_id' => $idSales,
                's_comp' => Session::get('mem_comp'),
                's_date' => $sekarang,
                's_member' => $mitra,
                's_nota' => $nota,
                's_disc_percent' => 0,
                's_disc_value' => 0
            );

            d_sales::insert($sales);

            for ($j = 0; $j < count($getStock); $j++){
                for ($i = 0; $i < count($ukuranUnik); $i++){

                    if ($ukuranUnik[$i] == $getStock[$j]->id_size && !in_array($ukuranUnik[$i], $ukuranSudah, true)){

                        $permintaan = $jumlahUkuran[$ukuranUnik[$i]];
                        $updateUse = $getStock[$j]->sm_use + $permintaan;
                        $cekStock = $getStock[$j]->sm_qty - $getStock[$j]->sm_use;
                        $sisaSmQty = $cekStock - $permintaan;

//========== jika terdapat sisa pada transaksi sebelumnya
                        if ($temp_sisa['sisa'] != 0 && $temp_sisa['size'] == $getStock[$j]->id_size){
//========== stok tidak memenuhi
                            if ($sisaSmQty < $temp_sisa['sisa']){
                                d_stock_mutation::where('sm_stock', '=', $getStock[$j]->sm_stock)
                                    ->where('sm_detailid', '=', $getStock[$j]->sm_detailid)
                                    ->update(array(
                                        'sm_use' => $cekStock
                                    ));
                                $temp_sisa['sisa'] = $sisaSmQty * (-1);
                                $temp_sisa['size'] = $getStock[$j]->id_size;
                            }
//========== stok memenuhi
                            else {
                                d_stock_mutation::where('sm_stock', '=', $getStock[$j]->sm_stock)
                                    ->where('sm_detailid', '=', $getStock[$j]->sm_detailid)
                                    ->update(array(
                                        'sm_use' => $updateUse
                                    ));

                                $detel = DB::table('d_stock_mutation')
                                    ->where('sm_stock', '=', $getStock[$j]->sm_stock)
                                    ->max('sm_detailid');

                                $sm_detil = $detel + 1;

                                $data = array(
                                    'sm_stock' => $getStock[$j]->sm_stock,
                                    'sm_detailid' => $sm_detil,
                                    'sm_comp' => Session::get('mem_comp'),
                                    'sm_date' => $sekarang,
                                    'sm_item' => $seragam,
                                    'sm_item_dt' => $getStock[$j]->sm_item_dt,
                                    'sm_detail' => 'Pengeluaran',
                                    'sm_qty' => $jumlahSimpan[$getStock[$j]->id_size],
                                    'sm_use' => '0',
                                    'sm_hpp' => $getStock[$j]->sm_hpp,
                                    'sm_sell' => $getStock[$j]->sm_sell,
                                    'sm_nota' => $nota,
                                    'sm_delivery_order' => $nota,
                                    'sm_petugas' => Session::get('mem')
                                );

                                d_stock_mutation::insert($data);

                                $getQtyStock = DB::table('d_stock')
                                    ->select('s_qty')
                                    ->where('s_id', '=', $getStock[$j]->sm_stock)
                                    ->max('s_qty');

                                $updateQtyStock = $getQtyStock - $jumlahSimpan[$getStock[$j]->id_size];

                                d_stock::where('s_id', '=', $getStock[$j]->sm_stock)->update(array(
                                    's_qty' => $updateQtyStock
                                ));

                                $salesdt = array(
                                    'sd_sales' => $idSales,
                                    'sd_detailid' => $detailSales,
                                    'sd_comp' => Session::get('mem_comp'),
                                    'sd_item' => $seragam,
                                    'sd_item_dt' => $getStock[$j]->sm_item_dt,
                                    'sd_qty' => $jumlahSimpan[$getStock[$j]->id_size],
                                    'sd_value' => $getStock[$j]->id_price,
                                    'sd_total_gross' => $getStock[$j]->id_price * $jumlahSimpan[$getStock[$j]->id_size],
                                    'sd_disc_percent' => 0,
                                    'sd_disc_value' => 0,
                                    'sd_total_net' => $getStock[$j]->id_price * $jumlahSimpan[$getStock[$j]->id_size],
                                    'sd_sell' => $getStock[$j]->sm_sell,
                                    'sd_hpp' => $getStock[$j]->sm_hpp
                                );

                                $detailSales = $detailSales + 1;

                                d_sales_dt::insert($salesdt);
                                $totalGross = $totalGross + ($getStock[$j]->id_price * $jumlahSimpan[$getStock[$j]->id_size]);
                                array_push($ukuranSudah, $ukuranUnik[$i]);
                                $i = count($ukuranUnik) + 1;
                                $temp_sisa['sisa'] = 0;
                                $temp_sisa['size'] = null;
                            }
                        }
//========== jika tidak terdapat sisa pada transaksi sebelumnya
                        else {
//========== stok tidak memenuhi
                            if ($sisaSmQty < 0){
                                d_stock_mutation::where('sm_stock', '=', $getStock[$j]->sm_stock)
                                    ->where('sm_detailid', '=', $getStock[$j]->sm_detailid)
                                    ->update(array(
                                        'sm_use' => $cekStock
                                    ));
                                $temp_sisa['sisa'] = $sisaSmQty * (-1);
                                $temp_sisa['size'] = $getStock[$j]->id_size;
                                $jumlahUkuran[$ukuranUnik[$i]] = $jumlahUkuran[$ukuranUnik[$i]] - $cekStock;
                            }
//========== stok memenuhi
                            else {
                                d_stock_mutation::where('sm_stock', '=', $getStock[$j]->sm_stock)
                                    ->where('sm_detailid', '=', $getStock[$j]->sm_detailid)
                                    ->update(array(
                                        'sm_use' => $updateUse
                                    ));

                                $detel = DB::table('d_stock_mutation')
                                    ->where('sm_stock', '=', $getStock[$j]->sm_stock)
                                    ->max('sm_detailid');

                                $sm_detil = $detel + 1;

                                $data = array(
                                    'sm_stock' => $getStock[$j]->sm_stock,
                                    'sm_detailid' => $sm_detil,
                                    'sm_comp' => Session::get('mem_comp'),
                                    'sm_date' => $sekarang,
                                    'sm_item' => $seragam,
                                    'sm_item_dt' => $getStock[$j]->sm_item_dt,
                                    'sm_detail' => 'Pengeluaran',
                                    'sm_qty' => $jumlahSimpan[$getStock[$j]->id_size],
                                    'sm_use' => '0',
                                    'sm_hpp' => $getStock[$j]->sm_hpp,
                                    'sm_sell' => $getStock[$j]->sm_sell,
                                    'sm_nota' => $nota,
                                    'sm_delivery_order' => $nota,
                                    'sm_petugas' => Session::get('mem')
                                );

                                d_stock_mutation::insert($data);

                                $getQtyStock = DB::table('d_stock')
                                    ->select('s_qty')
                                    ->where('s_id', '=', $getStock[$j]->sm_stock)
                                    ->max('s_qty');

                                $updateQtyStock = $getQtyStock - $jumlahSimpan[$getStock[$j]->id_size];

                                d_stock::where('s_id', '=', $getStock[$j]->sm_stock)->update(array(
                                    's_qty' => $updateQtyStock
                                ));

                                $salesdt = array(
                                    'sd_sales' => $idSales,
                                    'sd_detailid' => $detailSales,
                                    'sd_comp' => Session::get('mem_comp'),
                                    'sd_item' => $seragam,
                                    'sd_item_dt' => $getStock[$j]->sm_item_dt,
                                    'sd_qty' => $jumlahSimpan[$getStock[$j]->id_size],
                                    'sd_value' => $getStock[$j]->id_price,
                                    'sd_total_gross' => $getStock[$j]->id_price * $jumlahSimpan[$getStock[$j]->id_size],
                                    'sd_disc_percent' => 0,
                                    'sd_disc_value' => 0,
                                    'sd_total_net' => $getStock[$j]->id_price * $jumlahSimpan[$getStock[$j]->id_size],
                                    'sd_sell' => $getStock[$j]->sm_sell,
                                    'sd_hpp' => $getStock[$j]->sm_hpp
                                );

                                $detailSales = $detailSales + 1;

                                d_sales_dt::insert($salesdt);
                                $totalGross = $totalGross + ($getStock[$j]->id_price * $jumlahSimpan[$getStock[$j]->id_size]);
                                array_push($ukuranSudah, $ukuranUnik[$i]);
                                $i = count($ukuranUnik) + 1;
                                $temp_sisa['sisa'] = 0;
                                $temp_sisa['size'] = null;
                            }
                        }
                    }
                }
            }

            $getHarga = DB::table('d_item_dt')
                ->select('id_item', 'id_detailid', 'id_size', 'id_price')
                ->whereIn('id_size', $ukuran)
                ->get();

            $getId = DB::table('d_seragam_pekerja')
                ->where('sp_sales', '=', $idSales)
                ->max('sp_id');

            $idSP = $getId + 1;

            $data = [];
            $count = 0;

            for ($j = 0; $j < count($ukuran); $j++){
                for ($i = 0; $i < count($getHarga); $i++){
                    if ($ukuran[$j] == $getHarga[$i]->id_size){
                        $temp = array(
                            'sp_sales' => $idSales,
                            'sp_id' => $idSP + $count,
                            'sp_pekerja' => $pekerja[$j],
                            'sp_item' => $seragam,
                            'sp_item_size' => $ukuran[$j],
                            'sp_qty' => 1,
                            'sp_value' => $getHarga[$i]->id_price,
                            'sp_pay_value' => 0,
                            'sp_status' => 'Belum'
                        );
                        array_push($data, $temp);
                        $i = count($getHarga) + 1;
                        $count = $count + 1;
                    }
                }
            }

            d_seragam_pekerja::insert($data);

            d_sales::where('s_id', '=', $idSales)->update(array(
                's_total_gross' => $totalGross,
                's_total_net' => $totalGross
            ));

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
}
