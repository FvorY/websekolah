<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\d_mem;

use Auth;

use Carbon\Carbon;

use File;



class profilController extends Controller
{   
    public function __construct() {
        $this->middleware('auth');
    }
    public function index(){
        $member=d_mem::where('m_id',Auth::user()->m_id)->first();
        return view('profil.index',compact('member'));
        
    }
    public function ubahProfil($id) {
        $member=d_mem::where('m_id',$id)->first();
        return view('profil.formProfil',compact('member'));        
    }
    public function perbaruiProfil(Request $request) {   
        $member=d_mem::where('m_id',Auth::user()->m_id);
       $file = $request->file('imageUpload');
        $imgPath = "";
        $pathPic = $member->first()->m_image;

        
        if($file != ""){

            $childPath = 'image/uploads/item/'.Auth::user()->m_id;
            $path = $childPath;
            $name = 'item-'.Auth::user()->m_id.'.'.$file->getClientOriginalExtension();
            
        
            if($member->first()->m_image != ""){


                
                
                if(File::delete($pathPic)){
                    $file->move($path, $name);
                    $imgPath = $childPath.'/'.$name;                    
                }

            }
            else{                
                $file->move($path, $name);
                $imgPath = $childPath.'/'.$name;                
            }

            
      $member->update([
          'm_name'=>$request->name,
          'm_birth_tgl'=>$request->tanggal_lahir,
          'm_addr'=>$request->alamat,
          'm_image'=>$imgPath,
      ]);
        }
        else{
           // return "file kosong";            
      $request->tanggal_lahir=date('Y-m-d', strtotime($request->tanggal_lahir));            
      $member->update([
          'm_name'=>$request->name,
          'm_birth_tgl'=>$request->tanggal_lahir,
          'm_addr'=>$request->alamat,
          'm_image'=>$pathPic,
      ]);
            
            
            
        }  
        
        
      
      
    $response = [
                'status' => 'berhasil',                
                ];

    return json_encode($response);   
        
    }
    public function ubahPassword($id) {
        $member=d_mem::where('m_id',Auth::user()->m_id)->first();        
        if(count($member)!=0){
            return view('profil.formPassword',compact('member'));
        }
    }
    public function perbaruiPassword(Request $request) {        
//         $rules = [
//            'Kata_Sandi_Lama' => 'required',
//            'Kata_Sandi_Baru' => 'required',
//            'Konfirmasi_Kata_Sandi' => 'required'
//        ];
//
//        $validation = Validator::make($request->all(), $rules);
//        
//        if ($validation->fails()) {
//           return response()->json([
//                        'success' => false,
//                        'errors' => $validation->errors()->toArray()
//            ]);
//        }

        $mem = d_mem::find(Auth::user()->m_id);        
        $check = sha1(md5('passwordAllah') + $request->Kata_Sandi_Lama);
        if ($mem->m_passwd != $check) {
            
        }
        else if ( $request->Kata_Sandi_Baru!=$request->Konfirmasi_Kata_Sandi) {
            
        }

        $mem->m_passwd = sha1(md5('passwordAllah') + $request->Kata_Sandi_Baru);
        if ($mem->save()) {            
             return response()->json([
                        'success' => true,        
            ]);
        }
        }
    
}
