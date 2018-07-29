<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\pdm;
use Redirect;
use Response;
use DB;
use Validator;
use App\Http\Controllers\Controller;
use App\d_pekerja;
use App\d_mitra_pekerja;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;

class pdmController extends Controller
{

    public function index(Request $request)
    {
      $data = DB::table('d_mitra')
              ->join('d_mitra_divisi', 'md_mitra', '=', 'm_id')
              ->groupBy('m_name')
              ->get();
        // $apk = DB::table('d_mitra')
        //         ->groupBy('m_name')
        //         ->get();
        // dd($data);
        //dd($dpm);

        return view('pekerja-di-mitra.index',compact('data'));
    }

    public function getdivisi(Request $request){
      $data = DB::table('d_mitra_divisi')
            ->select('md_mitra', 'md_id', 'md_name')
            ->where('md_mitra',$request->id)
            ->get();

      return response()->json($data);
    }

    public function getnomor(Request $request){
      $keyword = $request->term;
      // dd($request);
      $data = DB::table('d_mitra_pekerja')
            ->join('d_pekerja', 'p_id', '=', 'mp_pekerja')
            ->join('d_mitra_contract', 'mc_contractid', '=', 'mp_contract')
            ->join('d_mitra', 'm_id', '=', 'mp_mitra')
            ->leftJoin('d_mitra_divisi', function ($q){
                $q->on('md_mitra', '=', 'mp_mitra')
                    ->on('md_id', '=', 'mp_divisi');
            })
            ->select('p_name', 'mp_mitra_nik', 'mp_workin_date', 'm_name', 'md_name', 'mp_id', 'p_id', 'mc_no')
            ->where('mp_mitra_nik', 'LIKE', '%'.$keyword.'%')
            ->ORwhere('mc_no', 'LIKE', '%'.$keyword.'%')
            ->LIMIT(50)
            ->get();

            if ($data == null) {
                $results[] = ['id' => null, 'label' => 'Tidak ditemukan data terkait'];
            } else {

                foreach ($data as $query) {
                    $results[] = ['id' => $query->mp_id, 'label' => $query->mc_no . ' (' . $query->mp_mitra_nik . ' ' . $query->p_name. ' ' . $query->md_name . ')'];
                }
            }

            return response()->json($results);
    }

    public function getdata(Request $request){
      // dd($request);
      $id = $request->id;
      $pekerja = DB::table('d_mitra_pekerja')
          ->join('d_pekerja', 'p_id', '=', 'mp_pekerja')
          ->join('d_mitra_contract', 'mc_contractid', '=', 'mp_contract')
          ->join('d_mitra', 'm_id', '=', 'mp_mitra')
          ->leftJoin('d_mitra_divisi', function ($q){
              $q->on('md_mitra', '=', 'mp_mitra')
                  ->on('md_id', '=', 'mp_divisi');
          })
          ->select('p_name', 'mp_mitra_nik', 'mp_workin_date', 'm_name', 'md_name', 'mp_id', 'p_id')
          ->where('mp_id', '=', $id)
          ->get();

          // dd($pekerja);
          return response()->json($pekerja);
    }

    public function getpekerja(Request $request){
      // dd($request);
      $mitra = $request->mitra;
      $divisi = $request->divisi;

      if ($mitra == 'all') {
        $pekerja = DB::table('d_mitra_pekerja')
            ->join('d_pekerja', 'p_id', '=', 'mp_pekerja')
            ->join('d_mitra_contract', 'mc_contractid', '=', 'mp_contract')
            ->join('d_mitra', 'm_id', '=', 'mp_mitra')
            ->leftJoin('d_mitra_divisi', function ($q){
                $q->on('md_mitra', '=', 'mp_mitra')
                    ->on('md_id', '=', 'mp_divisi');
            })
            ->select('p_name', 'mp_mitra_nik', 'mp_workin_date', 'm_name', 'md_name', 'mp_id', 'p_id')
            ->get();
      } elseif (!empty($mitra) && $divisi == "all") {
        $pekerja = DB::table('d_mitra_pekerja')
            ->join('d_pekerja', 'p_id', '=', 'mp_pekerja')
            ->join('d_mitra_contract', 'mc_contractid', '=', 'mp_contract')
            ->join('d_mitra', 'm_id', '=', 'mp_mitra')
            ->leftJoin('d_mitra_divisi', function ($q){
                $q->on('md_mitra', '=', 'mp_mitra')
                    ->on('md_id', '=', 'mp_divisi');
            })
            ->select('p_name', 'mp_mitra_nik', 'mp_workin_date', 'm_name', 'md_name', 'mp_id', 'p_id')
            ->where('mp_mitra', '=', $mitra)
            ->get();
      }
      else {
        $pekerja = DB::table('d_mitra_pekerja')
            ->join('d_pekerja', 'p_id', '=', 'mp_pekerja')
            ->join('d_mitra_contract', 'mc_contractid', '=', 'mp_contract')
            ->join('d_mitra', 'm_id', '=', 'mp_mitra')
            ->leftJoin('d_mitra_divisi', function ($q){
                $q->on('md_mitra', '=', 'mp_mitra')
                    ->on('md_id', '=', 'mp_divisi');
            })
            ->select('p_name', 'mp_mitra_nik', 'mp_workin_date', 'm_name', 'md_name', 'mp_id', 'p_id')
            ->where('mp_mitra', '=', $mitra)
            ->where('mp_divisi', '=', $divisi)
            ->get();
      }



          return response()->json($pekerja);
           // return view('pekerja-di-mitra.index',compact('pekerja'));
           // dd($pekerja);
    }

