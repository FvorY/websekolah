<?php

namespace App\Http\Controllers;

use App\d_item_dt;
use App\d_mitra_item;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\d_item;
use App\Http\Requests;
use DB;
use Response;
use Yajra\Datatables\Datatables;
use Session;

class ItemController extends Controller
{
    public function index()
    {
        return view('master-item.index', compact('data'));
    }

    public function GetDataY()
    {
        setlocale(LC_MONETARY, 'id');
        $getData = DB::table('d_item')
            ->join('d_item_dt', 'id_item', '=', 'i_id')
            ->leftJoin('d_size', 'id_size', '=', 's_id')
            ->select('i_id', 'i_nama', 'i_warna', 'id_price', 's_nama', 'id_detailid', 'i_note')
            ->where('i_isactive', '=', 'y')
            ->orderBy('i_id', 'id_size')
            ->get();

        $getData = collect($getData);
        return Datatables::of($getData)
            ->editColumn('id_price', function ($getData) {
                $rupiah = '<span style="float: left;">Rp. </span><span style="float: right">' . number_format($getData->id_price, 0, ',', '.') . '</span>';
                return $rupiah;
            })
            ->addColumn('action', function ($getData) {
                return '<div class="text-center"><button style="margin-left:5px;" title="Tambah" type="button" class="btn btn-info btn-xs" onclick="add('.$getData->i_id.', '.$getData->id_detailid.')"><i class="glyphicon glyphicon-plus"></i></button>
                    <a style="margin-left:5px;" title="Edit" type="button" class="btn btn-warning btn-xs" onclick="edit('.$getData->i_id.', '.$getData->id_detailid.')"><i class="glyphicon glyphicon-edit"></i></a>
                    <a style="margin-left:5px;" type="button" class="btn btn-danger btn-xs" title="Hapus" onclick="hapus('.$getData->i_id.', '.$getData->id_detailid.')"><i class="glyphicon glyphicon-trash"></i></a>
                  </div>';
            })
            ->make(true);
    }

    public function GetDataN()
    {
        setlocale(LC_MONETARY, 'id');
        $getData = DB::table('d_item')
            ->join('d_item_dt', 'id_item', '=', 'i_id')
            ->leftJoin('d_size', 'id_size', '=', 's_id')
            ->select('i_id', 'i_nama', 'i_warna', 'id_price', 's_nama', 'id_detailid', 'i_note')
            ->where('i_isactive', '=', 'n')
            ->orderBy('i_id', 'id_size')
            ->get();

        $getData = collect($getData);
        return Datatables::of($getData)
            ->editColumn('id_price', function ($getData) {
                $rupiah = '<span style="float: left;">Rp. </span><span style="float: right">' . number_format($getData->id_price, 0, ',', '.') . '</span>';
                return $rupiah;
            })
            ->addColumn('action', function ($getData) {
                return '<div class="text-center"><button style="margin-left:5px;" title="Tambah" type="button" class="btn btn-info btn-xs" onclick="add('.$getData->i_id.', '.$getData->id_detailid.')"><i class="glyphicon glyphicon-plus"></i></button>
                    <button style="margin-left:5px;" title="Edit" type="button" class="btn btn-warning btn-xs" onclick="edit('.$getData->i_id.', '.$getData->id_detailid.')"><i class="glyphicon glyphicon-edit"></i></button>
                    <button style="margin-left:5px;" type="button" class="btn btn-danger btn-xs" title="Hapus" onclick="hapus('.$getData->i_id.', '.$getData->id_detailid.')"><i class="glyphicon glyphicon-trash"></i></button>
                  </div>';
            })
            ->make(true);
    }

    public function GetDataA()
    {
        setlocale(LC_MONETARY, 'id');
        $getData = DB::table('d_item')
            ->join('d_item_dt', 'id_item', '=', 'i_id')
            ->leftJoin('d_size', 'id_size', '=', 's_id')
            ->select('i_id', 'i_nama', 'i_warna', 'id_price', 's_nama', 'id_detailid', 'i_note')
            ->orderBy('i_id', 'id_size')
            ->get();

        $getData = collect($getData);
        return Datatables::of($getData)
            ->editColumn('id_price', function ($getData) {
                $rupiah = '<span style="float: left;">Rp. </span><span style="float: right">' . number_format($getData->id_price, 0, ',', '.') . '</span>';
                return $rupiah;
            })
            ->addColumn('action', function ($getData) {
                return '<div class="text-center">
                    <button style="margin-left:5px;" title="Tambah" type="button" class="btn btn-info btn-xs" onclick="add('.$getData->i_id.', '.$getData->id_detailid.')"><i class="glyphicon glyphicon-plus"></i></button>
                    <button style="margin-left:5px;" title="Edit" type="button" class="btn btn-warning btn-xs" onclick="edit('.$getData->i_id.', '.$getData->id_detailid.')"><i class="glyphicon glyphicon-edit"></i></button>
                    <button style="margin-left:5px;" type="button" class="btn btn-danger btn-xs" title="Hapus" onclick="hapus('.$getData->i_id.', '.$getData->id_detailid.')"><i class="glyphicon glyphicon-trash"></i></button>
                  </div>';
            })
            ->make(true);
    }

