<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\users;
use App\Models\Apps;
use App\Models\Setting;
use App\Models\Key;

use Auth;
use Session;


class ChatController extends Controller
{
    public function index(Request $request){
        
        $value = Session::get('admin');
        if(!empty($value)){
           if(isset($request->app) && isset($request->date)){
                $date = $request->date;
                $data['chat'] = Chat::where('app_id',$request->app)->whereDate('created_at',$date)->orderby('id','DESC')->paginate(10);
                $data['app_id'] = $request->app;
                $data['date'] = $request->date;
            if($request->app == 'all'){
               $data['chat'] = Chat::orderby('id','DESC')->paginate(10);
               $data['date'] = '';
            }
            }else if(isset($request->app)){
                $data['chat'] = Chat::where('app_id',$request->app)->orderby('id','DESC')->paginate(10);
                $data['app_id'] = $request->app;
                if($request->app == 'all'){
                    $data['chat'] = Chat::orderby('id','DESC')->paginate(10);
                $data['date'] = ''; 
                }
            }
       
        else{
            $data['chat'] = Chat::orderby('id','DESC')->paginate(10);
        }
            $data['app'] = Apps::orderby('id','DESC')->get();
            return view('admin.chat.index',$data);
        }else{
           return redirect('/');
        }
        
    }
    
    public function delete_question(Request $request){
        if($request->check_btn == 1){
            $data = $request->multi_question_id;
            $dd= rtrim($data, ',');
            $res = Chat::whereIn('id',explode(",",$dd))->delete();
            Session::flash('message',"Delete Successfully"); 
            return redirect()->back();
            
        }else{
            $id = $request->question_id;
            $res = Chat::where('id',$id)->delete();
            Session::flash('message',"Delete Successfully"); 
            return redirect()->back();
        }
    }
    
    public function get_answer(Request $request,$id){
        $ans = Chat::find($id);
        $ans_data = $ans->answer;
       
        $data = nl2br($ans_data);
        $string =preg_replace('/^(?:<br\s*\/?>\s*)+/', '', $data);
        $str =  str_replace("<br>", "", $string);
        echo $str;
       
    }
    
    public function chat_detail(Request $request,$id){
        $data['detail'] = Chat::where('id',$id)->first();
        return view('admin.chat.detail',$data);
    }
    
    //this is for web chatgpt
    
    public function web_chat(Request $request){
        return view('front.chat');
    }
    
    public function web_answer(Request $request){
        $q = $request->txt;
        $app_id = 9;
       
        
        $setting = Setting::find(1);
        
        if($setting->openai_set == 'on'){
            $data = $this->getanswer_web($q,$app_id);
                   
        }else{

            $data = $this->getanswer_opeanai_web($q,$app_id);
        }
         return $data;
        // return redirect()->back();
    }
    
    public function getanswer_web($q,$id){
   
    	$postData = [
    	"ads_country"=>"",
    	"response_length"=>"m",
    	"plan_type"=>"",
    	"invoice_no"=>"inv001",
    	"ads_id"=>"",
    	"q"=>$q,
    	"package_name"=>"com.chat.gpt.magic",
    	"price"=>"",
    	"vrsn"=>"1.1.1",
    	"ads"=>"no",
    	"purchased_from"=>""
    	];
    	$headers  = [
    	            'Accept: application/json',
    	            'Content-Type: application/json',
    	        ];
    
    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, 'http://chat.eyuva.xyz/request/');
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    	curl_setopt($ch, CURLOPT_POST, 1);
    	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    
    	$result = curl_exec($ch);
    	        
    	$decoded_json = json_decode($result, true);
    	$ans = $decoded_json['ans'];
        if(!empty($ans)){
            $ans_data = trim($ans);
        }else{
            $ans_data ='';
        }
    	$chat_data = Chat::where('query',$q)->first();
    	if(empty($chat_data)){
    		$chat = new Chat();
    	    $chat->query = $q;
    	    $chat->answer = $ans_data;
            $chat->app_id = $id;
            $chat->key = 'Default';
    	    $chat->save();  
    	}
    	 return $ans;
    }
    
       public function getanswer_opeanai_web($q,$id){
          

   	    $setting = Setting::find(1);
        $key_data = Key::where('id',$setting->openai_key_id)->first();

        $OPENAI_API_KEY=$key_data->openai_key; 

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
        $ans = $decoded_json['choices'][0]['text'];
    
        $chat_data = Chat::where('query',$q)->first();
		if(empty($chat_data)){
			$chat = new Chat();
		    $chat->query = $q;
		    $chat->answer = trim($ans);
            $chat->app_id = $id;
            $chat->key = $key_data->id;
		    $chat->save();  
		}
        return $ans;
    }
    
    public function get_query(Request $request,$id){
        $ans = Chat::find($id);
        $data = $ans->query;
        return $data;
    }
    
    public function privacy_policy(){
        return view('privacy_policy');
    }
    
    public function delete_account(){
        return view('delete_account');
    }
    
     public function delete_user_account(Request $req)
    {
        // Validate the request data
        $validator = $req->validate([
            'email' => 'required|email',
            'name' => 'required|string',
            'password' => 'required|string',
            'mobile' => 'required|numeric|digits:10',
        ]);

        
        // Retrieve the user by email
        $user = users::where('email', $req->email)->first();
        if($user){

             if ($req->password == $user->password) {
                // Additional validation of name and mobile could be done here
    
                // Delete the user account
                $user->delete();
    
                return redirect()->back()->with('success', 'User account deleted successfully');
            } else {
                return redirect()->back()->with('error', 'Invalid email or password');
            }
        } else {
            return redirect()->back()->with('error', 'Wrong Credintials');
        }
    } 
}
