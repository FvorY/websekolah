<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use DB;

use Carbon\Carbon;

use Yajra\Datatables\Datatables;

use App\d_pekerja;

class approvalpelamarController extends Controller
{
    public function index(){
      $data = DB::table('d_pekerja')
            ->where('p_status_approval', '=', null)
            ->get();

      return view("approvalpelamar.index", compact('data'));
    }

    public function detail(Request $request){
      $id = $request->id;

      $data = DB::table('d_pekerja')->selectRaw(
        "*,
        coalesce(p_jabatan_lamaran, '-') as p_jabatan_lamaran,
        coalesce(p_nip, '-') as p_nip,
        coalesce(p_jabatan, '-') as p_jabatan,
        coalesce(p_nip_mitra, '-') as p_nip_mitra,
        coalesce(p_sex, '-') as p_sex,
        coalesce(p_birthdate, '-') as p_birthdate,
        coalesce(p_birthplace, '-') as p_birthplace,
        coalesce(p_ktp, '-') as p_ktp,
        coalesce(p_name, '-') as p_name,
        coalesce(p_hp, '-') as p_hp,
        coalesce(p_telp, '-') as p_telp,
        coalesce(p_status, '-') as p_status,
        coalesce(p_many_kids, '-') as p_many_kids,
        coalesce(p_religion, '-') as p_religion,
        coalesce(p_address, '-') as p_address,
        coalesce(p_rt_rw, '-') as p_rt_rw,
        coalesce(p_kel, '-') as p_kel,
        coalesce(p_kecamatan, '-') as p_kecamatan,
        coalesce(p_city, '-') as p_city,
        coalesce(p_address_now, '-') as p_address_now,
        coalesce(p_rt_rw_now, '-') as p_rt_rw_now,
        coalesce(p_kel_now, '-') as p_kel_now,
        coalesce(p_kecamatan_now, '-') as p_kecamatan_now,
        coalesce(p_city_now, '-') as p_city_now,
        coalesce(p_name_family, '-') as p_name_family,
        coalesce(p_address_family, '-') as p_address_family,
        coalesce(p_telp_family, '-') as p_telp_family,
        coalesce(p_hp_family, '-') as p_hp_family,
        coalesce(p_hubungan_family, '-') as p_hubungan_family,
        coalesce(p_wife_name, '-') as p_wife_name,
        coalesce(p_wife_birth, '-') as p_wife_birth,
        coalesce(p_wife_birthplace, '-') as p_wife_birthplace,
        coalesce(p_dad_name, '-') as p_dad_name,
        coalesce(p_dad_job, '-') as p_dad_job,
        coalesce(p_mom_name, '-') as p_mom_name,
        coalesce(p_mom_job, '-') as p_mom_job,
        coalesce(p_job_now, '-') as p_job_now,
        coalesce(p_weight, '-') as p_weight,
        coalesce(p_height, '-') as p_height,
        coalesce(p_seragam_size, '-') as p_seragam_size,
        coalesce(p_celana_size, '-') as p_celana_size,
        coalesce(p_sepatu_size, '-') as p_sepatu_size,
        coalesce(p_kpk, '-') as p_kpk,
        coalesce(p_bu, '-') as p_bu,
        coalesce(p_ktp_expired, '-') as p_ktp_expired,
        coalesce(p_ktp_seumurhidup, '-') as p_ktp_seumurhidup,
        coalesce(p_education, '-') as p_education,
        coalesce(p_kpj_no, '-') as p_kpj_no,
        coalesce(p_state, '-') as p_state,
        coalesce(p_note, '-') as p_note,
        coalesce(p_workdate, '-') as p_workdate,
        coalesce(p_insert, '-') as p_insert,
        coalesce(p_update, '-') as p_update,
        coalesce(p_note, '-') as p_note"
        )
      ->where('p_id', $id)->get();

      if ($data[0]->p_birthdate == '-' ) {
          $birthdate = '-';
      }
      else {
        $birthdate = Carbon::parse($data[0]->p_birthdate)->format('d/m/Y');
      }

      if ($data[0]->p_wife_birth == '-' ) {
          $wifebirthdate = '-';
      }
      else {
        $wifebirthdate = Carbon::parse($data[0]->p_wife_birth)->format('d/m/Y');
      }

       // dd($data);

      return response()->json([
        'p_id' => $data[0]->p_id,
        'p_jabatan_lamaran' => $data[0]->p_jabatan_lamaran,
        'p_jabatan' => $data[0]->p_jabatan,
        'p_nip' => $data[0]->p_nip,
        'p_nip_mitra' => $data[0]->p_nip_mitra,
        'p_birthdate' => $birthdate,
        'p_birthplace' => $data[0]->p_birthplace,
        'p_telp' => $data[0]->p_telp,
        'p_sex'  => $data[0]->p_sex,
        'p_ktp' => $data[0]->p_ktp,
        'p_name' => $data[0]->p_name,
        'p_hp' => $data[0]->p_hp,
        'p_telp'  => $data[0]->p_telp,
        'p_status' => $data[0]->p_status,
        'p_many_kids' => $data[0]->p_many_kids,
        'p_religion' => $data[0]->p_religion,
        'p_address' => $data[0]->p_address,
        'p_rt_rw' => $data[0]->p_rt_rw,
        'p_kel' => $data[0]->p_kel,
        'p_kecamatan' => $data[0]->p_kecamatan,
        'p_city' => $data[0]->p_city,
        'p_address_now' => $data[0]->p_address_now,
        'p_rt_rw_now' => $data[0]->p_rt_rw_now,
        'p_kel_now' => $data[0]->p_kel_now,
        'p_kecamatan_now' => $data[0]->p_kecamatan_now,
        'p_city_now' => $data[0]->p_city_now,
        'p_name_family' => $data[0]->p_name_family,
        'p_address_family' => $data[0]->p_address_family,
        'p_telp_family' => $data[0]->p_telp_family,
        'p_hp_family' => $data[0]->p_hp_family,
        'p_hubungan_family' => $data[0]->p_hubungan_family,
        'p_wife_name' => $data[0]->p_wife_name,
        'p_wife_birth' => $wifebirthdate,
        'p_wife_birthplace' => $data[0]->p_wife_birthplace,
        'p_dad_name' => $data[0]->p_dad_name,
        'p_dad_job' => $data[0]->p_dad_job,
        'p_mom_name' => $data[0]->p_mom_name,
        'p_mom_job' => $data[0]->p_mom_job,
        'p_job_now' => $data[0]->p_job_now,
        'p_weight' => $data[0]->p_weight,
        'p_height' => $data[0]->p_height,
        'p_seragam_size' => $data[0]->p_seragam_size,
        'p_celana_size'  => $data[0]->p_celana_size,
        'p_sepatu_size' => $data[0]->p_sepatu_size,
        'p_kpk' => $data[0]->p_kpk,
        'p_bu' => $data[0]->p_bu,
        'p_ktp_expired' => $data[0]->p_ktp_expired,
        'p_ktp_seumurhidup' => $data[0]->p_ktp_seumurhidup,
        'p_education' => $data[0]->p_education,
        'p_kpj_no' => $data[0]->p_kpj_no,
        'p_state' => $data[0]->p_state,
        'p_note' => $data[0]->p_note,
        'p_workdate' => $data[0]->p_workdate,
        'p_insert' => $data[0]->p_insert,
        'p_img' => $data[0]->p_img,
        'p_update' => $data[0]->p_update,
        'p_note' => $data[0]->p_note
      ]);
    }

