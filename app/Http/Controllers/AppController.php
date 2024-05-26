<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Apps;
use Session;
use DB;

class AppController extends Controller
{
    public function index(Request $request){
        $value = Session::get('admin');
        if(!empty($value)){
           $data['apps'] = Apps::orderby('id','DESC')->get();        
		   return view('admin.app.index',$data);
        }else{
           return redirect('/');
        }
	}

	public function add(Request $request){
    	$this->validate($request,[
         'name' =>'required',
         
       ]);
    	
    	$apps = new Apps();
    	$apps->name = ucfirst($request->name);
    	$apps->save();

    	Session::flash('message',"App Added Successfully"); 
        Session::flash('alert-class', 'alert-success');
        return redirect()->back();
    }


    public function edit(Request $request,$id){
    	$main_id = $id;
        $data = Apps::where('id', $main_id)->first();
        return $data; 	
    }

    public function edit_app(Request $request){
    	
    	$this->validate($request,[
         'name'=>'required',
      
       ]);


    	$id = $request->app_id;
			
    	

    	$lang = Apps::find($id);
    	$lang->name = ucfirst($request->name);
    	$lang->update();

    	Session::flash('message',"App Edit Successfully"); 
        Session::flash('alert-class', 'alert-success');
        return redirect()->back();
    }

    public function delete(Request $request,$id){

        $data=$id;
        return $data;   

    }

    public function deleteapp(Request $request){

        $id = $request->appid;
        
        $res = Apps::where('id',$id)->delete();
        Session::flash('message',"App Delete Successfully"); 
        return redirect()->back(); 
    }
    
    public function play_status(Request $request,$id){

        $res = Apps::find($id);
        if($res->play_status == '1'){
            $status = 0;
        }else{
            $status = 1;
        }
        $res->play_status = $status;
        $res->update();
    }
}