    public function autoitem(Request $request)
    {
        $cari = $request->term;

        $data = DB::table('d_item')
            ->join('d_item_dt', 'id_item', '=', 'i_id')
            ->join('d_size', 's_id', '=', 'id_size')
            ->select('i_id', 'id_detailid', 'i_nama', 's_nama', 'id_price', 'i_warna', 'i_note', DB::raw('concat(i_nama, " ", i_warna, " ", coalesce(s_nama, ""), " ") as nama'))
            ->whereRaw('concat(i_nama, " ", i_warna, " ", coalesce(s_nama, ""), " ") like "%'.$cari.'%"')
            ->take(50)->get();

        if ($data == null) {
            $results[] = ['id' => null, 'label' => 'Tidak ditemukan data terkait'];
        } else {

            foreach ($data as $query) {
                $results[] = ['id' => $query->i_id, 'label' => $query->nama, 'harga' => $query->id_price, 'warna' => $query->i_warna, 'i_nama' => $query->i_nama, 'ukuran' => $query->s_nama, 'detailid' => $query->id_detailid, 'note' => $query->i_note ];
            }
        }

        return Response::json($results);
    }

    public function create()
    {
        $kategori = DB::table('d_kategori')
            ->select('*')
            ->get();
        $satuan = DB::table('m_satuan')
            ->select('*')
            ->get();
        $ukuran = DB::table('d_size')
            ->select('*')
            ->get();
        $mitra = DB::table('d_mitra')
            ->select('m_id', 'm_name')
            ->orderBy('m_name')
            ->get();

        return view('master-item.create', compact('kategori', 'satuan', 'ukuran', 'mitra'));
    }

    public function save(Request $request)
    {
        DB::beginTransaction();
        try {
            $ukuran = $request->ukuran;
            $nama = $request->nama;
            $warna = $request->warna;
            $kategori = $request->kategori;
            $satuan = $request->satuan;
            $harga = $request->harga;
            $mitra = $request->mitra;
            $idItem = null;
            $detailid = 1;

            for ($i = 0; $i < count($harga); $i++) {
                $harga[$i] = str_replace("Rp. ", '', $harga[$i]);
                $harga[$i] = str_replace(".", '', $harga[$i]);
            }

            $cek = DB::table('d_item')
                ->join('d_item_dt', 'i_id', '=', 'id_item')
                ->select('i_id', 'id_item')
                ->where('i_nama', '=', $nama)
                ->where('i_warna', '=', $warna)
                ->whereIn('id_size', $ukuran)
                ->get();

            if (count($cek) > 0) {
                Session::flash('gagal', 'data yang didaftarkan sudah ada sebelumnya');
                return redirect()->route('master-item/create');
            } else {
                $idItem = DB::table('d_item')
                    ->select('i_id')
                    ->where('i_nama', '=', $nama)
                    ->where('i_warna', '=', $warna)
                    ->max('i_id');

                if ($idItem == null) {
                    $idItem = DB::table('d_item')
                        ->max('i_id');

                    $idItem = $idItem + 1;

                    $item = new d_item();
                    $item->i_id = $idItem;
                    $item->i_nama = strtoupper($nama);
                    $item->i_warna = strtoupper($warna);
                    $item->i_satuan = $satuan;
                    $item->i_kategori = $kategori;
                    $item->i_isactive = 'y';
                    $item->save();

                } else {
                    $detailid = DB::table('d_item_dt')
                        ->select('id_detailid')
                        ->where('id_item', '=', $idItem)
                        ->max('id_detailid');

                    $detailid = $detailid + 1;
                }
            }

            $data = [];

            $sekarang = Carbon::now('Asia/Jakarta');

            for ($i = 0; $i < count($ukuran); $i++) {
                $temp = array('id_item' => $idItem, 'id_detailid' => $detailid + $i, 'id_price' => $harga[$i], 'id_size' => $ukuran[$i], 'id_inserted' => $sekarang, 'id_updated' => $sekarang);
                array_push($data, $temp);
            }

            d_item_dt::insert($data);

            $idMitraItem = DB::table('d_mitra_item')
                ->max('mi_id');

            $idMitraItem = $idMitraItem + 1;

            $mitraitem = array(
                'mi_id' => $idMitraItem,
                'mi_item' => $idItem,
                'mi_mitra' => $mitra
            );

            d_mitra_item::insert($mitraitem);

            DB::commit();
            Session::flash('sukses', 'Data berhasil disimpan');
            return redirect()->route('master-item.create');
        }catch (\Exception $e) {
            DB::rollback();
            Session::flash('gagal', 'Terjadi kesalahan server, mungkin barang yang didaftarkan sudah terdaftar sebelumnya atau hubungi admin');
            return redirect()->route('master-item.create');
        }
    }

