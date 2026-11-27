<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ALqcTrainingQualificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            // ── Sec 1 · Training Items ────────────────────────────────────────────
            'text_training_orientation_inspector'  => ['required'],
            // 'text_training_orientation_ins_4'      => ['nullable', 'string', 'max:255'],
            // 'text_training_orientation_ins_13'     => ['nullable', 'string', 'max:255'],
            // 'text_training_orientation_ins_21'     => ['nullable', 'string', 'max:255'],
            // 'text_training_orientation_ins_54'     => ['nullable', 'string', 'max:255'],
            // // ── Sec 1 · Approvers ────────────────────────────────────────────────
            'text_certified_inspector'             => ['required', 'string'],
            'text_mentored'                        => ['required', 'string'],
            'text_date_inspector'                  => ['required', 'date'],
            'text_time_inspector'                  => ['required', 'string'],
            // // ── Sec 2 · Theoretical Exam ─────────────────────────────────────────
            // 'text_result_input1_inspector'         => ['nullable', 'string', 'max:50'],
            // 'text_sel_result1_inspector'           => ['nullable', 'in:PASSED,FAILED'],
            // 'text_result_input2_inspector'         => ['nullable', 'string', 'max:50'],
            // 'text_sel_result2_inspector'           => ['nullable', 'in:PASSED,FAILED'],
            // // ── Sec 2 · Hands-on ─────────────────────────────────────────────────
            // 'text_hands_on_inspector'              => ['nullable'],
            // 'text_hands_on_ins_3'                  => ['nullable', 'string', 'max:255'],
            // 'text_sec2_result_inspector'           => ['nullable', 'in:PASSED,RE-TRAIN,FAILED'],
            // 'text_sec2_certified_inspector'        => ['nullable', 'string'],
            // 'text_sec2_date_inspector'             => ['nullable', 'date'],
            // 'text_sec2_time_inspector'             => ['nullable', 'string'],
            // // ── Sec 3 · VPQCS Validation ─────────────────────────────────────────
            // 'text_ref_docno_inspector'             => ['nullable'],
            // 'text_ref_docno_input_inspector'       => ['nullable', 'string', 'max:255'],
            // 'text_ins_seq_inspector'               => ['nullable'],
            // 'text_vpqcs_result1_inspector'         => ['nullable', 'in:PASSED,FAILED'],
            // 'text_vpqcs_result2_inspector'         => ['nullable', 'in:PASSED,FAILED'],
            // 'text_vpqcs_validated1_inspector'      => ['nullable', 'string'],
            // 'text_vpqcs_validated2_inspector'      => ['nullable', 'string'],
            // 'text_vpqcs_date1_inspector'           => ['nullable', 'date'],
            // 'text_vpqcs_date2_inspector'           => ['nullable', 'date'],
            // 'text_sec3_approved_inspector'         => ['nullable', 'string'],
        ];
    }
}
