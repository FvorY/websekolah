<?php

/*
  |--------------------------------------------------------------------------
  | Application Routes front acccounting
  |--------------------------------------------------------------------------
  |
  | Here is where you can register all of the routes for an application.
  | It's a breeze. Simply tell Laravel the URIs it should respond to
  | and give it the controller to call when that URI is requested.
  |
 */

Route::get('/', 'loginController@index');
Route::post('login', 'loginController@authenticate');
Route::get('login', 'loginController@authenticate');
Route::get('logout', 'loginController@logout');
Route::get('dashboard', 'dashboardController@index');

Route::get('profil', 'profilController@index');
Route::get('profil/ubah-profil/{id}', 'profilController@ubahProfil');
Route::post('profil/perbarui-profil', 'profilController@perbaruiProfil');
Route::get('profil/perbarui-profil', 'profilController@perbaruiProfil');
Route::get('profil/ubah-password/{id}', 'profilController@ubahPassword');
Route::post('profil/perbarui-password', 'profilController@perbaruiPassword');
Route::get('profil/perbarui-password', 'profilController@perbaruiPassword');

Route::get('manajemen-pengguna/pengguna', 'manajemenPenggunaController@index');
Route::get('manajemen-pengguna/edit/{id}', 'manajemenPenggunaController@edit');


Route::get('manajemen-hak-akses/group', 'aksesGroupController@index');
Route::get('manajemen-hak-akses/group/tambah', 'aksesGroupController@tambah');
Route::get('manajemen-hak-akses/group-detail', 'aksesGroupController@detailGroup');
Route::get('manajemen-hak-akses/group/simpan', 'aksesGroupController@simpan');
Route::get('manajemen-hak-akses/group/{id}/edit', 'aksesGroupController@edit');


//mitra yajra
Route::get('manajemen-mitra/data-mitra','mitraController@index');
Route::get('manajemen-mitra/data-mitra/table','mitraController@data');
Route::get('manajemen-mitra/data-mitra/tambah','mitraController@tambah');
Route::get('manajemen-mitra/data-mitra/simpan','mitraController@simpan');
Route::get('manajemen-mitra/data-mitra/detail','mitraController@detail');
Route::POST('manajemen-mitra/data-mitra/simpan','mitraController@simpan');
Route::get('manajemen-mitra/data-mitra/{id}/edit','mitraController@edit');
Route::get('manajemen-mitra/data-mitra/perbarui/{id}','mitraController@perbarui');
Route::get('manajemen-mitra/data-mitra/hapus/{id}','mitraController@hapus');

//mitra Contract
Route::get('manajemen-kontrak-mitra/data-kontrak-mitra','mitraContractController@index');
Route::get('manajemen-kontrak-mitra/data-kontrak-mitra/table','mitraContractController@data');
Route::post('manajemen-kontrak-mitra/data-kontrak-mitra/table','mitraContractController@data');
Route::get('manajemen-kontrak-mitra/data-kontrak-mitra/tambah','mitraContractController@tambah');
Route::get('manajemen-kontrak-mitra/data-kontrak-mitra/simpan','mitraContractController@simpan');
Route::post('manajemen-kontrak-mitra/data-kontrak-mitra/simpan','mitraContractController@simpan');
Route::get('manajemen-kontrak-mitra/data-kontrak-mitra/cari','mitraContractController@cari');
Route::get('manajemen-kontrak-mitra/data-kontrak-mitra/nomou','mitraContractController@nomou');
Route::get('manajemen-kontrak-mitra/data-kontrak-mitra/searchresult','mitraContractController@searchresult');
Route::get('manajemen-kontrak-mitra/data-kontrak-mitra/searchresult/getdata', 'mitraContractController@getdata');
Route::get('manajemen-kontrak-mitra/data-kontrak-mitra/{idmitra}/{iddetail}/detail','mitraContractController@detail');
Route::get('manajemen-kontrak-mitra/data-kontrak-mitra/{mitra}/{mc_contractid}/edit','mitraContractController@edit');
Route::get('manajemen-kontrak-mitra/data-kontrak-mitra/perbarui/{mitra}/{mc_contractid}','mitraContractController@perbarui');
Route::get('manajemen-kontrak-mitra/data-kontrak-mitra/update/{mitra}/{mc_contractid}','mitraContractController@update');
Route::get('manajemen-kontrak-mitra/data-kontrak-mitra/hapus/{mitra}/{mc_contractid}','mitraContractController@hapus');
Route::get('manajemen-kontrak-mitra/data-kontrak-mitra/getdivisi','mitraContractController@GetDivisi');