    public function AddSupp($id)
    {
        $getData = DB::table('d_item')
            ->join('d_item_dt', 'id_item', '=', 'i_id')
            ->leftJoin('d_size', 'id_size', '=', 's_id')
            ->select('i_id', 'i_nama', 'i_warna', 'id_price', 's_nama', 'id_item')
            ->where('i_isactive', '=', 'y')
            ->where('i_id', '=', $id)
            ->orderBy('i_id', 'id_size')
            ->get();
    }

    public function edit($id)
    {
        $data = DB::table('d_item')
            ->join('d_item_dt', 'id_item', '=', 'i_id')
            ->join('d_size', 's_id', '=', 'id_size')
            ->leftJoin('m_satuan', 'm_satuan.s_id', '=', 'i_satuan')
            ->leftJoin('d_kategori', 'k_id', '=', 'i_kategori')
            ->select('d_size.s_id as ukuran', 'i_nama as nama', 'i_warna as warna', 'm_satuan.s_id', DB::raw('ROUND(id_price) as harga'), 'i_id', 'k_id')
            ->where('i_id', '=', $id)
            ->get();

        $kategori = DB::table('d_kategori')
            ->select('*')
            ->get();
        $satuan = DB::table('m_satuan')
            ->select('*')
            ->get();
        $ukuran = DB::table('d_size')
            ->select('*')
            ->get();
        $hitung = count($data);
        return view('master-item.edit', compact('kategori', 'satuan', 'ukuran', 'data', 'hitung'));
    }

    public function update(Request $request)
    {
        DB::beginTransaction();
        try {

            d_item::where('i_id', '=', $request->id)
                ->update(array(
                    'i_nama' => $request->nama,
                    'i_warna' => $request->warna,
                    'i_satuan' => $request->satuan,
                    'i_kategori' => $request->kategori,
                    'i_price' => 0,
                    'i_isactive' => 'Y'
                ));

            d_item_dt::where('id_item', '=', $request->id)->delete();
            $sekarang = Carbon::now('Asia/Jakarta');
            $data = [];

            for ($i = 0; $i < count($request->ukuran); $i++) {
                $temp = array('id_item' => $request->id, 'id_detailid' => $i + 1, 'id_price' => $this->convert($request->harga[$i]), 'id_size' => $request->ukuran[$i], 'id_inserted' => $sekarang, 'id_updated' => $sekarang);
                array_push($data, $temp);
            }
            d_item_dt::insert($data);

            DB::commit();
            Session::flash('sukses', 'Data berhasil di update');
            return redirect()->route('master-item');
        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('gagal', 'Tidak dapat update data, silahkan coba lagi');
            return redirect()->route('master-item');
        }
    }

    public function delete(Request $request)
    {
        DB::beginTransaction();
        try {
            $id = $request->id;
            $dt = $request->dt;
            d_item_dt::where('id_item', '=', $id)->where('id_detailid', '=', $dt)->delete();

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

    function convert($str)
    {
        $str = preg_replace("/([^0-9\\.])/i", "", $str);
        $str = (str_replace(".","",$str));
        return (int)$str;
    }

    public function getInfo(Request $request)
    {
        $info = DB::table('d_mitra_item')
            ->select('mi_mitra')
            ->where('mi_item', '=', $request->id)
            ->get();

        $mitra = [];

        for ($i = 0; $i < count($info); $i++){
            $mitra[$i] = $info[$i]->mi_mitra;
        }

        $getMitra = DB::table('d_mitra')
            ->select('m_id', 'm_name')
            ->whereNotIn('m_id', $mitra)
            ->orderBy('m_name')
            ->get();

        return response()->json([
            'info' => $getMitra
        ]);
    }

    public function addmitra(Request $request){
      DB::beginTransaction();
      try {

        $data = $request->data;

        for ($i=0; $i < count($data); $i++) {
          $id = DB::table('d_mitra_item')->MAX('mi_id');
          
          d_mitra_item::insert([
            'mi_id' => $id += 1,
            'mi_mitra' => $data[$i],
            'mi_item' => $request->id
          ]);
        }

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
