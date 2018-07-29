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


class StockMutController extends Controller
{
    public function tabel(){

     $tanggal2 = Carbon::now()->toDateString();
     $tanggal1 = Carbon::now()->subMonth()->toDateString();

     $start = Carbon::parse($tanggal1)->startOfDay();  //2016-09-29 00:00:00.000000
     $end = Carbon::parse($tanggal2)->endOfDay(); //2016-09-29 23:59:59.000000
     
     $pp = DB::table('d_stock_mutation')
                ->join('d_comp', 'd_stock_mutation.sm_comp', '=', 'd_comp.c_id')
                ->join('d_item', 'd_stock_mutation.sm_item', '=', 'd_item.i_id')
                ->join('d_item_dt', function ($join) {
                      $join->on('d_stock_mutation.sm_item_dt', '=', 'd_item_dt.id_detailid')
                           ->on('d_item.i_id', '=', 'd_item_dt.id_item');
                      })
                ->join('d_size', 'd_item_dt.id_size', '=', 'd_size.s_id')
                ->select('d_stock_mutation.sm_comp','d_stock_mutation.sm_date','d_stock_mutation.sm_detail',
                  'd_stock_mutation.sm_qty','d_stock_mutation.sm_nota','d_comp.c_name','d_size.s_nama',
                  'd_item.i_nama',DB::raw("(d_stock_mutation.sm_qty - d_stock_mutation.sm_use) as sisa_stok_gudang"))
                ->where('d_stock_mutation.sm_date', '>', $start)
                ->where('d_stock_mutation.sm_date', '<', $end)
                ->groupBy('d_stock_mutation.sm_stock','d_stock_mutation.sm_qty','d_stock_mutation.sm_date',
                  'd_stock_mutation.sm_nota')
                ->orderBy('d_stock_mutation.sm_date', 'asc')
                ->get();
        
    $datax = $this->setData($pp);
    //dd(($datax));
    
    echo json_encode($datax);


    
  }

