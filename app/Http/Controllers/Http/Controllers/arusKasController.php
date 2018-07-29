<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\d_comp_jurnal_resume;

use Session;

use DB;




class arusKasController extends Controller
{
    public function aruskasIndex(){
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
        return view('laporan.arus_kas_index',compact('bulan'));
        
    }
    public function aruskas(){
        $year = Session::get('mem_year');
        $comp = Session::get('mem_comp');
        $ocf = DB::select(DB::raw("select tr_code,tr_name,sum(jr_value*tr_cashflow) as jum     from d_jurnal dj
                left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                where jr_comp = '$comp' and jr_year = '$year'
                and tr_cashtype ='O'  and jr_transsub=tr_codesub
                group by tr_code order by tr_code"));
        //dd($ocf);
        $total_ocf = 0;
        foreach ($ocf as $data_ocf) {
            $total_ocf = $total_ocf + $data_ocf->jum;
        }

        $icf = DB::select(DB::raw("select tr_code,tr_name,sum(jr_value*tr_cashflow) as jum
                from d_jurnal dj
                left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                where jr_comp = '$comp' and jr_year = '$year'
                and tr_cashtype ='I'  and jr_transsub=tr_codesub
                group by tr_code order by tr_code"));
        $total_icf = 0;
        foreach ($icf as $data_icf) {
            $total_icf = $total_icf + $data_icf->jum;
        }

        $fcf = DB::select(DB::raw("select tr_code,tr_name,sum(jr_value*tr_cashflow) as jum
                from d_jurnal dj
                left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                where jr_comp = '$comp' and jr_year = '$year'
                and tr_cashtype ='F'  and jr_transsub=tr_codesub
                group by tr_code order by tr_code"));
        $total_fcf = 0;
        foreach ($fcf as $data_fcf) {
            $total_fcf = $total_fcf + $data_fcf->jum;
        }
        
        
       
        
         $kasAwal=DB::select("select sum(coa_opening) kas      from d_comp_coa
                        where coa_comp = '$comp' and coa_year = '$year'
                        and coa_code in (101000000,101010000,101010001,101010002,101010003)
                        and (coa_isparent = 1 or coa_isactive = 1)
                        order by coa_code");
       $nilaiKasAwal=0;
        foreach ($kasAwal as $index => $data) {
            $nilaiKasAwal+=$data->kas;            
        }
       
        return view('laporan.arus_kas', compact('ocf', 'icf', 'fcf', 'total_fcf', 'total_icf', 'total_ocf', 
                    'total_fcf','nilaiKasAwal'));
    }
    
    public function aruskasPercobaanPer($date) {
        
       //$tgl = date('Y-m-d', strtotime($req->tgl));
        $comp = Session::get('mem_comp');
        $year = Session::get('mem_year');
        
        $coba=str_replace(' ', '-', $date).'-'.$year;        
        $date = date('Y-m-d', strtotime($coba));  
        
        $comp = Session::get('mem_comp');
        $year = Session::get('mem_year');
        $ocf = DB::select(DB::raw("select tr_code,tr_name,sum(jr_value*tr_cashflow) as jum     from d_jurnal dj
                left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                where jr_comp = '$comp' and jr_year = '$year'
        
                    
and jr_tgl BETWEEN (select min(jr_tgl) from d_jurnal where jr_year = '$year' and jr_comp = '$comp')
								and '$date'                       



                and tr_cashtype ='O'  and jr_transsub=tr_codesub
                group by tr_code order by tr_code"));
        //dd($ocf);
        $total_ocf = 0;
        foreach ($ocf as $data_ocf) {
            $total_ocf = $total_ocf + $data_ocf->jum;
        }

        $icf = DB::select(DB::raw("select tr_code,tr_name,sum(jr_value*tr_cashflow) as jum
                from d_jurnal dj
                left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                where jr_comp = '$comp' and jr_year = '$year'


and jr_tgl BETWEEN (select min(jr_tgl) from d_jurnal where jr_year = '$year' and jr_comp = '$comp')
								and '$date'                       
                    


                and tr_cashtype ='I'  and jr_transsub=tr_codesub
                group by tr_code order by tr_code"));
        $total_icf = 0;
        foreach ($icf as $data_icf) {
            $total_icf = $total_icf + $data_icf->jum;
        }

        $fcf = DB::select(DB::raw("select tr_code,tr_name,sum(jr_value*tr_cashflow) as jum
                from d_jurnal dj
                left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                where jr_comp = '$comp' and jr_year = '$year'
                    
and jr_tgl BETWEEN (select min(jr_tgl) from d_jurnal where jr_year = '$year' and jr_comp = '$comp')
								and '$date'                       


                    

                and tr_cashtype ='F'  and jr_transsub=tr_codesub
                group by tr_code order by tr_code"));
        $total_fcf = 0;
        foreach ($fcf as $data_fcf) {
            $total_fcf = $total_fcf + $data_fcf->jum;
        }
        
        
        $bulan = date('m', strtotime($coba));  
        
        
        if($bulan=='1'){
          
        }
        else if($bulan!='1'){
       $bulan-=1;           
        }
        
        $kasAwal=DB::select("select sum(coa_opening) kas      from d_comp_coa
                        where coa_comp = '$comp' and coa_year = '$year'
                        and coa_code in (101000000,101010000,101010001,101010002,101010003)
                        and (coa_isparent = 1 or coa_isactive = 1)
                        order by coa_code");
       dd($kasAwal);
        
        
        
        

        return view('laporan.arus_kas', compact('ocf', 'icf', 'fcf', 'total_fcf', 'nilaiKasAwal', 'total_icf', 'total_ocf', 'total_fcf'));
        
    }
    public function aruskasFinalPer($date) {
        
       //$tgl = date('Y-m-d', strtotime($req->tgl));
        $comp = Session::get('mem_comp');
        $year = Session::get('mem_year');
        $coba=str_replace(' ', '-', $date).'-'.$year;        
        $date = date('Y-m-t', strtotime($coba)); 
        
        $comp = Session::get('mem_comp');
        $year = Session::get('mem_year');
        $ocf = DB::select(DB::raw("select tr_code,tr_name,sum(jr_value*tr_cashflow) as jum     from d_jurnal dj
                left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                where jr_comp = '$comp' and jr_year = '$year'
        
                    
and jr_tgl BETWEEN (select min(jr_tgl) from d_jurnal where jr_year = '$year' and jr_comp = '$comp')
								and '$date'                       



                and tr_cashtype ='O'  and jr_transsub=tr_codesub
                group by tr_code order by tr_code"));
        //dd($ocf);
        $total_ocf = 0;
        foreach ($ocf as $data_ocf) {
            $total_ocf = $total_ocf + $data_ocf->jum;
        }

        $icf = DB::select(DB::raw("select tr_code,tr_name,sum(jr_value*tr_cashflow) as jum
                from d_jurnal dj
                left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                where jr_comp = '$comp' and jr_year = '$year'


and jr_tgl BETWEEN (select min(jr_tgl) from d_jurnal where jr_year = '$year' and jr_comp = '$comp')
								and '$date'                       
                    


                and tr_cashtype ='I'  and jr_transsub=tr_codesub
                group by tr_code order by tr_code"));
        $total_icf = 0;
        foreach ($icf as $data_icf) {
            $total_icf = $total_icf + $data_icf->jum;
        }

        $fcf = DB::select(DB::raw("select tr_code,tr_name,sum(jr_value*tr_cashflow) as jum
                from d_jurnal dj
                left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                where jr_comp = '$comp' and jr_year = '$year'
                    
and jr_tgl BETWEEN (select min(jr_tgl) from d_jurnal where jr_year = '$year' and jr_comp = '$comp')
								and '$date'                       


                    

                and tr_cashtype ='F'  and jr_transsub=tr_codesub
                group by tr_code order by tr_code"));
        $total_fcf = 0;
        foreach ($fcf as $data_fcf) {
            $total_fcf = $total_fcf + $data_fcf->jum;
        }
        
        
        $bulan = date('m', strtotime($coba));  
        
        
        if($bulan=='1'){
          
        }
        else if($bulan!='1'){
       $bulan-=1;           
        }
        
        $kasAwal=DB::select("select coa_opening + (select COALESCE(sum(jrdt_value),0) from d_jurnal_dt
                        where jrdt_id in (select jr_id from d_jurnal where jr_comp = coa_comp and jr_year = coa_year
			and MONTH(jr_tgl) BETWEEN MONTH(coa_opening_tgl) and '$bulan' )
                        and jrdt_acc = coa_code) as COAend
                        from d_comp_coa
                        where coa_comp = '$comp' and coa_year = '$year'
                        and coa_code in (101000000,101010000,101010001,101010002,101010003)
                        and (coa_isparent = 1 or coa_isactive = 1)
                        order by coa_code");
        $nilaiKasAwal=0;
        foreach ($kasAwal as $index => $data) {
            $nilaiKasAwal+=$data->COAend;            
        }
        

        return view('laporan.arus_kas', compact('ocf', 'icf', 'fcf', 'total_fcf', 'nilaiKasAwal', 'total_icf', 'total_ocf', 'total_fcf'));
        
    }
    public function aruskasPercobaanPeriode($date) {
        $comp = Session::get('mem_comp');
        $year = Session::get('mem_year');
        $date=str_replace(' ', '-', $date).'-'.$year;        
        $bulan = date('m', strtotime($date));
        
        
        $comp = Session::get('mem_comp');
        $year = Session::get('mem_year');
        $ocf = DB::select(DB::raw("select tr_code,tr_name,sum(jr_value*tr_cashflow) as jum     from d_jurnal dj
                left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                where jr_comp = '$comp' and jr_year = '$year'
        
                    and month(jr_tgl)=$bulan and Year(jr_tgl)=$year



                and tr_cashtype ='O'  and jr_transsub=tr_codesub
                group by tr_code order by tr_code"));
        //dd($ocf);
        $total_ocf = 0;
        foreach ($ocf as $data_ocf) {
            $total_ocf = $total_ocf + $data_ocf->jum;
        }

        $icf = DB::select(DB::raw("select tr_code,tr_name,sum(jr_value*tr_cashflow) as jum
                from d_jurnal dj
                left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                where jr_comp = '$comp' and jr_year = '$year'

                    and month(jr_tgl)=$bulan and Year(jr_tgl)=$year
                    


                and tr_cashtype ='I'  and jr_transsub=tr_codesub
                group by tr_code order by tr_code"));
        $total_icf = 0;
        foreach ($icf as $data_icf) {
            $total_icf = $total_icf + $data_icf->jum;
        }

        $fcf = DB::select(DB::raw("select tr_code,tr_name,sum(jr_value*tr_cashflow) as jum
                from d_jurnal dj
                left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                where jr_comp = '$comp' and jr_year = '$year'
                    
                    and month(jr_tgl)=$bulan and Year(jr_tgl)=$year

                    

                and tr_cashtype ='F'  and jr_transsub=tr_codesub
                group by tr_code order by tr_code"));
        $total_fcf = 0;
        foreach ($fcf as $data_fcf) {
            $total_fcf = $total_fcf + $data_fcf->jum;
        }
        
        
        
        
        if($bulan=='1'){
          
        }
        else if($bulan!='1'){
       $bulan-=1;           
        }
        
        $kasAwal=DB::select("select coa_opening + (select COALESCE(sum(jrdt_value),0) from d_jurnal_dt
                        where jrdt_id in (select jr_id from d_jurnal where jr_comp = coa_comp and jr_year = coa_year
			and MONTH(jr_tgl) BETWEEN MONTH(coa_opening_tgl) and '$bulan' )
                        and jrdt_acc = coa_code) as COAend
                        from d_comp_coa
                        where coa_comp = '$comp' and coa_year = '$year'
                        and coa_code in (101000000,101010000,101010001,101010002,101010003)
                        and (coa_isparent = 1 or coa_isactive = 1)
                        order by coa_code");
        $nilaiKasAwal=0;
        foreach ($kasAwal as $index => $data) {
            $nilaiKasAwal+=$data->COAend;            
        }
        
        

        return view('laporan.arus_kas', compact('ocf', 'icf', 'fcf', 'total_fcf', 'nilaiKasAwal', 'total_icf', 'total_ocf', 'total_fcf'));
        
    }
    public function aruskasFinalPeriode($date) {
        $comp = Session::get('mem_comp');
        $year = Session::get('mem_year');               
        $bulan = $date;  
        
        
        $comp = Session::get('mem_comp');
        $year = Session::get('mem_year');
        $ocf = DB::select(DB::raw("select tr_code,tr_name,sum(jr_value*tr_cashflow) as jum     from d_jurnal dj
                left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                where jr_comp = '$comp' and jr_year = '$year'
        
                    and month(jr_tgl)=$bulan and Year(jr_tgl)=$year



                and tr_cashtype ='O'  and jr_transsub=tr_codesub
                group by tr_code order by tr_code"));
        //dd($ocf);
        $total_ocf = 0;
        foreach ($ocf as $data_ocf) {
            $total_ocf = $total_ocf + $data_ocf->jum;
        }

        $icf = DB::select(DB::raw("select tr_code,tr_name,sum(jr_value*tr_cashflow) as jum
                from d_jurnal dj
                left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                where jr_comp = '$comp' and jr_year = '$year'

                    and month(jr_tgl)=$bulan and Year(jr_tgl)=$year
                    


                and tr_cashtype ='I'  and jr_transsub=tr_codesub
                group by tr_code order by tr_code"));
        $total_icf = 0;
        foreach ($icf as $data_icf) {
            $total_icf = $total_icf + $data_icf->jum;
        }

        $fcf = DB::select(DB::raw("select tr_code,tr_name,sum(jr_value*tr_cashflow) as jum
                from d_jurnal dj
                left join d_comp_trans on jr_comp = tr_comp and jr_year = tr_year and jr_trans = tr_code
                where jr_comp = '$comp' and jr_year = '$year'
                    
                    and month(jr_tgl)=$bulan and Year(jr_tgl)=$year

                    

                and tr_cashtype ='F'  and jr_transsub=tr_codesub
                group by tr_code order by tr_code"));
        $total_fcf = 0;
        foreach ($fcf as $data_fcf) {
            $total_fcf = $total_fcf + $data_fcf->jum;
        }
        
        
              
        if($bulan=='1'){
          
        }
        else if($bulan!='1'){
       $bulan-=1;           
        }
        
        $kasAwal=DB::select("select coa_opening + (select COALESCE(sum(jrdt_value),0) from d_jurnal_dt
                        where jrdt_id in (select jr_id from d_jurnal where jr_comp = coa_comp and jr_year = coa_year
			and MONTH(jr_tgl) BETWEEN MONTH(coa_opening_tgl) and '$bulan' )
                        and jrdt_acc = coa_code) as COAend
                        from d_comp_coa
                        where coa_comp = '$comp' and coa_year = '$year'
                        and coa_code in (101000000,101010000,101010001,101010002,101010003)
                        and (coa_isparent = 1 or coa_isactive = 1)
                        order by coa_code");
        $nilaiKasAwal=0;
        foreach ($kasAwal as $index => $data) {
            $nilaiKasAwal+=$data->COAend;            
        }

        return view('laporan.arus_kas', compact('ocf', 'icf', 'fcf', 'total_fcf', 'nilaiKasAwal', 'total_icf', 'total_ocf', 'total_fcf'));
        
    }
}