//pegawai yajra
Route::get('manajemen-pegawai/data-pegawai','pegawaiController@index');
Route::get('manajemen-pegawai/data-pegawai/table','pegawaiController@data');
Route::get('manajemen-pegawai/data-pegawai/tambah','pegawaiController@tambah');
Route::get('manajemen-pegawai/data-pegawai/simpan','pegawaiController@simpan');
Route::get('manajemen-pegawai/data-pegawai/{id}/edit','pegawaiController@edit');
Route::get('manajemen-pegawai/data-pegawai/perbarui/{id}','pegawaiController@perbarui');
Route::get('manajemen-pegawai/data-pegawai/hapus/{id}','pegawaiController@hapus');


//BPJS
Route::get('manajemen-pegawai/data-pegawai','pegawaiController@index');
Route::get('manajemen-pegawai/data-pegawai/table','pegawaiController@data');
Route::get('manajemen-pegawai/data-pegawai/tambah','pegawaiController@tambah');
Route::get('manajemen-pegawai/data-pegawai/simpan','pegawaiController@simpan');
Route::get('manajemen-pegawai/data-pegawai/{id}/edit','pegawaiController@edit');
Route::get('manajemen-pegawai/data-pegawai/perbarui/{id}','pegawaiController@perbarui');
Route::get('manajemen-pegawai/data-pegawai/hapus/{id}','pegawaiController@hapus');

//pekerja yajra
Route::get('manajemen-pekerja/data-pekerja','pekerjaController@index');
Route::POST('manajemen-pekerja/data-pekerja/table','pekerjaController@data');
Route::get('manajemen-pekerja/data-pekerja/table','pekerjaController@data');
Route::POST('manajemen-pekerja/data-pekerja/tablenon','pekerjaController@dataEx');
Route::get('manajemen-pekerja/data-pekerja/tablenon','pekerjaController@dataEx');
Route::POST('manajemen-pekerja/data-pekerja/tablecalon','pekerjaController@dataCalon');
Route::get('manajemen-pekerja/data-pekerja/tablecalon','pekerjaController@dataCalon');
Route::get('manajemen-pekerja/data-pekerja/tambah','pekerjaController@tambah');
Route::get('manajemen-pekerja/data-pekerja/simpan','pekerjaController@simpan');
Route::POST('manajemen-pekerja/data-pekerja/simpan','pekerjaController@simpan');
Route::get('manajemen-pekerja/data-pekerja/{id}/edit','pekerjaController@edit');
Route::get('manajemen-pekerja/data-pekerja/perbarui/','pekerjaController@perbarui');
Route::POST('manajemen-pekerja/data-pekerja/perbarui/','pekerjaController@perbarui');
Route::get('manajemen-pekerja/data-pekerja/hapus/{id}','pekerjaController@hapus');
Route::get('manajemen-pekerja/data-pekerja/detail','pekerjaController@detail');
Route::get('manajemen-pekerja/data-pekerja/resign','pekerjaController@resign');
Route::get('manajemen-pekerja/data-pekerja/detail-mutasi','pekerjaController@detail_mutasi');


//mitra-pekerja
Route::get('manajemen-pekerja-mitra/data-pekerja-mitra','mitraPekerjaController@index');
Route::get('manajemen-pekerja-mitra/data-pekerja-mitra/table','mitraPekerjaController@data');
Route::post('manajemen-pekerja-mitra/data-pekerja-mitra/table','mitraPekerjaController@data');
Route::get('manajemen-pekerja-mitra/data-pekerja-mitra/tambah','mitraPekerjaController@tambah');
Route::get('manajemen-pekerja-mitra/data-pekerja-mitra/lanjut','mitraPekerjaController@lanjutkan');
Route::post('manajemen-pekerja-mitra/data-pekerja-mitra/lanjut','mitraPekerjaController@lanjutkan');
Route::get('manajemen-pekerja-mitra/data-pekerja-mitra/lanjutkan', [
        'uses' => 'mitraPekerjaController@lanjutkan',
        'as' => 'mitraPekerjaController.lanjutkan'
]);
Route::post('manajemen-pekerja-mitra/data-pekerja-mitra/lanjutkan','mitraPekerjaController@lanjutkan');
Route::get('manajemen-pekerja-mitra/data-pekerja-mitra/simpan','mitraPekerjaController@simpan');
Route::post('manajemen-pekerja-mitra/data-pekerja-mitra/simpan','mitraPekerjaController@simpan');
Route::get('manajemen-pekerja-mitra/data-pekerja-mitra/{mitra}/{id_detail}/edit','mitraPekerjaController@edit');
Route::get('manajemen-pekerja-mitra/data-pekerja-mitra/perbarui/{mitra}/{mc_contractid}','mitraPekerjaController@perbarui');
Route::get('manajemen-pekerja-mitra/data-pekerja-mitra/update','mitraPekerjaController@update');
Route::post('manajemen-pekerja-mitra/data-pekerja-mitra/update','mitraPekerjaController@update');
Route::get('manajemen-pekerja-mitra/data-pekerja-mitra/hapus/{mitra}/{iddetail}','mitraPekerjaController@hapus');
Route::get('manajemen-pekerja-mitra/data-pekerja-mitra/delete/{mp_pekerja}/{mp_contract}','suratController@delete');
Route::get('get-data-mitra-kontrak/{mitra}/{kontrak}','mitraPekerjaController@mitraContrak');
Route::get('manajemen-pekerja-mitra/data-pekerja-mitra/cari','mitraPekerjaController@cari');
Route::get('manajemen-pekerja-mitra/data-pekerja-mitra/cari/pencarian','mitraPekerjaController@pencarian');
Route::get('manajemen-pekerja-mitra/data-pekerja-mitra/cari/getData','mitraPekerjaController@getDataPencarian');
Route::get('manajemen-pekerja-mitra/data-pekerja-mitra/selesai','mitraPekerjaController@selesai');

