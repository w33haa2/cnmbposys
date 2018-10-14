<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\User;
use App\UserInfo;
use App\UserBenefit;
use App\AccessLevelHierarchy;
use App\AccessLevel;
use Mail;
use Carbon\Carbon;


class EmployeeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('loginVerif');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user="";
        $userinfo="";
        $access_level_hierarchy="";
        $email="";
        $fullname_hash = strtolower($request->first_name.$request->middle_name.$request->last_name);
        $excel_hash = UserInfo::all()->pluck('excel_hash')->toArray();
        $admin_designation = "required";
        $role = $request->role;
        if($role==1){
            $admin_designation="";
        }
        

        ////////////////////////if
                if($request->action=='add'){
                    $userinfo = new UserInfo;
                    $user = new User;
                    $access_level_hierarchy = new AccessLevelHierarchy;
                    $email = 'required|unique:users|email';
                    if(in_array($fullname_hash,$excel_hash)){
                        return response()->json(['errors'=>['first_name'=>'Name Already Exist.','middle_name'=>'Name Already Exist.','last_name'=>'Name Already Exist.']]);
                    }
                }else if($request->action=='edit'){
                    $userinfo = UserInfo::find($request->id);
                    $user = User::find($request->id);
                    $access_level_hierarchy = AccessLevelHierarchy::where('child_id','=',$request->id)->first();
                    if($user->email == $request->email){
                        $email = 'required|email';
                    }else{
                        $email = 'required|unique:users|email';
                    }
                    if($userinfo->excel_hash != $fullname_hash){
                        if(in_array($fullname_hash,$excel_hash)){
                            return response()->json(['errors'=>['first_name'=>'Name Already Exist.','middle_name'=>'Name Already Exist.','last_name'=>'Name Already Exist.']]);
                        }
                    }
                }
        ////////////////////////endif

        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'middle_name' => 'required',
            'address' => 'required',
            'birthdate' => 'required',
            'gender' => 'required',
            'contact' => 'required',
            'email' => $email,
            'position' => 'required',
            // 'salary' => 'required',
            'designation'=>$admin_designation,
            'hired_date'=>'required',
            'photo'=>'image|max:2000',
        ]);


        if ($validator->fails())
        {
            return response()->json(['errors'=>$validator->errors()]);
        }

        $userinfo->firstname=$request->first_name;
        $userinfo->lastname=$request->last_name;
        $userinfo->middlename=$request->middle_name;
        $userinfo->address=$request->address;
        $userinfo->birthdate=$request->birthdate;
        $userinfo->gender=$request->gender;
        $userinfo->salary_rate=$request->salary;
        $userinfo->status="Active";
        $userinfo->contact_number=$request->contact;
        $userinfo->hired_date=$request->hired_date;
        $userinfo->excel_hash = $fullname_hash;
        if($request->hasFile('photo')){
            $binaryfile = file_get_contents($_FILES['photo']['tmp_name']);
            $userinfo->image_ext= explode(".", strtolower($_FILES['photo']['name']))[1];
            $userinfo->image = 'data:image/'.explode(".", strtolower($_FILES['photo']['name']))[1].';base64, '.base64_encode($binaryfile);
            $userinfo->save();
        }
        if($request->captured_photo){
            $userinfo->image_ext='jpg';
            $userinfo->image = $request->captured_photo;
            $userinfo->save();
        }
        $userinfo->save();

        
        $user->uid= $userinfo->id;
        $user->email = $request->email;
        $user->password = $userinfo->firstname.$userinfo->lastname;
        $user->access_id = $request->position;
        $user->save();

        $obj_benefit=[];
        
        if($request->action=='add'){
            for($l=0;$l<4;$l++){
                $obj_benefit[]=['user_info_id'=>$userinfo->id,'benefit_id'=>$l+1,'id_number'=>$request->id_number[$l]];
            }
            UserBenefit::insert($obj_benefit);
            $access_level_hierarchy->child_id = $userinfo->id;
        }else if($request->action=='edit'){
            for($l=0;$l<4;$l++){
                UserBenefit::where('user_info_id',$request->id)
                ->where('benefit_id',$l+1)
                ->update(['id_number'=>$request->id_number[$l]]);
            }
            $access_level_hierarchy->child_id = $request->id;
        }
        if($request->position>1){
            $access_level_hierarchy->parent_id = $request->designation;
        }else if($request->position==1){
            $access_level_hierarchy->parent_id = null;
        }
        
        $check = $access_level_hierarchy->save();
        if($check){
            return response()->json(['success'=>'Record is successfully added','info'=>$userinfo,'user'=>$user,'benefit'=>UserBenefit::where('user_info_id',$request->id)->get()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }




    //fetching designation dynamic data

    function fetch(Request $request)
    {
        $position = $request->get('applicant_position');
        $userposition = $request->get('user_position'); 
        $eid = $request->get('employee_id'); 
        $accesslevel = new AccessLevel;
        $parentLevel = $accesslevel->getParentLevel($position);
        
        // echo '<option>here'.$position.'</option>';
        $data = DB::table('users')
                    ->join('access_levels','users.access_id','=','access_levels.id')
                    ->join('user_infos','user_infos.id','=','users.uid')
                    ->select('user_infos.id','user_infos.firstname','user_infos.lastname','user_infos.middlename','access_levels.name as accesslevelname')
                    ->where([['access_levels.id','=',$parentLevel],['user_infos.status','!=','Terminated']])
                    ->get();
        $output="";
        if($data->count()>0){
            $output.= '<option value="">Select '.$data[0]->accesslevelname.'</option>';
            foreach($data as $datum){
                if($datum->id!=$eid){
                    $output .= '<option value="'.$datum->id.'">'.$datum->lastname.", ".$datum->firstname." ".$datum->middlename.'</option>';
                }
            }
        }else{
            $val = "";
            if($userposition==1){
                $val=null;
            }
            $output .= '<option value="'.$val.'">NA</option>';
        }
        
        echo $output;

    }

    

    function fetch_employee_data(Request $request){
        $id = $request->id;
        $data=[];
        $data['userinfo'] = UserInfo::find($id);
        $data['user'] = User::where('uid','=',$id)->get();
        $data['userbenefit'] = UserBenefit::where('user_info_id','=',$id)->get();
        $data['accesslevelhierarchy'] = AccessLevelHierarchy::where('child_id','=',$id)->get();
        return json_encode($data);
    }

   

    public function update_status(Request $request){
        $user = UserInfo::where('id', $request->status_id)->first();
        $user->status = $request->status_data;
        $user->separation_date = Carbon::now();
        $user->save();
        $account = User::where('uid', '=',$request->status_id)
                   ->first(); 
        $userInfo = UserInfo::where('id', '=',$request->status_id)
                   ->first(); 
        $data = array(
               'name' => $userInfo->firstname,
               'email' => $account->email
                    );
        if($request->status_data=="Active"){
         Mail::send([],[],function($message) use ($data){
                $message->to($data['email'],'Hello Mr/Mrs '.$data['name'])->subject('Activation Of Account of Mr/Mrs '.$data['name'])
                ->setBody('Hello Mr/Mrs '.$data['name'].', This is to inform you that your account has been activated by the HR. Thank You!. ');
                $message->from('bfjax5@gmail.com','CNM BPO');
                 });     
        }else{
         Mail::send([],[],function($message) use ($data){
                $message->to($data['email'],'Hello Mr/Mrs '.$data['name'])->subject('Termination Mail of Mr/Mrs '.$data['name'])
                ->setBody('Hello Mr/Mrs '.$data['name'].', This is to inform you that your account has been terminated by the HR');
                $message->from('bfjax5@gmail.com','CNM BPO');
                 }); 
        }
    }

    public function get_status(Request $request){
        $user = UserInfo::where('id', $request->id)->get();
        return $user;
    }
    
    
}
