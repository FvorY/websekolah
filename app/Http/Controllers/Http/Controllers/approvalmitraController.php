<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use DB;

use Carbon\Carbon;

use Yajra\Datatables\Datatables;

use App\d_mitra;

use App\d_mitra_mou;

class approvalmitraController extends Controller
{
    public function index(){
      $data = DB::table('d_mitra')
            ->where('m_status_approval', '=', null)->get();

      return view('approvalmitra.index', compact('data'));
    }

    public function detail(Request $request){
      $id = $request->id;

      $data = DB::table('d_mitra')->selectRaw(
        "*,
        coalesce(m_name, '-') as m_name,
        coalesce(m_address, '-') as m_address,
        coalesce(m_cp, '-') as m_cp,
        coalesce(m_cp_phone, '-') as m_cp_phone,
        coalesce(m_phone, '-') as m_phone,
        coalesce(m_fax, '-') as m_fax,
        coalesce(m_note, '-') as m_note"
        )
      ->where('m_id', $id)->get();

      return response()->json([
        'm_name' => $data[0]->m_name,
        'm_address' => $data[0]->m_address,
        'm_cp' => $data[0]->m_cp,
        'm_cp_phone' => $data[0]->m_cp_phone,
        'm_phone' => $data[0]->m_phone,
        'm_fax' => $data[0]->m_fax,
        'm_note' => $data[0]->m_note
      ]);
    }


    public function setujui(Request $request){
      // dd($request->id);
      try {
        $d_mitra = d_mitra::where('m_id',$request->id)->where('m_status_approval', null);
        $d_mitra->update([
          'm_status_approval' => 'Y',
          'm_date_approval' => Carbon::now()
        ]);

        $d_mitra_mou = d_mitra_mou::where('mm_mitra',$request->id)->where('mm_status', null);
        $d_mitra_mou->update([
          'mm_status' => 'Aktif',
          'mm_aktif' => Carbon::now()
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
      // dd($request);
      try {
        $d_mitra = d_mitra::where('m_id',$request->id)->where('m_status_approval', null);
        $d_mitra->update([
          'm_status_approval' => 'N',
          'm_date_approval' => Carbon::now()
        ]);

        $d_mitra_mou = d_mitra_mou::where('mm_mitra',$request->id)->where('mm_status', null);
        $d_mitra_mou->update([
          'mm_status' => 'Tidak',
          'mm_aktif' => Carbon::now()
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

    public function print(Request $request){
      try {
        $id = $request->id;

        $data = DB::table('d_mitra')->join('d_mitra_mou', 'mm_mitra', '=', 'm_id')->selectRaw(
          "*,
          coalesce(m_name, '-') as m_name,
          coalesce(m_address, '-') as m_address,
          coalesce(m_cp, '-') as m_cp,
          coalesce(m_cp_phone, '-') as m_cp_phone,
          coalesce(m_phone, '-') as m_phone,
          coalesce(m_fax, '-') as m_fax,
          coalesce(m_note, '-') as m_note,
          coalesce(mm_mou, '-') as mm_mou,
          coalesce(mm_mou_start, '-') as mm_mou_start,
          coalesce(mm_mou_end, '-') as mm_mou_end"
          )
        ->where('m_id', $id)->where('mm_status', 'Aktif')->get();

        if (!empty($data[0]->mm_mou_start)) {
          $moustart = Carbon::parse($data[0]->mm_mou_start)->format('d/m/Y');
        }

        if (!empty($data[0]->mm_mou_end)) {
          $mouend = Carbon::parse($data[0]->mm_mou_end)->format('d/m/Y');
        }


        $lempar = array(
          'm_name' => $data[0]->m_name,
          'm_address' => $data[0]->m_address,
          'm_cp' => $data[0]->m_cp,
          'm_cp_phone' => $data[0]->m_cp_phone,
          'm_phone' => $data[0]->m_phone,
          'm_fax' => $data[0]->m_fax,
          'm_note' => $data[0]->m_note,
          'mm_mou' => $data[0]->mm_mou,
          'mm_mou_start' => $moustart,
          'mm_mou_end' => $mouend
        );
        // return response()->json([
        //   'm_name' => $data[0]->m_name,
        //   'm_address' => $data[0]->m_address,
        //   'm_cp' => $data[0]->m_cp,
        //   'm_cp_phone' => $data[0]->m_cp_phone,
        //   'm_phone' => $data[0]->m_phone,
        //   'm_fax' => $data[0]->m_fax,
        //   'm_note' => $data[0]->m_note
        // ]);
        // dd($lempar);
        return view('approvalmitra.print', compact('lempar'));
      } catch (\Exception $e) {
        echo "Terjadi Kesalahan!";
      }

      // dd($request);

    }

    public function setujuilist(Request $request){
      DB::beginTransaction();
      try {
        for ($i=0; $i < count($request->pilih); $i++) {
          $d_mitra = d_mitra::where('m_id',$request->pilih[$i])->where('m_status_approval', null);
          $d_mitra->update([
            'm_status_approval' => 'Y',
            'm_date_approval' => Carbon::now()
          ]);

          $d_mitra_mou = d_mitra_mou::where('mm_mitra',$request->pilih[$i])->where('mm_status', null);
          $d_mitra_mou->update([
            'mm_status' => 'Aktif',
            'mm_aktif' => Carbon::now()
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
          $d_mitra = d_mitra::where('m_id',$request->pilih[$i])->where('m_status_approval', null);
          $d_mitra->update([
            'm_status_approval' => 'N',
            'm_date_approval' => Carbon::now()
          ]);

          $d_mitra_mou = d_mitra_mou::where('mm_mitra',$request->pilih[$i])->where('mm_status', null);
          $d_mitra_mou->update([
            'mm_status' => 'Tidak',
            'mm_aktif' => Carbon::now()
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
