<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use DB;

use Carbon\Carbon;

class approvalspController extends Controller
{
    public function index(){
      $data = DB::table('d_mitra_pekerja')
          ->join('d_pekerja', 'p_id', '=', 'mp_pekerja')
          ->join('d_surat_pringatan', 'sp_pekerja', '=', 'p_id')
          ->join('d_mitra', 'm_id', '=', 'mp_mitra')
          ->join('d_mitra_divisi', function($e){
                $e->on('m_id', '=', 'md_mitra');
                $e->on('mp_divisi', '=', 'md_id');
          })
          ->select('sp_id','sp_no','p_name','md_name','sp_date_start','sp_date_end','sp_note','sp_isapproved', DB::Raw("coalesce(p_jabatan, '-') as p_jabatan"))
          ->where('sp_isapproved', 'P')
          ->get();

    for ($i=0; $i < count($data); $i++) {
      $data[$i]->sp_date_start = Carbon::Parse($data[$i]->sp_date_start)->format('d/m/Y');
      $data[$i]->sp_date_start = Carbon::Parse($data[$i]->sp_date_end)->format('d/m/Y');
    }

      return view('approvalsp.index', compact('data'));
    }

    public function setujui(Request $request){
      DB::beginTransaction();
      try {
        DB::table('d_surat_pringatan')
            ->where('sp_id',$request->id)
            ->update([
              'sp_isapproved' => 'Y'
            ]);
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

    public function tolak(Request $request){
      DB::beginTransaction();
      try {
        DB::table('d_surat_pringatan')
            ->where('sp_id',$request->id)
            ->update([
              'sp_isapproved' => 'N'
            ]);
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

    public function setujuilist(Request $request){
      DB::beginTransaction();
      try {
        for ($i=0; $i < count($request->pilih); $i++) {
          DB::table('d_surat_pringatan')
              ->where('sp_id',$request->pilih[$i])
              ->update([
                'sp_isapproved' => 'Y'
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
          DB::table('d_surat_pringatan')
              ->where('sp_id',$request->pilih[$i])
              ->update([
                'sp_isapproved' => 'N'
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

    public function detail(Request $request){
      $id = $request->id;

      $data = DB::table('d_mitra_pekerja')
          ->join('d_pekerja', 'p_id', '=', 'mp_pekerja')
          ->join('d_surat_pringatan', 'sp_pekerja', '=', 'p_id')
          ->join('d_mitra', 'm_id', '=', 'mp_mitra')
          ->join('d_mitra_divisi', function($e){
                $e->on('m_id', '=', 'md_mitra');
                $e->on('mp_divisi', '=', 'md_id');
          })
          ->select('sp_no','p_name','md_name','sp_date_start','sp_date_end','sp_note','sp_isapproved', DB::Raw("coalesce(p_jabatan, '-') as p_jabatan"))
          ->where('sp_id',$id)
          ->get();

      $data[0]->sp_date_start = Carbon::parse($data[0]->sp_date_start)->format('d/m/Y');
      $data[0]->sp_date_end = Carbon::parse($data[0]->sp_date_end)->format('d/m/Y');

      return response()->json($data);
    }
}
