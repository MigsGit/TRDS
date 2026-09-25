<?php

namespace App\Http\Controllers;

use App\Http\Requests\TrainingRecordUpdateRequest;
use App\Imports\TRUserImport;
use App\Model\DropdownMasterDetail;
use App\Model\Qc\QcSlipEmployee;
use App\Model\SystemOneHrisSubcon;
use App\Model\TrainingRecord;
use App\Model\TrainingRecordEmployee;
use DataTables;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

date_default_timezone_set('Asia/Manila');

class TrainingRecordUpdateController extends Controller
{
    public function getTypeOfTrainings(Request $request){
        return DropdownMasterDetail::where('dropdown_masters_id', 10)->whereNull('deleted_at')->get(['dropdown_masters_details', 'id']);
    }

    public function getVenue(Request $request){
        return DropdownMasterDetail::where('dropdown_masters_id', 11)->whereNull('deleted_at')->get(['dropdown_masters_details', 'id']);
    }

    public function getEmployees(Request $request){
        return SystemOneHrisSubcon::get();
    }

    public function saveTrainingRecord(TrainingRecordUpdateRequest $request){
        $data = $request->validated();
        DB::beginTransaction();
        try{
            $data['trainer'] = implode(',', $data['trainer']);
            unset($data['attachments']); // Remove attachments from the main data array as they will be handled separately
            $savedId = null;

            if(isset($data['editing_record_id'])){
                // Update existing record logic here
                $data['updated_by'] =  $_SESSION["rapidx_user_id"] ?? 0;
                $data['updated_at'] = NOW();
                unset($data['editing_record_id']);
                unset($data['trainee']);
                $savedId = $request->editing_record_id;
                TrainingRecord::where('id', $request->editing_record_id)->update($data);
                // Delete existing trainees for this training record if updating
                TrainingRecordEmployee::where('training_record_id', $savedId)->delete();
            } else {
                // Insert new record logic here
                $data['created_by'] =  $_SESSION["rapidx_user_id"] ?? 0;
                $data['created_at'] = NOW();
                unset($data['trainee']);
                $savedId = TrainingRecord::insertGetId($data);
            }

            $attachments = $request->file('attachments'); // Handle attachments separately
            $filenames = [];
            
            // remove attachments if reuploading
            if(isset($request->editing_record_id) && isset($request->attachments_checkbox_reupload)){
                // delete existing attachments from storage
                Storage::deleteDirectory("training_update/{$savedId}");
                // clear the attachments field in the database
                TrainingRecord::where('id', $savedId)->update(['attachments' => null]);
            }
            if(isset($attachments)){
                // process each attachment file
                foreach($attachments as $attachment){
                    $filename = $attachment->getClientOriginalName();
                    $filenames[] = $filename;
                    Storage::putFileAs("training_update/{$savedId}", $attachment, $filename);
                    // You can save the file or perform other operations here
                }
                if(!empty($filenames)){
                    // update the attachments field in the database
                    TrainingRecord::where('id', $savedId)->update(['attachments' => implode(',', $filenames)]);
                }
            }

            // Save the filenames of the attachments in the database
            

            // Save trainees associated with the training record
            if(isset($request->trainee) && is_array($request->trainee)){
                foreach($request->trainee as $trainee){
                    $result = json_decode($trainee,true);
                    TrainingRecordEmployee::insert([
                        'training_record_id' => $savedId,
                        'employee_no' => $result['empNo'],
                        'station' => $result['qcSlipStation'],
                        'series' => $result['qcSlipSeries'],
                        'created_by' => $_SESSION["rapidx_user_id"] ?? 0,
                        'updated_by' => $_SESSION["rapidx_user_id"] ?? 0,
                        'created_at' => NOW(),
                        'updated_at' => NOW(),
                    ]);
                }
            }
            DB::commit();
            return response()->json(['success' => true, 'msg' => 'Training record saved successfully.']);
        }catch(Throwable $e){
            DB::rollback();
            return $e->getMessage();
        }
    }

    public function dtGetTrainingRecords(Request $request){

        $query = TrainingRecord::with([
            'venue_details'
        ])
        ->whereNull('deleted_at')->get();

        return DataTables::of($query)
        ->addColumn('action', function($row){
            $result = "";
            $result .= "<div class='d-flex justify-content-center' style='gap: 3px;'>";
            $result .= '<button class="btn btn-sm btn-primary btnViewTraining" data-id="'.$row->id.'"><i class="fas fa-eye"></i></button>';
            $result .= '<button class="btn btn-sm btn-secondary btnEditTraining" data-id="'.$row->id.'"><i class="fas fa-edit"></i></button>';
            $result .= '<button class="btn btn-sm btn-danger btnDeleteTraining" data-id="'.$row->id.'"><i class="fas fa-trash"></i></button>';
            $result .= "</div>";
            return $result;
        })
        ->make(true);
    }
    public function getTrainingRecordById(Request $request){
        $data = TrainingRecord::with([
            'trainee_details',
            'trainee_details.employee_details'
        ])
        ->where('id', $request->id)->first();
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function getTraineeDetails(Request $request){
        $data = SystemOneHrisSubcon::where('EmpNo', $request->EmpId)->first();
        // $qc_slip = QcSlipEmployee::with([
        //     'qcSlip',
        //     'get_station_to'
        // ])
        // ->where('employee_no', $request->EmpId)
        // ->whereHas('qcSlip') // This filters out records where qcSlip is null
        // ->whereNull('deleted_at')
        // ->orderBy('qc_slips_id', 'DESC')
        // // ->first();
        // if(is_null($qc_slip)){
        //     return response()->json(['success' => false, 'msg' => 'No QC slip found for this trainee.']);
        // }
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function deleteTrainingRecord(Request $request){
        DB::beginTransaction();
        
        try{
            $training = TrainingRecord::with(['trainee_details'])
            ->where('id', $request->trainingId)
            ->first();

            if($training){
                $training->update([
                    'deleted_at' => now()
                ]);

                $training->trainee_details()->update([
                    'deleted_at' => now()
                ]);
                
            }
            DB::commit();
            return response()->json(['success' => true, 'msg' => 'Training record deleted successfully.']);
        }catch(\Throwable $e){
            DB::rollback();
            return response()->json(['success' => false, 'msg' => $e->getMessage()]);
        }
    }
    public function importTrainees(Request $request){
        $file = $request->file('file');

        $collections = Excel::toCollection(new TRUserImport, $file);
        $importedData = collect($collections->first())->keyBy('employee_number');
        $imported_emp_no = $importedData->keys()->toArray();

        $data = SystemOneHrisSubcon::whereIn('EmpNo', $imported_emp_no)
        ->get([
            'EmpNo',
            'empname',
            'Deparment',
        ])
        ->unique('EmpNo')
        ->values()
        ->map(function ($item) use ($importedData) {
            // Find matching record from imported Excel data
            $excelRecord = $importedData->get($item->EmpNo);

            return [
                'action'        => '<center><button type="button" class="btn btn-sm btn-danger btnRemoveTrainee"><i class="fas fa-times"></i></button></center>',
                'empNo'         => $item->EmpNo ?? '',
                'empName'       => $item->empname ?? '',
                'empDept'       => $item->Deparment ?? '',
                'qcSlipStation' => $excelRecord['station'] ?? '',
                'qcSlipSeries'  => $excelRecord['series'] ?? '',
            ];
        });

        return response()->json(['success' => true, 'msg' => 'Employee details fetched successfully.', 'data' => $data]);
    }
}
