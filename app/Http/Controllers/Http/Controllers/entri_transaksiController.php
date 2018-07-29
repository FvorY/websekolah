<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\d_jurnal;
use App\d_jurnal_dt;
use DB;
use Carbon\carbon;
use Validator;
use Session;
use App\d_mem;
use Auth;
use Yajra\Datatables\Datatables;

class entri_transaksiController extends Controller {

    public function index() {
        return view('entri_transaksi.index');
    }

    public function table() {
        return view('entri_transaksi.table');
    }

    public function index_data(Request $request) {        
        $comp = Session::get('mem_comp');
        $year = Session::get('mem_year');
        DB::statement(DB::raw('set @rownum=0'));

        $jurnal = d_jurnal::select(DB::raw('@rownum  := @rownum  + 1 AS rownum'), 'jr_id', 'jr_trans', 'jr_cashtype', 'jr_tgl', 'jr_detail', 'jr_value', 'jr_note')->
                        where('jr_comp', '=', $comp)->where('jr_year', '=', $year)->OrderBy('jr_tgl','DESC')->get();

        return Datatables::of($jurnal)
                        ->editColumn('jr_tgl', function ($jurnal) {
                            return $jurnal->jr_tgl ? with(new Carbon($jurnal->jr_tgl))->format('d F Y') : '';
                        })
                        ->editColumn('jr_cashtype', function ($jurnal) {
                            if ($jurnal->jr_cashtype == 'O')
                                return 'OCF';
                            if ($jurnal->jr_cashtype == 'I')
                                return 'ICF';
                            if ($jurnal->jr_cashtype == 'F')
                                return 'FCF';
                        })->addColumn('action', function ($jurnal) {
                            return' <div class="dropdown">                                
                                            <button class="btn btn-primary btn-flat btn-xs dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                Kelola
                                                <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                                <li><a href="data-transaksi/edit/' . $jurnal->jr_id . '" ><i class="fa fa-pencil" aria-hidden="true"></i>Edit Data</a></li>
                                                <li role="separator" class="divider"></li>                                                                        
                                                 <li><a href="data-transaksi/duplikasi/' . $jurnal->jr_id . '" ><i class="glyphicon glyphicon-duplicate" aria-hidden="true"></i>Duplikasi</a></li>
                                                <li role="separator" class="divider"></li>                                                                        
                                                <li><a class="btn-delete" data-remote="data-transaksi/destroy/' . $jurnal->jr_id . '"></i>Hapus Data</a></li>                                                
                                            </ul>
                                        </div>';
                        })->editColumn('jr_value', function ($jurnal) {
                            return number_format($jurnal->jr_value, 2, ',', '.');
                        })
                        ->make(true);


//        return view('entri_transaksi.index', compact('jurnal'));
//        return view('entri_transaksi.index', compact('jurnal'));
    }