    public function data(Request $request)
    {
        $pekerja = DB::table('d_mitra_pekerja')
            ->join('d_pekerja', 'p_id', '=', 'mp_pekerja')
            ->join('d_mitra_contract', 'mc_contractid', '=', 'mp_contract')
            ->join('d_comp', 'c_id', '=', 'mc_comp')
            ->join('d_mitra', 'm_id', '=', 'mp_mitra')
            ->leftJoin('d_mitra_divisi', function ($q){
                $q->on('md_mitra', '=', 'mp_mitra')
                    ->on('md_id', '=', 'mp_divisi');
            })
            ->select('mp_id', 'c_name', 'p_name', 'p_id', 'm_name', 'mc_no', 'md_name', 'mp_mitra_nik', 'mp_selection_date', 'mp_workin_date')
            ->groupBy('p_id')
            ->get();

        $pekerja = collect($pekerja);

        return Datatables::of($pekerja)
            ->editColumn('mp_workin_date', function ($pekerja){
                if ($pekerja->mp_workin_date == null || $pekerja->mp_workin_date = ''){
                    return '-';
                } else {
                    return Carbon::parse($pekerja->mp_workin_date)->format('d/m/Y');
                }
            })
            ->addColumn('action', function ($pekerja) {
                return '<div class="btn-group text-center" style="text-align: center; width: 100%;"><a href="edit/'.$pekerja->mp_id.'/'.$pekerja->p_id.'"><button style="margin-left:5px;" title="Edit" type="button" class="btn btn-warning btn-xs"><i class="glyphicon glyphicon-edit"></i></button></a><a href="hapus/'.$pekerja->p_id.'"><button style="margin-left:5px;" type="button" class="btn btn-danger btn-xs" title="Hapus"><i class="glyphicon glyphicon-trash"></i></button></a></div>';
            })
            ->make(true);
    }

    public function edit($mp_id,$p_id){

     $dpm1 = pdm::findOrfail($mp_id);
     $dpm = DB::table('d_mitra_pekerja')
     ->join('d_pekerja','d_mitra_pekerja.mp_pekerja','=','d_pekerja.p_id')
     ->join('d_comp','d_mitra_pekerja.mp_comp','=','d_comp.c_id')
     ->where('p_id','=',$p_id)
     ->get();
     foreach ($dpm as $key => $value) {
            $p = $value->p_name;

          }
          foreach ($dpm as $key => $value) {
            $c = $value->c_name;

          }
        return view('pekerja-di-mitra.Formedit',compact('c','p','dpm','dpm1','pekerja'));
    }
    public function update(Request $request,$mp_id){

       $dpm = pdm::findOrfail($mp_id);
       $dpm->mp_comp=$request->p_n;
       $dpm->mp_pekerja=$request->n_p;
       $dpm->mp_mitra=$request->mitra;
       $dpm->mp_divisi=$request->divisi;
       $dpm->mp_mitra_nik=$request->m_n;
       $dpm->mp_state=$request->st;
       $dpm->mp_selection_date=date('Y-m-d',strtotime($request->tgl_s));
       $dpm->mp_workin_date=date('Y-m-d',strtotime($request->tgl_k));
       $dpm->mp_uniform_receive_date=date('Y-m-d',strtotime($request->a_s));
       $dpm->mp_uniform_paid_date=date('Y-m-d',strtotime($request->b_s));
       $dpm->save();
       return redirect('/pekerja-di-mitra/pekerja-mitra');

    }

     public function hapus($mp_pekerja){

     DB::table('d_mitra_pekerja')->where('mp_pekerja','=',$mp_pekerja)->delete();
      return redirect('pekerja-di-mitra/pekerja-mitra');

    }

    public function getnik(Request $request){
      $data = DB::table('d_mitra_pekerja')
          ->select('mp_mitra_nik')
          ->where('mp_id',$request->id)
          ->get();

      return response()->json($data);
    }

    public function simpannik(Request $request){
      DB::beginTransaction();
      try {
        $d_mitra_pekerja = d_mitra_pekerja::where('mp_id',$request->id);
        $d_mitra_pekerja->update([
          'mp_mitra_nik' => $request->nik
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
}
