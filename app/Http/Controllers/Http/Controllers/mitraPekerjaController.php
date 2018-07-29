<?php

namespace App\Http\Controllers;

use App\d_pekerja_mutation;
use Illuminate\Http\Request;

use App\Http\Requests;

use App\d_mitra_pekerja;
use Response;
use App\d_pekerja;
use App\surat;
use Illuminate\Support\Facades\Session;
use Redirect;
use App\d_mitra_contract;

use function Sodium\add;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\mitraContractController;
use App\Http\Controllers\pdmController;

use Validator;
use Carbon\carbon;

use DB;

class mitraPekerjaController extends Controller
{

    public function index()
    {
        return view('mitra-pekerja.index');

    }

    public function cari()
    {
        return view('mitra-pekerja.cari');

    }

    public function pencarian(Request $request)
    {
        $kondisi = $request->term;

        $data = DB::table('d_mitra_contract')
            ->leftJoin('d_mitra', 'mc_mitra', '=', 'm_id')
            ->select('mc_no', 'm_name', DB::raw('(mc_need - mc_fulfilled) as sisa'), 'mc_date', 'mc_expired', 'mc_contractid')
            ->where(function ($q) use ($kondisi){
                $q->orWhere('m_name', 'like', '%' . $kondisi . '%')
                    ->orWhere('mc_no', 'like', '%' . $kondisi . '%');
            })
            ->take(50)
            ->get();

        if ($data == null) {
            $results[] = ['id' => null, 'label' => 'Tidak ditemukan data terkait'];
        } else {

            foreach ($data as $query) {
                $results[] = ['id' => $query->mc_contractid, 'label' => $query->mc_no . ' (' . $query->m_name . ' Sisa ' . $query->sisa. ')'];
            }
        }

        return Response::json($results);
    }

    public function getDataPencarian(Request $request)
    {
        $id = $request->id;
        $info = DB::table('d_mitra_contract')
            ->join('d_mitra', 'd_mitra.m_id', '=', 'd_mitra_contract.mc_mitra')
            ->join('d_comp', 'd_comp.c_id', '=', 'd_mitra_contract.mc_comp')
            ->join('d_mitra_divisi', function ($q) {
                $q->on('d_mitra_contract.mc_divisi', '=', 'd_mitra_divisi.md_id')
                    ->on('d_mitra_contract.mc_mitra', '=', 'd_mitra_divisi.md_mitra');
            })
            ->select('d_mitra_contract.mc_mitra'
                , 'd_mitra_contract.mc_contractid'
                , 'd_mitra_contract.mc_no'
                , 'd_mitra_contract.mc_divisi'
                , 'd_mitra_divisi.md_name'
                , DB::raw('date_format(mc_date, "%d/%m/%Y") as mc_date')
                , DB::raw('date_format(mc_expired, "%d/%m/%Y") as mc_expired')
                , 'd_mitra_contract.mc_need'
                , 'd_mitra_contract.mc_fulfilled'
                , 'd_mitra_divisi.md_name'
                , 'd_mitra.m_name'
                , 'd_comp.c_name'
            )
            ->where('mc_contractid', '=', $id)
            ->groupBy('mc_no')
            ->orderBy('d_mitra_contract.mc_date', 'DESC')
            ->get();

        return Response::json($info);
    }

