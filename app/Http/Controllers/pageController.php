<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Data\Models\UserBenefit;
use App\Data\Models\AccessLevel;
use App\Data\Models\AccessLevelHierarchy;
use App\Data\Models\UserInfo;
use App\User;

class pageController extends Controller
{

    
    public function __construct()
    {
        $this->middleware('loginVerif');
        $this->middleware('pageAccess');
    }


    public function dashboard(){
        $id = auth()->user()->id;
        $access_level = auth()->user()->access_id;
        $role = AccessLevel::find($access_level);
        $profile = UserInfo::with('benefits')->find($id);
        $user = User::find($id);
        $emp = AccessLevelHierarchy::with('childInfo.user.access')->orderBy('parent_id')->get();

        $userInfo = AccessLevel::all();

        $position = '';
        switch($access_level){
            case 1:
            case 2:
            case 3:
            case 12:
            case 13:
            case 16:
                $view = 'admin.dashboard.hr';
            break;
            case 14:
                $view = 'admin.dashboard.rta';
            break;
            case 17:
                $view = 'admin.dashboard.agent';
            break;
        }
        return view($view, compact('profile', 'role', 'user', 'userInfo', 'emp'));
    }

    public function agent(){
        return view('admin.dashboard.agent');
    }
    public function schedule(){
        return view('admin.schedule.rta');
    }

    public function rtaschedule(){
        return view('admin.schedule.rta');
    }

    public function rtadashboard(){
        return view('admin.dashboard.rta');
    }

    public function rtareport(){
        $id = auth()->user()->id;
        $access_level = auth()->user()->access_id;
        $role = AccessLevel::find($access_level);
        $profile = UserInfo::with('benefits')->find($id);
        $user = User::find($id);
        $emp = AccessLevelHierarchy::with('childInfo.user.access')->orderBy('parent_id')->get();

        $userInfo = AccessLevel::all();

        $position = '';
        switch($access_level){
            case 17:
                $view = 'admin.report.agent';
            break;
            default:
                $view = 'admin.report.rta';
            break;
        }
        return view($view);
    }

    public function tldashboard(){
        return view('admin.dashboard.tl');
    }

    public function tlreport(){
        return view('admin.report.tl');
    }

    public function rtaeventrequest(){
        $id = auth()->user()->id;
        $access_level = auth()->user()->access_id;
        $role = AccessLevel::find($access_level);
        $profile = UserInfo::with('benefits')->find($id);
        $user = User::find($id);
        $emp = AccessLevelHierarchy::with('childInfo.user.access')->orderBy('parent_id')->get();

        $userInfo = AccessLevel::all();

        $position = '';
        switch($access_level){
            case 17:
                $view = 'admin.event_request.agent';
            break;
            default:
                $view = 'admin.event_request.rta';
            break;
        }
        return view($view);
        return view('admin.event_request.rta');
    }
    public function incident_report(){
        $id = auth()->user()->id;
        $access_level = auth()->user()->access_id;
        $role = AccessLevel::find($access_level);
        $profile = UserInfo::with('benefits')->find($id);
        $user = User::find($id);
        $emp = AccessLevelHierarchy::with('childInfo.user.access')->orderBy('parent_id')->get();

        $userInfo = AccessLevel::all();

        $position = '';
        switch($access_level){
            case 17:
                $view = 'admin.incident_report.agent';
            break;
            default:
                $view = 'admin.incident_report.index';
            break;
        }
        return view($view);
    }
    
    public function action_logs(){
        // $id = auth()->user()->id;
        // $access_level = auth()->user()->access_id;
        // $position = '';
        // switch($access_level){
        //     case 1:
        //     case 2:
        //     case 3:
        //         $position = 'hr';
        //     break;
        //     case 12:
        //     case 13:
        //     case 14:
        //         $position = 'rta';
        //     break;
        // }
        return view('admin.action_log.index');
    }
}