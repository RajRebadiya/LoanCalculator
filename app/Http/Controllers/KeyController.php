<?php

namespace App\Http\Controllers;
use App\Models\Key;
use App\Models\Setting;

use Session;
use DB;
use Illuminate\Http\Request;

class KeyController extends Controller
{
    public function index(Request $request){
        $value = Session::get('admin');
        if(!empty($value)){
            $data['setting'] = Setting::find(1);
    		$data['key'] = Key::orderby('id','ASC')->get();        
    		return view('admin.key.index',$data);
        }else{
           return redirect('/');
        }
    	
	}

	public function add(Request $request){
    	$this->validate($request,[
         'key' =>'required',
         
       ]);
    	
    	$key = new Key();
    	$key->openai_key = $request->key;
    	$key->status = 1;
    	$key->save();

    	Session::flash('message',"Key Added Successfully"); 
        Session::flash('alert-class', 'alert-success');
        return redirect()->back();
    }


    public function edit(Request $request,$id){
    	$main_id = $id;
        $data = Key::where('id', $main_id)->first();
        return $data; 	
    }

    public function edit_key(Request $request){
    	
    	$this->validate($request,[
         'key'=>'required',
      
       ]);

    	$id = $request->key_id;
    	$lang = Key::find($id);
    	$lang->openai_key = $request->key;
    	$lang->update();

    	Session::flash('message',"Key Edit Successfully"); 
        Session::flash('alert-class', 'alert-success');
       return redirect()->back();
    }

    public function delete(Request $request,$id){
        $data=$id;
        return $data;   
    }

    public function deletekey(Request $request){

        $id = $request->keyid;
       
        $res = Key::where('id',$id)->delete();
        Session::flash('message',"Key Delete Successfully"); 
        return redirect()->back();
    }
    
    public function check_key(Request $request,$id){
        $data_key = Key::find($id);
        $OPENAI_API_KEY = $data_key->openai_key;
        $q='Name of Indian PM';

        $dTemperature = 0.9;
        $iMaxTokens = 300;
        $top_p = 1;
        $frequency_penalty = 0.0;
        $presence_penalty = 0.6;

        $sModel = "text-davinci-003";
        $prompt = $q;
        $ch = curl_init();
        $headers  = [
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: Bearer ' . $OPENAI_API_KEY . ''
        ];

        $postData = [
            'model' => $sModel,
            'prompt' => str_replace('"', '', $prompt),
            'temperature' => $dTemperature,
            'max_tokens' => $iMaxTokens,
            'top_p' => $top_p,
            'frequency_penalty' => $frequency_penalty,
            'presence_penalty' => $presence_penalty,
            'stop' => '[" Human:", " AI:"]',
        ];

        curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/completions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));

        $result = curl_exec($ch);
       
        $decoded_json = json_decode($result, true);
       
        if(!empty($decoded_json['choices'][0]['text'])){
            $ans = $decoded_json['choices'][0]['text'];
            $ans_data = trim($ans);
        }
        else if($decoded_json['error']['message']){
             $ans_data = $decoded_json['error']['message'];
        }

        return $ans_data;
        
    }
    
    
    public function change_status(Request $request,$id){
        
        $data = Key::where('id',$id)->first();
        $status = $data->status;
      
        
        if($status == 0){
            $val=1;
        }else{
            $val=0;
        }
        $key_status = Key::find($id);
        $key_status->status  = $val;
        $key_status->save();
       
    }
}
