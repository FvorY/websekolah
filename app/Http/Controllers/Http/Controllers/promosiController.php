<?php

namespace App\Http\Controllers;

use function foo\func;
use Illuminate\Http\Request;
use DB;
use App\Http\Requests;

class promosiController extends Controller
{
    public function index()
    {
        return view('promosi.index');
    }

    public function getData()
    {
        $pekerja = DB::table('d_pekerja')
            ->leftJoin('d_jabatan_pelamar', 'jp_id', '=', 'p_jabatan_lamaran')
            ->leftJoin('d_mitra_pekerja', function ($e){
                $e->on('mp_pekerja', '=', 'p_id');
            })
            ->leftJoin('d_mitra', 'm_id', '=', 'mp_mitra')
            ->leftJoin('d_mitra_divisi', function ($q){
                $q->on('md_mitra', '=', 'mp_mitra');
                $q->on('md_id', '=', 'mp_divisi');
                $q->on('md_mitra', '=', 'm_id');
            })
            ->select('p_id', 'p_name', 'jp_name', 'p_nip', 'p_nip_mitra', 'd_mitra.m_name', 'd_mitra_divisi.md_name')
            ->where('p_note', '!=', 'Calon')
            ->where('p_note', '!=', 'Ex')
            ->where('mp_status', '=', 'Aktif')
            ->groupBy('p_id')
            ->get();

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
}
