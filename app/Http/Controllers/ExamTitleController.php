<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use DataTables;

use App\Model\ExamTitle;

class ExamTitleController extends Controller
{
    public function viewExamTitle(){
        $examTitles = ExamTitle::where('logdel', 0)->get();

        return DataTables::of($examTitles)
        ->addColumn('action', function($examTitle){
            $result =   '<center>';

            if($examTitle->status == 0){
                $result .= '<button type="button" class="btn btn-dark btn-sm text-center actionCreateUpdateExamTitle mr-3" exam-title-id="' . $examTitle->id . '" data-toggle="modal" data-target="#modalCreateUpdateExamTitle" title="Update Exam Title"><i class="fas fa-edit"></i></button>';
                $result .= '<button type="button" class="btn btn-danger btn-sm text-center actionChangeExamTitleStatus" exam-title-id="' . $examTitle->id . '" status="1" data-toggle="modal" data-target="#modalChangeExamTitleStatus" title="Activate Exam Title"><i class="fas fa-undo"></i></button>';
            }else{
                $result .= '<button type="button" class="btn btn-warning btn-sm text-center actionChangeExamTitleStatus" exam-title-id="' . $examTitle->id . '" status="0" data-toggle="modal" data-target="#modalChangeExamTitleStatus" title="Activate Exam Title"><i class="fas fa-redo"></i></button>';
            }

            $result .= '</center>';
            return $result;
        })

        ->rawColumns(['action'])
        ->make(true);
    }

    public function createUpdateExamTitle(Request $request){
        date_default_timezone_set('Asia/Manila');

        $validator = Validator::make($request->all(), [
            'exam_title' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'validationHasError' => 1,
                'error' => $validator->errors()
            ]);
        }

        DB::beginTransaction();
        try{
            $examTitle = trim($request->exam_title);
            $exists = 
                ExamTitle::where('exam_title', $examTitle)
                    ->where('status', 0)
                    ->where('logdel', 0);

            if($request->exam_title_id){
                $exists->where('id', '!=', $request->exam_title_id);
            }

            if($exists->exists()){
                return response()->json([
                    'result'    =>  1,
                    'message'   => 'Exam title already exists.'
                ]);
            }

            $record = [
                'exam_title' => $examTitle,
            ];

            if($request->exam_title_id){
                $record['updated_at'] = date('Y-m-d H:i:s');

                ExamTitle::where('id', $request->exam_title_id)
                    ->where('logdel', 0)
                    ->update($record);
            }else{
                $record['created_at'] = date('Y-m-d H:i:s');

                ExamTitle::insert($record);
            }
            DB::commit();

            return response()->json([
                'hasError' => 0
            ]);
        }catch(\Exception $e){
            DB::rollback();

            return response()->json([
                'hasError' => 1,
                'exceptionError' => $e->getMessage()
            ]);
        }
    }

    public function getExamTitleById(Request $request){
        date_default_timezone_set('Asia/Manila');

        $exam_title_info =
            ExamTitle::where('id', $request->examTitleId)
                ->where('logdel', '0')
                ->get();

        return response()->json($exam_title_info);
    }

    public function changeExamTitleStatus(Request $request){
        date_default_timezone_set('Asia/Manila');

        DB::beginTransaction();
        try{
            ExamTitle::where('id', $request->exam_title_id)->where('logdel', 0)->update(['status' => $request->status]);
            DB::commit();
            return response()->json(['hasError' => 0]);
        }catch (\Exception $e){
            DB::rollback();
            return response()->json(['hasError' => 1, 'exceptionError' => $e]);
        }
    }
}
