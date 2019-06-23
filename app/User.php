<?php

namespace App;

use App\BaseAuthModel;
use App\Data\Models\AccessLevelHierarchy;
use App\Data\Models\UserInfo;
use App\User;
use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends BaseAuthModel
{
    use Notifiable, HasApiTokens;

    protected $primaryKey = 'id';
    protected $table = 'users';
    protected $appends = [
        'team_leader',
        'operations_manager',
        'full_name',
        'is_agent',
        'has_schedule',
        'calendar',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uid', 'email', 'password', 'access_id', 'loginFlag', 'company_id', 'created_at', 'updated_at',
    ];

    protected $searchable = [
        'info.firstname',
        'info.middlename',
        'info.lastname',
        'id',
        'uid',
        'email',
        'password',
        'access_id',
        'loginFlag',
        'company_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'hierarchy', 'created_at', 'updated_at', 'deleted_at',
    ];

    /*
    |------------------------------------------------------------------------------------
    | Validations
    |------------------------------------------------------------------------------------
     */
    public static function rules($update = false, $id = null)
    {
        $commun = [
            'email' => "required|email|unique:users,email,$id",
            'password' => 'nullable|confirmed',
        ];

        if ($update) {
            return $commun;
        }

        return array_merge($commun, [
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    /*
    |------------------------------------------------------------------------------------
    | Attributes
    |------------------------------------------------------------------------------------
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower($value);
    }
    /*
    public function getAvatarAttribute($value)
    {
    if (!$value) {
    return 'http://placehold.it/160x160';
    }

    return config('variables.avatar.public').$value;
    }
    public function setAvatarAttribute($photo)
    {
    $this->attributes['avatar'] = move_file($photo, 'avatar');
    }

    |------------------------------------------------------------------------------------
    | Boot
    |------------------------------------------------------------------------------------
     */
    public static function boot()
    {
        parent::boot();
        static::updating(function ($user) {
            $original = $user->getOriginal();

            if (\Hash::check('', $user->password)) {
                $user->attributes['password'] = $original['password'];
            }
        });
    }

    public function info()
    {
        return $this->hasOne('\App\Data\Models\UserInfo', 'id', 'uid');
    }

    public function access()
    {
        return $this->hasOne('\App\Data\Models\AccessLevel', 'id', 'access_id');
    }

    public function schedule()
    {
        return $this->hasMany('\App\Data\Models\AgentSchedule', 'user_id', 'uid');
    }

    public function hierarchy()
    {
        return $this->hasOne('\App\Data\Models\AccessLevelHierarchy', 'child_id', 'uid');
    }

    public function user_info()
    {
        return $this->hasOne('\App\Data\Models\UserInfo', 'id', 'uid');
    }

    public function user_logs()
    {
        return $this->hasMany('\App\Data\Models\ActionLogs', 'user_id', 'id');
    }

    public function accesslevel()
    {
        return $this->hasOne('\App\Data\Models\AccessLevel', 'id', 'access_id');
    }

    public function getCalendarAttribute()
    {
        $events = [];
        foreach ($this->schedule as $sched) {
            $events[] = [
                'id' => $sched->id,
                'start' => $sched->start_event,
                'end' => $sched->end_event,
                'title' => $sched->title->title,
                'color' => $sched->title->color,
            ];
        }

        return [
            'events' => $events,
        ];
    }
    public function getFullNameAttribute()
    {
        $name = null;
        if (isset($this->info)) {
            $name = $this->info->full_name;
        }

        return $name;
    }

    public function getIsAgentAttribute()
    {
        return ($this->access) ? ($this->access->code === 'representative_op') ? 1 : 0 : 0;
    }

    public function getHasScheduleAttribute()
    {
        return ($this->schedule->count()) ? $this->schedule->where('start_event', '<=', Carbon::now())->where('end_event', '>=', Carbon::now())->count() : 0;
    }

    public function getTeamLeaderAttribute()
    {
        if (isset($this->hierarchy)) {
            $id = $this->hierarchy->parent_id;
        } else {
            $id = null;
        }
        if (isset($id)) {
            $info = UserInfo::find($id);
        } else {
            $info = null;
        }
        if (isset($info)) {
            $user = User::where('uid', $id)->first();
        } else {
            $user = null;
        }
        $team_leader = [];
        if (isset($info) && isset($user) && $user->access_id == 16) {
            $team_leader = [
                'id' => $info->id,
                'full_name' => $info->firstname . ' ' . $info->middlename . ' ' . $info->lastname,
                'email' => $user->email,
                'image' => $info->image,
            ];
        }
        return $team_leader;
    }

    public function getOperationsManagerAttribute()
    {
        if (isset($this->hierarchy)) {
            $tl_id = $this->hierarchy->parent_id;
        } else {
            $tl_id = null;
        }
        if (isset($tl_id)) {
            $om_id = AccessLevelHierarchy::where('child_id', $tl_id)->get()->first()->parent_id;
        }
        if (isset($om_id)) {
            $info = UserInfo::find($om_id);
        }
        if (isset($info)) {
            $user = User::where('uid', $om_id)->first();
        } else {
            $user = null;
        }
        $operations_manager = [];
        if (isset($info) && isset($user) && $user->access_id == 15) {
            $operations_manager = [
                'id' => $info->id,
                'full_name' => $info->firstname . ' ' . $info->middlename . ' ' . $info->lastname,
                'email' => $user->email,
                'image' => $info->image,
            ];
        }
        return $operations_manager;
    }

}
