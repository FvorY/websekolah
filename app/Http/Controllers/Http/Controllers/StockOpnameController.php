<?php  
namespace App\Http\Controllers;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PDF;
use DB;
use Response;
Use Carbon\Carbon;
use Session;
use Mail;
use Illuminate\Support\Facades\Input;
use Dompdf\Dompdf;


class StockOpnameController extends Controller
{

public function tabel () {
        $list = DB::table('d_stock_opname')
                  ->join('d_comp','d_stock_opname.so_comp', '=', 'd_comp.c_id')
                  ->select('d_stock_opname.*','d_comp.*')
                  ->get();
        $data = array();
        foreach ($list as $r) {
            $data[] = (array) $r;
        }
        $i=0;
        foreach ($data as $key) {
            // add new button
            $data[$i]['button'] = '<div class="btn-group">
                                   <button id="'.$data[$i]['so_id'].'" data-toggle="tooltip" title="edit" class="btn btn-warning btn-md edit" ><i class="fa fa-pencil-square-o"></i></button>
                                   <button id="'.$data[$i]['so_id'].'" data-toggle="tooltip" title="detail" class="btn btn-success btn-md detail" ><i class="fa fa-folder-open"></i></button>
                                   </div> ';
            $data[$i]['so_status'] ='<label class="label label-lg label-warning">'.$data[$i]['so_status'].'</label>';
            $data[$i]['so_date']=Date('d-m-Y H:i:s', strtotime($data[$i]['so_date']));
            $i++;
        }
        $datax = array('data' => $data);
        echo json_encode($datax);
    }
    
public function index() {
    $databarang = DB::select(DB::raw("SELECT * FROM d_item"));
    return view('manajemen-stock/stock-opname/index',compact('databarang'));
  }


public function opname(Request $request){
    $req_barang = $request->barang;

    $pp = DB::table('d_stock')
                ->join('d_comp', 'd_stock.s_comp', '=', 'd_comp.c_id')
                ->join('d_item', 'd_stock.s_item', '=', 'd_item.i_id')
                ->join('d_item_dt', function ($join) {
                      $join->on('d_stock.s_item_dt', '=', 'd_item_dt.id_detailid')
                           ->on('d_item.i_id', '=', 'd_item_dt.id_item');
                      })
                ->join('d_mitra_item', function ($join) {
                      $join->on('d_item.i_id', '=', 'd_mitra_item.mi_item');
                      })
                ->join('d_mitra','d_mitra.m_id', '=', 'd_mitra_item.mi_mitra')
                ->join('d_size', 'd_item_dt.id_size', '=', 'd_size.s_id')
                ->select('d_stock.*','d_comp.c_name','d_size.*','d_item.i_nama','d_mitra.m_name')
                ->where('d_stock.s_item', '=', $req_barang)
                ->orderBy('d_item.i_nama', 'asc')
                ->orderBy('d_size.s_id', 'asc')
                ->get();

    $nota = $this->setnota();

    $data = array();
      foreach ($pp as $r) {
          $data[] = (array) $r;
      }
      $i=0;
      foreach ($data as $key) {
          // add new data
          $data[$i]['hidden'] = $nota;
          $i++;
      }
                
    return $data;
}

public function get_data(Request $request){
   $req_so_stock_opname = $request->id;

   $data = DB::table('d_stock_opname_dt')
                ->join('d_stock_opname', 'd_stock_opname_dt.so_stock_opname', '=', 'd_stock_opname.so_id')
                ->join('d_item', 'd_stock_opname_dt.so_item', '=', 'd_item.i_id')
                ->join('d_item_dt', function ($join) {
                          $join->on('d_stock_opname_dt.so_item_dt', '=', 'd_item_dt.id_detailid')
                               ->on('d_item.i_id', '=', 'd_item_dt.id_item');
                          })
                ->join('d_size', 'd_item_dt.id_size', '=', 'd_size.s_id')
                ->select('d_stock_opname.*','d_stock_opname_dt.*','d_item.*','d_item_dt.*','d_size.*')
                ->where('d_stock_opname_dt.so_stock_opname', '=', $req_so_stock_opname)
                ->get();

   return $data;

}

public function detail(Request $request){
  $id =$request->id;
  $list = DB::table('d_stock_opname')
            ->join('d_comp','d_stock_opname.so_comp', '=', 'd_comp.c_id')
            ->join('d_stock_opname_dt','d_stock_opname.so_id', '=', 'd_stock_opname_dt.so_stock_opname')
            ->join('d_item', 'd_stock_opname_dt.so_item', '=', 'd_item.i_id')
            ->join('d_item_dt', function ($join) {
                      $join->on('d_stock_opname_dt.so_item_dt', '=', 'd_item_dt.id_detailid')
                           ->on('d_item.i_id', '=', 'd_item_dt.id_item');
                      })
            ->join('d_size', 'd_item_dt.id_size', '=', 'd_size.s_id')
            ->select('d_stock_opname.*','d_stock_opname_dt.*','d_item.*','d_size.*','d_comp.*')
            ->where('d_stock_opname.so_id', '=', $id)
            ->get();

  $data = array();
        foreach ($list as $r) {
            $data[] = (array) $r;
        }
        $i=0;
        foreach ($data as $key) {
            // add new button
            $data[$i]['so_date']=Date('d-m-Y H:i:s', strtotime($data[$i]['so_date']));
            $i++;
        }
        echo json_encode($data);
}

public function simpan(Request $request){

      for ($j=0; $j <count($request->item) ; $j++) { 
        if($request->sesuaikan[$j] == null){
          return response()->json(['status' => 0]);  
        }

      }

        $cari_max_so_id = DB::table('d_stock_opname')
                          ->max('so_id');

        if ($cari_max_so_id != null) {
          $cari_max_so_id += 1;
        }else{
          $cari_max_so_id = 1;
        }


        $save_stock_opname = DB::table('d_stock_opname')
                 ->insert([
                  'so_id'         => $cari_max_so_id,
                  'so_comp'       => $request->s_comp[0],
                  'so_date'       => Carbon::now(),
                  'so_status'     => "PENDING",
                  'so_nota'       => $request->nota[0]
              ]);
      
       for ($i=0; $i < count($request->item); $i++) {

        $cari_max_so_detailid = DB::table('d_stock_opname_dt')
                                ->max('so_detailid');

        if ($cari_max_so_detailid != null) {
          $cari_max_so_detailid += 1;
        }else{
          $cari_max_so_detailid = 1;
        }

        $save_stock_opname_dt = DB::table('d_stock_opname_dt')
                 ->insert([
                  'so_stock_opname' => $cari_max_so_id,
                  'so_detailid'     => $cari_max_so_detailid,
                  'so_item'         => $request->item[$i],
                  'so_item_dt'      => $request->item_dt[$i],
                  'so_qty_sistem'   => $request->stocksistem[$i],
                  'so_qty_real'     => $request->stockreal[$i],
                  'so_keterangan'   => $request->sesuaikan[$i]
              ]);
       }

      return response()->json(['status' => 1]);  
  }

  public function edit(Request $request){

    for ($j=0; $j <count($request->so_detailid) ; $j++) { 
        if($request->sesuaikan_edit[$j] == null){
          return response()->json(['status' => 0]);  
        }
      }

    for ($i=0; $i <count($request->so_detailid); $i++) { 
       $update_stock_opname_dt = DB::table('d_stock_opname_dt')
                                     ->where('so_stock_opname', $request->so_stock_opname[$i])
                                     ->where('so_detailid', $request->so_detailid[$i])
                                     ->update([
                                      'so_qty_real'     => $request->so_qty_real[$i],
                                      'so_keterangan'   => $request->sesuaikan_edit[$i]
                                  ]);
     } 

     return response()->json(['status' => 1]); 

  }

  public function setnota(){
    $angka = rand(10, 99);
    $tanggal = date('y/m/d/His');
    $nota = 'PO-' . $tanggal . '/'. $angka;
    return $nota;
  }

}
//imh


