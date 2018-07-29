<?php

namespace App\Http\Controllers;

use App\d_mitra_mou;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;
use App\Http\Requests;
use Yajra\Datatables\Datatables;

class MouController extends Controller
{
    public function index()
    {
        return view('mou-mitra.index');
    }

    public function table(Request $request)
    {
        $data = DB::table('d_mitra')
            ->leftJoin('d_mitra_mou', 'mm_mitra', '=', 'm_id')
            ->select('m_id', 'm_name', 'm_note', 'mm_detailid', 'mm_mou', DB::raw('DATE_FORMAT(mm_mou_start, "%d/%m/%Y") as mm_mou_start'), DB::raw('DATE_FORMAT(mm_mou_end, "%d/%m/%Y") as mm_mou_end'))
            ->where('mm_status', '=', 'Aktif')
            ->get();

        $mou = collect($data);
        return Datatables::of($mou)
            ->addColumn('sisa', function ($mou) {
                Carbon::setLocale('id');
                $akhir = Carbon::createFromFormat('d/m/Y', $mou->mm_mou_end)->diffForHumans(null, true);
                return $akhir;
            })
            ->addColumn('action', function ($mou) {
                return '<div class="text-center">
                    <button style="margin-left:5px;" title="Perpanjang" data-toggle="modal" data-target="#myModal"  type="button" class="btn btn-info btn-xs" onclick="perpanjang(' . $mou->m_id . ',' . $mou->mm_detailid . ')"><i class="glyphicon glyphicon-export"></i></button>
                    <a style="margin-left:5px;" title="Edit" type="button" class="btn btn-warning btn-xs" data-target="modal-edit" onclick="edit('. $mou->m_id .')"><i class="glyphicon glyphicon-edit"></i></a>
                    <button style="margin-left:5px;" type="button" class="btn btn-danger btn-xs" title="Non Aktif" onclick="hapus(' . $mou->m_id . ')"><i class="glyphicon glyphicon-remove"></i></button>
                  </div>';
            })
            ->make(true);
    }

    public function tglMou(Request $request)
    {
        $idMou = $request->id;
        $detailMou = $request->detail;
        $info = DB::table('d_mitra_mou')
            ->select(DB::raw('DATE_FORMAT(mm_mou_start, "%d/%m/%Y") as mm_mou_start'), DB::raw('DATE_FORMAT(mm_mou_end, "%d/%m/%Y") as mm_mou_end'))
            ->where('mm_mitra', '=', $idMou)
            ->where('mm_detailid', '=', $detailMou)
            ->get();

        return $info;
    }

    public function UpdateMou(Request $request)
    {
        DB::beginTransaction();
        try {
            $idMou = $request->id;
            $detailMou = $request->detail;
            $awal = Carbon::createFromFormat('d/m/Y', $request->awal);
            $akhir = Carbon::createFromFormat('d/m/Y', $request->akhir);

            d_mitra_mou::where('mm_mitra', '=', $idMou)
                ->where('mm_detailid', '=', $detailMou)
                ->update([
                    'mm_mou_start' => $awal,
                    'mm_mou_end' => $akhir
                ]);

            DB::commit();
            return response()->json([
                'status' => 'berhasil'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'gagal',
                'data' => $e
            ]);
        }
    }

    public function edit(Request $request){
      // dd($request);
      $id = $request->id;

      $data = DB::table('d_mitra_mou')->where('mm_mitra', $id)->get();

      if (!empty($data[0]->mm_mou_start)) {
        $moustart = Carbon::parse($data[0]->mm_mou_start)->format('d/m/Y');
      }

      if (!empty($data[0]->mm_mou_end)) {
        $mouend = Carbon::parse($data[0]->mm_mou_end)->format('d/m/Y');
      }

      return response()->json([
        'mm_mitra' => $data[0]->mm_mitra,
        'mm_detailid' => $data[0]->mm_detailid,
        'mm_mou' => $data[0]->mm_mou,
        'mm_mou_start' => $data[0]->mm_mou_start,
        'mm_mou_end' => $data[0]->mm_mou_end
      ]);
    }

    public function updateedit(Request $request){
      DB::beginTransaction();
      try {
      $idmitra = $request->mitra;
      $detailid = $request->detail;
      $nomou = $request->nomou;
      $startmou = Carbon::createFromFormat('d/m/Y', $request->startmou);
      $endmou = Carbon::createFromFormat('d/m/Y', $request->endmou);

      d_mitra_mou::where('mm_mitra',$idmitra)
                  ->where('mm_detailid',$detailid)
                  ->update([
                    'mm_mou' => $nomou,
                    'mm_mou_start' => $startmou,
                    'mm_mou_end' => $endmou
                  ]);
      DB::commit();
      return response()->json([
        'status' => 'berhasil'
      ]);
    } catch (\Exception $e) {
      DB::rollback();
      return response()->json([
        'status' => 'gagal',
        'data' => $e
      ]);
      }
    }

    public function hapus(Request $request){
      DB::beginTransaction();
      try {
        $idmitra = $request->id;
        d_mitra_mou::where('mm_mitra',$idmitra)
                    ->update([
                      'mm_aktif' => Carbon::now(),
                      'mm_status' => 'Tidak'
                    ]);
        DB::commit();
        return response()->json([
          'status' => 'berhasil'
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
