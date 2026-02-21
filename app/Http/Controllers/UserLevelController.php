<?php

namespace App\Http\Controllers;

use App\Model\UserLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use DataTables;

class UserLevelController extends Controller
{
    //

    public function get_user_levels(Request $request){
    	$user_levels = UserLevel::all();

    	return response()->json(['user_levels' => $user_levels]);
    }
}