//penerimaan-pekerja
Route::get('manajemen-pekerja-mitra/penerimaan-pekerja-mitra','PenerimaanPekerjaController@index');
Route::get('manajemen-pekerja-mitra/penerimaan-pekerja-mitra/getNomor','PenerimaanPekerjaController@getNomor');
Route::get('manajemen-pekerja-mitra/penerimaan-pekerja-mitra/simpan','PenerimaanPekerjaController@save');

//======= SP Pekerja
Route::get('manajemen-pekerja/surat-peringatan','SuratPeringatanController@index');
Route::get('manajemen-pekerja/surat-peringatan/simpan/{id}','SuratPeringatanController@simpan');
Route::post('manajemen-pekerja/surat-peringatan/simpan/{id}','SuratPeringatanController@simpan');
Route::get('manajemen-pekerja/surat-peringatan/getsp','SuratPeringatanController@getsp');
Route::get('manajemen-pekerja/surat-peringatan/getdata','SuratPeringatanController@getdata');
Route::get('manajemen-pekerja/surat-peringatan/cari','SuratPeringatanController@cari');
Route::get('manajemen-pekerja/surat-peringatan/hapus','SuratPeringatanController@hapus');
Route::get('manajemen-pekerja/surat-peringatan/getcari','SuratPeringatanController@getcari');
Route::get('manajemen-pekerja/surat-peringatan/detail','SuratPeringatanController@detail');
Route::get('manajemen-pekerja/surat-peringatan/edit','SuratPeringatanController@edit');
Route::get('manajemen-pekerja/surat-peringatan/update/{id}','SuratPeringatanController@update');
Route::post('manajemen-pekerja/surat-peringatan/update/{id}','SuratPeringatanController@update');

//======= Promosi & Demosi
Route::get('manajemen-pekerja/promosi-demosi','promosiController@index');
Route::get('manajemen-pekerja/promosi-demosi/getData','promosiController@getData');
Route::post('manajemen-pekerja/promosi-demosi/getData','promosiController@getData');

//seragam
Route::get('manajemen-seragam/data-seragam','seragamController@index');
Route::get('manajemen-seragam/data-seragam/data','seragamController@data');
Route::get('manajemen-seragam/data-seragam/{id}/edit','seragamController@edit');
Route::get('manajemen-seragam/data-seragam/tambah','seragamController@tambah');
Route::get('manajemen-seragam/data-seragam/simpan','seragamController@simpan');
Route::get('manajemen-seragam/data-seragam/perbarui/{id}','seragamController@perbarui');
Route::get('manajemen-seragam/data-seragam/hapus/{id}','seragamController@hapus');

Route::get('ambil-data-supplier/autocomplete','supplierController@ambilSupplier');

//red

Route::get('manajemen-akun/auto-generate-akun','d_comp_coaController@generate_akun');
Route::get('manajemen-akun/data-akun','d_comp_coaController@index');
Route::get('manajemen-akun/data-akun/delete/{id}','d_comp_coaController@delete');
Route::get('manajemen-akun/data-akun/{id}/edit','d_comp_coaController@edit');
Route::get('manajemen-akun/data-akun/update/{id}','d_comp_coaController@update');
Route::get('manajemen-akun/data-akun/sub_akun/{id}','d_comp_coaController@create_sub_akun');
Route::get('manajemen-akun/data-akun/simpan','d_comp_coaController@store');

//neraca
Route::get('laporan-keuangan/neraca', [
        'uses' => 'neracaController@neracaindex',
        'as' => 'neracaController.neracaindex'
]);
Route::get('laporan-keuangan/neraca/table', [
        'uses' => 'neracaController@neraca',
        'as' => 'neracaController.neraca'
]);
Route::get('laporan-keuangan/neraca/cari-neraca/{bulan}', [
        'uses' => 'neracaController@cari_neraca',
        'as' => 'neracaController.cari_neraca'
]);
Route::get('laporan-keuangan/neraca/cari-neraca-final/{bulan}', [
        'uses' => 'neracaController@cari_neraca_final',
        'as' => 'neracaController.cari_neraca_final'
]);
Route::get('laporan-keuangan/neraca-per', [
        'uses' => 'laporan_keuanganController@neracaper',
        'as' => 'laporan_keuanganController.neracaper'
]);
//neraca

