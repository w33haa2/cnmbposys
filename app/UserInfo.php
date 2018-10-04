<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\UserBenefit;

class UserInfo extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstname', 'middlename', 'lastname', 'birthdate', 'gender', 'contact_number', 'address', 'image', 'salary_rate','image_ext', 'status', 'hired_date', 'separation_date', 'excel_hash'
    ];
    /**
     * Set the user's firstname.
     *
     * @param  string  $value
     * @return void
     */
    public function setFirstnameAttribute($value)
    {
        $this->attributes['firstname'] = ucwords($value);
    }
    
    /**
     * Set the user's middlename.
     *
     * @param  string  $value
     * @return void
     */
    public function setMiddlenameAttribute($value)
    {
        $this->attributes['middlename'] = ucwords($value);
    }
    
    /**
     * Set the user's middlename.
     *
     * @param  string  $value
     * @return void
     */
    public function setLastnameAttribute($value)
    {
        $this->attributes['lastname'] = ucwords($value);
    }

    public function user() {
        return $this->hasOne('\App\User', 'uid', 'id');
    }

    public function benefits() {
        return $this->hasMany('\App\UserBenefit', 'user_info_id', 'id');
    }

    public function getAllEmployee(){
        $query = DB::table('user_infos')
        ->join('users','users.uid','=','user_infos.id')
        ->join('user_benefits','user_benefits.user_info_id','=','user_infos.id')
        ->join('access_levels','access_levels.id','=','users.access_id')
        ->join('access_level_hierarchies','access_level_hierarchies.child_id','=','user_infos.id')
        ->select('user_infos.id','user_infos.firstname','user_infos.middlename','user_infos.lastname','user_infos.status','user_infos.gender','user_infos.birthdate','user_infos.address','users.email','user_infos.contact_number',DB::raw('max(case when user_benefits.benefit_id = 1 then id_number end) as col1,max(case when user_benefits.benefit_id = 2 then id_number end) as col2,max(case when user_benefits.benefit_id = 3 then id_number end) as col3,max(case when user_benefits.benefit_id = 4 then id_number end) as col4'),'access_levels.name','user_infos.hired_date','user_infos.separation_date')
        ->groupBy('user_infos.id')
        ->orderBy('user_infos.id','asc');
        return $query;
    }

    public function getExcelTemplate(){
        $dbraw = 'concat("") as c1,concat("") as c2,concat("") as c3,concat("") as c4,concat("") as c5,concat("") as c6';
        $dbraw .= ',concat("") as c11,concat("") as c2,concat("") as c13,concat("") as c4,concat("") as c15,concat("") as c16';
        $dbraw .= ',concat("") as c21,concat("") as c22,concat("")  as c23,concat("")  as c24,concat("")  as c25,concat("")  as c26';

        $query = DB::table('excel_functions')
        ->where('id','=',1)
        ->select(DB::raw($dbraw),'formula1','formula2','formula3')
        ->orderBy('id','asc');
        return $query;
    }

    public function getParentsWithId(){
        $query = DB::table('users')
        ->join('user_infos', 'user_infos.id','=','users.uid')
        ->join('access_levels', 'access_levels.id','=','users.access_id')
        ->select('user_infos.id',DB::raw('CONCAT_WS(" ",user_infos.firstname,user_infos.lastname)'))
        ->orderBy('user_infos.id','asc');
        return $query;
    }
}
