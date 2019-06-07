<?php

namespace App\Data\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\CanResetPassword;
use App\Data\Models\UserInfo;
use App\Data\Models\AccessLevelHierarchy;
use App\Data\Models\BaseModel;
use Illuminate\Support\Facades\Crypt;

class UsersData extends BaseModel
{
    use Notifiable;
    protected $primaryKey = 'id';
    protected $table = 'users';
    protected $appends = [
       'full_name','lname','fname','mname','position','address','contact','gender','image','suffix','status','type','birthdate','parent_id','child_id','crypted_id','head_name','status_color',
    ];

    public $status_color = [
        'active' => 'bg-primary',
        'new_hired' => 'bg-success',
        'inactive' => 'bg-danger',
        'terminated' => 'bg-danger'
    ];


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uid','email', 'password', 'access_id','loginFlag','company_id','created_at','updated_at'
    ];

    protected $searchable = [
        'user_info.firstname',
        'user_info.middlename',
        'user_info.lastname',
        'id',
        'uid',
        'email', 
        'password', 
        'access_id',
        'loginFlag',
        'company_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'user_info','password','deleted_at', 'remember_token', 'hierarchy','accesslevel','accesslevelhierarchy','loginFlag','created_at','updated_at',
    ];

  
    public function user_logs() {
        return $this->hasMany('\App\Data\Models\ActionLogs', 'user_id', 'id');
    }

    public function user_info() {
        return $this->hasOne('\App\Data\Models\UserInfo', 'id', 'uid');
    }
    public function benefits() {
        return $this->hasMany('\App\Data\Models\UserBenefit', 'user_info_id', 'uid');
    }
    
    public function accesslevel(){
       return $this->hasOne('\App\Data\Models\AccessLevel', 'id', 'access_id');
    }
    public function accesslevelhierarchy(){
        return $this->hasOne('\App\Data\Models\AccessLevelHierarchy', 'child_id', 'uid');
     }
    public function getFnameAttribute(){
        $name = null;
        if(isset($this->user_info)){
            $name = $this->user_info->firstname;
        }
        
        return $name;
    }
    public function getlnameAttribute(){
        $name = null;
        if(isset($this->user_info)){
            $name = $this->user_info->lastname;
        }
        
        return $name;
    }
    public function getMnameAttribute(){
        $name = null;
        if(isset($this->user_info)){
            $name = $this->user_info->middlename;
        }
        
        return $name;
    }
    public function getSuffixAttribute(){
        $name = null;
        if(isset($this->user_info)){
            $name = $this->user_info->suffix;
        }
        
        return $name;
    }
    
    public function getPositionAttribute(){
        $name = null;
        if(isset($this->accesslevel)){
            $name = $this->accesslevel->name;
        }
        
        return $name;
    }
    public function getParentidAttribute(){
        $name = null;
        if(isset($this->accesslevelhierarchy)){
            $name = $this->accesslevelhierarchy->parent_id;
        }
        
        return $name;
    }
    public function getHeadNameAttribute(){
        $name = null;
        if(isset($this->accesslevelhierarchy)){
            if($this->accesslevelhierarchy->parent_id){
                $head_details = UserInfo::find($this->accesslevelhierarchy->parent_id);
                $name = $head_details->firstname." ".$head_details->lastname;
            }else{
                $name = null;
            }
        }
        
        return $name;
    }
    public function getChildidAttribute(){
        $name = null;
        if(isset($this->accesslevelhierarchy)){
            $name = $this->accesslevelhierarchy->child_id;
        }
        
        return $name;
    }
    public function getAddressAttribute(){
        $name = null;
        if(isset($this->user_info)){
            $name = $this->user_info->address;
        }
        
        return $name;
    }
    public function getContactAttribute(){
        $name = null;
        if(isset($this->user_info)){
            $name = $this->user_info->contact_number;
        }
        
        return $name;
    }
    public function getGenderAttribute(){
        $name = null;
        if(isset($this->user_info)){
            $name = $this->user_info->gender;
        }
        
        return $name;
    }
    public function getImageAttribute(){
        $name = null;
        if(isset($this->user_info)){
            $name = $this->user_info->image_url;
        }
        
        return $name;
    }

    public function getStatusAttribute(){
        $name = null;
        if(isset($this->user_info)){
            if(strtolower($this->user_info->status) == 'new_hired'){
                $name = strtoupper('NEW');
            }else{
                $name = strtoupper($this->user_info->status);
            }
        }
        
        return $name;
    }
    public function getTypeAttribute(){
        $name = null;
        if(isset($this->user_info)){  
                $name = strtoupper($this->user_info->type);
        }
        
        return $name;
    }
    public function getStatusColorAttribute(){
        $name = null;
        if(isset($this->user_info)){
            if($this->user_info->status!=null){
                $name = $this->status_color[strtolower($this->user_info->status)];
            }
           
        }
        
        return $name;
    }

    public function getBirthdateAttribute(){
        $name = null;
        if(isset($this->user_info)){
            $name = $this->user_info->birthdate;
        }
        
        return $name;
    }
    public function getCryptedIdAttribute(){
        $name = null;
        if(isset($this->user_info)){
            $name = Crypt::encrypt($this->user_info->id);
        }
        
        return $name;
    }
    public function getFullNameAttribute(){
        $name = null;
        if(isset($this->user_info)){
            $name = $this->user_info->firstname." ".$this->user_info->middlename." ".$this->user_info->lastname." ".$this->user_info->suffix;
        }
        
        return $name;
    }
}