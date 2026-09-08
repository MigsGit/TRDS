<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class CheckSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        session_start();

        // dd($_SESSION);
        if(!isset($_SESSION['rapidx_user_id'])){
            return redirect('../');
        }

        $user = DB::table('users')
        ->join('user_access_modules', 'users.id', '=', 'user_access_modules.users_id')
        ->where('rapidx_emp_id', $_SESSION['rapidx_user_id'])
        ->first();
        $userAccess = DB::connection('rapidx')->select('SELECT users.*,user_accesses.
            module_id,departments.department_name,departments.department_group
            FROM  users
            LEFT JOIN user_accesses user_accesses ON user_accesses.user_id = users.id
            LEFT JOIN departments departments ON departments.department_id = users.department_id
            WHERE 1=1
            AND users.id = "'.$_SESSION['rapidx_user_id'].'"
            AND users.user_stat = 1
            AND user_accesses.module_id = 54'
        );

        if($userAccess == null){
            return redirect('../');
        }
        if($user == null){
            return redirect('../');
        }
        if ($user) {
            $user->name = $_SESSION['rapidx_name'];
        }
        session(['global_user' => $user]);
        View::share('globalUser', $user);
        // return $next($request);
        return $next($request);
    }
}