    public function cari_tanggal_get(Request $request) {        
        $request->start_tgl = date('Y-m-d', strtotime($request->start_tgl));
        $request->end_tgl = date('Y-m-d', strtotime($request->end_tgl));
        $comp = Session::get('mem_comp');
        $year = Session::get('mem_year');
        DB::statement(DB::raw('set @rownum=0'));
        $pilihan=$request->pilih;
        if($pilihan==1){
          // dd($request->start_tgl);
            $jurnal = d_jurnal::select(DB::raw('@rownum  := @rownum  + 1 AS rownum'), 'jr_id', 'jr_trans', 'jr_cashtype', 'jr_tgl', 'jr_detail', 'jr_value', 'jr_note')->
                        where('jr_comp', '=', $comp)->where('jr_year', '=', $year)
                        ->whereBetween('jr_tgl', array($request->start_tgl,$request->end_tgl))
                        ->OrderBy('jr_tgl','DESC')->get();
            //dd($jurnal);            
            return view('entri_transaksi.cari',compact('jurnal','pilihan','start_nominal','end_nominal'));
        }                        
        else if($pilihan==0){    
            $start_nominal=str_replace(['Rp', '\\','.',' '], '', $request->start_nominal);
            $end_nominal=str_replace(['Rp', '\\','.',' '], '', $request->end_nominal);
            $jurnal = d_jurnal::select(DB::raw('@rownum  := @rownum  + 1 AS rownum'), 'jr_id', 'jr_trans', 'jr_cashtype', 'jr_tgl', 'jr_detail', 'jr_value', 'jr_note')->
                        where('jr_comp', '=', $comp)->where('jr_year', '=', $year)
                        ->whereBetween('jr_value', array($start_nominal,$end_nominal))
                        ->OrderBy('jr_tgl','DESC')->get();                    
            return view('entri_transaksi.cari',compact('jurnal','pilihan','start_nominal','end_nominal'));
        }
    }    
    public function create() {
        $comp = Session::get('mem_comp');
        $year = Session::get('mem_year');
        $trans = DB::select(DB::raw("select tr.*,coa1.coa_name as coa1name,coa2.coa_name as coa2name from d_comp_trans tr
                left join d_comp_coa coa1 on coa1.coa_comp = tr.tr_comp and coa1.coa_year = tr.tr_year and coa1.coa_code = tr.tr_acc01
                left join d_comp_coa coa2 on coa2.coa_comp = tr.tr_comp and coa2.coa_year = tr.tr_year and coa2.coa_code = tr.tr_acc02
                where tr_comp='$comp' and tr_year='$year'
                "));
        //dd($trans);

        $d_mem = d_mem::where('m_id', '!=', Auth::user()->m_id)->lists('m_name', 'm_id');

        return view('entri_transaksi.create', compact('trans', 'd_mem'));
    }

    public function store(Request $req) {
// binging

        $comp = Session::get('mem_comp');
        $year = Session::get('mem_year');


        $tgl = date('Y-m-d', strtotime($req->tanggal));


        $id = d_jurnal::OrderBy('jr_id', 'Desc')->first();

        if ($id == null) {
            $id = 1;
        } else {
            $id = $id->jr_id;
            $id = $id + 1;
        }


        $rules = array(
            'KodeTransaksi' => 'required', // make sure the email is an actual email
            'tanggal' => 'required',
            'Tipe' => 'required',
            'Transaksi' => 'required',
            'Nominal' => 'required',
        );




        $saldo1 = DB::select(DB::raw(" select *,coa_opening + (select COALESCE(sum(jrdt_value),0) from d_jurnal_dt
                        where jrdt_id in (select jr_id from d_jurnal where jr_comp = coa_comp and jr_year = coa_year)
                        and jrdt_acc = coa_code) as COAend
                        from d_comp_coa
                        where coa_comp = '$comp' and coa_year = '$year'
                        and coa_code ='$req->jrdt_acc1'
                        and (coa_isparent = 1 or coa_isactive = 1)
                        order by coa_code
                "));
        $saldo2 = DB::select(DB::raw(" select *,coa_opening + (select COALESCE(sum(jrdt_value),0) from d_jurnal_dt
                        where jrdt_id in (select jr_id from d_jurnal where jr_comp = coa_comp and jr_year = coa_year)
                        and jrdt_acc = coa_code) as COAend
                        from d_comp_coa
                        where coa_comp = '$comp' and coa_year = '$year'
                        and coa_code ='$req->jrdt_acc2'
                        and (coa_isparent = 1 or coa_isactive = 1)
                        order by coa_code
                "));

        $validator = Validator::make($req->all(), $rules);
        $req->Nominal = str_replace(['Rp', '\\', '.', ' '], '', $req->Nominal);
        if ($validator->fails()) {
            return Redirect('entri-transaksi/data-transaksi/create')
                            ->withInput()
                            ->withErrors($validator); // send back the input (not the password) so that we can repopulate the form
        } else if ($req->dk1 == '-' && $req->dk2 == '+') {

            if (($saldo1[0]->COAend - $req->Nominal) < 0) {
                //dd('oo1');
                $validator->getMessageBag()->add('login', 'Saldo Pada Akun 1 Tidak Terpenuhi, Jumlah Saldo : ' . $saldo1[0]->COAend); //manual validation                
                return redirect('entri-transaksi/data-transaksi/create')->withErrors($validator)->withInput();
            } else if (($saldo2[0]->COAend + $req->Nominal) < 0) {
                $validator->getMessageBag()->add('login', 'Saldo Pada Akun 2 Tidak Terpenuhi, Jumlah Saldo : ' . $saldo2[0]->COAend); //manual validation                '); //manual validation                
                return redirect('entri-transaksi/data-transaksi/create')->withErrors($validator)->withInput();
            }
        } else if ($req->dk1 == '+' && $req->dk2 == '-') {
            if (($saldo1[0]->COAend + $req->Nominal) < 0) {
                $validator->getMessageBag()->add('login', 'Saldo Pada Akun 1 Tidak Terpenuhi, Jumlah Saldo : ' . $saldo1[0]->COAend); //manual validation                
                return redirect('entri-transaksi/data-transaksi/create')->withErrors($validator)->withInput();
            } else if (($saldo2[0]->COAend - $req->Nominal) < 0) {
                //dd('oo5');
                $validator->getMessageBag()->add('login', 'Saldo Pada Akun 2 Tidak Terpenuhi, Jumlah Saldo : ' . $saldo2[0]->COAend); //manual validation                
                return redirect('entri-transaksi/data-transaksi/create')->withErrors($validator)->withInput();
            }
        } else if ($req->dk1 == '-' && $req->dk2 == '-') {
            if (($saldo1[0]->COAend - $req->Nominal) < 0) {
                $validator->getMessageBag()->add('login', 'Saldo Pada Akun 1 Tidak Terpenuhi, Jumlah Saldo : ' . $saldo1[0]->COAend); //manual validation                
                return redirect('entri-transaksi/data-transaksi/create')->withErrors($validator)->withInput();
            } else if (($saldo2[0]->COAend - $req->Nominal) < 0) {
                $validator->getMessageBag()->add('login', 'Saldo Pada Akun 2 Tidak Terpenuhi, Jumlah Saldo : ' . $saldo2[0]->COAend); //manual validation                
                return redirect('entri-transaksi/data-transaksi/create')->withErrors($validator)->withInput();
            }
        } else if ($req->dk1 == '+' && $req->dk2 == '+') {
            if (($saldo1[0]->COAend + $req->Nominal) < 0) {
                $validator->getMessageBag()->add('login', 'Saldo Pada Akun 1 Tidak Terpenuhi, Jumlah Saldo : ' . $saldo1[0]->COAend); //manual validation                
                return redirect('entri-transaksi/data-transaksi/create')->withErrors($validator)->withInput();
            } else if (($saldo2[0]->COAend + $req->Nominal) < 0) {
                $validator->getMessageBag()->add('login', 'Saldo Pada Akun 2 Tidak Terpenuhi, Jumlah Saldo : ' . $saldo2[0]->COAend); //manual validation                
                return redirect('entri-transaksi/data-transaksi/create')->withErrors($validator)->withInput();
            }
        }

        d_jurnal::create([
            'jr_id' => $id,
            'jr_comp' => $comp,
            'jr_year' => $year,
            'jr_trans' => $req->KodeTransaksi,
            'jr_transsub' => $req->jr_transsub,
            'jr_cashtype' => substr($req->Tipe, 0, 1),
            'jr_tgl' => $tgl,
            // 'jr_detail' => $req->division,
            'jr_value' => $req->Nominal,
            //'jr_ref' => $req->division,
            'jr_note' => $req->Note,
            'jr_memcode' => $req->Member,
            'jr_subcomp' => $req->kodeSub,
            'jr_project' => $req->Poject,
        ]);
        Session::flash('flash_message', 'Data Berhasil Disimpan.');

        if ($req->dk1 == '+') {
            $dk1 = $req->Nominal;
        } else if ($req->dk1 == '-') {
            $dk1 = -abs($req->Nominal);
        }
        if ($req->dk2 == '+') {
            $dk2 = $req->Nominal;
        } else if ($req->dk2 == '-') {
            $dk2 = -abs($req->Nominal);
        }

        d_jurnal_dt::create([
            'jrdt_id' => $id,
            'jrdt_dt' => '1',
            'jrdt_acc' => $req->jrdt_acc1,
            'jrdt_value' => $dk1,
        ]);
        d_jurnal_dt::create([
            'jrdt_id' => $id,
            'jrdt_dt' => '2',
            'jrdt_acc' => $req->jrdt_acc2,
            'jrdt_value' => $dk2,
        ]);

        return Redirect('entri-transaksi/data-transaksi');
    }

    public function edit($id) {
        // binging
        $comp = Session::get('mem_comp');
        $year = Session::get('mem_year');

        $d_mem = d_mem::where('m_id', '!=', Auth::user()->m_id)->lists('m_name', 'm_id');
        $jurnal = d_jurnal::where('jr_id', '=', $id)->first();
        $trans = DB::select(DB::raw("select tr.*,coa1.coa_name as coa1name,coa2.coa_name as coa2name from d_comp_trans tr
    left join d_comp_coa coa1 on coa1.coa_comp = tr.tr_comp and coa1.coa_year = tr.tr_year and coa1.coa_code = tr.tr_acc01
    left join d_comp_coa coa2 on coa2.coa_comp = tr.tr_comp and coa2.coa_year = tr.tr_year and coa2.coa_code = tr.tr_acc02
    where tr_code = $jurnal->jr_trans and tr_year='$year' and tr_comp='$comp' 
                    "))[0];
        //dd($trans);
//        $datatrans = DB::select(DB::raw("select tr.*,coa1.coa_name as coa1name,coa2.coa_name as coa2name from d_comp_trans tr
//                left join d_comp_coa coa1 on coa1.coa_comp = tr.tr_comp and coa1.coa_year = tr.tr_year and coa1.coa_code = tr.tr_acc01
//                left join d_comp_coa coa2 on coa2.coa_comp = tr.tr_comp and coa2.coa_year = tr.tr_year and coa2.coa_code = tr.tr_acc02
//                "));
        // $comp_trans = d_comp_trans::select('tr_code', 'tr_name', 'tr_cashtype')->where('tr_comp', '=', 'AA00000004')->where('tr_code', '=', $jurnal->jr_trans)->first();
        // $jurnal_dt = d_jurnal_dt::where('jrdt_id', '=', $id)->get();
        return view('entri_transaksi.edit', compact('jurnal', 'jurnal_dt', 'trans', 'datatrans', 'd_mem'));
    }

    public function update(Request $req) {
        $comp = Session::get('mem_comp');
        $year = Session::get('mem_year');
        $jurnal = d_jurnal::findOrFail($req->jr_id);
        
        $rules = array(
            'KodeTransaksi' => 'required', // make sure the email is an actual email
            'tanggal' => 'required',
            'Tipe' => 'required',
            'Transaksi' => 'required',
            'Nominal' => 'required',
        );
        $req->Nominal = str_replace(['Rp', '\\', '.', ' '], '', $req->Nominal);
        $validator = Validator::make($req->all(), $rules);

        $saldo1 = DB::select(DB::raw(" select *,coa_opening + (select COALESCE(sum(jrdt_value),0) from d_jurnal_dt
                        where jrdt_id in (select jr_id from d_jurnal where jr_comp = coa_comp and jr_year = coa_year)
                        and jrdt_acc = coa_code) as COAend
                        from d_comp_coa
                        where coa_comp = '$comp' and coa_year = '$year'
                        and coa_code ='$req->jrdt_acc1'
                        and (coa_isparent = 1 or coa_isactive = 1)
                        order by coa_code
                "));
        $saldo2 = DB::select(DB::raw(" select *,coa_opening + (select COALESCE(sum(jrdt_value),0) from d_jurnal_dt
                        where jrdt_id in (select jr_id from d_jurnal where jr_comp = coa_comp and jr_year = coa_year)
                        and jrdt_acc = coa_code) as COAend
                        from d_comp_coa
                        where coa_comp = '$comp' and coa_year = '$year'
                        and coa_code ='$req->jrdt_acc2'
                        and (coa_isparent = 1 or coa_isactive = 1)
                        order by coa_code
                "));
        //dd($saldo2);

        if ($validator->fails()) {
            return Redirect('entri-transaksi/data-transaksi/edit/' . $req->jr_id)
                            ->withInput()
                            ->withErrors($validator); // send back the input (not the password) so that we can repopulate the form
        } else if ($req->dk1 == '-' && $req->dk2 == '+') {
            if ((($saldo1[0]->COAend+$jurnal->jr_value) - $req->Nominal) < 0) {
                //dd('oo1');
                $validator->getMessageBag()->add('login', 'Saldo Pada Akun 1 Tidak Terpenuhi, Jumlah Saldo : ' . $saldo1[0]->COAend); //manual validation                
                return Redirect('entri-transaksi/data-transaksi/edit/' . $req->jr_id)->withErrors($validator)->withInput();
            } else if ((($saldo2[0]->COAend-$jurnal->jr_value) + $req->Nominal) < 0) {
                $validator->getMessageBag()->add('login', 'Saldo Pada Akun 2 Tidak Terpenuhi, Jumlah Saldo : ' . $saldo2[0]->COAend); //manual validation                '); //manual validation                
                return Redirect('entri-transaksi/data-transaksi/edit/' . $req->jr_id)->withErrors($validator)->withInput();
            }
        } else if ($req->dk1 == '+' && $req->dk2 == '-') {
            if ((($saldo1[0]->COAend-$jurnal->jr_value) + $req->Nominal) < 0) {
                $validator->getMessageBag()->add('login', 'Saldo Pada Akun 1 Tidak Terpenuhi, Jumlah Saldo : ' . $saldo1[0]->COAend); //manual validation                
                return Redirect('entri-transaksi/data-transaksi/edit/' . $req->jr_id)->withErrors($validator)->withInput();
            } else if ((($saldo2[0]->COAend+$jurnal->jr_value) - $req->Nominal) < 0) {
                //dd('oo5');
                $validator->getMessageBag()->add('login', 'Saldo Pada Akun 2 Tidak Terpenuhi, Jumlah Saldo : ' . $saldo2[0]->COAend); //manual validation                
                return Redirect('entri-transaksi/data-transaksi/edit/' . $req->jr_id)->withErrors($validator)->withInput();
            }
        } else if ($req->dk1 == '-' && $req->dk2 == '-') {
            if ((($saldo1[0]->COAend + $jurnal->jr_value) - $req->Nominal) < 0) {
                $validator->getMessageBag()->add('login', 'Saldo Pada Akun 1 Tidak Terpenuhi, Jumlah Saldo : ' . $saldo1[0]->COAend); //manual validation                
                return Redirect('entri-transaksi/data-transaksi/edit/' . $req->jr_id)->withErrors($validator)->withInput();
            } else if ((($saldo2[0]->COAend + $jurnal->jr_value)- $req->Nominal) < 0) {
                $validator->getMessageBag()->add('login', 'Saldo Pada Akun 2 Tidak Terpenuhi, Jumlah Saldo : ' . $saldo2[0]->COAend); //manual validation                
                return Redirect('entri-transaksi/data-transaksi/edit/' . $req->jr_id)->withErrors($validator)->withInput();
            }
        } else if ($req->dk1 == '+' && $req->dk2 == '+') {
            if ((($saldo1[0]->COAend - $jurnal->jr_value) + $req->Nominal) < 0) {
                $validator->getMessageBag()->add('login', 'Saldo Pada Akun 1 Tidak Terpenuhi, Jumlah Saldo : ' . $saldo1[0]->COAend); //manual validation                
                return Redirect('entri-transaksi/data-transaksi/edit/' . $req->jr_id)->withErrors($validator)->withInput();
            } else if ((($saldo2[0]->COAend - $jurnal->jr_value)+ $req->Nominal) < 0) {
                $validator->getMessageBag()->add('login', 'Saldo Pada Akun 2 Tidak Terpenuhi, Jumlah Saldo : ' . $saldo2[0]->COAend); //manual validation                
                return Redirect('entri-transaksi/data-transaksi/edit/' . $req->jr_id)->withErrors($validator)->withInput();
            }
        }






        $jurnal->update([

            'jr_comp' => $comp,
            'jr_year' => $year,
            'jr_trans' => $req->KodeTransaksi,
            //'jr_transsub' => '1',
            //'jr_cashtype' => 'O',
            //'jr_tgl' => $tgl,
            // 'jr_detail' => $req->division,
            'jr_value' => $req->Nominal,
            //'jr_ref' => $req->division,
            'jr_note' => $req->Note,
            'jr_memcode' => $req->Member,
            'jr_subcomp' => $req->kodeSub,
            'jr_project' => $req->Poject,
        ]);
        if ($req->dk1 == '+') {
            $dk1 = $req->Nominal;
        } else if ($req->dk1 == '-') {
            $dk1 = -abs($req->Nominal);
        }
        if ($req->dk2 == '+') {
            $dk2 = $req->Nominal;
        } else if ($req->dk2 == '-') {
            $dk2 = -abs($req->Nominal);
        }
        $jurnal_dt = DB::update("UPDATE d_jurnal_dt SET jrdt_value=$dk1
                                            where `jrdt_id` = $jurnal->jr_id and jrdt_dt=1");
        $jurnal_dt = DB::update("UPDATE d_jurnal_dt SET jrdt_value=$dk2
                                             where `jrdt_id` = $jurnal->jr_id and jrdt_dt=2");
        Session::flash('flash_message', 'Data Berhasil Diupdate.');
        return redirect('entri-transaksi/data-transaksi');
    }

    public function duplikasi_transaksi($id) {

        $comp = Session::get('mem_comp');
        $year = Session::get('mem_year');
        $jurnal = d_jurnal::findOrFail($id);
        $trans = DB::select(DB::raw("select tr.*,coa1.coa_name as coa1name,coa2.coa_name as coa2name from d_comp_trans tr
    left join d_comp_coa coa1 on coa1.coa_comp = tr.tr_comp and coa1.coa_year = tr.tr_year and coa1.coa_code = tr.tr_acc01
    left join d_comp_coa coa2 on coa2.coa_comp = tr.tr_comp and coa2.coa_year = tr.tr_year and coa2.coa_code = tr.tr_acc02
    where tr_code = $jurnal->jr_trans and tr_year='$year' and tr_comp='$comp' 
                    "))[0];
        $jurnal_dt = d_jurnal_dt::where('jrdt_id', '=', $id)->get();
        $akun1 = $jurnal_dt[0]->jrdt_acc;
        $akun2 = $jurnal_dt[1]->jrdt_acc;
        return view('entri_transaksi.duplicate', compact('jurnal', 'akun1', 'akun2', 'trans', 'datatrans'));
    }

    public function simpanduplikasi(Request $req, $idd) {
        $comp = Session::get('mem_comp');
        $year = Session::get('mem_year');

        // $input = $req->tanggal;
        //$tgl = Carbon::parse($input)->format('Y/m/d');
        $tgl = date('Y-m-d', strtotime($req->tanggal));
        $id = d_jurnal::OrderBy('jr_id', 'Desc')->first();

        if ($id == null) {
            $id = 1;
        } else {
            $id = $id->jr_id;
            $id = $id + 1;
        }


        $rules = array(
            'KodeTransaksi' => 'required', // make sure the email is an actual email
            'tanggal' => 'required',
            'Tipe' => 'required',
            'Transaksi' => 'required',
            'Nominal' => 'required',
        );




        $saldo1 = DB::select(DB::raw(" select *,coa_opening + (select COALESCE(sum(jrdt_value),0) from d_jurnal_dt
                        where jrdt_id in (select jr_id from d_jurnal where jr_comp = coa_comp and jr_year = coa_year)
                        and jrdt_acc = coa_code) as COAend
                        from d_comp_coa
                        where coa_comp = '$comp' and coa_year = '$year'
                        and coa_code ='$req->jrdt_acc1'
                        and (coa_isparent = 1 or coa_isactive = 1)
                        order by coa_code
                "));
        $saldo2 = DB::select(DB::raw(" select *,coa_opening + (select COALESCE(sum(jrdt_value),0) from d_jurnal_dt
                        where jrdt_id in (select jr_id from d_jurnal where jr_comp = coa_comp and jr_year = coa_year)
                        and jrdt_acc = coa_code) as COAend
                        from d_comp_coa
                        where coa_comp = '$comp' and coa_year = '$year'
                        and coa_code ='$req->jrdt_acc2'
                        and (coa_isparent = 1 or coa_isactive = 1)
                        order by coa_code
                "));
        $req->Nominal = str_replace(['Rp', '\\', '.', ' '], '', $req->Nominal);
        $validator = Validator::make($req->all(), $rules);
        if ($validator->fails()) {
            return Redirect('entri-transaksi/data-transaksi/duplikasi/' . $idd)
                            ->withInput()
                            ->withErrors($validator); // send back the input (not the password) so that we can repopulate the form
        } else if ($req->dk1 == '-' && $req->dk2 == '+') {

            if (($saldo1[0]->COAend - $req->Nominal) < 0) {
                //dd('oo1');
                $validator->getMessageBag()->add('login', 'Saldo Pada Akun 1 Tidak Terpenuhi, Jumlah Saldo : ' . $saldo1[0]->COAend); //manual validation                
                return redirect('entri-transaksi/data-transaksi/duplikasi/' . $idd)->withErrors($validator)->withInput();
            } else if (($saldo2[0]->COAend + $req->Nominal) < 0) {
                $validator->getMessageBag()->add('login', 'Saldo Pada Akun 2 Tidak Terpenuhi, Jumlah Saldo : ' . $saldo2[0]->COAend); //manual validation                '); //manual validation                
                return redirect('entri-transaksi/data-transaksi/duplikasi/' . $idd)->withErrors($validator)->withInput();
            }
        } else if ($req->dk1 == '+' && $req->dk2 == '-') {
            if (($saldo1[0]->COAend + $req->Nominal) < 0) {
                $validator->getMessageBag()->add('login', 'Saldo Pada Akun 1 Tidak Terpenuhi, Jumlah Saldo : ' . $saldo1[0]->COAend); //manual validation                
                return redirect('entri-transaksi/data-transaksi/duplikasi/' . $idd)->withErrors($validator)->withInput();
            } else if (($saldo2[0]->COAend - $req->Nominal) < 0) {
                //dd('oo5');
                $validator->getMessageBag()->add('login', 'Saldo Pada Akun 2 Tidak Terpenuhi, Jumlah Saldo : ' . $saldo2[0]->COAend); //manual validation                
                return redirect('entri-transaksi/data-transaksi/duplikasi/' . $idd)->withErrors($validator)->withInput();
            }
        } else if ($req->dk1 == '-' && $req->dk2 == '-') {
            if (($saldo1[0]->COAend - $req->Nominal) < 0) {
                $validator->getMessageBag()->add('login', 'Saldo Pada Akun 1 Tidak Terpenuhi, Jumlah Saldo : ' . $saldo1[0]->COAend); //manual validation                
                return redirect('entri-transaksi/data-transaksi/duplikasi/' . $idd)->withErrors($validator)->withInput();
            } else if (($saldo2[0]->COAend - $req->Nominal) < 0) {
                $validator->getMessageBag()->add('login', 'Saldo Pada Akun 2 Tidak Terpenuhi, Jumlah Saldo : ' . $saldo2[0]->COAend); //manual validation                
                return redirect('entri-transaksi/data-transaksi/duplikasi/' . $idd)->withErrors($validator)->withInput();
            }
        } else if ($req->dk1 == '+' && $req->dk2 == '+') {
            if (($saldo1[0]->COAend + $req->Nominal) < 0) {
                $validator->getMessageBag()->add('login', 'Saldo Pada Akun 1 Tidak Terpenuhi, Jumlah Saldo : ' . $saldo1[0]->COAend); //manual validation                
                return redirect('entri-transaksi/data-transaksi/duplikasi/' . $idd)->withErrors($validator)->withInput();
            } else if (($saldo2[0]->COAend + $req->Nominal) < 0) {
                $validator->getMessageBag()->add('login', 'Saldo Pada Akun 2 Tidak Terpenuhi, Jumlah Saldo : ' . $saldo2[0]->COAend); //manual validation                
                return redirect('entri-transaksi/data-transaksi/duplikasi/' . $idd)->withErrors($validator)->withInput();
            }
        }

        d_jurnal::create([
            'jr_id' => $id,
            'jr_comp' => $comp,
            'jr_year' => $year,
            'jr_trans' => $req->KodeTransaksi,
            'jr_transsub' => $req->jr_transsub,
            'jr_cashtype' => substr($req->Tipe, 0, 1),
            'jr_tgl' => $tgl,
            // 'jr_detail' => $req->division,
            'jr_value' => $req->Nominal,
            //'jr_ref' => $req->division,
            'jr_note' => $req->Note,
            'jr_memcode' => $req->Member,
            'jr_subcomp' => $req->kodeSub,
            'jr_project' => $req->Poject,
        ]);
        Session::flash('flash_message', 'Data Berhasil Disimpan.');

        if ($req->dk1 == '+') {
            $dk1 = $req->Nominal;
        } else if ($req->dk1 == '-') {
            $dk1 = -abs($req->Nominal);
        }
        if ($req->dk2 == '+') {
            $dk2 = $req->Nominal;
        } else if ($req->dk2 == '-') {
            $dk2 = -abs($req->Nominal);
        }

        d_jurnal_dt::create([
            'jrdt_id' => $id,
            'jrdt_dt' => '1',
            'jrdt_acc' => $req->jrdt_acc1,
            'jrdt_value' => $dk1,
        ]);
        d_jurnal_dt::create([
            'jrdt_id' => $id,
            'jrdt_dt' => '2',
            'jrdt_acc' => $req->jrdt_acc2,
            'jrdt_value' => $dk2,
        ]);

        return Redirect('entri-transaksi/data-transaksi');
    }

    public function destroy($id) {

        d_jurnal::where('jr_id', '=', $id)->delete();
        d_jurnal_dt::where('jrdt_id', '=', $id)->delete();
        return "sukses";
    }

}
