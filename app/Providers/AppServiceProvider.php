<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // session()->flush();

        // if(session_status() == PHP_SESSION_NONE){
        //     session_start(); // Start the session if it hasn't been started yet
        // }
        // // dd($_SESSION);
        // if(isset($_SESSION['rapidx_user_id'])){
        //     $user = DB::table('users')
        //                 ->where('rapidx_emp_id', $_SESSION['rapidx_user_id'])
        //                 ->first();
        //     // dd($user);
        //     // View::share('globalPosition', optional($user)->position);
        //     session(['global_user' => $user]);
        //     // dd(session()->all());

        //     View::share('globalUser', $user);
        // }
    }
}
