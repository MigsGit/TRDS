<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TrainingAttendanceController extends Controller
{
    public function view_training_attendance(Request $request){
        return 'true' ;
        try {
            return response()->json(['is_success' => 'true']);
        } catch (Exception $e) {
            throw $e;
        }
    }
}
