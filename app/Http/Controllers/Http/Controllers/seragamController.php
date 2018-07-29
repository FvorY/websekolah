<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\d_item;

use App\d_mitra;

use App\d_seragam;

use App\d_item_supplier;

use App\d_supplier;

use DB;

use Yajra\Datatables\Datatables;

use Validator;

class seragamController extends Controller
{
    public function index() {
        return view('seragam.index');
        
    }
//    public function data() {    
//        
//        $seragam = DB::table('d_item')->join('d_seragam', function($join)
//    {
//        $join->on('d_seragam.s_id','=','d_item.i_ref_id');
//        $join->on('d_item.i_ref_table','=',DB::raw("'d_seragam'"));
//    })->join('d_mitra','d_mitra.m_id','=','d_seragam.s_mitra')
//    ->groupBy('i_ref_id')->select(DB::raw('GROUP_CONCAT( DISTINCT (m_name)) as nama_mitra'),
//            's_nama','s_colour','s_jenis','i_id','i_ref_id')    
//    ->orderBy('s_nama')->get();      
//    
//foreach ($seragam as $key => $data) {
//        $seragam[$key]->number=$key+1;
//}
//
//         $pekerja= collect($seragam);
//         
//        
//         
//        return Datatables::of($pekerja)         
//                       ->addColumn('action', function ($pekerja) {
//                            return' <div class="dropdown">                                
//                                            <button class="btn btn-primary btn-flat btn-xs dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
//                                                Kelola
//                                                <span class="caret"></span>
//                                            </button>
//                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
//                                                <li><a href="data-seragam/' . $pekerja->i_id .'/edit" ><i class="fa fa-pencil" aria-hidden="true"></i>Edit Data</a></li>
//                                                <li role="separator" class="divider"></li>                                                                                                                                                                         
//                                                <li><a class="btn-delete" onclick="hapus('.$pekerja->i_ref_id.')"></i>Hapus Data</a></li>                                                
//                                            </ul>
//                                        </div>';
//                        })
//                        ->make(true);
//    }
    public function data() {    
        
        $seragam = DB::select("select i_id,
            
GROUP_CONCAT(s_name ORDER BY s_company ASC)s_name

,GROUP_CONCAT(DISTINCT i_itemnama ORDER BY s_company ASC)i_itemnama
,GROUP_CONCAT(DISTINCT s_colour ORDER BY s_company ASC)s_colour
,GROUP_CONCAT(i_jenis ORDER BY s_company ASC)i_jenis
,GROUP_CONCAT( s_company ORDER BY s_company ASC)s_company,
GROUP_CONCAT(is_price ORDER BY s_company ASC)is_price

,i_ref_id from d_item join d_item_supplier on d_item_supplier.is_item=d_item.i_id
join d_seragam on d_seragam.s_id=d_item.i_ref_id and d_item.i_ref_table='d_seragam'
join d_supplier on d_supplier.s_id=d_item_supplier.is_supplier

group by i_ref_id");      
    //dd($seragam);
foreach ($seragam as $key => $data) {
        $seragam[$key]->number=$key+1;
}

         $pekerja= collect($seragam);
         
        
         
        return Datatables::of($pekerja)         
                       ->addColumn('action', function ($pekerja) {
                            return' <div class="dropdown">                                
                                            <button class="btn btn-primary btn-flat btn-xs dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                Kelola
                                                <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                                <li><a href="data-seragam/' . $pekerja->i_ref_id .'/edit" ><i class="fa fa-pencil" aria-hidden="true"></i>Edit Data</a></li>
                                                <li role="separator" class="divider"></li>                                                                                                                                                                         
                                                <li><a class="btn-delete" onclick="hapus('.$pekerja->i_ref_id.')"></i>Hapus Data</a></li>                                                
                                            </ul>
                                        </div>';
                        })
                      
                       ->addColumn('i_itemnama', function ($pekerja) {
                            $data = explode(',', $pekerja->i_itemnama);
                            $html='';
                            foreach ($data as $perusahaan) {
                              $html.='<ul class="no-padding"><li>'.$perusahaan.'</li></ul>';
                            }
                            return $html;
                        })
                       ->addColumn('s_colour', function ($pekerja) {
                            $data = explode(',', $pekerja->s_colour);
                            $html='';
                            foreach ($data as $perusahaan) {
                              $html.='<ul class="no-padding"><li>'.$perusahaan.'</li></ul>';
                            }
                            return $html;
                        })
                       ->addColumn('i_jenis', function ($pekerja) {
                            $data = explode(',', $pekerja->i_jenis);
                            $html='';
                            foreach ($data as $perusahaan) {
                              $html.='<ul class="no-padding"><li>'.$perusahaan.'</li></ul>';
                            }
                            return $html;
                        })
                       ->addColumn('s_company', function ($pekerja) {
                            $data = explode(',', $pekerja->s_company);
                            $html='';
                            $chek='';
                            foreach ($data as $index => $perusahaan) {
                                if($chek==$perusahaan){
                                     $html.='<ul style="color:white" class="no-padding"><li></li></ul>';
                                }else if($chek!=$perusahaan){                                    
                                    $html.='<ul class="no-padding"><li>'.$perusahaan.'</li></ul>';
                                    $chek=$perusahaan;
                                }
                              
                            }
                            return $html;
                        })
                       ->addColumn('is_price', function ($pekerja) {
                            $data = explode(',', $pekerja->is_price);
                            $html='';
                            foreach ($data as $perusahaan) {
                              $html.='<ul class="no-padding"><li> Rp. '.number_format($perusahaan,2,',','.').'</li></ul>';
                            }
                            return $html;
                        })
                        ->make(true);
    }
    public function edit($id) {
            
    $mitra=d_mitra::get();
    $editSeragam=DB::select("select i_id,d_seragam.s_id as id_seragam,
 GROUP_CONCAT( DISTINCT i_id ORDER BY s_company ASC) i_id,
 GROUP_CONCAT( DISTINCT d_supplier.s_id ORDER BY s_company ASC)
 id_supplier,


GROUP_CONCAT( DISTINCT s_jenis ORDER BY s_company ASC) s_jenis,
GROUP_CONCAT(s_name ORDER BY s_company ASC) nama_supplier
,GROUP_CONCAT(DISTINCT i_itemnama ORDER BY s_company ASC)i_itemnama
,GROUP_CONCAT(DISTINCT s_colour ORDER BY s_company ASC)s_colour
,GROUP_CONCAT(DISTINCT i_jenis ORDER BY s_company ASC)ukuran
,GROUP_CONCAT(s_company ORDER BY s_company ASC)s_company,
GROUP_CONCAT(is_price ORDER BY s_company ASC)is_price
,i_ref_id from d_item join d_item_supplier on d_item_supplier.is_item=d_item.i_id
join d_seragam on d_seragam.s_id=d_item.i_ref_id and d_item.i_ref_table='d_seragam'
join d_supplier on d_supplier.s_id=d_item_supplier.is_supplier
where i_ref_id=$id and d_seragam.s_id=$id
group by i_ref_id");  
    if($editSeragam){
    $editSeragam=$editSeragam[0];
    }else{    
        return 'kosong'; 
    
    }
    
    
    
    
    $supplier =DB::select("select 
GROUP_CONCAT(DISTINCT i_jenis ORDER BY s_company ASC) i_jenis,
GROUP_CONCAT(DISTINCT is_id ORDER BY s_company ASC) is_id,
GROUP_CONCAT(DISTINCT s_company ORDER BY s_company ASC)s_company,
GROUP_CONCAT(DISTINCT is_supplier ORDER BY s_company ASC) id_supplier,
GROUP_CONCAT(DISTINCT is_item ORDER BY s_company ASC) is_item,
GROUP_CONCAT(DISTINCT is_price ORDER BY s_company ASC) is_price 
from d_item_supplier
join d_item on d_item.i_id = d_item_supplier.is_item
join d_supplier on d_supplier.s_id = d_item_supplier.is_supplier
 where is_item in($editSeragam->i_id) and is_supplier in ($editSeragam->id_supplier) group by is_supplier order by is_supplier");
    

//dd($editSeragam->id_supplier);
//dd($editSeragam->i_id);
    
    
    //dd($editSeragam);
return view('seragam.formEdit',compact('editSeragam','editMitra','mitra','supplier'));
    }
    
    public function perbarui($id,Request $request) {
    
        $item=d_item::where('i_ref_id',$id);
        $item->update([
           'i_itemnama'=>$request->Nama_Seragam, 
        ]);
        $seragam=d_seragam::where('s_id',$id);
        $seragam->update([           
            "s_nama"=>$request->Nama_Seragam,        
            "s_colour"=>$request->Warna_Seragam,
            "s_jenis"=>$request->Jenis_Seragam,  
        ]);
    
}


    
    
    
    public function tambah() {
        $mitra=d_mitra::get();
        $supplier=d_supplier::get();        
return view('seragam.formTambah',compact('mitra'));
    }
    public function simpan(Request $request) {                
         return DB::transaction(function() use ($request) {    
             
          
           $rules = [
                "Nama_Seragam" => "required",
                "Warna_Seragam" => "required",
                "Jenis_Seragam" => "required" ,              
            ];
           
      $validator = Validator::make($request->all(), $rules);
      $chekItem=d_item::where('i_mitra',$request->Mitra)->where('i_itemnama',$request->Nama_Seragam)->first();

        if ($validator->fails()) {
            return response()->json([
                        'status' => 'gagal',
                        'data' => $validator->errors()->toArray()
            ]);
        }
        if($chekItem){
            return response()->json([
                        'status' => 'gagal',
                        'data' => 'Data sudah ada'
            ]);
        }
        	
        
      $id_seragam=d_seragam::max('s_id')+1;  
      
      $item_id=  array();
      for ($index1 = 0; $index1 < count($request->ukuran); $index1++) {
          $id_item=d_item::max('i_id')+1;          
          $item_id[$index1]=$id_item;
           d_item::create([               
              "i_id"=>$id_item,        
              "i_mitra"=>$request->Mitra,        
              "i_itemnama"=>$request->Nama_Seragam,
              "i_jenis"=>$request->ukuran[$index1],              
              "i_ref_table"=>'d_seragam',
              "i_ref_id"=>$id_seragam,                                               
             ]); 
           $uk="input".$request->ukuran[$index1];
           
            for ($i = 0; $i < count($request->$uk); $i++) {               
               $id_is=d_item_supplier::max('is_id')+1;
                d_item_supplier::create([
                'is_id'=>$id_is,
                'is_item'=>$id_item,
                'is_supplier'=>$request->id_supplier[$i],
                'is_price'=>$request->$uk[$i]
                ]);
               
           }
           
       //   dd($request->$uk[$index1]);
         // dd($request->inputXS[$index1]);
           
      }
       
      
      
      
      for ($index = 0; $index < count($request->Mitra); $index++) {
          d_seragam::create([               
            "s_id"=>$id_seragam,                    
            "s_mitra"=>$request->Mitra[$index],
            "s_nama"=>$request->Nama_Seragam,        
            "s_colour"=>$request->Warna_Seragam,
            "s_jenis"=>$request->Jenis_Seragam,            
          ]); 
      }
      
      
      
      
       return response()->json([
                        'status' => 'berhasil',                        
            ]);
      
    });
    }
    public function hapus($id) {
        $item       =d_item::where('i_ref_id',$id);
        $seragam    =d_seragam::where('s_id',$id);
        if($item->delete() && $seragam->delete()){
                   return response()->json([
                        'status' => 'berhasil',                        
                    ]);
            }
        
    }
}