    public function data()
    {
        DB::statement(DB::raw('set @rownum=0'));
        $mc = DB::table('d_mitra_contract')
            ->join('d_mitra', 'd_mitra.m_id', '=', 'd_mitra_contract.mc_mitra')
            ->join('d_comp', 'd_comp.c_id', '=', 'd_mitra_contract.mc_comp')
            ->join('d_mitra_divisi', function ($q) {
                $q->on('d_mitra_contract.mc_divisi', '=', 'd_mitra_divisi.md_id')
                    ->on('d_mitra_contract.mc_mitra', '=', 'd_mitra_divisi.md_mitra');
            })
            ->select('d_mitra_contract.mc_mitra'
                , 'd_mitra_contract.mc_contractid'
                , 'd_mitra_contract.mc_no'
                , 'd_mitra_contract.mc_divisi'
                , 'd_mitra_divisi.md_name'
                , 'd_mitra_contract.mc_date'
                , 'd_mitra_contract.mc_expired'
                , 'd_mitra_contract.mc_need'
                , 'd_mitra_contract.mc_fulfilled'
                , 'd_mitra_divisi.md_id'
                , 'd_mitra.m_name'
                , 'd_comp.c_name',
                DB::raw('@rownum  := @rownum  + 1 AS number')
            )
            ->whereRaw('mc_need > mc_fulfilled')
            ->groupBy('mc_no')
            ->orderBy('d_mitra_contract.mc_date', 'DESC')
            ->get();

        $mc = collect($mc);
        return Datatables::of($mc)
            ->editColumn('mc_date', function ($mc) {
                return $mc->mc_date ? with(new Carbon($mc->mc_date))->format('d-m-Y') : '';

            })
            ->editColumn('mc_expired', function ($mc) {
                return $mc->mc_expired ? with(new Carbon($mc->mc_expired))->format('d-m-Y') : '';

            })
            ->addColumn('action', function ($mc) {
                return '<div class="btn-group">
                            <a href="data-pekerja-mitra/' . $mc->mc_mitra . '/' . $mc->mc_contractid . '/edit" ><button style="margin-left:5px;" title="Edit" type="button" class="btn btn-warning btn-xs"><i class="glyphicon glyphicon-edit"></i></button></a>
                            <a class="btn-delete" onclick="hapus(' . $mc->mc_mitra . ',' . $mc->mc_contractid . ')"><button style="margin-left:5px;" type="button" class="btn btn-danger btn-xs" title="Hapus"><i class="glyphicon glyphicon-trash"></i></button></a>
                            </div>';
            })
            ->make(true);

    }

    public function tambah()
    {
        $mitra_contract = DB::table('d_mitra_contract')
                      ->join('d_mitra', 'm_id', '=', 'mc_mitra')
                      ->join('d_mitra_divisi', 'md_id', '=', 'mc_divisi')
                      ->where('mc_fulfilled', '<', DB::raw('mc_need'))
                      ->groupBy('mc_no')
                      ->get();

        // d_mitra_contract::where('mc_fulfilled', '<', DB::raw('mc_need'))->get();
        $pekerja = DB::select('select p_id, p_name, p_sex, p_address, p_hp, p_education from d_pekerja left join d_mitra_pekerja on mp_pekerja = p_id where p_id not in (select mp_pekerja from d_mitra_pekerja where mp_status = "Aktif")');
        $update_mitra_contract = DB::table('d_mitra_contract')->get();

        return view('mitra-pekerja.formTambah', compact('pekerja1', 'update_mitra_contract', 'pekerja', 'mitra_contract'));
    }

    public function lanjutkan(Request $request)
    {
        $mc_contractid = $request->mc_contractid;
        $no_kontrak = $request->kontrak;
        $seleksi = $request->mp_selection_date;
        $kerja = $request->mp_workin_date;

        $pekerja = DB::table('d_pekerja')
            ->select('p_name', 'p_ktp', 'p_sex', 'p_hp', 'p_id', 'p_nip', 'p_nip_mitra')
            ->whereIn('p_id', $request->pilih)
            ->get();

        $info = DB::table('d_mitra_contract')
            ->leftJoin('d_mitra', 'mc_mitra', '=', 'm_id')
            ->leftJoin('d_mitra_divisi', function ($q) {
                $q->on('mc_divisi', '=', 'md_id')
                    ->on('mc_mitra', '=', 'md_mitra');
            })
            ->select('m_name', 'md_name', 'mc_contractid', 'mc_no', 'mc_need', 'mc_fulfilled')
            ->where('mc_contractid', '=', $mc_contractid)
            ->where('md_id', DB::raw('mc_divisi'))
            ->get();

        $tahun = Carbon::now('Asia/Jakarta')->year;

        $nik = DB::table('d_pekerja')
            ->select(DB::raw('coalesce(max(mid(p_nip, 4, 5)) + 1, "00001") as counter'))
            ->where(DB::raw('right(p_nip, 4)'), '=', $tahun)
            ->get();

        for ($i = 0; $i < count($pekerja); $i++){
            $kode = "00001";
            if (count($nik) > 0) {
                foreach ($nik as $x) {
                    $temp = ((int)$x->counter)+$i;
                    $kode = sprintf("%05s",$temp);
                }
            }
            $tempKode = 'PN-' . $kode . '.' . $tahun;
            $pekerja[$i]->nikbaru = $tempKode;
        }

        return view('mitra-pekerja.formLanjutan', compact('pekerja', 'info', 'seleksi', 'kerja'));
    }

