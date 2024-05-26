<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Apps;
use App\Models\Key;
use Session;


class AdminController extends Controller
{
    public function index(){
       $value = Session::get('admin');
       if(!empty($value)){
           $data['app'] = Apps::count();
           return view('admin.dashboard',$data);
       }else{
           return redirect('/');
       }
    }

    public function web_service(Request $request){
        $value = Session::get('admin');
       if(!empty($value)){
           return view('admin.webservice');
       }else{
           return redirect('/');
       }
       
    }

    public function setting(){
         $value = Session::get('admin');
           if(!empty($value)){
               $data['setting'] = Setting::find(1);
               $data['key']=Key::orderby('id','DESC')->where('status',1)->get();
    	       return view('admin.setting',$data);
           }else{
               return redirect('/');
           }
    	
    }

    public function openai_setting(Request $request){
        
        if(isset($request->chat)){
            $openai_key=$request->keys;
        	if($request->api_sets == 'on'){
        		$openai_set=$request->api_sets;
        	}else{
        		$openai_set='off';
        	}

        	$setting = Setting::find(1);
        	$setting->openai_chatkey_id = $openai_key;
        	$setting->openai_chat = $openai_set;
        	$setting->save();
        }else{
            $openai_key=$request->key;
        	$token=$request->token;
        	if($request->api_set=='on'){
        		$openai_set=$request->api_set;
        	}else{
        		$openai_set='off';
        	}
    
        	$setting = Setting::find(1);
        	$setting->openai_key_id = $openai_key;
        	$setting->openai_set = $openai_set;
            $setting->bearer_token = $token;
        	$setting->save();
        }
    	Session::flash('message',"Data Save Successfully"); 
        return redirect()->back();
    }

    
}