//laba rugi
Route::get('laporan-keuangan/laba-rugi', [
        'uses' => 'labaRugiController@labarugiIndex',
        'as' => 'laporan_keuanganController.labarugi'
]);
Route::get('laporan-keuangan/laba-rugi/table', [
        'uses' => 'labaRugiController@labarugi',
        'as' => 'laporan_keuanganController.labarugi'
]);
Route::get('laporan-keuangan/laba-rugi-percobaan/per/{bulan}', [
        'uses' => 'labaRugiController@labarugiPercobaanPer',
        'as' => 'labaRugiController.labarugiPercobaanPer'
]);
Route::get('laporan-keuangan/laba-rugi-percobaan/periode/{bulan}', [
        'uses' => 'labaRugiController@labarugiPercobaanPeriode',
        'as' => 'labaRugiController.labarugiPercobaanPeriode'
]);
Route::get('laporan-keuangan/laba-rugi-final/per/{bulan}', [
        'uses' => 'labaRugiController@labarugiFinalPer',
        'as' => 'labaRugiController.labarugiFinalPer'
]);
Route::get('laporan-keuangan/laba-rugi-final/periode/{bulan}', [
        'uses' => 'labaRugiController@labarugiFinalPeriode',
        'as' => 'labaRugiController.labarugiFinalPeriode'
]);
//laba rugi


//arus khas
Route::get('laporan-keuangan/arus-kas', [
        'uses' => 'arusKasController@aruskasIndex',
        'as' => 'arusKasController.aruskasindex'
]);
Route::get('laporan-keuangan/arus-kas/table', [
        'uses' => 'arusKasController@aruskas',
        'as' => 'arusKasController.aruskas'
]);
Route::get('laporan-keuangan/arus-kas-percobaan/per/{bulan}', [
        'uses' => 'arusKasController@aruskasPercobaanPer',
        'as' => 'arusKasController.aruskasPercobaanPer'
]);
Route::get('laporan-keuangan/arus-kas-percobaan/periode/{bulan}', [
        'uses' => 'arusKasController@aruskasPercobaanPeriode',
        'as' => 'arusKasController.aruskasPercobaanPeriode'
]);
Route::get('laporan-keuangan/arus-kas-final/per/{bulan}', [
        'uses' => 'arusKasController@aruskasFinalPer',
        'as' => 'arusKasController.aruskasPercobaanPer'
]);
Route::get('laporan-keuangan/arus-kas-final/periode/{bulan}', [
        'uses' => 'arusKasController@aruskasFinalPeriode',
        'as' => 'arusKasController.aruskasPercobaanPeriode'
]);

Route::get('laporan-keuangan/arus-kas/periode/tahun', [
        'uses' => 'laporan_keuanganController@arus_khas_periode_tahun',
        'as' => 'laporan_keuanganController.arus_khas_periode'
]);
Route::get('laporan-keuangan/arus-kas/periode/bulan', [
        'uses' => 'laporan_keuanganController@arus_khas_periode_bulan',
        'as' => 'laporan_keuanganController.arus_khas_periode_bulan'
]);
    //---------arus khas selesai

//entri transaksi
Route::get('entri-transaksi/data-transaksi', [
        'uses' => 'entri_transaksiController@index',
        'as' => 'entri_transaksiController.index'
]);
Route::get('entri-transaksi/data-transaksi/get', [
        'uses' => 'entri_transaksiController@index_data',
        'as' => 'entri_transaksiController.index_data'
]);
Route::get('entri-transaksi/data-transaksi/cari_tanggal/get', [
        'uses' => 'entri_transaksiController@cari_tanggal_get',
        'as' => 'entri_transaksiController.cari_tanggal_get'
]);
    //entri transaksi_tanggal


Route::get('entri-transaksi/data-transaksi/create', [
        'uses' => 'entri_transaksiController@create',
        'as' => 'entri_transaksiController.create'
]);
Route::get('entri-transaksi/data-transaksi/store', [
        'uses' => 'entri_transaksiController@store',
        'as' => 'entri_transaksiController.store'
]);
Route::get('entri-transaksi/data-transaksi/edit/{id}', [
        'uses' => 'entri_transaksiController@edit',
        'as' => 'entri_transaksiController.edit'
]);
Route::get('entri-transaksi/data-transaksi/duplikasi/{id}', [
        'uses' => 'entri_transaksiController@duplikasi_transaksi',
        'as' => 'entri_transaksiController.duplikasi_transaksi'
]);
Route::get('entri-transaksi/data-transaksi/simpan-duplikasi/{id}', [
        'uses' => 'entri_transaksiController@simpanduplikasi',
        'as' => 'entri_transaksiController.edit'
]);
Route::get('entri-transaksi/data-transaksi/update/{id}', [
        'uses' => 'entri_transaksiController@update',
        'as' => 'entri_transaksiController.update'
]);
Route::DELETE('entri-transaksi/data-transaksi/destroy/{id}', [
        'uses' => 'entri_transaksiController@destroy',
        'as' => 'entri_transaksiController.destroy'
]);
			//----------entri transaksi selesai--------