    public function getNewNik($jumlah)
    {
        $tahun = Carbon::now('Asia/Jakarta')->year;

        $nik = DB::table('d_pekerja')
            ->select(DB::raw('coalesce(max(mid(p_nip, 4, 5)) + 1, "00001") as counter'))
            ->where(DB::raw('right(p_nip, 4)'), '=', $tahun)
            ->get();

        $kode = "00001";
        $tempKode = [];
        for ($i = 0; $i < $jumlah; $i++){
            if (count($nik) > 0) {
                foreach ($nik as $x) {
                    $temp = ((int)$x->counter)+$i;
                    $kode = sprintf("%05s",$temp);
                }
                $tempKode[$i] = 'PN-' . $kode . '.' . $tahun;

            }
        }

        return $tempKode;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function simpan(Request $request)
    {

        DB::beginTransaction();
        try {
            $sekarang = Carbon::now('Asia/Jakarta');
            $id_pekerja = $request->id_pekerja;
            $no_kontrak = $request->no_kontrak;
            $nik_mitra = $request->nik_mitra;
            $id_kontrak = $request->id_kontrak;
            $tgl_kerja = $request->tgl_kerja;
            $tgl_seleksi = $request->tgl_seleksi;
            $nik = $request->nik;
            $nikbaru = $request->nikbaru;
            $newNik = $this->getNewNik(count($nikbaru));
            for ($n = 0; $n < count($nikbaru); $n++){
                for ($i = 0; $i < count($id_pekerja); $i++){
                    if ($id_pekerja[$i] == $nikbaru[$n]){
                        $nik[$i] == $newNik[$n];
                        $i = count($id_pekerja);
                    }
                }
            }
            $cekSelected = count($request->id_pekerja);
            if ($cekSelected == 0) {
                return response()->json([
                    'status' => 'gagal',
                    'data' => 'Belum ada detail pekerja yang di masukkan.'
                ]);
            }

            if (count($id_pekerja) != count($nik_mitra)) {
                return response()->json([
                    'status' => 'gagal',
                    'data' => 'Belum ada detail pekerja yang di masukkan.'
                ]);
            }

            $info = DB::table('d_mitra_contract')
                ->select('mc_contractid', 'mc_no', 'mc_mitra', 'mc_divisi', 'mc_comp')
                ->where('mc_contractid', '=', $id_kontrak)
                ->get();

            $getMaxId = DB::table('d_mitra_pekerja')
                ->select('mp_id')
                ->where('mp_contract', '=', $id_kontrak)
                ->max('mp_id');

            $maxId = $getMaxId + 1;

//====== menyiapkan data untuk penentuan data apakah di update/create
            $addPekerja = [];
            $addMutasi = [];
            for ($i = 0; $i < count($id_pekerja); $i++){
                $cek = DB::table('d_mitra_pekerja')
                    ->select('mp_id', 'mp_pekerja')
                    ->where('mp_contract', '=', $id_kontrak)
                    ->where('mp_pekerja', '=', $id_pekerja[$i])
                    ->get();

                if (count($cek) > 0){
//====== data di update
                    d_mitra_pekerja::where('mp_contract', '=', $id_kontrak)
                        ->where('mp_pekerja', '=', $id_pekerja[$i])
                        ->where('mp_id', '=', $cek[0]->mp_id)
                        ->update([
                            'mp_status' => 'Aktif',
                            'mp_mitra_nik' => strtoupper($nik_mitra[$i]),
                            'mp_selection_date' => Carbon::createFromFormat('d/m/Y', $tgl_seleksi, 'Asia/Jakarta')->format('Y-m-d'),
                            'mp_workin_date' => Carbon::createFromFormat('d/m/Y', $tgl_kerja, 'Asia/Jakarta')->format('Y-m-d')
                        ]);

                    if ($nik[$i] == ''){
                        $nik[$i] = $this->getNewNik(1);
                    }
                    d_pekerja::where('p_id', '=', $id_pekerja[$i])
                        ->update([
                            'p_note' => 'Seleksi',
                            'p_workdate' => Carbon::createFromFormat('d/m/Y', $tgl_kerja, 'Asia/Jakarta')->format('Y-m-d'),
                            'p_nip' => strtoupper($nik[$i]),
                            'p_nip_mitra' => strtoupper($nik_mitra[$i])
                        ]);

                    $pm_detail = DB::table('d_pekerja_mutation')
                        ->where('pm_pekerja', '=', $id_pekerja[$i])
                        ->max('pm_detailid');

                    $tempMutasi = array(
                        'pm_pekerja' => $id_pekerja[$i],
                        'pm_detailid' => $pm_detail + 1,
                        'pm_date' => $sekarang,
                        'pm_mitra' => $info[0]->mc_mitra,
                        'pm_divisi' => $info[0]->mc_divisi,
                        'pm_detail' => 'Seleksi',
                        'pm_from' => null,
                        'pm_status' => 'Aktif'
                    );
                    array_push($addMutasi, $tempMutasi);

                } else {
//====== data di create

                    if ($nik[$i] == ''){
                        $temp = $this->getNewNik(1);
                        $nik[$i] = $temp[0];
                    }

                    d_pekerja::where('p_id', '=', $id_pekerja[$i])
                        ->update([
                            'p_note' => 'Seleksi',
                            'p_workdate' => Carbon::createFromFormat('d/m/Y', $tgl_kerja, 'Asia/Jakarta')->format('Y-m-d'),
                            'p_nip' => strtoupper($nik[$i]),
                            'p_nip_mitra' => strtoupper($nik_mitra[$i])
                        ]);

                    $temp = array(
                        'mp_id' => $maxId + $i,
                        'mp_comp' => $info[0]->mc_comp,
                        'mp_pekerja' => $id_pekerja[$i],
                        'mp_mitra' => $info[0]->mc_mitra,
                        'mp_divisi' => $info[0]->mc_divisi,
                        'mp_mitra_nik' => strtoupper($nik_mitra[$i]),
                        'mp_selection_date' => Carbon::createFromFormat('d/m/Y', $tgl_seleksi, 'Asia/Jakarta')->format('Y-m-d'),
                        'mp_workin_date' => Carbon::createFromFormat('d/m/Y', $tgl_kerja, 'Asia/Jakarta')->format('Y-m-d'),
                        'mp_contract' => $id_kontrak,
                        'mp_status' => 'Aktif'
                    );
                    array_push($addPekerja, $temp);

                    $pm_detail = DB::table('d_pekerja_mutation')
                        ->where('pm_pekerja', '=', $id_pekerja[$i])
                        ->max('pm_detailid');

                    $tempMutasi = array(
                        'pm_pekerja' => $id_pekerja[$i],
                        'pm_detailid' => $pm_detail + 1,
                        'pm_date' => $sekarang,
                        'pm_mitra' => $info[0]->mc_mitra,
                        'pm_divisi' => $info[0]->mc_divisi,
                        'pm_detail' => 'Seleksi',
                        'pm_from' => null,
                        'pm_status' => 'Aktif'
                    );
                    array_push($addMutasi, $tempMutasi);
                }
            }
            d_pekerja_mutation::insert($addMutasi);
            d_mitra_pekerja::insert($addPekerja);

            d_mitra_contract::where('mc_contractid', '=', $id_kontrak)
                ->update([
                    'mc_fulfilled' => DB::raw('mc_fulfilled + '. count($id_pekerja))
                ]);

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

    public function edit($mitra, $kontrak)
    {
        $update_mitra_contract = DB::table('d_mitra_contract')
            ->join('d_mitra', 'd_mitra.m_id', '=', 'd_mitra_contract.mc_mitra')
            ->join('d_comp', 'd_comp.c_id', '=', 'd_mitra_contract.mc_comp')
            ->join('d_mitra_pekerja', 'd_mitra_contract.mc_contractid', '=', 'd_mitra_pekerja.mp_contract')
            ->join('d_mitra_divisi', function ($q) {
                $q->on('d_mitra_contract.mc_divisi', '=', 'd_mitra_divisi.md_id')
                    ->on('d_mitra_contract.mc_mitra', '=', 'd_mitra_divisi.md_mitra');
            })
            ->select('d_mitra_contract.mc_mitra'
                , 'd_mitra_contract.mc_contractid'
                , 'd_mitra_contract.mc_no'
                , DB::raw('DATE_FORMAT(mc_date, "%d/%m/%Y") as mc_date')
                , DB::raw('DATE_FORMAT(mc_expired, "%d/%m/%Y") as mc_expired')
                , 'd_mitra_contract.mc_need'
                , 'd_mitra_contract.mc_fulfilled'
                , 'd_mitra_contract.mc_comp'
                , 'd_mitra.m_id'
                , 'd_mitra_divisi.md_id'
                , 'd_comp.c_id'
                , 'd_comp.c_name'
                , 'd_mitra_pekerja.mp_pekerja'
                , 'm_name'
                , 'md_name'
            )
            ->where('d_mitra_contract.mc_mitra', $mitra)
            ->where('d_mitra_contract.mc_contractid', $kontrak)
            ->first();

        $pekerja = DB::table('d_mitra_pekerja')
            ->leftJoin('d_pekerja', 'p_id', '=', 'mp_pekerja')
            ->select('p_name', 'mp_mitra_nik', 'p_nip', 'mp_id', DB::raw('DATE_FORMAT(mp_selection_date, "%d/%m/%Y") as mp_selection_date'), DB::raw('DATE_FORMAT(mp_workin_date, "%d/%m/%Y") as mp_workin_date'), 'p_id')
            ->where('mp_status', '=', 'Aktif')
            ->where('mp_contract', '=', $kontrak)
            ->get();

        return view('mitra-pekerja.formEdit', compact('pekerja', 'update_mitra_contract'));
    }

    public function update(Request $request)
    {
        //dd($request);
        $hapus = $request->hapus;
        $hapus = json_decode($hapus);
        foreach ($hapus as $key => $var) {
            $hapus[$key] = (int)$var;
        }
        $hapusIn = implode("','",$hapus);
        $idKontrak = $request->mc_contractid;
        $kontrak = $request->kontrak;
        $p_id = $request->p_id;
        $nip = $request->nip;
        $mitra_nip = $request->nip_mitra;
        $seleksi = $request->seleksi;
        $kerja = $request->kerja;
        DB::beginTransaction();
        try {

            $sekarang = Carbon::now('Asia/Jakarta');
//====== update jumlah pekerja
            d_mitra_contract::where('mc_contractid', '=', $idKontrak)
                ->update(array(
                    'mc_no' => $kontrak,
                    'mc_fulfilled' => count($p_id)
                ));
//====== update status pekerja di mitra
            d_mitra_pekerja::whereIn('mp_pekerja', $hapus)
                ->update(array(
                    'mp_status' => 'Tidak'
                ));
//====== info kontrak
            $infoKontrak = DB::table('d_mitra_contract')
                ->where('mc_contractid', '=', $idKontrak)
                ->first();
//====== info pekerja yang dihapus dari list
            $pekerja = DB::select("select pm.pm_pekerja, pm.pm_detailid from (select * from d_pekerja_mutation where pm_pekerja in ('".$hapusIn."') group by pm_pekerja, pm_detailid desc) as pm group by pm.pm_pekerja");

            $addPekerja = [];
            for($i = 0; $i < count($pekerja); $i++){
                $temp = array(
                    'pm_pekerja' => $pekerja[$i]->pm_pekerja,
                    'pm_detailid' => $pekerja[$i]->pm_detailid + 1,
                    'pm_date' => $sekarang,
                    'pm_mitra' => $infoKontrak->mc_mitra,
                    'pm_divisi' => $infoKontrak->mc_divisi,
                    'pm_detail' => 'Edit Status',
                    'pm_status' => 'Calon',
                    'pm_note' => 'di hapus dari list',
                    'pm_insert_by' => Session::get('mem')
                );
                array_push($addPekerja, $temp);
            }
            d_pekerja_mutation::insert($addPekerja);

            d_pekerja::whereIn('p_id', $hapus)
                ->update([
                    'p_nip_mitra' => null
                ]);

//====== update tanggal kerja pekerja di mitra
            for($i = 0; $i < count($p_id); $i++){
                d_mitra_pekerja::where('mp_contract', '=', $idKontrak)
                    ->where('mp_pekerja', '=', $p_id[$i])
                    ->update([
                        'mp_mitra_nik' => strtoupper($mitra_nip[$i]),
                        'mp_selection_date' => Carbon::createFromFormat('d/m/Y', $seleksi[$i])->format('Y-m-d'),
                        'mp_workin_date' => Carbon::createFromFormat('d/m/Y', $kerja[$i])->format('Y-m-d')
                    ]);

                d_pekerja::where('p_id', '=', $p_id[$i])
                    ->update([
                        'p_nip_mitra' => strtoupper($mitra_nip[$i]),
                        'p_workdate' => Carbon::createFromFormat('d/m/Y', $kerja[$i])->format('Y-m-d')
                    ]);
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

    public function perbarui(Request $request, $mitra, $mc_contractid)
    {

        for ($i = 0; $i < count($request->chek); $i++) {
            if ($request->chek[$i] == "") {

            } else {
                $anjp = DB::table('d_mitra_pekerja')->where('mp_pekerja', '=', $request->pekerja[$i])->delete();

            }
        }

        for ($a = 0; $a < count($request->chek); $a++) {
            if ($request->chek[$a] == "") {

                $anjp = DB::table('d_mitra_pekerja')
                    ->where('mp_pekerja', '=', $request->pekerja[$a])
                    ->delete();
            }

        }
        $a = new mitraContractController();
        $a->hapus($mitra, $mc_contractid);


        $b = new mitraContractController();
        $b->simpan($request);

        $this->simpan($request);
        return response()->json([
            'status' => 'berhasil',
        ]);
    }


    public function mitraContrak($mitra, $kontrak)
    {

        $mc = DB::table('d_mitra_contract')
            ->join('d_mitra', 'd_mitra.m_id', '=', 'd_mitra_contract.mc_mitra')
            ->join('d_comp', 'd_comp.c_id', '=', 'd_mitra_contract.mc_comp')
            ->join('d_mitra_divisi', function ($q) {
                $q->on('d_mitra_contract.mc_divisi', '=', 'd_mitra_divisi.md_id')
                    ->on('d_mitra_contract.mc_mitra', '=', 'd_mitra_divisi.md_mitra');
            })
            ->select('d_mitra_contract.mc_mitra'
                , 'd_mitra_contract.mc_contractid'
                , 'd_mitra_contract.mc_no'
                , 'd_mitra_contract.mc_divisi'
                , 'd_mitra_contract.mc_date'
                , 'd_mitra_contract.mc_expired'
                , 'd_mitra_contract.mc_need'
                , 'd_mitra_contract.mc_fulfilled'
                , 'd_mitra_contract.mc_comp'
                , 'd_mitra.m_name'
                , 'd_mitra_divisi.md_name'
                , 'd_comp.c_name'
            )
            ->where('d_mitra_contract.mc_mitra', $mitra)
            ->where('d_mitra_contract.mc_contractid', $kontrak)
            ->first();

        $mc->mc_date = Carbon::createFromFormat('Y-m-d', $mc->mc_date, 'Asia/Jakarta')->format('d/m/Y');
        $mc->mc_expired = Carbon::createFromFormat('Y-m-d', $mc->mc_expired, 'Asia/Jakarta')->format('d/m/Y');
        if ($mc->mc_no != '' || $mc->mc_no != null) {
            return response()->json([
                'status' => 'berhasil',
                'data' => $mc,
            ]);
        }
        if (count($mc) == 0) {
            return response()->json([
                'status' => 'berhasil',
                'data' => 'Data Kosong',
            ]);
        }


    }


        public function selesai(Request $request){
          DB::beginTransaction();
          try {

            $pekerja = DB::table('d_mitra_pekerja')
                    ->select('mp_pekerja')->where('mp_contract',$request->kontrak)->where('mp_mitra', $request->mitra)->get();

            $d_mitra_pekerja = d_mitra_pekerja::where('mp_contract',$request->kontrak)->where('mp_mitra', $request->mitra);
            $d_mitra_pekerja->update([
                              'mp_status' => 'Tidak'
                            ]);

            $d_mitra_contract = d_mitra_contract::where('mc_contractid',$request->kontrak)->where('mc_mitra', $request->mitra);
            $d_mitra_contract->update([
              'mc_status' => 'Selesai'
            ]);

            for ($i=0; $i < count($pekerja); $i++) {
              $d_pekerja = d_pekerja::where('p_id',$pekerja[$i]->mp_pekerja);
              $d_pekerja->update([
                            'p_note' => 'Calon'
              ]);

              $pm_detailid[$i] = DB::table('d_pekerja_mutation')
                          ->select('pm_detailid')
                          ->where('pm_pekerja',$pekerja[$i]->mp_pekerja)
                          ->MAX('pm_detailid');

              DB::table('d_pekerja_mutation')
                  ->insert([
                    'pm_pekerja' => $pekerja[$i]->mp_pekerja,
                    'pm_detailid' => $pm_detailid[$i] + 1,
                    'pm_detail' => 'Matang',
                    'pm_status' => 'Calon',
                    'pm_note' => 'Selesai'
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
