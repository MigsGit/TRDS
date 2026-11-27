<?php

namespace App\Http\Controllers;

use App\Model\GrrSeriesSettings;
use App\Model\GrrSettings;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class GrrSeriesSettingsController extends Controller
{
    public function viewGrrSeries()
    {
        return DataTables::of(GrrSeriesSettings::query())
            ->addColumn('action', function ($series) {
                $id = (int) $series->id;
                $html = "<center>";
                $html .= "<button class='btn btn-secondary btn-sm btnEditSeries mr-1' data-id='$id'><i class='fas fa-edit'></i></button>";
                if ((int) $series->status === 0) {
                    $html .= "<button class='btn btn-danger btn-sm btnDisableSeries' data-id='$id'><i class='fas fa-ban'></i></button>";
                } else {
                    $html .= "<button class='btn btn-success btn-sm btnEnableSeries' data-id='$id'><i class='fas fa-undo'></i></button>";
                }
                $html .= "</center>";

                return $html;
            })
            ->addColumn('status_label', function ($series) {
                return (int) $series->status === 0
                    ? "<center><span class='badge rounded-pill bg-success'>Active</span></center>"
                    : "<center><span class='badge rounded-pill bg-danger'>Inactive</span></center>";
            })
            ->rawColumns(['action', 'status_label'])
            ->make(true);
    }

    public function saveGrrSeries(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'          => ['nullable', 'integer', 'exists:grr_series_settings,id'],
            'section'     => ['required', 'string', Rule::in(GrrSettings::SECTIONS)],
            'series_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('grr_series_settings', 'series_name')
                    ->where('section', $request->section)
                    ->ignore($request->id),
            ],
        ], [
            'section.required'     => 'Please select a section.',
            'section.in'           => 'The selected section is invalid.',
            'series_name.required' => 'Please enter a series name.',
            'series_name.unique'   => 'This series name already exists in the selected section.',
        ]);

        if ($validator->fails()) {
            return response()->json(['result' => 0, 'message' => $validator->errors()->first()], 422);
        }

        try {
            $data = [
                'section'     => $request->section,
                'series_name' => trim($request->series_name),
            ];

            if ($request->filled('id')) {
                GrrSeriesSettings::findOrFail($request->id)->update($data);

                return response()->json(['result' => 1, 'message' => 'Successfully updated!']);
            }

            GrrSeriesSettings::create($data + ['status' => 0]);

            return response()->json(['result' => 1, 'message' => 'Successfully added!']);
        } catch (\Throwable $e) {
            Log::error('saveGrrSeries failed: ' . $e->getMessage());

            return response()->json(['result' => 0, 'message' => 'Failed to save data.'], 500);
        }
    }

    public function getGrrSeriesById(Request $request)
    {
        $series = GrrSeriesSettings::find($request->input('id'));

        if (!$series) {
            return response()->json(['result' => 0, 'message' => 'Record not found.'], 404);
        }

        return response()->json($series);
    }

    public function updateGrrSeriesStatus(Request $request)
    {
        $series = GrrSeriesSettings::find($request->input('id'));

        if (!$series) {
            return response()->json(['success' => false, 'message' => 'Record not found.'], 404);
        }

        $series->status = (int) $series->status === 0 ? 1 : 0;
        $series->save();

        return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
    }
}
