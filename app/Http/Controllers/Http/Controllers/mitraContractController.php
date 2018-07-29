<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\d_jabatan;

use App\d_comp;

use App\d_mitra;

use App\d_mitra_contract;

use App\Http\Controllers\mitraPekerjaController;

use App\Http\Controllers\pdmController;

use Yajra\Datatables\Datatables;

use Response;

use Validator;

use Carbon\carbon;

use DB;

class mitraContractController extends Controller
{
    public function index()
    {
        return view('mitra-contract.index');
    }

    public function cari()
    {
        return view('mitra-contract.cari');
    }

    public function data()
    {
        DB::statement(DB::raw('set @rownum=0'));
        $mc = DB::table('d_mitra_contract')
            ->join('d_mitra', 'd_mitra.m_id', '=', 'd_mitra_contract.mc_mitra')
            ->join('d_jabatan_pelamar', 'jp_id', '=', 'd_mitra_contract.mc_jabatan')
            ->join('d_mitra_divisi', function ($q){
                $q->on('d_mitra_contract.mc_divisi', '=', 'd_mitra_divisi.md_id')
                    ->on('d_mitra_contract.mc_mitra', '=', 'd_mitra_divisi.md_mitra');
            })
            ->select('d_mitra_contract.mc_mitra', 'd_mitra_contract.mc_contractid', 'd_mitra_contract.mc_divisi', 'd_mitra_contract.mc_no'
                , 'd_mitra_divisi.md_name'
                , 'd_jabatan_pelamar.jp_name'
                , 'jp_id'
                , 'd_mitra_contract.mc_need'
                , 'd_mitra_contract.mc_fulfilled'
                , 'd_mitra.m_name',
                DB::raw('@rownum  := @rownum  + 1 AS number')
            )
            ->orderBy('d_mitra_contract.mc_insert', 'DESC')->where('mc_need', '>', DB::raw('mc_fulfilled'))
            ->get();

        $mc = collect($mc);

        return Datatables::of($mc)

            ->addColumn('action', function ($mc) {
                return '<div class="text-center">
                    <a style="margin-left:5px;" title="Detail" type="button" onclick="detail('.$mc->mc_mitra.', '.$mc->mc_divisi.')"  class="btn btn-info btn-xs"><i class="glyphicon glyphicon-folder-open"></i></a>
                    <a style="margin-left:5px;" title="Edit" type="button" class="btn btn-warning btn-xs" href="data-kontrak-mitra/' . $mc->mc_mitra . '/' . $mc->mc_contractid . '/edit"><i class="glyphicon glyphicon-edit"></i></a>
                    <a style="margin-left:5px;" type="button" class="btn btn-danger btn-xs" title="Hapus" onclick="hapus(' . $mc->mc_mitra . ',' . $mc->mc_contractid . ')"><i class="glyphicon glyphicon-trash"></i></a>
                  </div>';
            })
            ->make(true);
    }

    public function tambah()
    {
        $comp = d_comp::get();
        $mitra = d_mitra::get();
        $jabatan = DB::table('d_jabatan_pelamar')->get();
        $d_mitra_divisi = DB::table('d_mitra_divisi')
            ->groupBy('md_name')
            ->get();

        return view('mitra-contract.formTambah', compact('d_mitra_divisi', 'comp', 'mitra','jabatan'));
    }

    public function simpan(Request $request)
    {
        DB::beginTransaction();
        try{
            $request->Tanggal_Kontrak = Carbon::createFromFormat('d/m/Y', $request->Tanggal_Kontrak, 'Asia/Jakarta');
            $request->Batas_Kontrak = Carbon::createFromFormat('d/m/Y', $request->Batas_Kontrak, 'Asia/Jakarta');
            $kontrak = $this->nomou();
            $mc_contractid = d_mitra_contract::max('mc_contractid');
            $mc_contractid = $mc_contractid + 1;
            d_mitra_contract::create([
                'mc_mitra' => $request->mitra,
                'mc_contractid' => $mc_contractid,
                'mc_divisi' => $request->divisi,
                'mc_comp' => $request->perusahaan,
                'mc_no' => $kontrak,
                'mc_date' => $request->Tanggal_Kontrak,
                'mc_expired' => $request->Batas_Kontrak,
                'mc_need' => $request->Jumlah_Pekerja,
                'mc_fulfilled' => $request->totalPekerja,
                'mc_jabatan' => $request->jabatan,
                'mc_jobdesk' => $request->jobdesk,
                'mc_note' => $request->note,
            ]);

            DB::commit();
            return response()->json([
                'status' => 'berhasil'
            ]);
        } catch (\Exception $e){
            DB::rollback();
            return response()->json([
                'status' => 'gagal',
                'data' => $e
            ]);
        }
    }