//d_comp_trans
Route::get('data-master/master-transaksi-akun', [
        'uses' => 'd_comp_transController@index_view',
        'as' => 'd_comp_trans.index_view'
]);
Route::get('data-master/master-transaksi-akun/index_view', [
        'uses' => 'd_comp_transController@index_view',
        'as' => 'd_comp_trans.index'
]);
Route::get('data-master/master-transaksi-akun/data', [
        'uses' => 'd_comp_transController@index_data',
        'as' => 'd_comp_trans.index'
]);
Route::get('data-master/master-transaksi-akun/create', [
        'uses' => 'd_comp_transController@create',
        'as' => 'd_comp_trans.create'
]);
Route::get('data-master/master-transaksi-akun/store', [
        'uses' => 'd_comp_transController@store',
        'as' => 'd_comp_trans.store'
]);
Route::get('data-master/master-transaksi-akun/edit/{tr_code}/{tr_codesub}', [
        'uses' => 'd_comp_transController@edit',
        'as' => 'd_comp_trans.edit'
]);
Route::get('data-master/master-transaksi-akun/update/{id}/{tr_codesub}', [
        'uses' => 'd_comp_transController@update',
        'as' => 'd_comp_trans.update'
]);
Route::Delete('data-master/master-transaksi-akun/delete/{id}/{tr_code}', [
        'uses' => 'd_comp_transController@destroy',
        'as' => 'd_comp_trans.destroy'
]);
Route::get('data-master/master-transaksi-akun/kode/{id}', [
        'uses' => 'd_comp_transController@kode',
        'as' => 'd_comp_trans.kode'
]);
Route::get('data-master/master-transaksi-akun/cheknama/{nama}/{kode}', [

     'uses' => 'd_comp_transController@chekNamaTransaksi',
        'as' => 'd_comp_trans.chekNamaTransaksi'

]);
Route::get('data-master/master-transaksi-akun/set-akun/{code}/{cashtype}', [
        'uses' => 'd_comp_transController@setAkun',
        'as' => 'd_comp_trans.setAkun'
]);

            //---selesai d_comp_trans

//d_comp_trans
Route::get('bpjs','bpjsController@index');
Route::get('select','bpjsController@select');
Route::get('bpjs/create','bpjsController@create');
Route::post('bpjs/update&{p_nik}','bpjsController@update');
Route::get('bpjs/delete/{no_kartu}','bpjsController@delete');
Route::post('bpjs','bpjsController@tambahdata');
Route::post('bpjs/store','bpjsController@store');
Route::get('bpjs/create/autocomplete','bpjsController@autocomplete');
Route::get('bpjs/edit/autocomplete','bpjsController@autocomplete');
Route::get('set-nama/{p_nik}','bpjsController@setnama');
Route::get('bpjs/edit&{p_nik}','bpjsController@edit');

//HALAMAN FORM SURAT
Route::get('surat/create','suratController@create'); //surat pengalaman
Route::get('surat/create-tkerja','suratController@create1'); //surat tidak lagi bekerja
Route::get('surat/create-daupa','suratController@create2'); //surat data upah
Route::get('surat/create-tibpjs','suratController@create3'); //surat tidak aktif bpjs
Route::get('surat/create-resign','suratController@create4'); //surat pekerja resign
Route::get('surat/create-pibank','suratController@create5'); //surat pinjam bank
Route::get('surat/create-pebpjs','suratController@create6'); //surat pendaftaran bpjs kesehatan
Route::get('surat/create-pekpr','suratController@create7'); //surat pengajuan kpr
//CETAK DATA DI LINK TAMBAH
Route::get('surat/laporan-legalisir-data-upah','suratController@gege'); //CETAK UPAH
Route::get('surat/laporan-pengalaman-kerja','suratController@gege1'); //CETAK PENGALAMAN KERJA
Route::get('surat/laporan-pekerja-resign','suratController@gege2'); //CETAK RESIGN KERJA
Route::get('surat/laporan-pinjam-bank','suratController@gege3'); //CETAK PINJAM BANK
Route::get('surat/laporan-pengajuan-kpr','suratController@gege4'); //CETAK PENGAJUAN KPR
Route::get('surat/laporan-pendaftaran-bpjs','suratController@gege5'); //CETAK PENDAFTARAN BPJS
Route::get('surat/laporan-tidak-aktif-bpjs','suratController@gege6'); //CETAK TIDAK AKTIF BPJS
Route::get('surat/laporan-tidak-lagi-bekerja','suratController@gege7'); //CETAK TIDAK BEKERJA
//FUNCTION SIMPAN
Route::get('surat/store','suratController@store'); //SIMPAN DATA MAINDATA
Route::get('surat/store1','suratController@store1'); //SIMPAN DATA PENGALAMAN KERJA
Route::get('surat/store2','suratController@store2'); //DATA UPAH
Route::get('surat/store3','suratController@store3'); //PINJAM BANK
Route::get('surat/store4','suratController@store4'); //PENDAFTARAN BPJS
Route::get('surat/store5','suratController@store5'); //PE KPR
//EDIT
Route::get('surat/edit-utama/{id_surat}','suratController@edit0');
Route::get('surat/edit/{id_surat}','suratController@edit');
Route::get('surat/edit/edit-tkerja/{id_surat}','suratController@edit1');
Route::get('surat/edit/edit-daupa/{id_surat}','suratController@edit2');
Route::get('surat/edit/edit-tibpjs/{id_surat}','suratController@edit3');
Route::get('surat/edit/edit-resign/{id_surat}','suratController@edit4');
Route::get('surat/edit/edit-pibank/{id_surat}','suratController@edit5');
Route::get('surat/edit/edit-pebpjs/{id_surat}','suratController@edit6');
Route::get('surat/edit/edit-pekpr/{id_surat}','suratController@edit7');


