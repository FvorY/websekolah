<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\d_comp_jurnal_resume;

use Session;

use DB;

class labaRugiController extends Controller
{
    
     public function labarugiIndex() {
         
        $comp = Session::get('mem_comp');
        $year = Session::get('mem_year');
//         $chek = d_comp_jurnal_resume::select(DB::raw('substr(cjr_period,5,6) cjr_period'), 'cjr_value')->where('cjr_comp', $comp)
//                        ->where(DB::raw('substr(cjr_period,1,4)'), $year)->groupBy('cjr_period')->get(); //where('cjr_comp',$year)        
//        $bulan = [];
//
//        foreach ($chek as $index => $data) {
//
//            $months = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');
//            $bulan[$index] = $months[(int) $data->cjr_period];
//        }
         return view('laporan.labarugiIndex',compact('bulan'));
     }
     public function labarugi() {
     
        $comp = Session::get('mem_comp');
        $year = Session::get('mem_year');
        //$tgl1 = Carbon::now()->format('Y-m-d');
        //$tgl2 = Carbon::now()->format('Y-m-d');

//pendapatan ada isi
        $pendapatan = DB::select(DB::raw("select tr_code,tr_name,sum(tt_income*jr_value) as jum from d_jurnal dj
                        left join m_trans_cat on left(jr_trans,2) = tt_code
                        left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                        where jr_comp = '$comp' and jr_year = '$year'
                        and tt_code in ('10','11','12')
                        group by tr_code order by tr_code"));
        $total_pendapata = 0;
        foreach ($pendapatan as $total_pendapatan) {
            $total_pendapata+=$total_pendapatan->jum;
        }        
//hpp ada isi
        $hpp = DB::select(DB::raw("select tr_code,tr_name,sum(tt_income*jr_value) as jum from d_jurnal dj
                        left join m_trans_cat on left(jr_trans,2) = tt_code
                        left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                        where jr_comp = '$comp' and jr_year = '$year'
                        and tt_code in ('20','21')
                        group by tr_code order by tr_code"));
        $total_hpp = 0;
        foreach ($hpp as $total) {            
            $total_hpp+=$total->jum;
        }

//expenses ada isi
        $expenses = DB::select(DB::raw("select tr_code,tr_name,sum(tt_income*jr_value) as jum from d_jurnal dj
                        left join m_trans_cat on left(jr_trans,2) = tt_code
                        left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                        where jr_comp = '$comp' and jr_year = '$year'
                        and tt_code in ('30')
                        group by tr_code order by tr_code"));

        $total_expenses = 0;
        foreach ($expenses as $total) {
            $total_expenses+=$total->jum;
        }


//DEPRESIASI / DEPRECIACION
        $depresiasi = DB::select(DB::raw("select tr_code,tr_name,sum(tt_income*jr_value) as jum from d_jurnal dj
                        left join m_trans_cat on left(jr_trans,2) = tt_code
                        left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                        where jr_comp = '$comp' and jr_year = '$year'
                        and tt_code in ('41')
                        group by tr_code order by tr_code
                            "));
        $total_depresiasi = 0;
        foreach ($depresiasi as $total) {
            $total_depresiasi+=$total->jum;
        }
        
//amortisasi 
        $amortisasi = DB::select(DB::raw("select tr_code,tr_name,sum(tt_income*jr_value) as jum from d_jurnal dj
                        left join m_trans_cat on left(jr_trans,2) = tt_code
                        left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                        where jr_comp = '$comp' and jr_year = '$year'
                        and tt_code in ('42')
                        group by tr_code order by tr_code
                            "));
        $total_amortisasi = 0;
        foreach ($amortisasi as $total) {
            $total_amortisasi+=$total->jum;
        }
//PENDAPATAN LAIN-LAIN
        $pendapatanlain = DB::select(DB::raw("select tr_code,tr_name,sum(tt_income*jr_value) as jum from d_jurnal dj
                        left join m_trans_cat on left(jr_trans,2) = tt_code
                        left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                        where jr_comp = '$comp' and jr_year = '$year'
                        and tt_code in ('51')
                        group by tr_code order by tr_code
                            "));
        $total_pendapatanlain = 0;
        foreach ($pendapatanlain as $total) {
            $total_pendapatanlain+=$total->jum;
        }
//PENGELUARAN LAIN-LAIN
        $pengeluaranlain = DB::select(DB::raw("select tr_code,tr_name,sum(tt_income*jr_value) as jum from d_jurnal dj
                        left join m_trans_cat on left(jr_trans,2) = tt_code
                        left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                        where jr_comp = '$comp' and jr_year = '$year'
                        and tt_code in ('52')
                        group by tr_code order by tr_code
                            "));
        $total_pengeluaranlain = 0;
        foreach ($pengeluaranlain as $total) {
            $total_pengeluaranlain+=$total->jum;
        }
//BUNGA / INTEREST
        $bunggainvesi = DB::select(DB::raw("select tr_code,tr_name,sum(tt_income*jr_value) as jum from d_jurnal dj
                        left join m_trans_cat on left(jr_trans,2) = tt_code
                        left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                        where jr_comp = '$comp' and jr_year = '$year'
                        and tt_code in ('61')
                        group by tr_code order by tr_code
                            "));
        $total_bunggainvesi = 0;
        foreach ($bunggainvesi as $total) {
            $total_bunggainvesi+=$total->jum;
        }
//PAJAK / TAX  
        $pajak = DB::select(DB::raw("select tr_code,tr_name,sum(tt_income*jr_value) as jum from d_jurnal dj
                        left join m_trans_cat on left(jr_trans,2) = tt_code
                        left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                        where jr_comp = '$comp' and jr_year = '$year'
                        and tt_code in ('62')
                        group by tr_code order by tr_code
                            "));

        $total_pajak = 0;
        foreach ($pajak as $total) {
            $total_pajak+=$total->jum;
        }


        return view('laporan.labarugi', compact('pendapatan', 'hpp', 'expenses', 'depresiasi', 'amortisasi', 'pendapatanlain', 'pengeluaranlain', 'bunggainvesi', 'pajak', 'date1', 'date2', 'total_pendapata', 'total_hpp'
                        , 'total_expenses'
                        , 'total_depresiasi', 'total_pendapatanlain', 'total_pengeluaranlain'
                        , 'total_bunggainvesi', 'total_pajak'));
    }
     public function labarugiPercobaanPer($date) {
     // tgl mulai sampai akhir
        $comp = Session::get('mem_comp');
        $year = Session::get('mem_year');
        $coba=str_replace(' ', '-', $date).'-'.$year;        
        $date = date('Y-m-d', strtotime($coba));        
        //$tgl1 = Carbon::now()->format('Y-m-d');
        //$tgl2 = Carbon::now()->format('Y-m-d');

//pendapatan ada isi
        $pendapatan = DB::select(DB::raw("select tr_code,tr_name,sum(tt_income*jr_value) as jum from d_jurnal dj
                        left join m_trans_cat on left(jr_trans,2) = tt_code
                        left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                        where jr_comp = '$comp' and jr_year = '$year'
                        and tt_code in ('10','11','12')
 and jr_tgl BETWEEN (select min(jr_tgl) from d_jurnal where jr_year = '$year' and jr_comp = '$comp')
								and '$date'                       



                        group by tr_code order by tr_code"));
        $total_pendapata = 0;
        foreach ($pendapatan as $total_pendapatan) {
            $total_pendapata+=$total_pendapatan->jum;
        }        
//hpp ada isi
        $hpp = DB::select(DB::raw("select tr_code,tr_name,sum(tt_income*jr_value) as jum from d_jurnal dj
                        left join m_trans_cat on left(jr_trans,2) = tt_code
                        left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                        where jr_comp = '$comp' and jr_year = '$year'
                        and tt_code in ('20','21')
and jr_tgl BETWEEN (select min(jr_tgl) from d_jurnal where jr_year = '$year' and jr_comp = '$comp')
								and '$date'                         

                        group by tr_code order by tr_code"));
        $total_hpp = 0;
        foreach ($hpp as $total) {            
            $total_hpp+=$total->jum;
        }

//expenses ada isi
        $expenses = DB::select(DB::raw("select tr_code,tr_name,sum(tt_income*jr_value) as jum from d_jurnal dj
                        left join m_trans_cat on left(jr_trans,2) = tt_code
                        left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                        where jr_comp = '$comp' and jr_year = '$year'
                        and tt_code in ('30')

and jr_tgl BETWEEN (select min(jr_tgl) from d_jurnal where jr_year = '$year' and jr_comp = '$comp')
								and '$date' 

                        group by tr_code order by tr_code"));

        $total_expenses = 0;
        foreach ($expenses as $total) {
            $total_expenses+=$total->jum;
        }


//DEPRESIASI / DEPRECIACION
        $depresiasi = DB::select(DB::raw("select tr_code,tr_name,sum(tt_income*jr_value) as jum from d_jurnal dj
                        left join m_trans_cat on left(jr_trans,2) = tt_code
                        left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                        where jr_comp = '$comp' and jr_year = '$year'
                        and tt_code in ('41')
                        
and jr_tgl BETWEEN (select min(jr_tgl) from d_jurnal where jr_year = '$year' and jr_comp = '$comp')
								and '$date' 
                        group by tr_code order by tr_code
                            "));
        $total_depresiasi = 0;
        foreach ($depresiasi as $total) {
            $total_depresiasi+=$total->jum;
        }
        
//amortisasi 
        $amortisasi = DB::select(DB::raw("select tr_code,tr_name,sum(tt_income*jr_value) as jum from d_jurnal dj
                        left join m_trans_cat on left(jr_trans,2) = tt_code
                        left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                        where jr_comp = '$comp' and jr_year = '$year'
                        and tt_code in ('42')
                        
and jr_tgl BETWEEN (select min(jr_tgl) from d_jurnal where jr_year = '$year' and jr_comp = '$comp')
								and '$date' 
                        group by tr_code order by tr_code
                            "));
        $total_amortisasi = 0;
        foreach ($amortisasi as $total) {
            $total_amortisasi+=$total->jum;
        }
//PENDAPATAN LAIN-LAIN
        $pendapatanlain = DB::select(DB::raw("select tr_code,tr_name,sum(tt_income*jr_value) as jum from d_jurnal dj
                        left join m_trans_cat on left(jr_trans,2) = tt_code
                        left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                        where jr_comp = '$comp' and jr_year = '$year'
                        and tt_code in ('51')
                        
and jr_tgl BETWEEN (select min(jr_tgl) from d_jurnal where jr_year = '$year' and jr_comp = '$comp')
								and '$date' 
                        group by tr_code order by tr_code
                            "));
        $total_pendapatanlain = 0;
        foreach ($pendapatanlain as $total) {
            $total_pendapatanlain+=$total->jum;
        }
//PENGELUARAN LAIN-LAIN
        $pengeluaranlain = DB::select(DB::raw("select tr_code,tr_name,sum(tt_income*jr_value) as jum from d_jurnal dj
                        left join m_trans_cat on left(jr_trans,2) = tt_code
                        left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                        where jr_comp = '$comp' and jr_year = '$year'
                        and tt_code in ('52')
                        
and jr_tgl BETWEEN (select min(jr_tgl) from d_jurnal where jr_year = '$year' and jr_comp = '$comp')
								and '$date' 
                        group by tr_code order by tr_code
                            "));
        $total_pengeluaranlain = 0;
        foreach ($pengeluaranlain as $total) {
            $total_pengeluaranlain+=$total->jum;
        }
//BUNGA / INTEREST
        $bunggainvesi = DB::select(DB::raw("select tr_code,tr_name,sum(tt_income*jr_value) as jum from d_jurnal dj
                        left join m_trans_cat on left(jr_trans,2) = tt_code
                        left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                        where jr_comp = '$comp' and jr_year = '$year'
                        and tt_code in ('61')
                        
and jr_tgl BETWEEN (select min(jr_tgl) from d_jurnal where jr_year = '$year' and jr_comp = '$comp')
								and '$date' 
                        group by tr_code order by tr_code
                            "));
        $total_bunggainvesi = 0;
        foreach ($bunggainvesi as $total) {
            $total_bunggainvesi+=$total->jum;
        }
//PAJAK / TAX  
        $pajak = DB::select(DB::raw("select tr_code,tr_name,sum(tt_income*jr_value) as jum from d_jurnal dj
                        left join m_trans_cat on left(jr_trans,2) = tt_code
                        left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                        where jr_comp = '$comp' and jr_year = '$year'
                        and tt_code in ('62')
                        
and jr_tgl BETWEEN (select min(jr_tgl) from d_jurnal where jr_year = '$year' and jr_comp = '$comp')
								and '$date' 
                        group by tr_code order by tr_code
                            "));

        $total_pajak = 0;
        foreach ($pajak as $total) {
            $total_pajak+=$total->jum;
        }


        return view('laporan.labarugi', compact('pendapatan', 'hpp', 'expenses', 'depresiasi', 'amortisasi', 'pendapatanlain', 'pengeluaranlain', 'bunggainvesi', 'pajak', 'date1', 'date2', 'total_pendapata', 'total_hpp'
                        , 'total_expenses'
                        , 'total_depresiasi', 'total_pendapatanlain', 'total_pengeluaranlain'
                        , 'total_bunggainvesi', 'total_pajak'));
    }
     public function labarugiFinalPer($date) {
     // tgl mulai sampai akhir
        $comp = Session::get('mem_comp');
        $year = Session::get('mem_year');
        $coba=str_replace(' ', '-', $date).'-'.$year;        
        $date = date('Y-m-t', strtotime($coba));             
        //$tgl1 = Carbon::now()->format('Y-m-d');
        //$tgl2 = Carbon::now()->format('Y-m-d');

//pendapatan ada isi
        $pendapatan = DB::select(DB::raw("select tr_code,tr_name,sum(tt_income*jr_value) as jum from d_jurnal dj
                        left join m_trans_cat on left(jr_trans,2) = tt_code
                        left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                        where jr_comp = '$comp' and jr_year = '$year'
                        and tt_code in ('10','11','12')
 and jr_tgl BETWEEN (select min(jr_tgl) from d_jurnal where jr_year = '$year' and jr_comp = '$comp')
								and '$date'                       



                        group by tr_code order by tr_code"));
        $total_pendapata = 0;
        foreach ($pendapatan as $total_pendapatan) {
            $total_pendapata+=$total_pendapatan->jum;
        }        
//hpp ada isi
        $hpp = DB::select(DB::raw("select tr_code,tr_name,sum(tt_income*jr_value) as jum from d_jurnal dj
                        left join m_trans_cat on left(jr_trans,2) = tt_code
                        left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                        where jr_comp = '$comp' and jr_year = '$year'
                        and tt_code in ('20','21')
and jr_tgl BETWEEN (select min(jr_tgl) from d_jurnal where jr_year = '$year' and jr_comp = '$comp')
								and '$date'                         

                        group by tr_code order by tr_code"));
        $total_hpp = 0;
        foreach ($hpp as $total) {            
            $total_hpp+=$total->jum;
        }

//expenses ada isi
        $expenses = DB::select(DB::raw("select tr_code,tr_name,sum(tt_income*jr_value) as jum from d_jurnal dj
                        left join m_trans_cat on left(jr_trans,2) = tt_code
                        left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                        where jr_comp = '$comp' and jr_year = '$year'
                        and tt_code in ('30')

and jr_tgl BETWEEN (select min(jr_tgl) from d_jurnal where jr_year = '$year' and jr_comp = '$comp')
								and '$date' 

                        group by tr_code order by tr_code"));

        $total_expenses = 0;
        foreach ($expenses as $total) {
            $total_expenses+=$total->jum;
        }


//DEPRESIASI / DEPRECIACION
        $depresiasi = DB::select(DB::raw("select tr_code,tr_name,sum(tt_income*jr_value) as jum from d_jurnal dj
                        left join m_trans_cat on left(jr_trans,2) = tt_code
                        left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                        where jr_comp = '$comp' and jr_year = '$year'
                        and tt_code in ('41')
                        
and jr_tgl BETWEEN (select min(jr_tgl) from d_jurnal where jr_year = '$year' and jr_comp = '$comp')
								and '$date' 
                        group by tr_code order by tr_code
                            "));
        $total_depresiasi = 0;
        foreach ($depresiasi as $total) {
            $total_depresiasi+=$total->jum;
        }
        
//amortisasi 
        $amortisasi = DB::select(DB::raw("select tr_code,tr_name,sum(tt_income*jr_value) as jum from d_jurnal dj
                        left join m_trans_cat on left(jr_trans,2) = tt_code
                        left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                        where jr_comp = '$comp' and jr_year = '$year'
                        and tt_code in ('42')
                        
and jr_tgl BETWEEN (select min(jr_tgl) from d_jurnal where jr_year = '$year' and jr_comp = '$comp')
								and '$date' 
                        group by tr_code order by tr_code
                            "));
        $total_amortisasi = 0;
        foreach ($amortisasi as $total) {
            $total_amortisasi+=$total->jum;
        }
//PENDAPATAN LAIN-LAIN
        $pendapatanlain = DB::select(DB::raw("select tr_code,tr_name,sum(tt_income*jr_value) as jum from d_jurnal dj
                        left join m_trans_cat on left(jr_trans,2) = tt_code
                        left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                        where jr_comp = '$comp' and jr_year = '$year'
                        and tt_code in ('51')
                        
and jr_tgl BETWEEN (select min(jr_tgl) from d_jurnal where jr_year = '$year' and jr_comp = '$comp')
								and '$date' 
                        group by tr_code order by tr_code
                            "));
        $total_pendapatanlain = 0;
        foreach ($pendapatanlain as $total) {
            $total_pendapatanlain+=$total->jum;
        }
//PENGELUARAN LAIN-LAIN
        $pengeluaranlain = DB::select(DB::raw("select tr_code,tr_name,sum(tt_income*jr_value) as jum from d_jurnal dj
                        left join m_trans_cat on left(jr_trans,2) = tt_code
                        left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                        where jr_comp = '$comp' and jr_year = '$year'
                        and tt_code in ('52')
                        
and jr_tgl BETWEEN (select min(jr_tgl) from d_jurnal where jr_year = '$year' and jr_comp = '$comp')
								and '$date' 
                        group by tr_code order by tr_code
                            "));
        $total_pengeluaranlain = 0;
        foreach ($pengeluaranlain as $total) {
            $total_pengeluaranlain+=$total->jum;
        }
//BUNGA / INTEREST
        $bunggainvesi = DB::select(DB::raw("select tr_code,tr_name,sum(tt_income*jr_value) as jum from d_jurnal dj
                        left join m_trans_cat on left(jr_trans,2) = tt_code
                        left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                        where jr_comp = '$comp' and jr_year = '$year'
                        and tt_code in ('61')
                        
and jr_tgl BETWEEN (select min(jr_tgl) from d_jurnal where jr_year = '$year' and jr_comp = '$comp')
								and '$date' 
                        group by tr_code order by tr_code
                            "));
        $total_bunggainvesi = 0;
        foreach ($bunggainvesi as $total) {
            $total_bunggainvesi+=$total->jum;
        }
//PAJAK / TAX  
        $pajak = DB::select(DB::raw("select tr_code,tr_name,sum(tt_income*jr_value) as jum from d_jurnal dj
                        left join m_trans_cat on left(jr_trans,2) = tt_code
                        left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                        where jr_comp = '$comp' and jr_year = '$year'
                        and tt_code in ('62')
                        
and jr_tgl BETWEEN (select min(jr_tgl) from d_jurnal where jr_year = '$year' and jr_comp = '$comp')
								and '$date' 
                        group by tr_code order by tr_code
                            "));

        $total_pajak = 0;
        foreach ($pajak as $total) {
            $total_pajak+=$total->jum;
        }


        return view('laporan.labarugi', compact('pendapatan', 'hpp', 'expenses', 'depresiasi', 'amortisasi', 'pendapatanlain', 'pengeluaranlain', 'bunggainvesi', 'pajak', 'date1', 'date2', 'total_pendapata', 'total_hpp'
                        , 'total_expenses'
                        , 'total_depresiasi', 'total_pendapatanlain', 'total_pengeluaranlain'
                        , 'total_bunggainvesi', 'total_pajak'));
    }
     public function labarugiPercobaanPeriode($date) {
         //per bulan     
        $comp = Session::get('mem_comp');
        $year = Session::get('mem_year');
        $date=str_replace(' ', '-', $date).'-'.$year;        
        $bulan = date('m', strtotime($date));
        
        
//pendapatan ada isi
        $pendapatan = DB::select(DB::raw("select tr_code,tr_name,sum(tt_income*jr_value) as jum from d_jurnal dj
                        left join m_trans_cat on left(jr_trans,2) = tt_code
                        left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                        where jr_comp = '$comp' and jr_year = '$year'
                        and tt_code in ('10','11','12')
 and month(jr_tgl)=$bulan and Year(jr_tgl)=$year



                        group by tr_code order by tr_code"));
        
        $total_pendapata = 0;
        foreach ($pendapatan as $total_pendapatan) {
            $total_pendapata+=$total_pendapatan->jum;
        }        
//hpp ada isi
        $hpp = DB::select(DB::raw("select tr_code,tr_name,sum(tt_income*jr_value) as jum from d_jurnal dj
                        left join m_trans_cat on left(jr_trans,2) = tt_code
                        left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                        where jr_comp = '$comp' and jr_year = '$year'
                        and tt_code in ('20','21')
and month(jr_tgl)=$bulan and Year(jr_tgl)=$year                        

                        group by tr_code order by tr_code"));
        $total_hpp = 0;
        foreach ($hpp as $total) {            
            $total_hpp+=$total->jum;
        }

//expenses ada isi
        $expenses = DB::select(DB::raw("select tr_code,tr_name,sum(tt_income*jr_value) as jum from d_jurnal dj
                        left join m_trans_cat on left(jr_trans,2) = tt_code
                        left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                        where jr_comp = '$comp' and jr_year = '$year'
                        and tt_code in ('30')

and month(jr_tgl)=$bulan and Year(jr_tgl)=$year

                        group by tr_code order by tr_code"));

        $total_expenses = 0;
        foreach ($expenses as $total) {
            $total_expenses+=$total->jum;
        }


//DEPRESIASI / DEPRECIACION
        $depresiasi = DB::select(DB::raw("select tr_code,tr_name,sum(tt_income*jr_value) as jum from d_jurnal dj
                        left join m_trans_cat on left(jr_trans,2) = tt_code
                        left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                        where jr_comp = '$comp' and jr_year = '$year'
                        and tt_code in ('41')
                        
and month(jr_tgl)=$bulan and Year(jr_tgl)=$year
                        group by tr_code order by tr_code
                            "));
        $total_depresiasi = 0;
        foreach ($depresiasi as $total) {
            $total_depresiasi+=$total->jum;
        }
        
//amortisasi 
        $amortisasi = DB::select(DB::raw("select tr_code,tr_name,sum(tt_income*jr_value) as jum from d_jurnal dj
                        left join m_trans_cat on left(jr_trans,2) = tt_code
                        left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                        where jr_comp = '$comp' and jr_year = '$year'
                        and tt_code in ('42')
                        
and month(jr_tgl)=$bulan and Year(jr_tgl)=$year
                        group by tr_code order by tr_code
                            "));
        $total_amortisasi = 0;
        foreach ($amortisasi as $total) {
            $total_amortisasi+=$total->jum;
        }
//PENDAPATAN LAIN-LAIN
        $pendapatanlain = DB::select(DB::raw("select tr_code,tr_name,sum(tt_income*jr_value) as jum from d_jurnal dj
                        left join m_trans_cat on left(jr_trans,2) = tt_code
                        left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                        where jr_comp = '$comp' and jr_year = '$year'
                        and tt_code in ('51')
                        
and month(jr_tgl)=$bulan and Year(jr_tgl)=$year
                        group by tr_code order by tr_code
                            "));
        $total_pendapatanlain = 0;
        foreach ($pendapatanlain as $total) {
            $total_pendapatanlain+=$total->jum;
        }
//PENGELUARAN LAIN-LAIN
        $pengeluaranlain = DB::select(DB::raw("select tr_code,tr_name,sum(tt_income*jr_value) as jum from d_jurnal dj
                        left join m_trans_cat on left(jr_trans,2) = tt_code
                        left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                        where jr_comp = '$comp' and jr_year = '$year'
                        and tt_code in ('52')
                        
and month(jr_tgl)=$bulan and Year(jr_tgl)=$year
                        group by tr_code order by tr_code
                            "));
        $total_pengeluaranlain = 0;
        foreach ($pengeluaranlain as $total) {
            $total_pengeluaranlain+=$total->jum;
        }
//BUNGA / INTEREST
        $bunggainvesi = DB::select(DB::raw("select tr_code,tr_name,sum(tt_income*jr_value) as jum from d_jurnal dj
                        left join m_trans_cat on left(jr_trans,2) = tt_code
                        left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                        where jr_comp = '$comp' and jr_year = '$year'
                        and tt_code in ('61')
                        
and month(jr_tgl)=$bulan and Year(jr_tgl)=$year
                        group by tr_code order by tr_code
                            "));
        $total_bunggainvesi = 0;
        foreach ($bunggainvesi as $total) {
            $total_bunggainvesi+=$total->jum;
        }
//PAJAK / TAX  
        $pajak = DB::select(DB::raw("select tr_code,tr_name,sum(tt_income*jr_value) as jum from d_jurnal dj
                        left join m_trans_cat on left(jr_trans,2) = tt_code
                        left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                        where jr_comp = '$comp' and jr_year = '$year'
                        and tt_code in ('62')
                        
and month(jr_tgl)=$bulan and Year(jr_tgl)=$year
                        group by tr_code order by tr_code
                            "));

        $total_pajak = 0;
        foreach ($pajak as $total) {
            $total_pajak+=$total->jum;
        }


        return view('laporan.labarugi', compact('pendapatan', 'hpp', 'expenses', 'depresiasi', 'amortisasi', 'pendapatanlain', 'pengeluaranlain', 'bunggainvesi', 'pajak', 'date1', 'date2', 'total_pendapata', 'total_hpp'
                        , 'total_expenses'
                        , 'total_depresiasi', 'total_pendapatanlain', 'total_pengeluaranlain'
                        , 'total_bunggainvesi', 'total_pajak'));
    }
     public function labarugiFinalPeriode($date) {
         //per bulan     
        $comp = Session::get('mem_comp');
        $year = Session::get('mem_year');               
        $bulan = $date;                
//pendapatan ada isi
        $pendapatan = DB::select(DB::raw("select tr_code,tr_name,sum(tt_income*jr_value) as jum from d_jurnal dj
                        left join m_trans_cat on left(jr_trans,2) = tt_code
                        left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                        where jr_comp = '$comp' and jr_year = '$year'
                        and tt_code in ('10','11','12')
 and month(jr_tgl)=$bulan and Year(jr_tgl)=$year



                        group by tr_code order by tr_code"));
        
        $total_pendapata = 0;
        foreach ($pendapatan as $total_pendapatan) {
            $total_pendapata+=$total_pendapatan->jum;
        }        
//hpp ada isi
        $hpp = DB::select(DB::raw("select tr_code,tr_name,sum(tt_income*jr_value) as jum from d_jurnal dj
                        left join m_trans_cat on left(jr_trans,2) = tt_code
                        left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                        where jr_comp = '$comp' and jr_year = '$year'
                        and tt_code in ('20','21')
and month(jr_tgl)=$bulan and Year(jr_tgl)=$year                        

                        group by tr_code order by tr_code"));
        $total_hpp = 0;
        foreach ($hpp as $total) {            
            $total_hpp+=$total->jum;
        }

//expenses ada isi
        $expenses = DB::select(DB::raw("select tr_code,tr_name,sum(tt_income*jr_value) as jum from d_jurnal dj
                        left join m_trans_cat on left(jr_trans,2) = tt_code
                        left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                        where jr_comp = '$comp' and jr_year = '$year'
                        and tt_code in ('30')

and month(jr_tgl)=$bulan and Year(jr_tgl)=$year

                        group by tr_code order by tr_code"));

        $total_expenses = 0;
        foreach ($expenses as $total) {
            $total_expenses+=$total->jum;
        }


//DEPRESIASI / DEPRECIACION
        $depresiasi = DB::select(DB::raw("select tr_code,tr_name,sum(tt_income*jr_value) as jum from d_jurnal dj
                        left join m_trans_cat on left(jr_trans,2) = tt_code
                        left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                        where jr_comp = '$comp' and jr_year = '$year'
                        and tt_code in ('41')
                        
and month(jr_tgl)=$bulan and Year(jr_tgl)=$year
                        group by tr_code order by tr_code
                            "));
        $total_depresiasi = 0;
        foreach ($depresiasi as $total) {
            $total_depresiasi+=$total->jum;
        }
        
//amortisasi 
        $amortisasi = DB::select(DB::raw("select tr_code,tr_name,sum(tt_income*jr_value) as jum from d_jurnal dj
                        left join m_trans_cat on left(jr_trans,2) = tt_code
                        left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                        where jr_comp = '$comp' and jr_year = '$year'
                        and tt_code in ('42')
                        
and month(jr_tgl)=$bulan and Year(jr_tgl)=$year
                        group by tr_code order by tr_code
                            "));
        $total_amortisasi = 0;
        foreach ($amortisasi as $total) {
            $total_amortisasi+=$total->jum;
        }
//PENDAPATAN LAIN-LAIN
        $pendapatanlain = DB::select(DB::raw("select tr_code,tr_name,sum(tt_income*jr_value) as jum from d_jurnal dj
                        left join m_trans_cat on left(jr_trans,2) = tt_code
                        left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                        where jr_comp = '$comp' and jr_year = '$year'
                        and tt_code in ('51')
                        
and month(jr_tgl)=$bulan and Year(jr_tgl)=$year
                        group by tr_code order by tr_code
                            "));
        $total_pendapatanlain = 0;
        foreach ($pendapatanlain as $total) {
            $total_pendapatanlain+=$total->jum;
        }
//PENGELUARAN LAIN-LAIN
        $pengeluaranlain = DB::select(DB::raw("select tr_code,tr_name,sum(tt_income*jr_value) as jum from d_jurnal dj
                        left join m_trans_cat on left(jr_trans,2) = tt_code
                        left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                        where jr_comp = '$comp' and jr_year = '$year'
                        and tt_code in ('52')
                        
and month(jr_tgl)=$bulan and Year(jr_tgl)=$year
                        group by tr_code order by tr_code
                            "));
        $total_pengeluaranlain = 0;
        foreach ($pengeluaranlain as $total) {
            $total_pengeluaranlain+=$total->jum;
        }
//BUNGA / INTEREST
        $bunggainvesi = DB::select(DB::raw("select tr_code,tr_name,sum(tt_income*jr_value) as jum from d_jurnal dj
                        left join m_trans_cat on left(jr_trans,2) = tt_code
                        left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                        where jr_comp = '$comp' and jr_year = '$year'
                        and tt_code in ('61')
                        
and month(jr_tgl)=$bulan and Year(jr_tgl)=$year
                        group by tr_code order by tr_code
                            "));
        $total_bunggainvesi = 0;
        foreach ($bunggainvesi as $total) {
            $total_bunggainvesi+=$total->jum;
        }
//PAJAK / TAX  
        $pajak = DB::select(DB::raw("select tr_code,tr_name,sum(tt_income*jr_value) as jum from d_jurnal dj
                        left join m_trans_cat on left(jr_trans,2) = tt_code
                        left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                        where jr_comp = '$comp' and jr_year = '$year'
                        and tt_code in ('62')
                        
and month(jr_tgl)=$bulan and Year(jr_tgl)=$year
                        group by tr_code order by tr_code
                            "));

        $total_pajak = 0;
        foreach ($pajak as $total) {
            $total_pajak+=$total->jum;
        }


        return view('laporan.labarugi', compact('pendapatan', 'hpp', 'expenses', 'depresiasi', 'amortisasi', 'pendapatanlain', 'pengeluaranlain', 'bunggainvesi', 'pajak', 'date1', 'date2', 'total_pendapata', 'total_hpp'
                        , 'total_expenses'
                        , 'total_depresiasi', 'total_pendapatanlain', 'total_pengeluaranlain'
                        , 'total_bunggainvesi', 'total_pajak'));
    }
}
