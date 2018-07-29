<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Session;

use App\d_comp_jurnal_resume;

use DB;

class neracaController extends Controller
{
    public function kunciNeracaIndex() {
        $comp = Session::get('mem_comp');
        $year = Session::get('mem_year');
        $chek = d_comp_jurnal_resume::select(DB::raw('substr(cjr_period,5,6) cjr_period'), 'cjr_value')->where('cjr_comp', $comp)
                        ->where(DB::raw('substr(cjr_period,1,4)'), $year)->groupBy('cjr_period')->get(); //where('cjr_comp',$year)        
        $bulan = [];
        foreach ($chek as $index => $data) {
            $bulan[$index] = $data->cjr_period;
        }        
        return view('laporan.kunci_neraca', compact('bulan'));
    }
    public function kunciBulan() {
        $comp = Session::get('mem_comp');
        $year = Session::get('mem_year');
        $chek = d_comp_jurnal_resume::select(DB::raw('substr(cjr_period,5,6) cjr_period'), 'cjr_value')->where('cjr_comp', $comp)
                        ->where(DB::raw('substr(cjr_period,1,4)'), $year)->groupBy('cjr_period')->get(); //where('cjr_comp',$year)        
        $bulan = [];
        foreach ($chek as $index => $data) {
            $bulan[$index] = $data->cjr_period;
        }
        return $bulan;
    }
    public function kunciNeracaCreate($bulan) {
        $comp = Session::get('mem_comp');
        $year = Session::get('mem_year');
        $chek = d_comp_jurnal_resume::where('cjr_comp', $comp)->where('cjr_period', $year . $bulan)->get();
        $asset = DB::select(DB::raw("select *,(select coa_opening from d_comp_coa coa where
month (coa.coa_opening_tgl) BETWEEN 1 and '$bulan'
and coa.coa_name=c.coa_name and coa.coa_comp=c.coa_comp and coa.coa_year=c.coa_year)+(select COALESCE(sum(jrdt_value),0)  from d_jurnal_dt
where jrdt_id in (select jr_id from d_jurnal where jr_comp = coa_comp and jr_year = coa_year
and YEAR(jr_tgl)='$year' and month(jr_tgl) BETWEEN month(coa_opening_tgl) and '$bulan')
and jrdt_acc = coa_code) as COAend
                        from d_comp_coa c
                        where coa_comp = '$comp' and coa_year = '$year'
                        and coa_code like '1%'
                        and (coa_isparent = 1 or coa_isactive = 1)
                        order by coa_code"));

        foreach ($asset as $index => $data) {
            d_comp_jurnal_resume::create([
                'cjr_comp' => $comp,
                'cjr_period' => $year . $bulan,
                'cjr_coa_code' => $data->coa_code,
                'cjr_value' => $data->COAend
            ]);
        }


        $kewajiban_modal = DB::select(DB::raw("select *,SUBSTRING(coa_code,1,1) as coa_kategori,(select coa_opening from d_comp_coa coa where
month (coa.coa_opening_tgl) BETWEEN 1 and '$bulan'
and coa.coa_name=c.coa_name and coa.coa_comp=c.coa_comp and coa.coa_year=c.coa_year)+(select COALESCE(sum(jrdt_value),0)  from d_jurnal_dt
where jrdt_id in (select jr_id from d_jurnal where jr_comp = coa_comp and jr_year = coa_year
and YEAR(jr_tgl)='$year' and month(jr_tgl) BETWEEN month(coa_opening_tgl) and '$bulan')
and jrdt_acc = coa_code) as COAend
                       from d_comp_coa c
                        where coa_comp = '$comp' and coa_year = '$year'
                        and (coa_code like '2%' or coa_code like '3%')
                        and (coa_isparent = 1 or coa_isactive = 1)
                        order by coa_code"));

        foreach ($kewajiban_modal as $index => $data) {
            d_comp_jurnal_resume::create([
                'cjr_comp' => $comp,
                'cjr_period' => $year . $bulan,
                'cjr_coa_code' => $data->coa_code,
                'cjr_value' => $data->COAend
            ]);
        }

        $chek = d_comp_jurnal_resume::select(DB::raw('substr(cjr_period,5,6) cjr_period'), 'cjr_value')->where('cjr_comp', $comp)
                        ->where(DB::raw('substr(cjr_period,1,4)'), $year)->groupBy('cjr_period')->get(); //where('cjr_comp',$year)        
        $bulan = [];
        foreach ($chek as $index => $data) {
            $bulan[$index] = $data->cjr_period;
        }
        return $bulan;
    }

    public function kunciNeracaHapus($bulan) {
        $comp = Session::get('mem_comp');
        $year = Session::get('mem_year');
        $hapus = d_comp_jurnal_resume::where('cjr_comp', $comp)->where('cjr_period', $year . $bulan);
        $hapus->delete();





        $chek = d_comp_jurnal_resume::select(DB::raw('substr(cjr_period,5,6) cjr_period'), 'cjr_value')->where('cjr_comp', $comp)
                        ->where(DB::raw('substr(cjr_period,1,4)'), $year)->groupBy('cjr_period')->get(); //where('cjr_comp',$year)        
        $bulan = [];
        foreach ($chek as $index => $data) {
            $bulan[$index] = $data->cjr_period;
        }
        return $bulan;
    }
    
    
    
    public function neracaIndex() {
        $comp = Session::get('mem_comp');
        $year = Session::get('mem_year');
//        $chek = d_comp_jurnal_resume::select(DB::raw('substr(cjr_period,5,6) cjr_period'), 'cjr_value')->where('cjr_comp', $comp)
//                        ->where(DB::raw('substr(cjr_period,1,4)'), $year)->groupBy('cjr_period')->get(); //where('cjr_comp',$year)        
        $bulan = [];
        $asli = [];
        
//        foreach ($chek as $index => $data) {
//            $months = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');
//            $bulan[$index] = $months[(int) $data->cjr_period];
//            $asli[$index] = $data->cjr_period;
//        }
//        $startDate=end($asli);
//        $startDate=$startDate+1;
//        
//        $a_date = $year.'-'.$startDate."-23";        
//        $stDate = date("01-M-Y", strtotime($a_date));        
        
        //return $bulan;
        return view('laporan.neraca_index',compact('bulan','stDate'));
    }
    
     public function neraca() {
        $comp = Session::get('mem_comp');
        $year = Session::get('mem_year');
        $tgl_now = date('Y-m-d');
        $asset = DB::select(DB::raw("select *,coa_opening + (select COALESCE(sum(jrdt_value),0) from d_jurnal_dt
                        where jrdt_id in (select jr_id from d_jurnal where jr_comp = coa_comp and jr_year = coa_year
			and jr_tgl BETWEEN coa_opening_tgl and '$tgl_now' )
                        and jrdt_acc = coa_code) as COAend
                        from d_comp_coa
                        where coa_comp = '$comp' and coa_year = '$year'
                        and coa_code like '1%'
                        and (coa_isparent = 1 or coa_isactive = 1)
                        order by coa_code"));
        $total_asset = 0;
        $cekAsetMinus = 0;
        foreach ($asset as $index => $asset_total) {
            if ($asset_total->COAend < 0) {
                $cekAsetMinus++;
            }
            $total_asset+=$asset_total->COAend;
        }

        $kewajiban_modal = DB::select(DB::raw("select *,SUBSTRING(coa_code,1,1) as coa_kategori,coa_opening + (select COALESCE(sum(jrdt_value),0) from d_jurnal_dt
                        where jrdt_id in (select jr_id from d_jurnal where jr_comp = coa_comp and jr_year = coa_year
                        and jr_tgl BETWEEN coa_opening_tgl and  '$tgl_now')
                        and jrdt_acc = coa_code) as COAend
                        from d_comp_coa
                        where coa_comp = '$comp' and coa_year = '$year'
                        and (coa_code like '2%' or coa_code like '3%')
                        and (coa_isparent = 1 or coa_isactive = 1)
                        order by coa_code"));
        $total_kewajiban_modal = 0;
        $cekkewajibanMinus = 0;
        foreach ($kewajiban_modal as $km) {
            if ($km->COAend < 0 && $km->coa_kategori == '2') {
                $cekkewajibanMinus++;
            }
            $total_kewajiban_modal+=$km->COAend;
        }


        return view('laporan.neraca', compact('asset', 'total_asset', 'kewajiban_modal', 'cekAsetMinus', 'cekkewajibanMinus', 'total_kewajiban_modal', 'tgl_now'));
    }

    public function cari_neraca($date) {
        $comp = Session::get('mem_comp');
        $year = Session::get('mem_year');
        $tgl_now = date('Y-m-d', strtotime($date));
        
        if (!empty($tgl_now)) {
            $asset = DB::select(DB::raw("select *,(select coa_opening from d_comp_coa coa where
month (coa.coa_opening_tgl) BETWEEN 1 and month('$tgl_now')
and coa.coa_name=c.coa_name and coa.coa_comp=c.coa_comp and coa.coa_year=c.coa_year)+(select COALESCE(sum(jrdt_value),0)  from d_jurnal_dt
where jrdt_id in (select jr_id from d_jurnal where jr_comp = coa_comp and jr_year = coa_year
and YEAR(jr_tgl)='$year' and jr_tgl BETWEEN date(coa_opening_tgl) and '$tgl_now')
    
and jrdt_acc = coa_code) as COAend
                        from d_comp_coa c
                        where coa_comp = '$comp' and coa_year = '$year'
                        and coa_code like '1%'
                        and (coa_isparent = 1 or coa_isactive = 1)
                        order by coa_code"));
            $total_asset = 0;
            $cekAsetMinus = 0;
            foreach ($asset as $index => $asset_total) {
                if ($asset_total->COAend < 0) {
                    $cekAsetMinus++;
                }
                $total_asset+=$asset_total->COAend;
            }            
            $kewajiban_modal = DB::select(DB::raw("select *,SUBSTRING(coa_code,1,1) as coa_kategori,(select coa_opening from d_comp_coa coa where
month (coa.coa_opening_tgl) BETWEEN 1 and month('$tgl_now')
and coa.coa_name=c.coa_name and coa.coa_comp=c.coa_comp and coa.coa_year=c.coa_year)+(select COALESCE(sum(jrdt_value),0)  from d_jurnal_dt
where jrdt_id in (select jr_id from d_jurnal where jr_comp = coa_comp and jr_year = coa_year
and YEAR(jr_tgl)='$year' and jr_tgl BETWEEN date(coa_opening_tgl) and '$tgl_now')
and jrdt_acc = coa_code) as COAend
                       from d_comp_coa c
                        where coa_comp = '$comp' and coa_year = '$year'
                        and (coa_code like '2%' or coa_code like '3%')
                        and (coa_isparent = 1 or coa_isactive = 1)
                        order by coa_code"));
            $total_kewajiban_modal = 0;
            $cekkewajibanMinus = 0;
            foreach ($kewajiban_modal as $km) {
                if ($km->COAend < 0 && $km->coa_kategori == '2') {
                    $cekkewajibanMinus++;
                }
                $total_kewajiban_modal+=$km->COAend;
            }
            return view('laporan.neraca', compact('asset', 'total_asset', 'kewajiban_modal', 'total_kewajiban_modal', 'cekAsetMinus', 'cekkewajibanMinus'));
        }
    }
    
     public function cari_neraca_final($bulan) {
        $comp = Session::get('mem_comp');
        $year = Session::get('mem_year');        
        if(strlen($bulan)==1){
          $bulan='0'.$bulan;  
        }
        //->join('m_satuan', 'm_satuan.s_id', '=', 'd_item.i_satuan')
//        select * from d_comp_jurnalresume join d_comp_coa on d_comp_jurnalresume.cjr_comp=d_comp_coa.coa_comp
//and d_comp_jurnalresume.cjr_coa_code=d_comp_coa.coa_code where d_comp_jurnalresume.cjr_period='201704'
            $final=DB::table('d_comp_jurnalresume')
                    ->join('d_comp_coa', function($join){
                         $join->on('d_comp_jurnalresume.cjr_comp','=','d_comp_coa.coa_comp');
                         $join->on('d_comp_jurnalresume.cjr_coa_code','=','d_comp_coa.coa_code');
                    })
            ->where('cjr_period',$year.$bulan)->orderBy('cjr_coa_code')->where('coa_comp',$comp)->get();
$total_asset=0;
$total_kewajiban_modal=0;

foreach ($final as $index => $data) {
    if(substr($data->coa_code,0,1)==1){
        $total_asset+=$data->cjr_value;
    }
    if(substr($data->coa_code,0,1)==2 || substr($data->coa_code,0,1)==3){
        $total_kewajiban_modal+=$data->cjr_value;
    
    }      
}

            //dd($final);
            return view('laporan.neraca_final', compact('final','total_asset','total_kewajiban_modal'));
        
    }
}
