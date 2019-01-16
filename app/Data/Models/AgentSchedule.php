<?php

namespace App\Data\Models;

use App\Data\Models\BaseModel;

class AgentSchedule extends BaseModel
{
    protected $primaryKey = 'id';
    protected $table = 'agent_schedules';
    protected $fillable = [
        'user_id',
        'title_id',
        'start_event',
        'end_event',
    ];

    protected $searchable = [
        'user_id',
        'title_id',
        'start_event',
        'end_event', 
    ];

    public $timestamps = true;

    protected $rules = [
        'user_id' => 'sometimes|required|numeric',
        'title_id' => 'sometimes|required|numeric',
        'start_event' => 'sometimes|required|date',
        'end_event' => 'sometimes|required|date',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function agentschedule(){
        return $this->belongsTo('App\User');
    }

    public function user_info(){
        return $this->hasOne('App\Data\Models\UserInfo',"id", "user_id" );
    }

    public function title(){
        return $this->hasOne('App\Data\Models\EventTitle',"id", "title_id" );
    }
}
