<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (!defined('ADMIN')) {
           define('ADMIN', config('variables.APP_ADMIN', 'admin'));
        }
        require_once base_path('resources/macros/form.php');
        Schema::defaultStringLength(191);



        //page data onload


        \View::composer(['admin.partials.menu','admin.partials.topbar','admin.schedule.rta','admin.dashboard.rta','admin.report.rta','admin.dashboard.tl','admin.report.tl','admin.incident_report.*', 'admin.rtaeventrequest.*'],function($view){
                //topbar, sidenav,
            $id = auth()->user()->id;
            $access_level = auth()->user()->access_id;
            $user = \App\Data\Models\UserInfo::find($id);
            $view->with('pageOnload',$user)->with('access_id',$access_level);
            // ->with('menu_items', $menu);
        });


        //end page data onload
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}