  // untuk filter
  public function tabel2(Request $request){ 

       $req_gudang = $request->gudang;
       $tanggal1 = $request->tanggal1;
       $tanggal2 = $request->tanggal2;
       $req_barang = $request->barang;

       // $tanggalawal=$tanggal1; // explode format date picker
       // $pisah=explode("/",$tanggalawal);
       // $tanggaljadi=$pisah[1]."-".$pisah[0]."-".$pisah[2];

       // $tanggalawal2=$tanggal2;
       // $pisah2=explode("/",$tanggalawal2);
       // $tanggaljadi2=$pisah2[1]."-".$pisah2[0]."-".$pisah2[2];

       $start = Carbon::parse($tanggal1)->startOfDay();  //2016-09-29 00:00:00.000000
       $end = Carbon::parse($tanggal2)->endOfDay(); //2016-09-29 23:59:59.000000
       $pp;

       
       if($req_gudang == "null" && $req_barang =="null"){
          $pp = DB::table('d_stock_mutation')
                ->join('d_comp', 'd_stock_mutation.sm_comp', '=', 'd_comp.c_id')
                ->join('d_item', 'd_stock_mutation.sm_item', '=', 'd_item.i_id')
                ->join('d_item_dt', function ($join) {
                      $join->on('d_stock_mutation.sm_item_dt', '=', 'd_item_dt.id_detailid')
                           ->on('d_item.i_id', '=', 'd_item_dt.id_item');
                      })
                ->join('d_size', 'd_item_dt.id_size', '=', 'd_size.s_id')
                ->select('d_stock_mutation.sm_comp','d_stock_mutation.sm_date','d_stock_mutation.sm_detail',
                  'd_stock_mutation.sm_qty','d_stock_mutation.sm_nota','d_comp.c_name','d_size.s_nama',
                  'd_item.i_nama',DB::raw("(d_stock_mutation.sm_qty - d_stock_mutation.sm_use) as sisa_stok_gudang"))
                ->where('d_stock_mutation.sm_date', '>', $start)
                ->where('d_stock_mutation.sm_date', '<', $end)
                ->groupBy('d_stock_mutation.sm_stock','d_stock_mutation.sm_qty','d_stock_mutation.sm_date',
                  'd_stock_mutation.sm_nota')
                ->orderBy('d_stock_mutation.sm_date', 'asc')
                ->get();
       }

       else if($req_gudang != "null" && $req_barang =="null"){
          $pp = DB::table('d_stock_mutation')
                ->join('d_comp', 'd_stock_mutation.sm_comp', '=', 'd_comp.c_id')
                ->join('d_item', 'd_stock_mutation.sm_item', '=', 'd_item.i_id')
                ->join('d_item_dt', function ($join) {
                      $join->on('d_stock_mutation.sm_item_dt', '=', 'd_item_dt.id_detailid')
                           ->on('d_item.i_id', '=', 'd_item_dt.id_item');
                      })
                ->join('d_size', 'd_item_dt.id_size', '=', 'd_size.s_id')
                ->select('d_stock_mutation.sm_comp','d_stock_mutation.sm_date','d_stock_mutation.sm_detail',
                  'd_stock_mutation.sm_qty','d_stock_mutation.sm_nota','d_comp.c_name','d_size.s_nama',
                  'd_item.i_nama',DB::raw("(d_stock_mutation.sm_qty - d_stock_mutation.sm_use) as sisa_stok_gudang"))
                ->where('d_stock_mutation.sm_date', '>', $start)
                ->where('d_stock_mutation.sm_date', '<', $end)
                ->where('d_stock_mutation.sm_comp', '=', $req_gudang)
                ->groupBy('d_stock_mutation.sm_stock','d_stock_mutation.sm_qty','d_stock_mutation.sm_date',
                  'd_stock_mutation.sm_nota')
                ->orderBy('d_stock_mutation.sm_date', 'asc')
                ->get();    
       }

       else if($req_gudang == "null" && $req_barang !="null"){
          $pp = DB::table('d_stock_mutation')
                ->join('d_comp', 'd_stock_mutation.sm_comp', '=', 'd_comp.c_id')
                ->join('d_item', 'd_stock_mutation.sm_item', '=', 'd_item.i_id')
                ->join('d_item_dt', function ($join) {
                      $join->on('d_stock_mutation.sm_item_dt', '=', 'd_item_dt.id_detailid')
                           ->on('d_item.i_id', '=', 'd_item_dt.id_item');
                      })
                ->join('d_size', 'd_item_dt.id_size', '=', 'd_size.s_id')
                ->select('d_stock_mutation.sm_comp','d_stock_mutation.sm_date','d_stock_mutation.sm_detail',
                  'd_stock_mutation.sm_qty','d_stock_mutation.sm_nota','d_comp.c_name','d_size.s_nama',
                  'd_item.i_nama',DB::raw("(d_stock_mutation.sm_qty - d_stock_mutation.sm_use) as sisa_stok_gudang"))
                ->where('d_stock_mutation.sm_date', '>', $start)
                ->where('d_stock_mutation.sm_date', '<', $end)
                ->where('d_stock_mutation.sm_item', '=', $req_barang)
                ->groupBy('d_stock_mutation.sm_stock','d_stock_mutation.sm_qty','d_stock_mutation.sm_date',
                  'd_stock_mutation.sm_nota')
                ->orderBy('d_stock_mutation.sm_date', 'asc')
                ->get();    
       }

       else if($req_gudang != "null" && $req_barang !="null"){
          $pp = DB::table('d_stock_mutation')
                ->join('d_comp', 'd_stock_mutation.sm_comp', '=', 'd_comp.c_id')
                ->join('d_item', 'd_stock_mutation.sm_item', '=', 'd_item.i_id')
                ->join('d_item_dt', function ($join) {
                      $join->on('d_stock_mutation.sm_item_dt', '=', 'd_item_dt.id_detailid')
                           ->on('d_item.i_id', '=', 'd_item_dt.id_item');
                      })
                ->join('d_size', 'd_item_dt.id_size', '=', 'd_size.s_id')
                ->select('d_stock_mutation.sm_comp','d_stock_mutation.sm_date','d_stock_mutation.sm_detail',
                  'd_stock_mutation.sm_qty','d_stock_mutation.sm_nota','d_comp.c_name','d_size.s_nama',
                  'd_item.i_nama',DB::raw("(d_stock_mutation.sm_qty - d_stock_mutation.sm_use) as sisa_stok_gudang"))
                ->where('d_stock_mutation.sm_date', '>', $start)
                ->where('d_stock_mutation.sm_date', '<', $end)
                ->where('d_stock_mutation.sm_item', '=', $req_barang)
                ->where('d_stock_mutation.sm_comp', '=', $req_gudang)
                ->groupBy('d_stock_mutation.sm_stock','d_stock_mutation.sm_qty','d_stock_mutation.sm_date',
                  'd_stock_mutation.sm_nota')
                ->orderBy('d_stock_mutation.sm_date', 'asc')
                ->get();    
       }
    
      $datax = $this->setData($pp);
   
      echo json_encode($datax);


    
  }




  public function index(){

    $databarang = DB::select(DB::raw("SELECT * FROM d_item"));
    $datagudang = DB::select(DB::raw("SELECT * FROM d_comp"));
    //dd($databarang);
    return view('manajemen-stock.mutasi-stock.index', compact('databarang','datagudang'));
  }

  public function setData($pp){
    $data = array();
    foreach ($pp as $r) {
      $data[] = (array) $r;
     }
     $j=0;
     $sisaakhir[] = array();
     for ($i=0; $i <count($data) ; $i++) { 
         $sisastok = 0;
       for ($x=$i; $x>=0 ; $x--) {

          if($data[$x]['c_name'] == $data[$i]['c_name'] && 
             $data[$x]['s_nama'] == $data[$i]['s_nama']&&
             $data[$x]['i_nama'] == $data[$i]['i_nama'] ){
                $sisastok = $sisastok + $data[$x]['sisa_stok_gudang'];
            }  
                if ($x == 0){
                $sisaakhir[$j] = $sisastok;
                //echo $sisaakhir[$j]." ";
                $j++;
              }
        }
        $data[$i]['sm_date']=Date('d-m-Y H:i:s', strtotime($data[$i]['sm_date']));
    }
        for ($k=0; $k <count($data) ; $k++) { 
           $data[$k]['sisa_stok_gudang'] = $sisaakhir[$k];
        }
        $datax = array('data' => $data);
        return $datax;
  }


}



