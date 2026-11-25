<?php

namespace App\Http\Controllers;

use App\Model\GrrExamEmployees;
use App\Model\GrrExamAnswers;
use App\Model\GrrExamSettings;
use App\Model\GrrQuestionnaireSettings;
use App\Model\GrrSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class GrrExaminationDashboardController extends Controller
{
    private const MAX_TRIAL = 3;

    private const TRIAL_LABELS = [
        1 => 'Chronological',
        2 => 'Even & Odd',
        3 => 'High & Low',
    ];

    private function nextTrial(GrrExamEmployees $employee): int
    {
        $last = (int) GrrExamAnswers::where('exam_employee_id', $employee->id)->max('trial');

        return $last + 1;
    }

    private function orderQuestionsForTrial($questions, int $trial)
    {
        $items = $questions->values()->all();
        $count = count($items);

        if ($trial === 2) {
            // 2,1,4,3,... (an odd leftover stays in place)
            for ($i = 0; $i + 1 < $count; $i += 2) {
                $tmp = $items[$i];
                $items[$i] = $items[$i + 1];
                $items[$i + 1] = $tmp;
            }
        } elseif ($trial === 3) {
            // n,1,n-1,2,... (high, low alternating)
            $ordered = [];
            $low = 0;
            $high = $count - 1;
            while ($low <= $high) {
                $ordered[] = $items[$high--];
                if ($low <= $high) {
                    $ordered[] = $items[$low++];
                }
            }
            $items = $ordered;
        }

        return collect($items)
            ->values()
            ->map(function ($question, $index) {
                $question['number'] = $index + 1;

                return $question;
            });
    }

    public function index()
    {
        return view('recertification.grr_examination_dashboard', [
            'sections' => GrrSettings::SECTIONS,
        ]);
    }

    public function fetchExaminationsBySection(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'section' => ['required', 'string', Rule::in(GrrSettings::SECTIONS)],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'result'  => 0,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $examinations = GrrSettings::where('section', $request->section)
                ->where('status', 0)
                ->orderBy('grr_no')
                ->orderBy('grr_sample')
                ->get(['id', 'section', 'grr_no', 'grr_sample'])
                ->map(function ($grr) {
                    return [
                        'id'         => $grr->id,
                        'grr_no'     => $grr->grr_no,
                        'grr_sample' => $grr->grr_sample,
                        'section'    => $grr->section,
                        'label'      => $grr->grr_no . ' - ' . $grr->grr_sample,
                    ];
                })
                ->values();

            return response()->json([
                'result' => 1,
                'data'   => $examinations,
            ]);
        } catch (\Throwable $e) {
            Log::error('fetchExaminationsBySection failed: ' . $e->getMessage());

            return response()->json([
                'result'  => 0,
                'message' => 'Unable to load examinations. Please try again.',
            ], 500);
        }
    }

    public function fetchExamSettingsByGrr(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'grr_setting_id' => [
                'required',
                'integer',
                Rule::exists('grr_settings', 'id')->where('status', 0),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['result' => 0, 'message' => $validator->errors()->first()], 422);
        }

        try {
            $examSettings = GrrExamSettings::where('grr_setting_id', $request->grr_setting_id)
                ->where('status', 0)
                ->withCount('examEmployees')
                ->orderBy('ctrl_number')
                ->get()
                ->map(function ($exam) {
                    return [
                        'id'              => $exam->id,
                        'ctrl_number'     => $exam->ctrl_number,
                        'section'         => $exam->section,
                        'description'     => $exam->description,
                        'purpose'         => $exam->purpose,
                        'instructions'    => $exam->instructions,
                        'employees_count' => $exam->exam_employees_count,
                    ];
                })
                ->values();

            return response()->json(['result' => 1, 'data' => $examSettings]);
        } catch (\Throwable $e) {
            Log::error('fetchExamSettingsByGrr failed: ' . $e->getMessage());

            return response()->json([
                'result'  => 0,
                'message' => 'Unable to load examination settings. Please try again.',
            ], 500);
        }
    }

    public function fetchExamEmployeesByExamSetting(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'exam_grouping_id' => [
                'required',
                'integer',
                Rule::exists('grr_exam_settings', 'id')->where('status', 0),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['result' => 0, 'message' => $validator->errors()->first()], 422);
        }

        try {
            $employees = GrrExamEmployees::with('getEmployeeAttribute')
                ->where('exam_grouping_id', $request->exam_grouping_id)
                ->where('status', 0)
                ->where('exam_status', '!=', 2)
                ->orderBy('id')
                ->get();

            $lastTrials = GrrExamAnswers::whereIn('exam_employee_id', $employees->pluck('id'))
                ->groupBy('exam_employee_id')
                ->selectRaw('exam_employee_id, MAX(trial) as last_trial')
                ->pluck('last_trial', 'exam_employee_id');

            $employees = $employees
                ->map(function ($employee) use ($lastTrials) {
                    $name = $employee->getEmployeeAttribute->empname ?? '';

                    return [
                        'id'          => $employee->id,
                        'emp_id'      => $employee->emp_id,
                        'name'        => str_replace('Ã±', 'ñ', $name),
                        'exam_status' => (int) $employee->exam_status,
                        'trial'       => (int) ($lastTrials[$employee->id] ?? 0) + 1,
                    ];
                })
                ->values();

            return response()->json(['result' => 1, 'data' => $employees]);
        } catch (\Throwable $e) {
            Log::error('fetchExamEmployeesByExamSetting failed: ' . $e->getMessage());

            return response()->json([
                'result'  => 0,
                'message' => 'Unable to load examinees. Please try again.',
            ], 500);
        }
    }

    public function fetchExaminationForEmployee(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'exam_grouping_id' => [
                'required',
                'integer',
                Rule::exists('grr_exam_settings', 'id')->where('status', 0),
            ],
            'exam_employee_id' => ['required', 'integer'],
        ]);

        if ($validator->fails()) {
            return response()->json(['result' => 0, 'message' => $validator->errors()->first()], 422);
        }

        try {
            $employee = GrrExamEmployees::with('getEmployeeAttribute')
                ->where('id', $request->exam_employee_id)
                ->where('exam_grouping_id', $request->exam_grouping_id)
                ->where('status', 0)
                ->where('exam_status', '!=', 2)
                ->first();

            if (!$employee) {
                return response()->json([
                    'result'  => 0,
                    'message' => 'This employee is not assigned to the selected examination.',
                ], 422);
            }

            $exam = GrrExamSettings::findOrFail($request->exam_grouping_id);

            $trial = $this->nextTrial($employee);

            if ($trial > self::MAX_TRIAL) {
                return response()->json([
                    'result'  => 0,
                    'message' => 'All trials of this examination have already been completed.',
                ], 422);
            }

            $questions = $this->orderQuestionsForTrial(
                GrrQuestionnaireSettings::where('grr_setting_id', $exam->grr_setting_id)
                    ->where('status', 0)
                    ->ordered()
                    ->get(['id', 'defect', 'location'])
                    ->values()
                    ->map(function ($question, $index) {
                        return [
                            'id'       => $question->id,
                            'number'   => $index + 1,
                            'defect'   => $question->defect,
                            'location' => $question->location,
                        ];
                    }),
                $trial
            );

            return response()->json([
                'result' => 1,
                'data'   => [
                    'exam' => [
                        'ctrl_number'  => $exam->ctrl_number,
                        'description'  => $exam->description,
                        'purpose'      => $exam->purpose,
                        'instructions' => $exam->instructions,
                    ],
                    'employee' => [
                        'emp_id' => $employee->emp_id,
                        'name'   => str_replace('Ã±', 'ñ', $employee->getEmployeeAttribute->empname ?? ''),
                    ],
                    'trial'       => $trial,
                    'trial_label' => self::TRIAL_LABELS[$trial],
                    'questions'   => $questions,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('fetchExaminationForEmployee failed: ' . $e->getMessage());

            return response()->json([
                'result'  => 0,
                'message' => 'Unable to load the examination. Please try again.',
            ], 500);
        }
    }

    public function saveExamAnswers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'exam_grouping_id' => [
                'required',
                'integer',
                Rule::exists('grr_exam_settings', 'id')->where('status', 0),
            ],
            'exam_employee_id' => ['required', 'integer'],
            'trial'            => ['required', 'integer', 'between:1,3'],
            'answers'          => ['required', 'array', 'min:1'],
            'answers.*'        => ['required', 'string', 'in:0,1'],
        ], [
            'answers.*.required' => 'Please answer all items before saving.',
            'answers.*.in'       => 'Each answer must be either 0 or 1.',
        ]);

        if ($validator->fails()) {
            return response()->json(['result' => 0, 'message' => $validator->errors()->first()], 422);
        }

        try {
            $result = DB::transaction(function () use ($request) {
                $employee = GrrExamEmployees::where('id', $request->exam_employee_id)
                    ->where('exam_grouping_id', $request->exam_grouping_id)
                    ->where('status', 0)
                    ->lockForUpdate()
                    ->first();

                if (!$employee) {
                    return 'This employee is not assigned to the selected examination.';
                }

                if ((int) $employee->exam_status === 2) {
                    return 'This examination has already been submitted.';
                }

                $trial = (int) $request->trial;
                $expectedTrial = $this->nextTrial($employee);

                if ($expectedTrial > self::MAX_TRIAL) {
                    return 'All trials of this examination have already been completed.';
                }

                if ($trial !== $expectedTrial) {
                    return 'Trial ' . $trial . ' is not the current trial. Please reload and try again.';
                }

                $exam = GrrExamSettings::findOrFail($request->exam_grouping_id);

                $questionIds = GrrQuestionnaireSettings::where('grr_setting_id', $exam->grr_setting_id)
                    ->where('status', 0)
                    ->pluck('id')
                    ->map(function ($id) { return (string) $id; })
                    ->all();

                $submittedIds = array_map('strval', array_keys($request->answers));

                if (count(array_diff($submittedIds, $questionIds)) > 0) {
                    return 'The submitted answers do not match the examination. Please reload and try again.';
                }

                if (count(array_diff($questionIds, $submittedIds)) > 0) {
                    return 'Please answer all items before saving.';
                }

                foreach ($request->answers as $questionId => $answer) {
                    GrrExamAnswers::updateOrCreate(
                        [
                            'exam_employee_id' => $employee->id,
                            'trial'            => $trial,
                            'questionnaire_id' => (int) $questionId,
                        ],
                        ['answer' => $answer]
                    );
                }

                $employee->exam_status = $trial >= self::MAX_TRIAL ? 2 : 1;
                $employee->save();

                return true;
            });

            if ($result !== true) {
                return response()->json(['result' => 0, 'message' => $result], 422);
            }

            $trial = (int) $request->trial;

            return response()->json([
                'result'    => 1,
                'completed' => $trial >= self::MAX_TRIAL,
                'message'   => $trial >= self::MAX_TRIAL
                    ? 'Trial 3 saved. The examination is now complete!'
                    : 'Trial ' . $trial . ' saved successfully! Next: Trial ' . ($trial + 1) . ' (' . self::TRIAL_LABELS[$trial + 1] . ').',
            ]);
        } catch (\Throwable $e) {
            Log::error('saveExamAnswers failed: ' . $e->getMessage());

            return response()->json([
                'result'  => 0,
                'message' => 'Unable to save your answers. Please try again.',
            ], 500);
        }
    }
}