    public function edit($mitra, $mc_contractid)
    {
        return DB::transaction(function () use ($mitra, $mc_contractid) {
            $mitra_contract = d_mitra_contract::where('mc_mitra', $mitra)->where('mc_contractid', $mc_contractid)->first();
            $comp = d_comp::get();
            $mitra = d_mitra::get();
            $jabatan = DB::table('d_jabatan_pelamar')->get();
            $d_mitra_divisi = DB::table('d_mitra_divisi')->get();
            if ($mitra_contract != null || $mitra_contract != '') {
                return view('mitra-contract.formEdit', compact('d_mitra_divisi', 'mitra_contract', 'mitra', 'comp','jabatan'));
            }
            return response()->json([
                'status' => 'Data tidak di temukan',
            ]);
        });
    }

    public function perbarui(Request $request, $mitra, $id_detail)
    {
      //  dd($request);
        return DB::transaction(function () use ($request, $mitra, $id_detail) {
            $Tanggal_Kontrak = Carbon::createFromFormat('d/m/Y', $request->Tanggal_Kontrak, 'Asia/Jakarta');
            $Batas_Kontrak = Carbon::createFromFormat('d/m/Y', $request->Batas_Kontrak, 'Asia/Jakarta');

            $rules = [
                "kontrak" => "required",
                "perusahaan" => "required",
                "mitra" => "required",
                "Jumlah_Pekerja" => "required|numeric",
                "jabatan" => "required",
                "divisi" => "required",
                "jobdesk" => "required",
                "note" => "required",

            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'gagal',
                    'data' => $validator->errors()->toArray()
                ]);
            }


            $mitra_contract = d_mitra_contract::where('mc_mitra', $mitra)->where('mc_contractid', $id_detail);
            if (count($mitra_contract->first()) != 0) {
                $mitra_contract->update([
                    'mc_divisi' => $request->divisi,
                    'mc_comp' => $request->perusahaan,
                    'mc_no' => $request->kontrak,
                    'mc_date' => $Tanggal_Kontrak,
                    'mc_expired' => $Batas_Kontrak,
                    'mc_need' => $request->Jumlah_Pekerja,
                    'mc_fulfilled' => $request->totalPekerja,
                    'mc_jabatan' => $request->jabatan,
                    'mc_jobdesk' => $request->jobdesk,
                    'mc_note' => $request->note,
                ]);

                return response()->json([
                    'status' => 'berhasil',
                ]);


            }
            return response()->json([
                'status' => 'Data tidak di temukan',
            ]);
        });
    }


    public function hapus($mitra, $mc_contractid)
    {
        /*  return DB::transaction(function() use ($mitra,$mc_contractid) {
              $mitra_contract=d_mitra_contract::where('mc_mitra',$mitra)
              ->where('mc_contractid',$mc_contractid);
              if($mitra_contract->delete()){
                     return response()->json([
                          'status' => 'berhasil',
                      ]);
              }
          });*/
        DB::beginTransaction();
        try {
            d_mitra_contract::where('mc_mitra', '=', $mitra)->where('mc_contractid', '=', $mc_contractid)
                ->delete();
            DB::commit();
            return response()->json([
                'status' => 'berhasil',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'gagal',
            ]);
        }
    }

    public function GetDivisi(Request $request)
    {
        $mitra = $request->mitra;
        $data = DB::table('d_mitra_divisi')
            ->select('md_name', 'md_id')
            ->where('md_mitra', '=', $mitra)
            ->orderBy('md_name')
            ->get();

        return response()->json([
            'data' => $data
        ]);
    }

    public function detail($idmitra, $iddivisi){
      $data = DB::table('d_mitra_contract')
                  ->join('d_mitra', 'm_id', '=', 'mc_mitra')
                  ->join('d_jabatan_pelamar', 'jp_id', '=', 'mc_jabatan')
                  ->join('d_comp', 'c_id', '=', 'mc_comp')
                  ->join('d_mitra_divisi', function($e){
                    $e->on('mc_mitra', '=', 'md_mitra')
                    ->on('mc_divisi', '=', 'md_id');
                  })
                  ->select('m_name'
                    , 'md_name'
                    , 'jp_name'
                    , 'c_name'
                    , 'mc_no'
                    , 'mc_jobdesk'
                    , 'mc_note')
                  ->where('mc_mitra', '=', $idmitra)
                  ->where('mc_divisi', '=', $iddivisi)
                  ->get();

      $pekerja = DB::table('d_mitra_pekerja')
                ->join('d_pekerja', 'p_id', '=', 'mp_pekerja')
                ->select('mp_mitra_nik'
                , 'p_name'
                , 'p_nip'
                , 'p_hp')
                ->where('mp_mitra', '=', $idmitra)
                ->where('mp_divisi', '=', $iddivisi)
                ->get();

              return response()->json([
                'data' => $data,
                'pekerja' => $pekerja
              ]);
    }

    public function searchresult(Request $request){

      $keyword = $request->term;

      $data = DB::table('d_mitra_contract')
      ->join('d_mitra', 'm_id', '=', 'mc_mitra')
      ->join('d_jabatan_pelamar', 'jp_id', '=', 'mc_jabatan')
      ->join('d_mitra_divisi', function($e){
        $e->on('md_mitra', '=', 'mc_mitra')
        ->on('md_id', '=', 'mc_divisi');
      })
      ->where('mc_no', 'LIKE', '%'.$keyword.'%')
      ->ORwhere('m_name', 'LIKE', '%'.$keyword.'%')
      ->select('mc_contractid', 'mc_no', 'mc_need', 'mc_fulfilled', 'jp_name', 'md_name', 'm_name')->get();

      if ($data == null) {
          $results[] = ['id' => null, 'label' => 'Tidak ditemukan data terkait'];
      } else {

          foreach ($data as $query) {
              $results[] = ['id' => $query->mc_contractid, 'label' => $query->mc_no . ' (' . $query->m_name . ' Sisa ' . $query->mc_need. ')'];
          }
      }

      return Response::json($results);
    }

    public function getdata(Request $request){
        //dd($request);
        $id = $request->id;

        $data = DB::table('d_mitra_contract')
        ->join('d_mitra', 'm_id', '=', 'mc_mitra')
        ->join('d_jabatan_pelamar', 'jp_id', '=', 'mc_jabatan')
        ->join('d_mitra_divisi', function($e){
          $e->on('md_mitra', '=', 'mc_mitra')
          ->on('md_id', '=', 'mc_divisi');
        })
        ->where('mc_contractid', '=', $id)
        ->select('mc_mitra', 'mc_contractid', 'mc_no', 'mc_need', 'mc_fulfilled', 'jp_name', 'md_name', 'm_name')->get();

        return response()->json([
          'data' => $data
        ]);
    }

    public function nomou(){
      $data = DB::select(DB::raw("SELECT MAX(LEFT(mc_no,5)) as kodemax From d_mitra_contract WHERE DATE_FORMAT(mc_insert, '%m/%Y') = DATE_FORMAT(CURRENT_DATE(), '%m/%Y')"));

      if (count($data) > 0) {
        foreach ($data as $x) {
          $temp = ((int)$x->kodemax)+1;
          $kode = sprintf("%05s",$temp);
        }
      } else {
        $kode = "00001";
      }

      $hasil = $kode . '/FPTK/PN/' . date('m') . '/' . date('Y');

      return $hasil;
    }
}