//UPDATE

Route::post('surat/update1&{id_surat}','suratController@update1');

//INDEX
Route::get('surat','suratController@index');
//DELETE
Route::get('surat/delete/{id_surat}','suratController@delete');
//CETAK
Route::get('surat/cetak/{id_surat}','suratController@cetak');
Route::get('surat/cetak2/{id_surat}','suratController@cetak2');
Route::get('surat/cetak3/{id_surat}','suratController@cetak3');
Route::get('surat/cetak4/{id_surat}','suratController@cetak4');
Route::get('surat/cetak5/{id_surat}','suratController@cetak5');
Route::get('surat/cetak6/{id_surat}','suratController@cetak6');
Route::get('surat/cetak7/{id_surat}','suratController@cetak7');
Route::get('surat/laporan/{id_surat}','suratController@laporan');
//Autocomplete
Route::get('surat/create/autocomplete','suratController@autocomplete');
Route::get('surat/create/auto','suratController@auto');
Route::get('getData/{p_id}','suratController@getData');
Route::get('getDatanama/{p_id}','suratController@getDatanama');

//PEKERJA-DI-MITRA
Route::get('pekerja-di-mitra/pekerja-mitra','pdmController@index');
Route::get('pekerja-di-mitra/pekerja-mitra/table','pdmController@data');
Route::get('pekerja-di-mitra/edit/{mp_id}/{p_id}','pdmController@edit');
Route::get('pekerja-di-mitra/hapus/{mp_pekerja}','pdmController@hapus');
Route::get('pekerja-di-mitra/update&{mp_id}','pdmController@update');
Route::get('pekerja-di-mitra/getdivisi', 'pdmController@getdivisi');
Route::get('pekerja-di-mitra/getpekerja', 'pdmController@getpekerja');
Route::post('pekerja-di-mitra/getpekerja', 'pdmController@getpekerja');
Route::get('pekerja-di-mitra/getnomor', 'pdmController@getnomor');
Route::get('pekerja-di-mitra/getdata', 'pdmController@getdata');
Route::get('pekerja-di-mitra/getnik', 'pdmController@getnik');
Route::get('pekerja-di-mitra/simpannik', 'pdmController@simpannik');

//================= master item ilham ========================
Route::get('master-item', [
                'uses' => 'ItemController@index',
                'as' => 'master-item'
            ]);
Route::get('master-item/create', [
                'uses' => 'ItemController@create',
                'as' => 'master-item.create'
            ]);
Route::post('master-item/save','ItemController@save');
Route::get('master-item/save','ItemController@save');
Route::get('master-item/edit/{id}','ItemController@edit');
Route::get('master-item/get-data-y','ItemController@GetDataY');
Route::get('master-item/get-data-n','ItemController@GetDataN');
Route::get('master-item/get-data-a','ItemController@GetDataA');
Route::post('master-item/get-data-y','ItemController@GetDataY');
Route::post('master-item/get-data-n','ItemController@GetDataN');
Route::post('master-item/get-data-a','ItemController@GetDataA');
Route::get('autoitem','ItemController@autoitem');
Route::get('master-item/tambah-supplier/{id}','ItemController@AddSupp');
Route::get('master-item/update','ItemController@update');
Route::post('master-item/update','ItemController@update');
Route::get('master-item/delete','ItemController@delete');
Route::get('master-item/getInfo','ItemController@getInfo');
Route::get('master-item/addmitra', 'ItemController@addmitra');

//================= master supplier ilham ========================
Route::get('master-supplier', [
                'uses' => 'SupplierController@index',
                'as' => 'master-supplier'
            ]);