    public function setujui(Request $request){
      // dd($request);
      try {
        $d_pekerja = d_pekerja::where('p_id',$request->id)->where('p_status_approval', null);
        $d_pekerja->update([
          'p_status_approval' => 'Y',
          'p_date_approval' => Carbon::now()
        ]);

        return response()->json([
          'status' => 'berhasil'
        ]);
      } catch (\Exception $e) {
        return response()->json([
          'status' => 'gagal'
        ]);
      }


    }

    public function tolak(Request $request){
      // dd($request);
      try {
        $d_pekerja = d_pekerja::where('p_id',$request->id)->where('p_status_approval', null);
        $d_pekerja->update([
          'p_status_approval' => 'N',
          'p_date_approval' => Carbon::now()
        ]);

        return response()->json([
          'status' => 'berhasil'
        ]);
      } catch (\Exception $e) {
        return response()->json([
          'status' => 'gagal'
        ]);
      }


    }

    public function print(Request $request){
      // dd($request);
      $id = $request->id;

      $data = DB::table('d_pekerja')->selectRaw(
        "*,
        coalesce(p_jabatan_lamaran, '-') as p_jabatan_lamaran,
        coalesce(p_nip, '-') as p_nip,
        coalesce(p_jabatan, '-') as p_jabatan,
        coalesce(p_nip_mitra, '-') as p_nip_mitra,
        coalesce(p_sex, '-') as p_sex,
        coalesce(p_birthdate, '-') as p_birthdate,
        coalesce(p_birthplace, '-') as p_birthplace,
        coalesce(p_ktp, '-') as p_ktp,
        coalesce(p_name, '-') as p_name,
        coalesce(p_hp, '-') as p_hp,
        coalesce(p_telp, '-') as p_telp,
        coalesce(p_status, '-') as p_status,
        coalesce(p_many_kids, '-') as p_many_kids,
        coalesce(p_religion, '-') as p_religion,
        coalesce(p_address, '-') as p_address,
        coalesce(p_rt_rw, '-') as p_rt_rw,
        coalesce(p_kel, '-') as p_kel,
        coalesce(p_kecamatan, '-') as p_kecamatan,
        coalesce(p_city, '-') as p_city,
        coalesce(p_address_now, '-') as p_address_now,
        coalesce(p_rt_rw_now, '-') as p_rt_rw_now,
        coalesce(p_kel_now, '-') as p_kel_now,
        coalesce(p_kecamatan_now, '-') as p_kecamatan_now,
        coalesce(p_city_now, '-') as p_city_now,
        coalesce(p_name_family, '-') as p_name_family,
        coalesce(p_address_family, '-') as p_address_family,
        coalesce(p_telp_family, '-') as p_telp_family,
        coalesce(p_hp_family, '-') as p_hp_family,
        coalesce(p_hubungan_family, '-') as p_hubungan_family,
        coalesce(p_wife_name, '-') as p_wife_name,
        coalesce(p_wife_birth, '-') as p_wife_birth,
        coalesce(p_wife_birthplace, '-') as p_wife_birthplace,
        coalesce(p_dad_name, '-') as p_dad_name,
        coalesce(p_dad_job, '-') as p_dad_job,
        coalesce(p_mom_name, '-') as p_mom_name,
        coalesce(p_mom_job, '-') as p_mom_job,
        coalesce(p_job_now, '-') as p_job_now,
        coalesce(p_weight, '-') as p_weight,
        coalesce(p_height, '-') as p_height,
        coalesce(p_seragam_size, '-') as p_seragam_size,
        coalesce(p_celana_size, '-') as p_celana_size,
        coalesce(p_sepatu_size, '-') as p_sepatu_size,
        coalesce(p_kpk, '-') as p_kpk,
        coalesce(p_bu, '-') as p_bu,
        coalesce(p_ktp_expired, '-') as p_ktp_expired,
        coalesce(p_ktp_seumurhidup, '-') as p_ktp_seumurhidup,
        coalesce(p_education, '-') as p_education,
        coalesce(p_kpj_no, '-') as p_kpj_no,
        coalesce(p_state, '-') as p_state,
        coalesce(p_note, '-') as p_note,
        coalesce(p_workdate, '-') as p_workdate,
        coalesce(p_insert, '-') as p_insert,
        coalesce(p_update, '-') as p_update,
        coalesce(p_note, '-') as p_note"
        )
      ->where('p_id', $id)->get();

        if ($data[0]->p_birthdate == '-' ) {
            $birthdate = '-';
        }
        else {
          $birthdate = Carbon::parse($data[0]->p_birthdate)->format('d/m/Y');
        }

        if ($data[0]->p_wife_birth == '-' ) {
            $wifebirthdate = '-';
        }
        else {
          $wifebirthdate = Carbon::parse($data[0]->p_wife_birth)->format('d/m/Y');
        }

         // dd($data);
         $lempar = array(
           'p_id' => $data[0]->p_id,
           'p_jabatan_lamaran' => $data[0]->p_jabatan_lamaran,
           'p_jabatan' => $data[0]->p_jabatan,
           'p_nip' => $data[0]->p_nip,
           'p_nip_mitra' => $data[0]->p_nip_mitra,
           'p_birthdate' => $birthdate,
           'p_birthplace' => $data[0]->p_birthplace,
           'p_telp' => $data[0]->p_telp,
           'p_sex'  => $data[0]->p_sex,
           'p_ktp' => $data[0]->p_ktp,
           'p_name' => $data[0]->p_name,
           'p_hp' => $data[0]->p_hp,
           'p_telp'  => $data[0]->p_telp,
           'p_status' => $data[0]->p_status,
           'p_many_kids' => $data[0]->p_many_kids,
           'p_religion' => $data[0]->p_religion,
           'p_address' => $data[0]->p_address,
           'p_rt_rw' => $data[0]->p_rt_rw,
           'p_kel' => $data[0]->p_kel,
           'p_kecamatan' => $data[0]->p_kecamatan,
           'p_city' => $data[0]->p_city,
           'p_address_now' => $data[0]->p_address_now,
           'p_rt_rw_now' => $data[0]->p_rt_rw_now,
           'p_kel_now' => $data[0]->p_kel_now,
           'p_kecamatan_now' => $data[0]->p_kecamatan_now,
           'p_city_now' => $data[0]->p_city_now,
           'p_name_family' => $data[0]->p_name_family,
           'p_address_family' => $data[0]->p_address_family,
           'p_telp_family' => $data[0]->p_telp_family,
           'p_hp_family' => $data[0]->p_hp_family,
           'p_hubungan_family' => $data[0]->p_hubungan_family,
           'p_wife_name' => $data[0]->p_wife_name,
           'p_wife_birth' => $wifebirthdate,
           'p_wife_birthplace' => $data[0]->p_wife_birthplace,
           'p_dad_name' => $data[0]->p_dad_name,
           'p_dad_job' => $data[0]->p_dad_job,
           'p_mom_name' => $data[0]->p_mom_name,
           'p_mom_job' => $data[0]->p_mom_job,
           'p_job_now' => $data[0]->p_job_now,
           'p_weight' => $data[0]->p_weight,
           'p_height' => $data[0]->p_height,
           'p_seragam_size' => $data[0]->p_seragam_size,
           'p_celana_size'  => $data[0]->p_celana_size,
           'p_sepatu_size' => $data[0]->p_sepatu_size,
           'p_kpk' => $data[0]->p_kpk,
           'p_bu' => $data[0]->p_bu,
           'p_ktp_expired' => $data[0]->p_ktp_expired,
           'p_ktp_seumurhidup' => $data[0]->p_ktp_seumurhidup,
           'p_education' => $data[0]->p_education,
           'p_kpj_no' => $data[0]->p_kpj_no,
           'p_state' => $data[0]->p_state,
           'p_note' => $data[0]->p_note,
           'p_workdate' => $data[0]->p_workdate,
           'p_insert' => $data[0]->p_insert,
           'p_img' => $data[0]->p_img,
           'p_update' => $data[0]->p_update,
           'p_note' => $data[0]->p_note
         );
         return view('approvalpelamar.print', compact('lempar'));
    }

    public function setujuilist(Request $request){
      DB::beginTransaction();
      try {
        for ($i=0; $i < count($request->pilih); $i++) {
          $d_pekerja = d_pekerja::where('p_id',$request->pilih[$i])->where('p_status_approval', null);
          $d_pekerja->update([
            'p_status_approval' => 'Y',
            'p_date_approval' => Carbon::now()
          ]);
        }

        DB::commit();
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

    public function tolaklist(Request $request){
      DB::beginTransaction();
      try {
        for ($i=0; $i < count($request->pilih); $i++) {
          $d_pekerja = d_pekerja::where('p_id',$request->pilih[$i])->where('p_status_approval', null);
          $d_pekerja->update([
            'p_status_approval' => 'N',
            'p_date_approval' => Carbon::now()
          ]);
        }

        DB::commit();
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
