<?php

namespace App\Http\Controllers;

use App\d_mitra_contract;
use App\d_mitra_pekerja;
use App\d_pekerja;
use App\d_pekerja_mutation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;
use App\Http\Requests;
use Illuminate\Support\Facades\Session;

class PenerimaanPekerjaController extends Controller
{
    public function index()
    {
        $pekerja = DB::table('d_pekerja')
            ->select('p_id', 'p_name', 'p_address', 'p_nik', 'p_hp')
            ->where(function ($query) {
                $query->where('p_state', '=', null)
                    ->orWhere('p_state', '=', 0);
            })
            ->get();

        return view('penerimaan-pekerja.index', compact('pekerja'));
    }

    public function getNomor(Request $request)
    {
        $jenis = $request->jenis;
        $data = DB::table('d_mitra_contract')
                ->join('d_mitra', 'm_id', '=', 'mc_mitra')
                ->select('mc_mitra', 'mc_contractid', 'mc_no', 'm_name', DB::raw('(mc_need - mc_fulfilled) as kurang'))
                ->get();
        return response()->json([
            'data' => $data
        ]);
    }

    public function save(Request $request)
    {
        DB::beginTransaction();
        try {
            $pekerja = $request->pekerja;
            $nomor = $request->nomor;
            $penerimaan = $request->penerimaan;

            $from = null;

            if ($penerimaan == 'Take Over'){
                $from = $request->from;
            }

            $mp_id = DB::table('d_mitra_pekerja')
                ->max('mp_id');
            $mp_id = $mp_id + 1;

            $info = DB::table('d_mitra_contract')
                ->select('mc_mitra', 'mc_comp', DB::raw('(mc_need - mc_fulfilled) as sisa'), 'mc_divisi', 'mc_contractid')
                ->where('mc_no', '=', $nomor)
                ->get();

            $dataPekerja = DB::table('d_pekerja')
                ->select('p_nik', 'p_id')
                ->whereIn('p_id', $pekerja)
                ->get();

            $data = [];
            $mutasi = [];
            for ($i = 0; $i < count($dataPekerja); $i++){
                $temp = array(
                    'mp_id' => $mp_id,
                    'mp_comp' => $info[0]->mc_comp,
                    'mp_pekerja' => $dataPekerja[$i]->p_id,
                    'mp_mitra' => $info[0]->mc_mitra,
                    'mp_contract' => $info[0]->mc_contractid,
                    'mp_divisi' => $info[0]->mc_divisi,
                    'mp_mitra_nik' => $dataPekerja[$i]->p_nik,
                    'mp_state' => 1,
                    'mp_selection_date' => Carbon::now('Asia/Jakarta'),
                    'mp_workin_date' => Carbon::now('Asia/Jakarta'),
                    'mp_insert' => Carbon::now('Asia/Jakarta'),
                    'mp_no' => $nomor
                );
                array_push($data, $temp);

                $pm_detail = DB::table('d_pekerja_mutation')
                    ->where('pm_pekerja', '=', $dataPekerja[$i]->p_id)
                    ->max('pm_detailid');
                $tempMutasi = array(
                    'pm_pekerja' => $dataPekerja[$i]->p_id,
                    'pm_detailid' => $pm_detail + 1,
                    'pm_date' => Carbon::now('Asia/Jakarta'),
                    'pm_mitra' => $info[0]->mc_mitra,
                    'pm_divisi' => $info[0]->mc_divisi,
                    'pm_detail' => $penerimaan,
                    'pm_from' => $from,
                    'pm_status' => 'Aktif'
                );
                array_push($mutasi, $tempMutasi);
            }

            d_mitra_pekerja::insert($data);
            d_pekerja::whereIn('p_id', $pekerja)->update(array('p_state' => 1, 'p_note' => $penerimaan));
            d_pekerja_mutation::insert($mutasi);
            d_mitra_contract::where('mc_no', '=', $nomor)
                ->update(array(
                    'mc_fulfilled' => DB::raw('(mc_fulfilled + '.count($pekerja).')')
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