Route::get('master-supplier/get-data-y','supplierController@GetDataY');
Route::get('master-supplier/get-data-n','supplierController@GetDataN');
Route::get('master-supplier/get-data-a','supplierController@GetDataA');
Route::get('autosupp','supplierController@autosupp');
Route::get('master-supplier/simpan','supplierController@save');
Route::post('master-supplier/simpan','supplierController@save');
Route::get('master-supplier/edit','supplierController@edit');
Route::get('master-supplier/hapus','supplierController@delete');
Route::get('master-supplier/getSupplier','supplierController@getSupplier');
//=============== manajemen pembelian =====================
Route::get('manajemen-seragam/pembelian','PembelianController@index');
Route::get('manajemen-seragam/tambah','PembelianController@create');
Route::get('manajemen-seragam/cari','PembelianController@cari');
Route::get('manajemen-seragam/getnota','PembelianController@getnota');
Route::get('manajemen-seragam/getdata','PembelianController@getdata');
Route::get('manajemen-seragam/filter','PembelianController@filter');
Route::get('manajemen-seragam/detail','PembelianController@detail');

Route::get('manajemen-seragam/print','PembelianController@print');
Route::get('manajemen-pembelian/getItem','PembelianController@getItem');
Route::get('manajemen-pembelian/simpan','PembelianController@save');
Route::post('manajemen-pembelian/simpan','PembelianController@save');

//============== penerimaan ===============
Route::get('manajemen-seragam/penerimaan','PenerimaanController@index');
Route::get('manajemen-pembelian/carinota','PenerimaanController@cari');
Route::get('manajemen-pembelian/penerimaan/update','PenerimaanController@update');
Route::post('manajemen-pembelian/penerimaan/update','PenerimaanController@update');
Route::get('manajemen-pembelian/print','PenerimaanController@print');

//============== manajemen penjualan =========
Route::get('manajemen-seragam/pengeluaran','PenjualanController@index');
Route::get('manajemen-seragam/tambah-pengeluaran','PenjualanController@create');
Route::get('manajemen-penjualan/getItem','PenjualanController@getItem');
Route::get('manajemen-penjualan/getPekerja','PenjualanController@getPekerja');
Route::get('manajemen-penjualan/save','PenjualanController@save');

//============= mutasi stock
Route::get('manajemen-stock/mutasi-stock/tabel', 'StockMutController@tabel');
Route::get('manajemen-stock/mutasi-stock/tabel2', 'StockMutController@tabel2');
Route::get('manajemen-stock/mutasi-stock', 'StockMutController@index');
Route::get('manajemen-stock/mutasi-stock/get_gudang', 'StockMutController@get_gudang');
Route::get('manajemen-stock/mutasi-stock/get_barang', 'StockMutController@get_barang');

//============== data stock
Route::get('manajemen-stock/data-stock/tabel', 'StockDataController@tabel');
Route::get('manajemen-stock/data-stock/tabel2', 'StockDataController@tabel2');
Route::get('manajemen-stock/data-stock', 'StockDataController@index');
Route::get('manajemen-stock/data-stock/printall', 'StockDataController@printall');
Route::get('manajemen-stock/data-stock/print', 'StockDataController@print');
Route::get('manajemen-stock/data-stock/getpilih', 'StockDataController@getpilih');
Route::get('manajemen-stock/data-stock/getprint', 'StockDataController@getprint');
Route::get('manajemen-stock/data-stock/get_gudang', 'StockDataController@get_gudang');
Route::get('manajemen-stock/data-stock/get_barang', 'StockDataController@get_barang');

//============= pembayaran seragam
Route::get('manajemen-seragam/pembayaran-seragam', 'PembayaranController@index');
Route::get('manajemen-seragam/pembayaran-pekerja', 'PembayaranController@bayar');
Route::get('manajemen-seragam/getPekerja', 'PembayaranController@getPekerja');
Route::post('manajemen-seragam/simpan', 'PembayaranController@save');
Route::get('manajemen-seragam/simpan', 'PembayaranController@save');
Route::get('manajemen-seragam/getInfoPembayaran', 'PembayaranController@getInfoPembayaran');

//============= return seragam (pembelian)
Route::get('manajemen-seragam/return', 'ReturnPembelianController@index');
Route::get('manajemen-seragam/return/getdata', 'ReturnPembelianController@getData');
Route::get('manajemen-seragam/return/add', 'ReturnPembelianController@add');
Route::post('manajemen-seragam/return/lanjut', 'ReturnPembelianController@lanjut');
Route::get('manajemen-seragam/return/simpan', 'ReturnPembelianController@save');
Route::post('manajemen-seragam/return/simpan', 'ReturnPembelianController@save');

