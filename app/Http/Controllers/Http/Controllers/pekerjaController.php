<?php

namespace App\Http\Controllers;

use App\d_pekerja_child;
use App\d_pekerja_keterampilan;
use App\d_pekerja_language;
use App\d_pekerja_pengalaman;
use App\d_pekerja_referensi;
use App\d_pekerja_sim;
use Dompdf\Exception;
use Illuminate\Http\Request;

use App\Http\Requests;

use App\d_pekerja;

use App\d_pekerja_mutation;

use App\d_mitra;

use App\d_mitra_pekerja;

use Yajra\Datatables\Datatables;
use Session;
use Validator;
use File;
use DB;

use Carbon\carbon;

class pekerjaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('pekerja.index');
    }

    public function data()
    {
        DB::statement(DB::raw('set @rownum=0'));

        $pekerja = DB::select("select @rownum := @rownum + 1 as number, p_id, p_name, p_nip, p_sex, p_address, p_hp, pm_detailid, pm_status from d_pekerja p join d_pekerja_mutation pm on p_id = pm_pekerja cross join (select @rownum := 0) r where pm_detailid = (select max(pm_detailid) from d_pekerja_mutation where pm_pekerja = p.p_id) and pm_status = 'Aktif' order by p_name");

        $pekerja = collect($pekerja);

        return Datatables::of($pekerja)
            ->editColumn('pm_status', function ($pekerja) {

                if ($pekerja->pm_status == 'Calon')
                    return '<div class="text-center"><span class="label label-warning ">Calon</span></div>';
                if ($pekerja->pm_status == 'Aktif')
                    return '<div class="text-center"><span class="label label-success ">Aktif</span></div>';
                if ($pekerja->pm_status == 'Ex')
                    return '<div class="text-center"><span class="label label-danger ">Tidak Aktif</span></div>';
            })
            ->addColumn('action', function ($pekerja) {
                return '<div class="text-center">
                    <button style="margin-left:5px;" title="Detail" type="button" class="btn btn-info btn-xs" onclick="detail(' . $pekerja->p_id . ')"><i class="glyphicon glyphicon-folder-open"></i></button>
                    <a style="margin-left:5px;" title="Edit" type="button" class="btn btn-warning btn-xs" href="data-pekerja/' . $pekerja->p_id . '/edit"><i class="glyphicon glyphicon-edit"></i></a>
                    <button style="margin-left:5px;" type="button" class="btn btn-danger btn-xs" title="Resign" onclick="resign(' . $pekerja->p_id . ')"><i class="fa fa-sign-out"></i></button>
                  </div>';
            })
            ->make(true);
    }

    public function dataCalon()
    {
        DB::statement(DB::raw('set @rownum=0'));

        $pekerja = DB::select("select @rownum := @rownum + 1 as number, p_id, p_name, p_sex, p_address, p_hp, pm_detailid, pm_status from d_pekerja p join d_pekerja_mutation pm on p_id = pm_pekerja cross join (select @rownum := 0) r where pm_detailid = (select max(pm_detailid) from d_pekerja_mutation where pm_pekerja = p.p_id) and pm_status = 'Calon' order by p_name");

        $pekerja = collect($pekerja);

        return Datatables::of($pekerja)
            ->editColumn('pm_status', function ($pekerja) {

                if ($pekerja->pm_status == 'Calon')
                    return '<div class="text-center"><span class="label label-warning ">Calon</span></div>';
                if ($pekerja->pm_status == 'Aktif')
                    return '<div class="text-center"><span class="label label-success ">Aktif</span></div>';
                if ($pekerja->pm_status == 'Ex')
                    return '<div class="text-center"><span class="label label-danger ">Tidak Aktif</span></div>';
            })
            ->addColumn('action', function ($pekerja) {
                return '<div class="text-center">
                    <button style="margin-left:5px;" title="Detail" type="button" class="btn btn-info btn-xs" onclick="detail(' . $pekerja->p_id . ')"><i class="glyphicon glyphicon-folder-open"></i></button>
                    <a style="margin-left:5px;" title="Edit" type="button" class="btn btn-warning btn-xs" href="data-pekerja/' . $pekerja->p_id . '/edit"><i class="glyphicon glyphicon-edit"></i></a>
                    <button style="margin-left:5px;" type="button" class="btn btn-danger btn-xs" title="Hapus" onclick="hapus(' . $pekerja->p_id . ')"><i class="glyphicon glyphicon-trash"></i></button>
                  </div>';
            })
            ->make(true);
    }

    public function dataEx()
    {
        DB::statement(DB::raw('set @rownum=0'));

        $pekerja = DB::select("select @rownum := @rownum + 1 as number, p_id, p_name, p_nip, p_sex, p_address, p_hp, pm_detailid, pm_status from d_pekerja p join d_pekerja_mutation pm on p_id = pm_pekerja cross join (select @rownum := 0) r where pm_detailid = (select max(pm_detailid) from d_pekerja_mutation where pm_pekerja = p.p_id) and pm_status = 'Ex' order by p_name");

        $pekerja = collect($pekerja);

        return Datatables::of($pekerja)
            ->editColumn('pm_status', function ($pekerja) {

                if ($pekerja->pm_status == 'Calon')
                    return '<div class="text-center"><span class="label label-warning ">Calon</span></div>';
                if ($pekerja->pm_status == 'Aktif')
                    return '<div class="text-center"><span class="label label-success ">Aktif</span></div>';
                if ($pekerja->pm_status == 'Ex')
                    return '<div class="text-center"><span class="label label-danger ">Tidak Aktif</span></div>';
            })
            ->addColumn('action', function ($pekerja) {
                return '<div class="text-center">
                    <button style="margin-left:5px;" title="Detail" type="button" class="btn btn-info btn-xs" onclick="detail(' . $pekerja->p_id . ')"><i class="glyphicon glyphicon-folder-open"></i></button>
                    <a style="margin-left:5px;" title="Edit" type="button" class="btn btn-warning btn-xs" href="data-pekerja/' . $pekerja->p_id . '/edit"><i class="glyphicon glyphicon-edit"></i></a>
                    <button style="margin-left:5px;" type="button" class="btn btn-danger btn-xs" title="Hapus" onclick="hapus(' . $pekerja->p_id . ')"><i class="glyphicon glyphicon-trash"></i></button>
                  </div>';
            })
            ->make(true);
    }

    public function tambah()
    {

        $jabPelamar = DB::table('d_jabatan_pelamar')
            ->select('*')
            ->orderBy('jp_name')
            ->get();

        return view('pekerja.formTambah', compact('jabPelamar'));

    }

    public function simpan(Request $request)
    {
        DB::beginTransaction();
        try {

            $nama = strtoupper($request->nama);
            $jabatanpelamar = strtoupper($request->jabatan_pelamar);
            $alamat = strtoupper($request->alamat);
            $rt = strtoupper($request->rt);
            $desa = strtoupper($request->desa);
            $kecamatan = strtoupper($request->kecamatan);
            $kota = strtoupper($request->kota);
            $alamat_now = strtoupper($request->alamat_now);
            $rt_now = strtoupper($request->rt_now);
            $desa_now = strtoupper($request->desa_now);
            $kecamatan_now = strtoupper($request->kecamatan_now);
            $kota_now = strtoupper($request->kota_now);
            $tempat_lahir = strtoupper($request->tempat_lahir);
            $tanggal_lahir = Carbon::createFromFormat('d/m/Y', $request->tanggal_lahir, 'Asia/Jakarta');
            $no_ktp = strtoupper($request->no_ktp);
            $no_tlp = strtoupper($request->no_tlp);
            $no_hp = strtoupper($request->no_hp);
            $warga_negara = strtoupper($request->wn);
            $status = strtoupper($request->status);
            $jml_anak = strtoupper($request->jml_anak);
            $agama = strtoupper($request->agama);
            $agama_lain = strtoupper($request->agamalain);
            $pendidikan = $request->pendidikan;
            $bahasa = $request->bahasa;
            $bahasa_lain = $request->bahasalain;
            $sim = $request->sim;
            $simket = $request->simket;
            $pengalaman_corp = $request->pengalamancorp;
            $start_pengalaman = $request->startpengalaman;
            $end_pengalaman = $request->endpengalaman;
            $jabatan_pengalaman = $request->jabatan;
            $keterampilan = $request->keterampilan;
            $referensi = [];
            if ($request->ref != null) {
                $referensi = $request->ref;
            }
            $referensi_lain = $request->reflain;
            $nama_keluarga = $request->namakeluarga;
            $hubungan_keluarga = $request->hubkeluarga;
            $telp_keluarga = $request->nokeluarga;
            $hp_keluarga = $request->hpkeluarga;
            $alamat_keluarga = $request->alamatkeluarga;
            $wife_name = $request->wifename;
            $wife_lahir = $request->wifelahir;
            $wife_tanggal = null;
            if ($request->wifettl != '' || $request->wifettl != null) {
                $wife_tanggal = Carbon::createFromFormat('d/m/Y', $request->wifettl, 'Asia/Jakarta');
            }
            $childname = $request->childname;
            $childplace = $request->childplace;
            $childdate = $request->childdate;
            $dadname = $request->dadname;
            $momname = $request->momname;
            $dadjob = $request->dadjob;
            $momjob = $request->momjob;
            $saatini = $request->saatini;
            $kuliahnow = $request->kuliahnow;
            $beratbadan = $request->beratbadan;
            $tinggibadan = $request->tinggibadan;
            $ukuranbaju = $request->ukuranbaju;
            $ukurancelana = $request->ukurancelana;
            $ukuransepatu = $request->ukuransepatu;
            $jenkel = $request->jenkel;

            $idPekerja = DB::table('d_pekerja')
                ->max('p_id');

            $idPekerja = $idPekerja + 1;

            $imgPath = null;
            $tgl = carbon::now('Asia/Jakarta');
            $folder = $tgl->year . $tgl->month . $tgl->timestamp;
            $dir = 'image/uploads/pekerja/' . $idPekerja;
            $this->deleteDir($dir);
            $childPath = $dir . '/';
            $path = $childPath;
            $file = $request->file('imageUpload');
            $name = null;
            if ($file != null) {
                $name = $folder . '.' . $file->getClientOriginalExtension();
                if (!File::exists($path)) {
                    if (File::makeDirectory($path, 0777, true)) {
                        $file->move($path, $name);
                        $imgPath = $childPath . $name;
                    } else
                        $imgPath = null;
                } else {
                    return 'already exist';
                }
            }

            if ($agama_lain != '' || $agama_lain != null) {
                $agama = $agama_lain;
            }

            if ($saatini == 'Kuliah') {
                $saatini = 'Kuliah di ' . $kuliahnow;
            }

            d_pekerja::insert(array(
                "p_id" => $idPekerja,
                "p_jabatan_lamaran" => strtoupper($jabatanpelamar),
                "p_nip" => null,
                "p_ktp" => $no_ktp,
                "p_name" => $nama,
                "p_sex" => $jenkel,
                "p_birthplace" => $tempat_lahir,
                "p_birthdate" => $tanggal_lahir,
                "p_hp" => $no_hp,
                "p_telp" => $no_tlp,
                "p_status" => strtoupper($status),
                "p_many_kids" => strtoupper($jml_anak),
                "p_religion" => strtoupper($agama),
                "p_address" => strtoupper($alamat),
                "p_rt_rw" => strtoupper($rt),
                "p_kel" => strtoupper($desa),
                "p_kecamatan" => strtoupper($kecamatan),
                "p_city" => strtoupper($kota),
                "p_address_now" => strtoupper($alamat_now),
                "p_rt_rw_now" => strtoupper($rt_now),
                "p_kel_now" => strtoupper($desa_now),
                "p_kecamatan_now" => strtoupper($kecamatan_now),
                "p_city_now" => strtoupper($kota_now),
                "p_name_family" => strtoupper($nama_keluarga),
                "p_address_family" => strtoupper($alamat_keluarga),
                "p_telp_family" => strtoupper($telp_keluarga),
                "p_hp_family" => strtoupper($hp_keluarga),
                "p_hubungan_family" => strtoupper($hubungan_keluarga),
                "p_wife_name" => strtoupper($wife_name),
                "p_wife_birth" => strtoupper($wife_tanggal),
                "p_wife_birthplace" => strtoupper($wife_lahir),
                "p_dad_name" => strtoupper($dadname),
                "p_dad_job" => strtoupper($dadjob),
                "p_mom_name" => strtoupper($momname),
                "p_mom_job" => strtoupper($momjob),
                "p_job_now" => strtoupper($saatini),
                "p_weight" => strtoupper($beratbadan),
                "p_height" => strtoupper($tinggibadan),
                "p_seragam_size" => strtoupper($ukuranbaju),
                "p_celana_size" => strtoupper($ukurancelana),
                "p_sepatu_size" => strtoupper($ukuransepatu),
                "p_kpk" => null,
                "p_bu" => null,
                "p_ktp_expired" => null,
                "p_ktp_seumurhidup" => null,
                "p_education" => strtoupper($pendidikan),
                "p_kpj_no" => null,
                "p_state" => strtoupper($warga_negara),
                "p_note" => null,
                "p_img" => $imgPath,
                "p_insert_by" => Session::get('mem'),
                "p_insert" => Carbon::now('Asia/Jakarta'),
                "p_update" => Carbon::now('Asia/Jakarta')
            ));

            $addKeterampilan = [];
            for ($i = 0; $i < count($keterampilan); $i++) {
                $temp = [];
                if ($keterampilan[$i] != '' || $keterampilan != null) {
                    $temp = array(
                        'pk_pekerja' => $idPekerja,
                        'pk_detailid' => $i + 1,
                        'pk_keterampilan' => strtoupper($keterampilan[$i])
                    );
                    array_push($addKeterampilan, $temp);
                }
            }
            d_pekerja_keterampilan::insert($addKeterampilan);

            $addBahasa = [];
            for ($i = 0; $i < count($bahasa); $i++) {
                $temp = [];
                if ($bahasa[$i] == 'Lain' && $bahasa_lain != '') {
                    $temp = array(
                        'pl_pekerja' => $idPekerja,
                        'pl_detailid' => $i + 1,
                        'pl_language' => strtoupper($bahasa_lain)
                    );
                } else {
                    if ($bahasa[$i] != null || $bahasa[$i] != '') {
                        $temp = array(
                            'pl_pekerja' => $idPekerja,
                            'pl_detailid' => $i + 1,
                            'pl_language' => strtoupper($bahasa[$i])
                        );
                    }
                }
                array_push($addBahasa, $temp);
            }
            d_pekerja_language::insert($addBahasa);

            $addSIM = [];
            for ($i = 0; $i < count($sim); $i++) {
                $temp = [];
                if ($sim[$i] != null || $sim[$i] != '') {
                    $temp = array(
                        'ps_pekerja' => $idPekerja,
                        'ps_detailid' => $i + 1,
                        'ps_sim' => $sim[$i],
                        'ps_note' => strtoupper($simket)
                    );
                    array_push($addSIM, $temp);
                }
            }
            d_pekerja_sim::insert($addSIM);

            $addPengalaman = [];
            for ($i = 0; $i < count($pengalaman_corp); $i++) {
                $temp = [];
                if ($pengalaman_corp[$i] != null || $pengalaman_corp[$i] != '') {
                    $temp = array(
                        'pp_pekerja' => $idPekerja,
                        'pp_detailid' => $i + 1,
                        'pp_perusahaan' => strtoupper($pengalaman_corp[$i]),
                        'pp_start' => $start_pengalaman[$i],
                        'pp_end' => $end_pengalaman[$i],
                        'pp_jabatan' => strtoupper($jabatan_pengalaman[$i])
                    );
                    array_push($addPengalaman, $temp);
                }
            }
            d_pekerja_pengalaman::insert($addPengalaman);

            $addReferensi = [];
            for ($i = 0; $i < count($referensi); $i++) {
                $temp = [];
                if ($referensi[$i] == 'Lain') {
                    $temp = array(
                        'pr_pekerja' => $idPekerja,
                        'pr_detailid' => $i + 1,
                        'pr_referensi' => strtoupper($referensi_lain)
                    );
                } else {
                    if ($referensi[$i] != null || $referensi[$i] != '') {
                        $temp = array(
                            'pr_pekerja' => $idPekerja,
                            'pr_detailid' => $i + 1,
                            'pr_referensi' => strtoupper($referensi[$i])
                        );
                    }
                }
                array_push($addReferensi, $temp);
            }
            d_pekerja_referensi::insert($addReferensi);

            $addChild = [];
            for ($i = 0; $i < count($childname); $i++) {
                $temp = [];
                if ($childname[$i] != '' || $childname[$i] != null || $childname[$i] != ' ' || $childname[$i] != "") {
                    if ($childdate[$i] != "") {
                        $childdate[$i] = Carbon::createFromFormat('d/m/Y', $childdate[$i], 'Asia/Jakarta');
                        $temp = array(
                            'pc_pekerja' => $idPekerja,
                            'pc_detailid' => $i + 1,
                            'pc_child_name' => strtoupper($childname[$i]),
                            'pc_birth_date' => $childdate[$i],
                            'pc_birth_place' => strtoupper($childplace[$i])
                        );
                        array_push($addChild, $temp);
                    }
                }
            }
            d_pekerja_child::insert($addChild);

            d_pekerja_mutation::insert(array(
                'pm_pekerja' => $idPekerja,
                'pm_detailid' => 1,
                'pm_date' => Carbon::now('Asia/Jakarta'),
                'pm_detail' => 'Masuk',
                'pm_status' => 'Calon',
                'pm_insert_by' => Session::get('mem')
            ));

            DB::commit();
            Session::flash('sukses', 'data pekerja baru anda berhasil disimpan');
            return redirect('manajemen-pekerja/data-pekerja');
        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('gagal', 'data pekerja tidak dapat di simpan');
            return redirect('manajemen-pekerja/data-pekerja');
        }
    }

    public function edit($id)
    {
        $jabatan = DB::table('d_jabatan_pelamar')
            ->select('jp_name', 'jp_id')->get();

        $pekerja = DB::table('d_pekerja')
            ->where('p_id', '=', $id)
            ->select('p_name'
                , 'p_address'
                , 'p_rt_rw'
                , 'p_kecamatan'
                , 'p_kel'
                , 'p_nip'
                , 'p_city'
                , 'p_jabatan_lamaran'
                , 'p_address_now'
                , 'p_rt_rw_now'
                , 'p_kecamatan_now'
                , 'p_kel_now'
                , 'p_city_now'
                , 'p_birthplace'
                , 'p_birthdate'
                , 'p_ktp'
                , 'p_telp'
                , 'p_hp'
                , 'p_sex'
                , 'p_state'
                , 'p_status'
                , 'p_many_kids'
                , 'p_religion'
                , 'p_education'
                , 'p_name_family'
                , 'p_address_family'
                , 'p_telp_family'
                , 'p_hp_family'
                , 'p_address_family'
                , 'p_hubungan_family'
                , 'p_wife_name'
                , 'p_wife_birth'
                , 'p_wife_birthplace'
                , 'p_dad_name'
                , 'p_dad_job'
                , 'p_mom_name'
                , 'p_mom_job'
                , 'p_job_now'
                , 'p_weight'
                , 'p_height'
                , 'p_seragam_size'
                , 'p_celana_size'
                , 'p_sepatu_size'
                , 'p_img')
            ->get();

        $child = DB::table('d_pekerja_child')
            ->select('pc_child_name', 'pc_birth_place', 'pc_birth_date')
            ->where('pc_pekerja', '=', $id)
            ->get();

        $keterampilan = DB::table('d_pekerja_keterampilan')
            ->select('pk_keterampilan')
            ->where('pk_pekerja', '=', $id)
            ->get();

        // $bahasa = DB::table('d_pekerja_language')
        //        ->select('pl_language')
        //        ->where('pl_pekerja', '=', $id)
        //        ->get();

        $bahasa = DB::select("select pl_pekerja, (select pl_language from d_pekerja_language pl where pl.pl_pekerja = '$id' and pl.pl_language = 'INDONESIA') as indonesia, (select pl_language from d_pekerja_language pl where pl.pl_pekerja = '$id' and pl.pl_language = 'INGGRIS') as inggris, (select pl_language from d_pekerja_language pl where pl.pl_pekerja = '$id' and pl.pl_language = 'MANDARIN') as mandarin, (select pl_language from d_pekerja_language pl where pl.pl_pekerja = '$id' and pl.pl_language != 'INDONESIA' and pl.pl_language != 'INGGRIS' and pl.pl_language != 'MANDARIN') as lain from d_pekerja_language dpl where pl_pekerja = '$id' group by pl_pekerja");
        //dd($bahasa);

        $pengalaman = DB::table('d_pekerja_pengalaman')
            ->select('pp_perusahaan', 'pp_start', 'pp_end', 'pp_jabatan')
            ->where('pp_pekerja', '=', $id)
            ->get();

        $referensi = DB::select("select *,
(select pr_referensi from d_pekerja_referensi pr where pr.pr_pekerja = '$id' and pr.pr_referensi = 'TEMAN') as teman,
(select pr_referensi from d_pekerja_referensi pr where pr.pr_pekerja = '$id' and pr.pr_referensi = 'KELUARGA') as keluarga,
(select pr_referensi from d_pekerja_referensi pr where pr.pr_pekerja = '$id' and pr.pr_referensi = 'KORAN') as koran,
(select pr_referensi from d_pekerja_referensi pr where pr.pr_pekerja = '$id' and pr.pr_referensi = 'INTERNET') as internet,
(select pr_referensi from d_pekerja_referensi pr where pr.pr_pekerja = '$id' and pr.pr_referensi != 'TEMAN' and pr.pr_referensi != 'KELUARGA' and pr.pr_referensi != 'KORAN' and pr.pr_referensi != 'INTERNET') as lain
from d_pekerja_referensi dpr
where pr_pekerja = '$id'
group by pr_pekerja");

        $sim = DB::select("select ps_pekerja,
(select ps_sim from d_pekerja_sim ps where ps.ps_pekerja = '$id' and ps.ps_sim = 'SIM C') as simc,
(select ps_sim from d_pekerja_sim ps where ps.ps_pekerja = '$id' and ps.ps_sim = 'SIM A') as sima,
(select ps_sim from d_pekerja_sim ps where ps.ps_pekerja = '$id' and ps.ps_sim = 'SIM B') as simb,
(select ps_note from d_pekerja_sim ps where ps.ps_pekerja = '$id' and ps.ps_note != '') as note
from d_pekerja_sim dps
where ps_pekerja = '$id'
group by ps_pekerja");
        //  dd($sim);
        // $sim = DB::table('d_pekerja_sim')
        //         ->select('ps_sim', 'ps_note')
        //         ->where('ps_pekerja', '=', $id)
        //         ->get();

        // echo "<pre>";
        // print_r($pekerja);
        // echo "</pre>";
        // echo "<pre>";
        // print_r($child);
        // echo "</pre>";
        // echo "<pre>";
        // print_r($keterampilan);
        // echo "</pre>";
        // echo "<pre>";
        // print_r($bahasa);
        // echo "</pre>";
        // echo "<pre>";
        // print_r($pengalaman);
        // echo "</pre>";
        // echo "<pre>";
        // print_r($referensi);
        // echo "</pre>";
        // echo "<pre>";
        // print_r($sim);
        // echo "</pre>";
        // echo "<pre>";
        // print_r($jabatan);
        // echo "</pre>";

        return view('pekerja.formEdit', compact('id', 'pekerja', 'jabatan', 'child', 'keterampilan', 'bahasa', 'pengalaman', 'referensi', 'sim'));

    }

    public function perbarui(Request $request)
    {
        // dd($request);
        //  DB::beginTransaction();
        //   try {
        $id = $request->id;
        $imglama = $request->imglama;

        DB::table('d_pekerja')->where('p_id', '=', $id)
            ->delete();

        DB::table('d_pekerja_child')->where('pc_pekerja', '=', $id)
            ->delete();

        DB::table('d_pekerja_keterampilan')->where('pk_pekerja', '=', $id)
            ->delete();

        DB::table('d_pekerja_language')->where('pl_pekerja', '=', $id)
            ->delete();

        DB::table('d_pekerja_pengalaman')->where('pp_pekerja', '=', $id)
            ->delete();

        DB::table('d_pekerja_referensi')->where('pr_pekerja', '=', $id)
            ->delete();

        DB::table('d_pekerja_sim')->where('ps_pekerja', '=', $id)
            ->delete();

        $nama = strtoupper($request->nama);
        $jabatanpelamar = strtoupper($request->jabatan_pelamar);
        $alamat = strtoupper($request->alamat);
        $rt = strtoupper($request->rt);
        $desa = strtoupper($request->desa);
        $kecamatan = strtoupper($request->kecamatan);
        $kota = strtoupper($request->kota);
        $alamat_now = strtoupper($request->alamat_now);
        $rt_now = strtoupper($request->rt_now);
        $desa_now = strtoupper($request->desa_now);
        $kecamatan_now = strtoupper($request->kecamatan_now);
        $kota_now = strtoupper($request->kota_now);
        $tempat_lahir = strtoupper($request->tempat_lahir);
        $tanggal_lahir = Carbon::createFromFormat('d/m/Y', $request->tanggal_lahir, 'Asia/Jakarta');
        $no_ktp = strtoupper($request->no_ktp);
        $no_tlp = strtoupper($request->no_tlp);
        $no_hp = strtoupper($request->no_hp);
        $warga_negara = strtoupper($request->wn);
        $status = strtoupper($request->status);
        $jml_anak = strtoupper($request->jml_anak);
        $agama = strtoupper($request->agama);
        $agama_lain = strtoupper($request->agamalain);
        $pendidikan = $request->pendidikan;
        $bahasa = $request->bahasa;
        $bahasa_lain = $request->bahasalain;
        $sim = $request->sim;
        $simket = $request->simket;
        $pengalaman_corp = $request->pengalamancorp;
        $start_pengalaman = $request->startpengalaman;
        $end_pengalaman = $request->endpengalaman;
        $jabatan_pengalaman = $request->jabatan;
        $keterampilan = $request->keterampilan;
        $referensi = [];
        if ($request->ref != null) {
            $referensi = $request->ref;
        }
        $referensi_lain = $request->reflain;
        $nama_keluarga = $request->namakeluarga;
        $hubungan_keluarga = $request->hubkeluarga;
        $telp_keluarga = $request->nokeluarga;
        $hp_keluarga = $request->hpkeluarga;
        $alamat_keluarga = $request->alamatkeluarga;
        $wife_name = $request->wifename;
        $wife_lahir = $request->wifelahir;
        $wife_tanggal = null;
        if ($request->wifettl != '' || $request->wifettl != null) {
            $wife_tanggal = Carbon::createFromFormat('d/m/Y', $request->wifettl, 'Asia/Jakarta');
        }
        $childname = $request->childname;
        $childplace = $request->childplace;
        $childdate = $request->childdate;
        $dadname = $request->dadname;
        $momname = $request->momname;
        $dadjob = $request->dadjob;
        $momjob = $request->momjob;
        $saatini = $request->saatini;
        $kuliahnow = $request->kuliahnow;
        $beratbadan = $request->beratbadan;
        $tinggibadan = $request->tinggibadan;
        $ukuranbaju = $request->ukuranbaju;
        $ukurancelana = $request->ukurancelana;
        $ukuransepatu = $request->ukuransepatu;
        $sex = $request->sex;


        if (!empty($request->file('imageUpload'))) {
            $imgPath = null;
            $tgl = carbon::now('Asia/Jakarta');
            $folder = $tgl->year . $tgl->month . $tgl->timestamp;
            $dir = 'image/uploads/pekerja/' . $id;
            $this->deleteDir($dir);
            $childPath = $dir . '/';
            $path = $childPath;
            $file = $request->file('imageUpload');
            $name = null;
            if ($file != null) {
                $name = $folder . '.' . $file->getClientOriginalExtension();
                if (!File::exists($path)) {
                    if (File::makeDirectory($path, 0777, true)) {
                        $file->move($path, $name);
                        $imgPath = $childPath . $name;
                    } else
                        $imgPath = null;
                } else {
                    return 'already exist';
                }
            }

            if ($agama_lain != '' || $agama_lain != null) {
                $agama = $agama_lain;
            }


            if ($saatini == 'kuliah') {
                $saatini = 'Kuliah di ' . $kuliahnow;
            }


            d_pekerja::insert(array(
                "p_id" => $id,
                "p_jabatan_lamaran" => strtoupper($jabatanpelamar),
                "p_nip" => null,
                "p_ktp" => $no_ktp,
                "p_name" => $nama,
                "p_sex" => $sex,
                "p_birthplace" => $tempat_lahir,
                "p_birthdate" => $tanggal_lahir,
                "p_hp" => $no_hp,
                "p_telp" => $no_tlp,
                "p_status" => strtoupper($status),
                "p_many_kids" => strtoupper($jml_anak),
                "p_religion" => strtoupper($agama),
                "p_address" => strtoupper($alamat),
                "p_rt_rw" => strtoupper($rt),
                "p_kel" => strtoupper($desa),
                "p_kecamatan" => strtoupper($kecamatan),
                "p_city" => strtoupper($kota),
                "p_address_now" => strtoupper($alamat_now),
                "p_rt_rw_now" => strtoupper($rt_now),
                "p_kel_now" => strtoupper($desa_now),
                "p_kecamatan_now" => strtoupper($kecamatan_now),
                "p_city_now" => strtoupper($kota_now),
                "p_name_family" => strtoupper($nama_keluarga),
                "p_address_family" => strtoupper($alamat_keluarga),
                "p_telp_family" => strtoupper($telp_keluarga),
                "p_hp_family" => strtoupper($hp_keluarga),
                "p_hubungan_family" => strtoupper($hubungan_keluarga),
                "p_wife_name" => strtoupper($wife_name),
                "p_wife_birth" => strtoupper($wife_tanggal),
                "p_wife_birthplace" => strtoupper($wife_lahir),
                "p_dad_name" => strtoupper($dadname),
                "p_dad_job" => strtoupper($dadjob),
                "p_mom_name" => strtoupper($momname),
                "p_mom_job" => strtoupper($momjob),
                "p_job_now" => strtoupper($saatini),
                "p_weight" => strtoupper($beratbadan),
                "p_height" => strtoupper($tinggibadan),
                "p_seragam_size" => strtoupper($ukuranbaju),
                "p_celana_size" => strtoupper($ukurancelana),
                "p_sepatu_size" => strtoupper($ukuransepatu),
                "p_kpk" => null,
                "p_bu" => null,
                "p_ktp_expired" => null,
                "p_ktp_seumurhidup" => null,
                "p_education" => strtoupper($pendidikan),
                "p_kpj_no" => null,
                "p_state" => strtoupper($warga_negara),
                "p_note" => null,
                "p_img" => $imgPath,
                "p_insert" => Carbon::now('Asia/Jakarta'),
                "p_update" => Carbon::now('Asia/Jakarta')
            ));

        } else {

            $imgPath = null;
            $tgl = carbon::now('Asia/Jakarta');
            $folder = $tgl->year . $tgl->month . $tgl->timestamp;
            $dir = 'image/uploads/pekerja/' . $id;
            $childPath = $dir . '/';
            $path = $childPath;
            $file = $request->file('imageUpload');
            $name = null;
            if ($file != null) {
                $name = $folder . '.' . $file->getClientOriginalExtension();
                if (!File::exists($path)) {
                    if (File::makeDirectory($path, 0777, true)) {
                        $file->move($path, $name);
                        $imgPath = $childPath . $name;
                    } else
                        $imgPath = null;
                } else {
                    return 'already exist';
                }
            }

            if ($agama_lain != '' || $agama_lain != null) {
                $agama = $agama_lain;
            }


            if ($saatini == 'kuliah') {
                $saatini = 'Kuliah di ' . $kuliahnow;
            }


            d_pekerja::insert(array(
                "p_id" => $id,
                "p_jabatan_lamaran" => strtoupper($jabatanpelamar),
                "p_nip" => null,
                "p_ktp" => $no_ktp,
                "p_name" => $nama,
                "p_sex" => $sex,
                "p_birthplace" => $tempat_lahir,
                "p_birthdate" => $tanggal_lahir,
                "p_hp" => $no_hp,
                "p_telp" => $no_tlp,
                "p_status" => strtoupper($status),
                "p_many_kids" => strtoupper($jml_anak),
                "p_religion" => strtoupper($agama),
                "p_address" => strtoupper($alamat),
                "p_rt_rw" => strtoupper($rt),
                "p_kel" => strtoupper($desa),
                "p_kecamatan" => strtoupper($kecamatan),
                "p_city" => strtoupper($kota),
                "p_address_now" => strtoupper($alamat_now),
                "p_rt_rw_now" => strtoupper($rt_now),
                "p_kel_now" => strtoupper($desa_now),
                "p_kecamatan_now" => strtoupper($kecamatan_now),
                "p_city_now" => strtoupper($kota_now),
                "p_name_family" => strtoupper($nama_keluarga),
                "p_address_family" => strtoupper($alamat_keluarga),
                "p_telp_family" => strtoupper($telp_keluarga),
                "p_hp_family" => strtoupper($hp_keluarga),
                "p_hubungan_family" => strtoupper($hubungan_keluarga),
                "p_wife_name" => strtoupper($wife_name),
                "p_wife_birth" => strtoupper($wife_tanggal),
                "p_wife_birthplace" => strtoupper($wife_lahir),
                "p_dad_name" => strtoupper($dadname),
                "p_dad_job" => strtoupper($dadjob),
                "p_mom_name" => strtoupper($momname),
                "p_mom_job" => strtoupper($momjob),
                "p_job_now" => strtoupper($saatini),
                "p_weight" => strtoupper($beratbadan),
                "p_height" => strtoupper($tinggibadan),
                "p_seragam_size" => strtoupper($ukuranbaju),
                "p_celana_size" => strtoupper($ukurancelana),
                "p_sepatu_size" => strtoupper($ukuransepatu),
                "p_kpk" => null,
                "p_bu" => null,
                "p_ktp_expired" => null,
                "p_ktp_seumurhidup" => null,
                "p_education" => strtoupper($pendidikan),
                "p_kpj_no" => null,
                "p_state" => strtoupper($warga_negara),
                "p_note" => null,
                "p_img" => $imglama,
                "p_insert" => Carbon::now('Asia/Jakarta'),
                "p_update" => Carbon::now('Asia/Jakarta')
            ));

        }

        $addKeterampilan = [];
        for ($i = 0; $i < count($keterampilan); $i++) {
            $temp = [];
            if ($keterampilan[$i] != '' || $keterampilan != null) {
                $temp = array(
                    'pk_pekerja' => $id,
                    'pk_detailid' => $i + 1,
                    'pk_keterampilan' => strtoupper($keterampilan[$i])
                );
                array_push($addKeterampilan, $temp);
            }
        }
        d_pekerja_keterampilan::insert($addKeterampilan);

        $addBahasa = [];
        for ($i = 0; $i < count($bahasa); $i++) {
            $temp = [];
            if ($bahasa[$i] == 'Lain' && $bahasa_lain != '') {
                $temp = array(
                    'pl_pekerja' => $id,
                    'pl_detailid' => $i + 1,
                    'pl_language' => strtoupper($bahasa_lain)
                );
            } else {
                if ($bahasa[$i] != null || $bahasa[$i] != '') {
                    $temp = array(
                        'pl_pekerja' => $id,
                        'pl_detailid' => $i + 1,
                        'pl_language' => strtoupper($bahasa[$i])
                    );
                }
            }
            array_push($addBahasa, $temp);
        }
        d_pekerja_language::insert($addBahasa);

        $addSIM = [];
        for ($i = 0; $i < count($sim); $i++) {
            $temp = [];
            if ($sim[$i] != null || $sim[$i] != '') {
                $temp = array(
                    'ps_pekerja' => $id,
                    'ps_detailid' => $i + 1,
                    'ps_sim' => $sim[$i],
                    'ps_note' => strtoupper($simket)
                );
                array_push($addSIM, $temp);
            }
        }
        d_pekerja_sim::insert($addSIM);

        $addPengalaman = [];
        for ($i = 0; $i < count($pengalaman_corp); $i++) {
            $temp = [];
            if ($pengalaman_corp[$i] != null || $pengalaman_corp[$i] != '') {
                $temp = array(
                    'pp_pekerja' => $id,
                    'pp_detailid' => $i + 1,
                    'pp_perusahaan' => strtoupper($pengalaman_corp[$i]),
                    'pp_start' => $start_pengalaman[$i],
                    'pp_end' => $end_pengalaman[$i],
                    'pp_jabatan' => strtoupper($jabatan_pengalaman[$i])
                );
                array_push($addPengalaman, $temp);
            }
        }
        d_pekerja_pengalaman::insert($addPengalaman);

        $addReferensi = [];
        for ($i = 0; $i < count($referensi); $i++) {
            $temp = [];
            if ($referensi[$i] == 'Lain') {
                $temp = array(
                    'pr_pekerja' => $id,
                    'pr_detailid' => $i + 1,
                    'pr_referensi' => strtoupper($referensi_lain)
                );
            } else {
                if ($referensi[$i] != null || $referensi[$i] != '') {
                    $temp = array(
                        'pr_pekerja' => $id,
                        'pr_detailid' => $i + 1,
                        'pr_referensi' => strtoupper($referensi[$i])
                    );
                }
            }
            array_push($addReferensi, $temp);
        }
        d_pekerja_referensi::insert($addReferensi);

        $addChild = [];
        for ($i = 0; $i < count($childname); $i++) {
            $temp = [];
            if ($childname[$i] != '' || $childname[$i] != null || $childname[$i] != ' ' || $childname[$i] != "") {
                if ($childdate[$i] != "") {
                    $childdate[$i] = Carbon::createFromFormat('d/m/Y', $childdate[$i], 'Asia/Jakarta');
                    $temp = array(
                        'pc_pekerja' => $id,
                        'pc_detailid' => $i + 1,
                        'pc_child_name' => strtoupper($childname[$i]),
                        'pc_birth_date' => $childdate[$i],
                        'pc_birth_place' => strtoupper($childplace[$i])
                    );
                    array_push($addChild, $temp);
                }
            }
        }
        d_pekerja_child::insert($addChild);


        DB::commit();
        Session::flash('sukses', 'data pekerja anda berhasil diperbarui');
        return redirect('manajemen-pekerja/data-pekerja');
        // } catch (\Exception $e) {
        //     DB::rollback();
        //     Session::flash('gagal', 'data pekerja anda tidak dapat di perbarui');
        //
        //     return redirect('manajemen-pekerja/data-pekerja');
        // }

    }

    public function hapus($id)
    {

        return DB::transaction(function () use ($id) {
            $pekerja = d_pekerja::where('p_id', $id);
            $pekerja_mutasi = d_pekerja_mutation::where('pm_pekerja', $id);

            $cari_max_pm_detailid = DB::table('d_pekerja_mutation')
                ->where('pm_pekerja', $id)
                ->max('pm_detailid');

            $pekerja->update([
                'p_state' => 3,
                'p_note' => 'Ex'
            ]);

            $pekerja_mutasi->insert([
                'pm_pekerja' => $id,
                'pm_detailid' => $cari_max_pm_detailid + 1,
                'pm_date' => Carbon::now('Asia/Jakarta'),
                'pm_mitra' => null,
                'pm_divisi' => null,
                'pm_detail' => "Ex",
                'pm_from' => null,
                'pm_status' => "Ex"
            ]);

            return response()->json([
                'status' => 'berhasil',
            ]);

        });

    }

    public function detail(Request $request)
    {

        $list = DB::table('d_pekerja')
            ->leftJoin('d_mitra_pekerja', 'd_pekerja.p_id', '=', 'd_mitra_pekerja.mp_pekerja')
            ->leftjoin('d_mitra', 'd_mitra_pekerja.mp_mitra', '=', 'd_mitra.m_id')
            ->leftjoin('d_mitra_divisi', 'd_mitra_pekerja.mp_divisi', '=', 'd_mitra_divisi.md_id')
            ->leftJoin('d_mitra_contract', function ($join) {
                $join->on('d_mitra_pekerja.mp_contract', '=', 'd_mitra_contract.mc_contractid')
                    ->on('d_mitra_pekerja.mp_mitra', '=', 'd_mitra_contract.mc_mitra');
            })
            ->select('d_pekerja.*', 'd_mitra_pekerja.*', 'd_mitra.*', 'd_mitra_contract.*', 'd_mitra_divisi.*')
            ->where('d_pekerja.p_id', $request->id)
            ->get();

        $data = array();
        foreach ($list as $r) {
            $data[] = (array)$r;
        }
        $i = 0;
        foreach ($data as $key) {
            // add new data
            Carbon::setLocale('id');
            $date1 = new Carbon($key['mc_date']);
            $date2 = new Carbon($key['mc_expired']);
            $datedefault = new Carbon('01-01-1970');
            $data[$i]['sisa_kontrak'] = $date2->diffForHumans();
            $data[$i]['p_birthdate'] = Date('d-m-Y', strtotime($data[$i]['p_birthdate']));
            $data[$i]['p_ktp_expired'] = Date('d-m-Y', strtotime($data[$i]['p_ktp_expired']));
            $data[$i]['mp_selection_date'] = Date('d-m-Y', strtotime($data[$i]['mp_selection_date']));
            $data[$i]['mp_workin_date'] = Date('d-m-Y', strtotime($data[$i]['mp_workin_date']));
            $data[$i]['mc_date'] = Date('d-m-Y', strtotime($data[$i]['mc_date']));
            $data[$i]['mc_expired'] = Date('d-m-Y', strtotime($data[$i]['mc_expired']));

            if ($key['mp_selection_date'] <= $datedefault) {
                $data[$i]['mp_selection_date'] = "-";
            }
            if ($key['mp_workin_date'] <= $datedefault) {
                $data[$i]['mp_workin_date'] = "-";
            }
            if ($key['mc_date'] <= $datedefault) {
                $data[$i]['mc_date'] = "-";
            }
            if ($key['mc_expired'] <= $datedefault) {
                $data[$i]['mc_expired'] = "-";
            }
            $i++;
        }

        // dd($data);
        echo json_encode($data);

    }

    public function detail_mutasi(Request $request)
    {
        $list_mutasi = DB::table('d_pekerja_mutation')
            ->leftjoin('d_mitra', 'd_pekerja_mutation.pm_mitra', '=', 'd_mitra.m_id')
            ->leftjoin('d_mitra_divisi', 'd_pekerja_mutation.pm_divisi', '=', 'd_mitra_divisi.md_id')
            ->select('d_pekerja_mutation.*', 'd_mitra.*', 'd_mitra_divisi.*')
            ->where('d_pekerja_mutation.pm_pekerja', '=', $request->id)
            ->get();

        $data = array();
        foreach ($list_mutasi as $r) {
            $data[] = (array)$r;
        }
        $i = 0;
        foreach ($data as $key) {
            // add new data
            Carbon::setLocale('id');
            $data[$i]['pm_date'] = Date('d-m-Y H:i:s', strtotime($data[$i]['pm_date']));
            if ($key['m_name'] == null) {
                $data[$i]['m_name'] = '-';
            }
            if ($key['md_name'] == null) {
                $data[$i]['md_name'] = '-';
            }
            if ($key['pm_from'] == null) {
                $data[$i]['pm_from'] = '-';
            }
            $i++;
        }

        // dd($data);
        echo json_encode($data);

    }

    public function deleteDir($dirPath)
    {
        if (!is_dir($dirPath)) {
            return false;
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }

    public function resign(Request $request)
    {
        DB::beginTransaction();
        try {
            $id = $request->id;
            $keterangan = $request->keterangan;
            $tanggal = Carbon::createFromFormat('d/m/Y', $request->tanggal);
            $d_mitra_pekerja = d_mitra_pekerja::where('mp_pekerja', $id)->where('mp_status', '=', 'Aktif')->get();

            if ($d_mitra_pekerja[0]->mp_status == 'Aktif') {
                d_mitra_pekerja::where('mp_pekerja', $id)->update([
                    'mp_status' => 'Tidak'
                ]);

                d_pekerja::where('p_id', $id)->update([
                    'p_note' => 'Resign'
                ]);

                $cari_max_pm_detailid = DB::table('d_pekerja_mutation')
                    ->where('pm_pekerja', $id)
                    ->max('pm_detailid');

                $d_pekerja_mutation = d_pekerja_mutation::where('pm_pekerja', $id);
                $d_pekerja_mutation->insert([
                    'pm_pekerja' => $id,
                    'pm_detailid' => $cari_max_pm_detailid + 1,
                    'pm_date' => $tanggal,
                    'pm_mitra' => null,
                    'pm_divisi' => null,
                    'pm_from' => null,
                    'pm_detail' => 'Resign',
                    'pm_status' => 'Ex',
                    'pm_note' => $keterangan
                ]);

                DB::commit();
                return response()->json([
                    'status' => 'berhasil'
                ]);
            }

        } catch (\Exception $e){
            DB::rollback();
            return response()->json([
                'status' => 'gagal'
            ]);
        }
    }

}
