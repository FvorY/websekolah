<?php

namespace App\Http\Controllers;

use App\d_supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Response;
use App\Http\Requests;
use Yajra\Datatables\Datatables;
use DB;


class supplierController extends Controller
{
      public function ambilSupplier(Request $request ){
	//echo $request->term;
	$term = $request->term;
	
	$results = array();		
	
	$queries = DB::table('d_supplier')
		->where('s_company', 'LIKE', '%'.$term.'%')
		->orWhere('s_name', 'LIKE', '%'.$term.'%')		
		->take(100)->get();
	
	foreach ($queries as $query)
	{
	    $results[] = [ 'id' => $query->s_id, 'label' => $query->s_company.' - '.$query->s_name,
                    ];
		//echo $query->i_jenis.' '.$query->i_jenissub.' '.$query->i_class.' '.$query->i_classsub.' '.$query->i_detail;
	}
	
	return response($results);
        
	//return Response::json($results);
    }

    public function index()
    {
        return view('supplier.index');
    }

    public function GetDataY()
    {
        $data = DB::table('d_supplier')
            ->select('s_company', 's_address', 's_phone', 's_id', 's_name')
            ->where('s_isactive', '=', 'Y')
            ->get();

        $data = collect($data);
        return Datatables::of($data)
            ->addColumn('action', function ($data) {
                return '<div class="text-center">
                    <a style="margin-left:5px;" title="Edit" type="button" class="btn btn-warning btn-xs" onclick="edit('.$data->s_id.')"><i class="glyphicon glyphicon-edit"></i></a>
                    <a style="margin-left:5px;" type="button" class="btn btn-danger btn-xs" title="Hapus" onclick="hapus('.$data->s_id.')"><i class="glyphicon glyphicon-trash"></i></a>
                  </div>';
            })
            ->make(true);
    }

    public function GetDataN()
    {
        $data = DB::table('d_supplier')
            ->select('s_company', 's_address', 's_phone', 's_id', 's_name')
            ->where('s_isactive', '=', 'N')
            ->get();

        $data = collect($data);
        return Datatables::of($data)
            ->addColumn('action', function ($data) {
                return '<div class="text-center">
                    <a style="margin-left:5px;" title="Edit" type="button" class="btn btn-warning btn-xs" onclick="edit('.$data->s_id.')"><i class="glyphicon glyphicon-edit"></i></a>
                    <a style="margin-left:5px;" type="button" class="btn btn-danger btn-xs" title="Hapus" onclick="hapus('.$data->s_id.')"><i class="glyphicon glyphicon-trash"></i></a>
                  </div>';
            })
            ->make(true);
    }

    public function GetDataA()
    {
        $data = DB::table('d_supplier')
            ->select('s_company', 's_address', 's_phone', 's_id', 's_name')
            ->get();

        $data = collect($data);
        return Datatables::of($data)
            ->addColumn('action', function ($data) {
                return '<div class="text-center">
                    <a style="margin-left:5px;" title="Edit" type="button" class="btn btn-warning btn-xs" onclick="edit('.$data->s_id.')"><i class="glyphicon glyphicon-edit"></i></a>
                    <a style="margin-left:5px;" type="button" class="btn btn-danger btn-xs" title="Hapus" onclick="hapus('.$data->s_id.')"><i class="glyphicon glyphicon-trash"></i></a>
                  </div>';
            })
            ->make(true);
    }

    public function autosupp(Request $request)
    {
        $cari = $request->term;

        $data = DB::table('d_supplier')
            ->select('s_company', 's_address', 's_phone', 's_id', 's_name')
            ->where(function ($q) use ($cari) {
                $q->orWhere('s_name', 'like', '%' . $cari . '%')
                    ->orWhere('s_company', 'like', '%' . $cari . '%')
                    ->orWhere('s_phone', 'like', '%' . $cari . '%');
            })
            ->take(50)->get();

        if ($data == null) {
            $results[] = ['id' => null, 'label' => 'Tidak ditemukan data terkait'];
        } else {

            foreach ($data as $query) {
                $results[] = ['id' => $query->s_id, 'nama' => $query->s_name, 'label' => $query->s_company.' ('.$query->s_name.')', 'alamat' => $query->s_address, 'phone' => $query->s_phone, 'company' => $query->s_company ];
            }
        }

        return Response::json($results);
    }

    public function save(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->id != null || $request->id != ''){
                $data = array(
                    's_id' => $request->id,
                    's_company' => $request->company,
                    's_name' => $request->nama,
                    's_address' => $request->alamat,
                    's_phone' => $request->nohp,
                    's_fax' => $request->fax,
                    's_note' => $request->keterangan,
                    's_isactive' => $request->aktif,
                    's_update' => Carbon::now('Asia/Jakarta')
                );
                d_supplier::where('s_id', '=', $request->id)->update($data);
            } else {
                $id = DB::table('d_supplier')
                    ->max('s_id');

                $id = $id + 1;

                $data = array(
                    's_id' => $id,
                    's_company' => $request->company,
                    's_name' => $request->nama,
                    's_address' => $request->alamat,
                    's_phone' => $request->nohp,
                    's_fax' => $request->fax,
                    's_note' => $request->keterangan,
                    's_isactive' => $request->aktif,
                    's_insert' => Carbon::now('Asia/Jakarta')
                );
                d_supplier::insert($data);
            }

            DB::commit();
            return response()->json([
                'status' => 'sukses'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'gagal',
                'data' => $e
            ]);
        }

    }

    public function edit(Request $request)
    {
        $data = DB::table('d_supplier')
            ->select('*')
            ->where('s_id', '=', $request->id)
            ->first();

        return response()->json([
            'data' => $data
        ]);
    }

    public function delete(Request $request)
    {
        DB::beginTransaction();
        try {
            $id = $request->id;

            d_supplier::where('s_id', '=', $id)->delete();

            DB::commit();
            return response()->json([
                'status' => 'sukses'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'gagal',
                'data' => $e
            ]);
        }
    }

    public function getSupplier(Request $request)
    {
        $id = $request->id;

        $data = DB::table('d_supplier')
            ->select('*')
            ->where('s_id', '=', $id)
            ->get();

        return response()->json([
            'data' => $data
        ]);
    }
    
}