//============ Stock Opname
Route::get('manajemen-stock/stock-opname/tabel', 'StockOpnameController@tabel');
Route::get('manajemen-stock/stock-opname', 'StockOpnameController@index');
Route::get('manajemen-stock/stock-opname/opname', 'StockOpnameController@opname');
Route::get('manajemen-stock/stock-opname/simpan', 'StockOpnameController@simpan');
Route::get('manajemen-stock/stock-opname/detail', 'StockOpnameController@detail');
Route::get('manajemen-stock/stock-opname/get_data', 'StockOpnameController@get_data');
Route::get('manajemen-stock/stock-opname/edit', 'StockOpnameController@edit');

//thoriq

/*System*/
Route::get('/system/hakuser/user', 'aksesUserController@indexAksesUser');
Route::get('/system/hakuser/tambah_user', 'aksesUserController@tambah_user');
Route::get('/system/hakuser/tambah_user/simpan-user', 'aksesUserController@simpanUser');
Route::get('/system/hakakses/edit-user-akses/{id}/edit', 'aksesUserController@editUserAkses');
Route::get('/system/hakuser/perbarui-user/perbarui-user/{id}', 'aksesUserController@perbaruiUser');
Route::get('/system/hakakses/simpan-user-akses', 'aksesUserController@simpanUserAkses');

//mitra divisi
Route::get('manajemen-mitra/mitra-divisi','mitraDivisiController@index');
Route::get('manajemen-mitra/mitra-divisi/tabel','mitraDivisiController@tabel');
Route::post('manajemen-mitra/mitra-divisi/tabel','mitraDivisiController@tabel');
Route::get('manajemen-mitra/mitra-divisi/get_mitra','mitraDivisiController@get_mitra');
Route::get('manajemen-mitra/mitra-divisi/get_data_edit','mitraDivisiController@get_data_edit');
Route::get('manajemen-mitra/mitra-divisi/tambah','mitraDivisiController@tambah');
Route::get('manajemen-mitra/mitra-divisi/detail','mitraDivisiController@detail');
Route::get('manajemen-mitra/mitra-divisi/edit','mitraDivisiController@edit');

//mou mitra
Route::get('manajemen-mitra/mitra-mou','MouController@index');
Route::post('manajemen-mitra/mitra-mou/table','MouController@table');
Route::get('manajemen-mitra/mitra-mou/table','MouController@table');
Route::get('manajemen-mitra/mitra-mou/edit','MouController@edit');
Route::get('manajemen-mitra/mitra-mou/updateedit','MouController@updateedit');
Route::post('manajemen-mitra/mitra-mou/updateedit','MouController@updateedit');
Route::get('manajemen-mitra/mitra-mou/hapus','MouController@hapus');
Route::get('manajemen-mitra/mitra-mou/get-tgl-mou','MouController@tglMou');
Route::get('manajemen-mitra/mitra-mou/update-mou','MouController@UpdateMou');
Route::get('manajemen-mitra/mitra-mou/update-mou','MouController@UpdateMou');

//Cek Approval
Route::get('approval/cekapproval', 'approvalController@cekapproval');

//Approval pelamar
Route::get('approvalpelamar', 'approvalpelamarController@index');
Route::get('approvalpelamar/detail', 'approvalpelamarController@detail');
Route::get('approvalpelamar/setujui', 'approvalpelamarController@setujui');
Route::get('approvalpelamar/setujuilist', 'approvalpelamarController@setujuilist');
Route::get('approvalpelamar/tolak', 'approvalpelamarController@tolak');
Route::get('approvalpelamar/tolaklist', 'approvalpelamarController@tolaklist');
Route::get('approvalpelamar/print', 'approvalpelamarController@print');

//Approval Mitra
Route::get('approvalmitra', 'approvalmitraController@index');
Route::get('approvalmitra/detail', 'approvalmitraController@detail');
Route::get('approvalmitra/setujui', 'approvalmitraController@setujui');
Route::get('approvalmitra/setujuilist', 'approvalmitraController@setujuilist');
Route::get('approvalmitra/tolak', 'approvalmitraController@tolak');
Route::get('approvalmitra/tolaklist', 'approvalmitraController@tolaklist');
Route::get('approvalmitra/print', 'approvalmitraController@print');

//Approval Pembelian
Route::get('approvalpembelian', 'approvalpembelianController@index');
Route::get('approvalpembelian/setujui', 'approvalpembelianController@setujui');
Route::get('approvalpembelian/tolak', 'approvalpembelianController@tolak');
Route::get('approvalpembelian/setujuilist', 'approvalpembelianController@setujuilist');
Route::get('approvalpembelian/tolaklist', 'approvalpembelianController@tolaklist');
Route::get('approvalpembelian/detail', 'approvalpembelianController@detail');

//Approval SP
Route::get('approvalsp', 'approvalspController@index');
Route::get('approvalsp/setujui', 'approvalspController@setujui');
Route::get('approvalsp/tolak', 'approvalspController@tolak');
Route::get('approvalsp/setujuilist', 'approvalspController@setujuilist');
Route::get('approvalsp/tolaklist', 'approvalspController@tolaklist');
Route::get('approvalsp/detail', 'approvalspController@detail');
