<div class="modal" id="modalSendEmail" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Send Email to next approver</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
                <div class="modal-body">
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="input-group flex-nowrap mb-2 input-group-sm">
                                <span class="input-group-text" id="addon-wrapping">Attention To:</span>
                                <select class="form-control select2bs4" style="width: 100%;" id="text_alert_prod_sec" name="text_alert_prod_sec" multiple>
                            </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group flex-nowrap mb-2 input-group-sm">
                                <span class="input-group-text" id="addon-wrapping">Attention CC:</span>
                                <select class="form-control select2bs4" style="width: 100%;" id="text_alert_prod_cc_sec" name="text_alert_prod_cc_sec" multiple>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id= "closeBtn" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                    <button type="button" id="formSendEmail" class="btn btn-success btn-sm"><font-awesome-icon class="nav-icon" icon="fas fa-save" />&nbsp; Save</button>
                </div>
        </div>
    </div>
</div>

 <!-- Modal Select Employee for Operator -->
<div class="modal" id="select_Employee_operator" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Qualification / Certification Form</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="">Station (From):</label>

                            <select class="form-control select2bs4" style="width: 100%;" name="text_oper_station_from" id="text_oper_station_from" placeholder="Enter Station (From)">
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="">Station (To):</label>
                            <select class="form-control select2bs4" style="width: 100%;" name="text_oper_station_to" id="text_oper_station_to" placeholder="Enter Station (From)">
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="">Operator Name:</label>
                        <select class="form-control select2bs4" style="width: 100%;" id="text_oper_emp_number" name="text_oper_emp_number" placeholder="Enter Employee Number">
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="">Remarks:</label>
                        <input type="text" class="form-control" id="text_oper_remarks" name="text_oper_remarks" placeholder="Enter Remarks">
                    </div>
                </div>
                <button type="button" class="btn btn-primary btnAddOPEREmp" id="btnAddOPEREmp"><i class="fa-solid fa-plus"></i> Add to Table</button>

                <hr style="border: 1px solid black;">

                <h5 class="mb-3">Selected Employees:</h5>

                <table class="table table-bordered tbl_oper_add_emp" id="tbl_oper_add_emp">
                    <thead class="table-info">
                        <tr>
                            <th>Action</th>
                            <th>Employee No.</th>
                            <th>Employee Name</th>
                            <th>Station (From)</th>
                            <th>Station (To)</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- <tr>
                            <td>Paul Bariring</td>
                            <td>PB0328</td>
                            <td>Gas Station</td>
                            <td>Police Station</td>
                        </tr>
                        <tr>
                            <td>Jowee Maramag</td>
                            <td>JM1213</td>
                            <td>Radio Station</td>
                            <td>Train Station</td>
                        </tr> -->
                    </tbody>
                </table>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-primary btnAddSelectedOPEREmp" id="btnAddSelectedOPEREmp"><i class="fa-solid fa-user-plus" aria-hidden="true"></i> Add</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal" >Close</button>
            </div>
        </div>
    </div>
</div>

<!-- VIEW MODAL FOR OPERATOR -->
<div class="modal" id="modalViewOper" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-xl" style="width: 95% !important; min-width: 95% !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Qualification / Certification Form</h1>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">

                    {{-- <form id="formSubmit_oper"> --}}
                        <h3 class="mb-3 text-center">OPERATOR'S TRAINING / QUALIFICATION / CERTIFICATION SLIP</h3>

                        <div class="row mb-5">
                            <div class="col-md-3">
                                <label for="">Control No.:</label>
                                <input class="form-control" type="text" class="form-control" id="text_view_oper_conno" name="text_view_oper_conno" readonly>
                            </div>

                            <div class="col-md-3">
                                <label for="">Production Section:</label>
                                <input class="form-control" type="text" name="text_view_oper_section" id="text_view_oper_section" readonly>
                            </div>

                            <div class="col-md-3">
                                <label for="">Series Name:</label>
                                <input class="form-control" type="text" id="text_view_oper_series" name="text_view_oper_series" readonly>
                            </div>

                            <div class="col-md-3">
                                <label for="">Product Line:</label>
                                <input class="form-control" type="text" id="text_view_oper_product_line" name="text_view_oper_product_line" readonly>
                            </div>
                        </div>

                        <div class="table-responsive mt-3 mb-3">
                            <table id="tbl_view_certified_list_Oper" class="table table-bordered table-hover nowrap">
                                <thead class="table-primary">
                                    <tr>
                                        <!-- <th>Action</th> -->
                                        <th>Employee No.</th>
                                        <th>Operator's Name</th>
                                        <th>Station (From)</th>
                                        <th>Station (To)</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>

                                <tbody>
                                </tbody>
                            </table>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="">Reason for Certification:</label>
                                <input type="text" class="form-control" name="text_view_oper_certification" id="text_view_oper_certification" readonly>
                            </div>
                        </div>

                        <hr style="height: 5px; background-color: black; border: none;">

                        <h4 class="mt-3 mb-3">PRODUCTION SECTION (Training and Orientation)</h4>

                        <div class="table-responsive mt-3 mb-5">
                            <table id="tbl_view_oper_training_orientation" class="table table-bordered table-hover nowrap">
                                <thead class="table-primary">
                                    <tr>
                                        <!-- <th>Action</th> -->
                                        <th>Training</th>
                                    </tr>
                                </thead>

                                <tbody>
                                </tbody>
                            </table>
                        </div>

                        <h5 class="mt-3 mb-3">RESULT</h5>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="" for="">First Take:</label>
                                <input class="form-control" name="text_view_first_result_oper" id="text_view_first_result_oper" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="" for="">Second Take:</label>
                                <input class="form-control" name="text_view_second_result_oper" id="text_view_second_result_oper" readonly>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="" for="">Trained by:</label>
                                <input class="form-control" type="text" id="text_view_first_trainedby_oper" name="text_view_first_trainedby_oper" multiple readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="" for="">Trained by:</label>
                                <input class="form-control" type="text" id="text_view_second_trainedby_oper" name="text_view_second_trainedby_oper" readonly>
                            </div>
                        </div>

                        <div class="row mb-5">
                            <div class="col-md-6">
                                <label class="" for="">Date & Time:</label>
                                <input class="form-control" type="text" id="text_view_first_date_time_oper" name="text_view_first_date_time_oper" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="" for="">Date & Time:</label>
                                <input class="form-control" type="text" id="text_view_second_date_time_oper" name="text_view_second_date_time_oper" readonly>
                            </div>
                        </div>

                        <!-- **************************************************************      2ND SECTION          ************************************************************************************************* -->

                        <hr style="height: 5px; background-color: black; border: none;">

                        <h4 class="mb-3">ENGINEERING SECTION (Training and Qualification)</h4>

                        <div class="table-responsive mt-3 mb-5">
                            <table id="tbl_view_oper_es_training_orientation" class="table table-bordered table-hover nowrap">
                                <thead class="table-primary">
                                    <tr>
                                        <!-- <th>Action</th> -->
                                        <th>Training</th>
                                    </tr>
                                </thead>

                                <tbody>
                                </tbody>
                            </table>
                        </div>

                        <h5 class="mt-3 mb-3">RESULT</h5>

                        <div class="row">
                            <div class="col-md-6">
                                <label class="" for="">First Take:</label>
                            </div>

                            <div class="col-md-6">
                                <label class="" for="">Second Take:</label>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="" for="">1.1 Observation / Interview Result</label>
                                <input class="form-control" name="text_view_obs_first_result_es_oper" id="text_view_obs_first_result_es_oper" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="" for="">1.1 Observation / Interview Result</label>
                                <input class="form-control" name="text_view_obs_second_result_es_oper" id="text_view_obs_second_result_es_oper" readonly>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-2">
                                <label class="" for="">2. Sample Checking:</label>
                                <input class="form-control" type="text" name="text_view_first_sample_es_oper" id="text_view_first_sample_es_oper" readonly>
                            </div>

                            <div class="col-md-2">
                                <label class="" for="">OK:</label>
                                <input class="form-control" type="text" name="text_view_first_ok_es_oper" id="text_view_first_ok_es_oper" readonly>
                            </div>

                            <div class="col-md-2">
                                <label class="" for="">NG:</label>
                                <input class="form-control" type="text" name="text_view_first_ng_es_oper" id="text_view_first_ng_es_oper" readonly>
                            </div>

                            <div class="col-md-2">
                                <label class="" for="">2. Sample Checking:</label>
                                <input class="form-control" type="text" name="text_view_second_sample_es_oper" id="text_view_second_sample_es_oper" readonly>
                            </div>

                            <div class="col-md-2">
                                <label class="" for="">OK:</label>
                                <input class="form-control" type="text" name="text_view_second_ok_es_oper" id="text_view_second_ok_es_oper" readonly>
                            </div>

                            <div class="col-md-2">
                                <label class="" for="">NG:</label>
                                <input class="form-control" type="text" name="text_view_second_ng_es_oper" id="text_view_second_ng_es_oper" readonly>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="" for="">3. Overall Assessment:</label>
                                <input class="form-control" name="text_view_oa_1st_result_es_oper" id="text_view_oa_1st_result_es_oper" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="" for="">3. Overall Assessment:</label>
                                <input class="form-control" name="text_view_oa_2nd_result_es_oper" id="text_view_oa_2nd_result_es_oper" readonly>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="" for="">Reason for Disqualification:</label>
                                <input class="form-control" type="text" id="text_view_1st_disqualification_es_oper" name="text_view_1st_disqualification_es_oper" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="" for="">Reason for Disqualification:</label>
                                <input class="form-control" type="text" id="text_view_2nd_disqualification_es_oper" name="text_view_2nd_disqualification_es_oper" readonly>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="" for="">Qualified by:</label>
                                <input class="form-control" type="text" id="text_view_1st_qualifiedby_es_oper" name="text_view_1st_qualifiedby_es_oper" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="" for="">Qualified by:</label>
                                <input class="form-control" type="text" id="text_view_2nd_qualifiedby_es_oper" name="text_view_2nd_qualifiedby_es_oper" readonly>
                            </div>
                        </div>

                        <div class="row mb-5">
                            <div class="col-md-6">
                                <label class="" for="">Date & Time:</label>
                                <input class="form-control" type="text" id="text_view_qc_1st_date_es_oper" name="text_view_qc_1st_date_es_oper" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="" for="">Date & Time:</label>
                                <input class="form-control" type="text" id="text_view_qc_2nd_date_es_oper" name="text_view_qc_2nd_date_es_oper" readonly>
                            </div>
                        </div>

                        <!-- ************************************************************ 3RD SECTION ******************************************************************* -->

                        <hr style="height: 5px; background-color: black; border: none;">

                        <h4 class="mb-3">QUALITY CONTROL SECTION (CERTIFICATTION)</h4>

                        <p class="mb-3">Let the operator discuss the details of training/orientation conducted by concerned Supervisor nad Eng'r as per check items specified.</p>

                        <h5 class="mt-3 mb-3">RESULT</h5>

                        <div class="row">
                            <div class="col-md-6">
                                <label class="" for="">First Take:</label>
                            </div>

                            <div class="col-md-6">
                                <label class="" for="">Second Take:</label>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="" for="">1.1 Observation / Interview Result</label>
                                <input class="form-control" name="text_view_obs_first_result_qcs_oper" id="text_view_obs_first_result_qcs_oper" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="" for="">1.1 Observation / Interview Result</label>
                                <input class="form-control" name="text_view_obs_second_result_qcs_oper" id="text_view_obs_second_result_qcs_oper" readonly>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-2">
                                <label class="" for="">2. Sample Checking:</label>
                                <input class="form-control" type="text" name="text_view_first_sample_qcs_oper" id="text_view_first_sample_qcs_oper" readonly>
                            </div>

                            <div class="col-md-2">
                                <label class="" for="">OK:</label>
                                <input class="form-control" type="text" name="text_view_first_ok_qcs_oper" id="text_view_first_ok_qcs_oper" readonly>
                            </div>

                            <div class="col-md-2">
                                <label class="" for="">NG:</label>
                                <input class="form-control" type="text" name="text_view_first_ng_qcs_oper" id="text_view_first_ng_qcs_oper" readonly>
                            </div>

                            <div class="col-md-2">
                                <label class="" for="">2. Sample Checking:</label>
                                <input class="form-control" type="text" name="text_view_second_sample_qcs_oper" id="text_view_second_sample_qcs_oper" readonly>
                            </div>

                            <div class="col-md-2">
                                <label class="" for="">OK:</label>
                                <input class="form-control" type="text" name="text_view_second_ok_qcs_oper" id="text_view_second_ok_qcs_oper" readonly>
                            </div>

                            <div class="col-md-2">
                                <label class="" for="">NG:</label>
                                <input class="form-control" type="text" name="text_view_second_ng_qcs_oper" id="text_view_second_ng_qcs_oper" readonly>
                            </div>
                        </div>

                        <div class="row mb-1">
                            <div class="col-md-6">
                                <div class="table-responsive">
                                    <table id="" class="table table-bordered table-hover nowrap">
                                        <thead class="table table-warning">
                                            <tr class="text-center">
                                                <th></th>
                                                <th>Station</th>
                                                <th>Type of Exam</th>
                                                <th>Method</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <tr>
                                                <td><input type="checkbox" id="text_view_qcs_station_1st_oper_1" name="text_view_qcs_station_1st_oper" value="Visual"></td>
                                                <td>Visual</td>
                                                <td>Judgement Confirmation</td>
                                                <td>Using GRR sample (50pcs.)</td>
                                            </tr>

                                            <tr>
                                                <td><input type="checkbox" id="text_view_qcs_station_1st_oper_2" name="text_view_qcs_station_1st_oper" value="Assembly"></td>
                                                <td>Assembly</td>
                                                <td>Judgement Confirmation</td>
                                                <td>50 samples drawn from their actual output</td>
                                            </tr>

                                            <tr>
                                                <td><input type="checkbox" id="text_view_qcs_station_1st_oper_3" name="text_view_qcs_station_1st_oper" value="Others"></td>
                                                <td>Others (Parts Prep/Prov. Insertion/Packing/ etc)</td>
                                                <td>Work Confirmation</td>
                                                <td>50 samples drawn from their actual output</td>
                                            </tr>

                                            <tr>
                                                <td><input type="checkbox" id="text_view_qcs_station_1st_oper_4" name="text_view_qcs_station_1st_oper" value="Rework Station"></td>
                                                <td>Rework Station (PPS only)</td>
                                                <td>Work Confirmation</td>
                                                <td>50 samples drawn from their actual output</td>
                                            </tr>

                                            <tr>
                                                <td><input type="checkbox" id="text_view_qcs_station_1st_oper_5" name="text_view_qcs_station_1st_oper" value="Segregation Station"></td>
                                                <td>Segregation Station</td>
                                                <td>Work Confirmation</td>
                                                <td>50 samples drawn from their actual output</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="table-responsive">
                                    <table id="" class="table table-bordered table-hover nowrap">
                                        <thead class="table table-warning">
                                            <tr class="text-center">
                                                <th></th>
                                                <th>Station</th>
                                                <th>Type of Exam</th>
                                                <th>Method</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <tr>
                                                <td><input type="checkbox" id="text_view_qcs_station_2nd_oper_1" name="text_view_qcs_station_2nd_oper" value="Visual"></td>
                                                <td>Visual</td>
                                                <td>Judgement Confirmation</td>
                                                <td>Using GRR sample (50pcs.)</td>
                                            </tr>

                                            <tr>
                                                <td><input type="checkbox" id="text_view_qcs_station_2nd_oper_2" name="text_view_qcs_station_2nd_oper" value="Assembly"></td>
                                                <td>Assembly</td>
                                                <td>Judgement Confirmation</td>
                                                <td>50 samples drawn from their actual output</td>
                                            </tr>

                                            <tr>
                                                <td><input type="checkbox" id="text_view_qcs_station_2nd_oper_3" name="text_view_qcs_station_2nd_oper" value="Others"></td>
                                                <td>Others (Parts Prep/Prov. Insertion/Packing/ etc)</td>
                                                <td>Work Confirmation</td>
                                                <td>50 samples drawn from their actual output</td>
                                            </tr>

                                            <tr>
                                                <td><input type="checkbox" id="text_view_qcs_station_2nd_oper_4" name="text_view_qcs_station_2nd_oper" value="Rework Station"></td>
                                                <td>Rework Station (PPS only)</td>
                                                <td>Work Confirmation</td>
                                                <td>50 samples drawn from their actual output</td>
                                            </tr>

                                            <tr>
                                                <td><input type="checkbox" id="text_view_qcs_station_2nd_oper_5" name="text_view_qcs_station_2nd_oper" value="Segregation Station"></td>
                                                <td>Segregation Station</td>
                                                <td>Work Confirmation</td>
                                                <td>50 samples drawn from their actual output</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <p for="">** Assembly operators are those who install parts</p>
                                <p for="">** if sample size is equals or exceeds on the lot size, conduct 100% inspection</p>
                            </div>

                            <div class="col-md-6">
                                <p for="">** Assembly operators are those who install parts</p>
                                <p for="">** if sample size is equals or exceeds on the lot size, conduct 100% inspection</p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="" for="">3. Overall Assessment:</label>
                                <input class="form-control" name="text_view_oa_1st_result_qcs_oper" id="text_view_oa_1st_result_qcs_oper" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="" for="">3. Overall Assessment:</label>
                                <input class="form-control" name="text_view_oa_2nd_result_qcs_oper" id="text_view_oa_2nd_result_qcs_oper" readonly>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="" for="">Reason for Disapproval:</label>
                                <input class="form-control" type="text" id="text_view_1st_disapproval_qcs_oper" name="text_view_1st_disapproval_qcs_oper" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="" for="">Reason for Disapproval:</label>
                                <input class="form-control" type="text" id="text_view_2nd_disapproval_qcs_oper" name="text_view_2nd_disapproval_qcs_oper" readonly>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="" for="">Certified by:</label>
                                <input class="form-control" type="text" id="text_view_1st_certifiedby_qcs_oper" name="text_view_1st_certifiedby_qcs_oper" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="" for="">Certified by:</label>
                                <input class="form-control" type="text" id="text_view_2nd_certifiedby_qcs_oper" name="text_view_2nd_certifiedby_qcs_oper" readonly>
                            </div>
                        </div>

                        <div class="row mb-5">
                            <div class="col-md-6">
                                <label class="" for="">Date & Time:</label>
                                <input class="form-control" type="text" id="text_view_1st_date_qcs_oper" name="text_view_1st_date_qcs_oper" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="" for="">Date & Time:</label>
                                <input class="form-control" type="text" id="text_view_2nd_date_qcs_oper" name="text_view_2nd_date_qcs_oper" readonly>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-1">
                                <label for="">Note:</label>
                            </div>

                            <div class="col-md-10">
                                <p for="">#1 For PPS Finishing and Visual, complete the certification/qualification at the back page</p>
                                <p for="">#2 For Engineering and QC Validation result, please update the back page</p>
                            </div>
                        </div>

                        <!-- ************************************************************ 4TH SECTION - BACK PAGE - ******************************************************************* -->

                        <hr style="height: 5px; background-color: black; border: none;">

                        <h4 class="mb-3">PRODUCTION, ENGINEERING & QUALITY CONTROL SECTION (Certification-Completion)</h4>

                        <p class="mb-3">FOR PPS Visual Operators only</p>

                        <div class="row">
                            <div class="col-md-6">
                                <label class="" for="">First Take:</label>
                            </div>

                            <div class="col-md-6">
                                <label class="" for="">Second Take:</label>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-2">
                                <label class="" for="">1. Lot Qty (1st lot):</label>
                                <input class="form-control" type="text" name="lot_view_1st_sample_peqcs_oper" id="lot_view_1st_sample_peqcs_oper" readonly>
                            </div>

                            <div class="col-md-2">
                                <label class="" for="">Injected NG Qty:</label>
                                <input class="form-control" type="text" name="text_view_1st_injected_ng_peqcs_oper" id="text_view_1st_injected_ng_peqcs_oper" readonly>
                            </div>

                            <div class="col-md-2">
                                <label class="" for="">Detected NG:</label>
                                <input class="form-control" type="text" name="text_view_1st_detected_ng_peqcs_oper" id="text_view_1st_detected_ng_peqcs_oper" readonly>
                            </div>

                            <div class="col-md-2">
                                <label class="" for="">1. Lot Qty (1st lot):</label>
                                <input class="form-control" type="text" name="text_view_2nd_sample_peqcs_oper" id="text_view_2nd_sample_peqcs_oper" readonly>
                            </div>

                            <div class="col-md-2">
                                <label class="" for="">Injected NG Qty:</label>
                                <input class="form-control" type="text" name="text_view_2nd_injected_ng_peqcs_oper" id="text_view_2nd_injected_ng_peqcs_oper" readonly>
                            </div>

                            <div class="col-md-2">
                                <label class="" for="">Detected NG:</label>
                                <input class="form-control" type="text" name="text_view_2nd_detected_ng_peqcs_oper" id="text_view_2nd_detected_ng_peqcs_oper" readonly>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="" for="">2. Overall Assessment:</label>
                                <input class="form-control" name="text_view_oa_1st_result_peqcs_oper" id="text_view_oa_1st_result_peqcs_oper" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="" for="">2. Overall Assessment:</label>
                                <input class="form-control" name="text_view_oa_2nd_result_peqcs_oper" id="text_view_oa_2nd_result_peqcs_oper" readonly>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="" for="">Reason for Disapproval:</label>
                                <input class="form-control" type="text" id="text_view_1st_disapproval_peqcs_oper" name="text_view_1st_disapproval_peqcs_oper" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="" for="">Reason for Disapproval:</label>
                                <input class="form-control" type="text" id="text_view_2nd_disapproval_peqcs_oper" name="text_view_2nd_disapproval_peqcs_oper" readonly>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <label for="">Certified by:</label>
                            </div>

                            <div class="col-md-6">
                                <label for="">Certified by:</label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-1">
                                <p for="">Production:</p>
                            </div>

                            <div class="col-md-5">
                                <input class="form-control mb-2" type="text" id="text_view_1st_certified_prod_peqcs_oper" name="text_view_1st_certified_prod_peqcs_oper" readonly>
                            </div>

                            <div class="col-md-1">
                                <p for="">Production:</p>
                            </div>

                            <div class="col-md-5">
                                <input class="form-control mb-2" type="text" id="text_view_2nd_certified_prod_peqcs_oper" name="text_view_2nd_certified_prod_peqcs_oper" readonly>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-1">
                                <p for="">Engineering:</p>
                            </div>

                            <div class="col-md-5">
                                <input class="form-control mb-2" type="text" id="text_view_1st_certified_eng_peqcs_oper" name="text_view_1st_certified_eng_peqcs_oper" readonly>
                            </div>

                            <div class="col-md-1">
                                <p for="">Engineering:</p>
                            </div>

                            <div class="col-md-5">
                                <input class="form-control mb-2" type="text" id="text_view_2nd_certified_eng_peqcs_oper" name="text_view_2nd_certified_eng_peqcs_oper" readonly>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-1">
                                <p for="">Quality Control:</p>
                            </div>

                            <div class="col-md-5">
                                <input class="form-control mb-2" type="text" id="text_view_1st_certified_qc_peqcs_oper" name="text_view_1st_certified_qc_peqcs_oper" readonly>
                            </div>

                            <div class="col-md-1">
                                <p for="">Quality Control:</p>
                            </div>

                            <div class="col-md-5">
                                <input class="form-control mb-2" type="text" id="text_view_2nd_certified_qc_peqcs_oper" name="text_view_2nd_certified_qc_peqcs_oper" readonly>
                            </div>
                        </div>

                        <div class="row mb-5">
                            <div class="col-md-6">
                                <label class="" for="">Date & Time:</label>
                                <input class="form-control" type="text" id="text_view_1st_date_peqcs_oper" name="text_view_1st_date_peqcs_oper" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="" for="">Date & Time:</label>
                                <input class="form-control" type="text" id="text_view_2nd_date_peqcs_oper" name="text_view_2nd_date_peqcs_oper" readonly>
                            </div>
                        </div>

                        <label for="">Note: NG Injection process shall be taken from first lot output</label>

                        <!-- ************************************************************ 5TH SECTION - BACK PAGE - ******************************************************************* -->

                        <hr style="height: 5px; background-color: black; border: none;">

                        <h4 class="mb-3">VALIDATION PROCESS: ENGINEERING SECTION</h4>

                        <div class="row mb-1">
                            <div class="col-md-6">
                                <label class="" for="">Engineering Validation Result:</label>
                            </div>

                            <div class="col-md-6">
                                <input class="form-control" name="text_view_application_vpes_oper" id="text_view_application_vpes_oper" readonly>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <input class="form-control" name="text_view_vpes_oper_1" id="text_view_vpes_oper_1" readonly>
                            </div>
                        </div>

                        <h5 class="mt-3 mb-3">RESULT</h5>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="" for="">First Take</label>
                                <input class="form-control" name="text_view_first_result_vpes_oper" id="text_view_first_result_vpes_oper" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="" for="">Second Take</label>
                                <input class="form-control" name="text_view_second_result_vpes_oper" id="text_view_second_result_vpes_oper" readonly>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="" for="">Validated by (after 2nd day):</label>
                                <input class="form-control" type="text" id="text_view_1st_validatedby_vpes_oper" name="text_view_1st_validatedby_vpes_oper" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="" for="">Validated by (after re-orientation):</label>
                                <input class="form-control" type="text" id="text_view_2nd_validatedby_vpes_oper" name="text_view_2nd_validatedby_vpes_oper" readonly>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="" for="">Date:</label>
                                <input class="form-control" type="text" id="text_view_1st_date_vpes_oper" name="text_view_1st_date_vpes_oper" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="" for="">Date:</label>
                                <input class="form-control" type="text" id="text_view_2nd_date_vpes_oper" name="text_view_2nd_date_vpes_oper" readonly>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="" for="">Remarks:</label>
                                <input class="form-control" type="text" name="text_view_remarks_vpes_oper" id="text_view_remarks_vpes_oper" readonly>
                            </div>
                        </div>

                        <!-- ************************************************************ 6TH SECTION - BACK PAGE - ******************************************************************* -->

                        <hr style="height: 5px; background-color: black; border: none;">

                        <h4 class="mb-3">VALIDATION PROCESS: QUALITY CONTROL SECTION</h4>

                        <div class="row mb-1">
                            <div class="col-md-3">
                                <label class="" for="">QC Validation for other section</label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <input class="form-control" name="text_view_vpqcs_oper" id="text_view_vpqcs_oper" readonly>
                            </div>
                        </div>

                        <h5 class="mt-3 mb-3">RESULT</h5>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="" for="">First Take</label>
                                <input class="form-control" name="text_view_first_result_vpqcs_oper" id="text_view_first_result_vpqcs_oper" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="" for="">Second Take</label>
                                <input class="form-control" name="text_view_second_result_vpqcs_oper" id="text_view_second_result_vpqcs_oper" readonly>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="" for="">Validated by (after 2nd day):</label>
                                <input class="form-control" type="text" id="text_view_1st_validatedby_vpqcs_oper" name="text_view_1st_validatedby_vpqcs_oper" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="" for="">Validated by (after re-orientation):</label>
                                <input class="form-control" type="text" id="text_view_2nd_validatedby_vpqcs_oper" name="text_view_2nd_validatedby_vpqcs_oper" readonly>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="" for="">Date:</label>
                                <input class="form-control" type="text" id="text_view_1st_date_vpqcs_oper" name="text_view_1st_date_vpqcs_oper" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="" for="">Date:</label>
                                <input class="form-control" type="text" id="text_view_2nd_date_vpqcs_oper" name="text_view_2nd_date_vpqcs_oper" readonly>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="" for="">Remarks:</label>
                                <input class="form-control" type="text" name="text_view_remarks_vpqcs_oper" id="text_view_remarks_vpqcs_oper" readonly>
                            </div>
                        </div>

                        <!-- ************************************************************ 2ND SECTION VPQCS ******************************************************************* -->

                        <hr style="height: 5px; background-color: black; border: none;">

                        <div class="row mt-3 mb-1">
                            <div class="col-md-6">
                                <input class="form-control" name="text_view_vpqcs_oper_1_1" id="text_view_vpqcs_oper_1_1" readonly>
                            </div>

                            <div class="col-md-6">
                                <input class="form-control" name="text_view_application_vpqcs_oper" id="text_view_application_vpqcs_oper" readonly>
                            </div>
                        </div>

                        <h5 class="mt-3 mb-3">RESULT</h5>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="" for="">First Take</label>
                                <input class="form-control" name="text_view_first_result_vpes_oper_2" id="text_view_first_result_vpes_oper_2" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="" for="">Second Take</label>
                                <input class="form-control" name="text_view_second_result_vpes_oper_2" id="text_view_second_result_vpes_oper_2" readonly>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="" for="">Validated by (after 3rd day):</label>
                                <input class="form-control" type="text" id="text_view_1st_validatedby_vpes_oper_2" name="text_view_1st_validatedby_vpes_oper_2" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="" for="">Validated by (after re-orientation):</label>
                                <input class="form-control" type="text" id="text_view_2nd_validatedby_vpes_oper_2" name="text_view_2nd_validatedby_vpes_oper_2" readonly>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="" for="">Date:</label>
                                <input class="form-control" type="text" id="text_view_1st_date_vpes_oper_2" name="text_view_1st_date_vpes_oper_2" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="" for="">Date:</label>
                                <input class="form-control" type="text" id="text_view_2nd_date_vpes_oper_2" name="text_view_2nd_date_vpes_oper_2" readonly>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="" for="">Remarks:</label>
                                <input class="form-control" type="text" name="text_view_remarks_vpes_oper_2" id="text_view_remarks_vpes_oper_2" readonly>
                            </div>
                        </div>

                        <!-- ************************************************************ 6TH SECTION - BACK PAGE - ******************************************************************* -->

                        <hr style="height: 5px; background-color: black; border: none;">

                        <h4 class="mb-3">QC Validation for Visual Operator</h4>

                        <p><> Reference Document</p>

                        <div class="row">
                            <div class="col-md-6">
                                <label class="" for="">Work Instruction Document</label>
                            </div>

                            <div class="col-md-6">
                                <label for="">Discuss the inspection sequence in detail.</label>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <input class="form-control" type="text" id="text_view_refdocno_input_qcvvo_oper" name="text_view_refdocno_input_qcvvo_oper" readonly>
                            </div>

                            <div class="col-md-6">
                                <input class="form-control" type="text" id="text_view_ins_seq_qcvvo_oper" name="text_view_ins_seq_qcvvo_oper" readonly>
                            </div>
                        </div>

                        <h6 class="mt-3 mb-3">RESULT:</h6>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="" for="">First Take:</label>
                                <input class="form-control" name="text_view_result1_qcvvo_oper" id="text_view_result1_qcvvo_oper" readonly>
                            </div>

                            <div class="col-md-4">
                                <label class="" for="">Validated by:</label>
                                <input class="form-control" type="text" id="text_view_validated1_qcvvo_oper" name="text_view_validated1_qcvvo_oper" readonly>
                            </div>

                            <div class="col-md-4">
                                <label class="" for="">Date:</label>
                                <input class="form-control" type="text" id="text_view_date1_qcvvo_oper" name="text_view_date1_qcvvo_oper" readonly>
                            </div>
                        </div>

                        <p><> Reference Document</p>

                        <div class="row">
                            <div class="col-md-6">
                                <label class="" for="">Work Instruction Document</label>
                            </div>

                            <div class="col-md-6">
                                <label for="">Discuss the inspection sequence in detail.</label>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <input class="form-control" type="text" id="text_view_refdocno_input_qcvvo_oper_2" name="text_view_refdocno_input_qcvvo_oper_2" readonly>
                            </div>

                            <div class="col-md-6">
                                <input class="form-control" type="text" id="text_view_ins_seq_qcvvo_oper_2" name="text_view_ins_seq_qcvvo_oper_2" readonly>
                            </div>
                        </div>

                        <h6 class="mt-3 mb-3">RESULT:</h6>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="ms-3" for="">Second Take:</label>
                                <input class="form-control" name="text_view_result2__qcvvo_oper" id="text_view_result2__qcvvo_oper" readonly>

                            </div>

                            <div class="col-md-4">
                                <label class="ms-3" for="">Validated by:</label>
                                <input class="form-control" type="text" id="text_view_validated2_qcvvo_oper" name="text_view_validated2_qcvvo_oper" readonly>
                            </div>

                            <div class="col-md-4">
                                <label class="ms-3" for="">Date:</label>
                                <input class="form-control" type="text" id="text_view_date2_qcvvo_oper" name="text_view_date2_qcvvo_oper" readonly>
                            </div>
                        </div>

                        <hr style="height: 5px; background-color: black; border: none;">

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="table-responsive">
                                    <table id="" class="table table-bordered table-hover nowrap">
                                        <thead class="table table-warning">
                                            <tr class="text-center">
                                                <th>Designation</th>
                                                <th>Training / Orientation</th>
                                                <th>Qualifier</th>
                                                <th>Certifier</th>
                                                <th>Approver / Confirmation</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <tr>
                                                <td>Operator</td>
                                                <td>Supervisor and/or Material Handler</td>
                                                <td>Process Engineer</td>
                                                <td>QC Inspector</td>
                                                <td>QC Supervisor</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="">Approved / Confirmed by:</label>
                                <input class="form-control" type="text" id="text_view_oper_approved_confirmed_by" name="text_view_oper_approved_confirmed_by" readonly>
                                <label for="" class="mt-1">QC Supervisor</label>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa-solid fa-xmark me-2" style="color: white"></i>CLOSE</button>
                        </div>

                    {{-- </form> --}}

            </div>
        </div>
    </div>
</div>

<!-- GENERATE REPORT FOR OPERATOR -->
<div class="modal" id="" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">GENERATE PDF</h1>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <form method="POST" action="exportOperFormPDF.php" id="formGeneratePDF" target="_blank">

                    <input type="text" name="text_report_oper_conno" id="text_report_oper_conno">
                    <input type="text" name="text_report_fk_cq_oper_conno" id="text_report_fk_cq_oper_conno">
                    <input type="text" name="text_report_oper_cq_section" id="text_report_oper_cq_section">

                    <input type="text" name="text_report_oper_cq_series" id="text_report_oper_cq_series">
                    <input type="text" name="text_report_oper_cq_prodLine" id="text_report_oper_cq_prodLine">
                    <input type="text" name="text_report_oper_cq_cert_reason" id="text_report_oper_cq_cert_reason">
                    <input type="text" name="text_report_oper_cq_training" id="text_report_oper_cq_training">
                    <input type="text" name="text_report_oper_cq_pt_result1" id="text_report_oper_cq_pt_result1">
                    <input type="text" name="text_report_oper_cq_pt_trainedby1" id="text_report_oper_cq_pt_trainedby1">
                    <input type="text" name="text_report_oper_cq_pt_date_time1" id="text_report_oper_cq_pt_date_time1">
                    <input type="text" name="text_report_oper_cq_pt_result2" id="text_report_oper_cq_pt_result2">
                    <input type="text" name="text_report_oper_cq_pt_trainedby2" id="text_report_oper_cq_pt_trainedby2">
                    <input type="text" name="text_report_oper_cq_pt_date_time2" id="text_report_oper_cq_pt_date_time2">
                    <input type="text" name="text_report_oper_cq_et_training" id="text_report_oper_cq_et_training">
                    <input type="text" name="text_report_oper_cq_es_obs_result1" id="text_report_oper_cq_es_obs_result1">
                    <input type="text" name="text_report_oper_cq_es_sample1" id="text_report_oper_cq_es_sample1">
                    <input type="text" name="text_report_oper_cq_es_ok1" id="text_report_oper_cq_es_ok1">
                    <input type="text" name="text_report_oper_cq_es_ng1" id="text_report_oper_cq_es_ng1">
                    <input type="text" name="text_report_oper_cq_es_oa1" id="text_report_oper_cq_es_oa1">
                    <input type="text" name="text_report_oper_cq_es_disqualification_reason1" id="text_report_oper_cq_es_disqualification_reason1">
                    <input type="text" name="text_report_oper_cq_es_qualifiedby1" id="text_report_oper_cq_es_qualifiedby1">
                    <input type="text" name="text_report_oper_cq_es_date_time1" id="text_report_oper_cq_es_date_time1">
                    <input type="text" name="text_report_oper_cq_es_obs_result2" id="text_report_oper_cq_es_obs_result2">
                    <input type="text" name="text_report_oper_cq_es_sample2" id="text_report_oper_cq_es_sample2">
                    <input type="text" name="text_report_oper_cq_es_ok2" id="text_report_oper_cq_es_ok2">
                    <input type="text" name="text_report_oper_cq_es_ng2" id="text_report_oper_cq_es_ng2">
                    <input type="text" name="text_report_oper_cq_es_oa2" id="text_report_oper_cq_es_oa2">
                    <input type="text" name="text_report_oper_cq_es_disqualification_reason2" id="text_report_oper_cq_es_disqualification_reason2">
                    <input type="text" name="text_report_oper_cq_es_qualifiedby2" id="text_report_oper_cq_es_qualifiedby2">
                    <input type="text" name="text_report_oper_cq_es_date_time2" id="text_report_oper_cq_es_date_time2">
                    <input type="text" name="text_report_oper_cq_qcs_obs_result1" id="text_report_oper_cq_qcs_obs_result1">
                    <input type="text" name="text_report_oper_cq_qcs_sample1" id="text_report_oper_cq_qcs_sample1">
                    <input type="text" name="text_report_oper_cq_qcs_ok1" id="text_report_oper_cq_qcs_ok1">
                    <input type="text" name="text_report_oper_cq_qcs_ng1" id="text_report_oper_cq_qcs_ng1">
                    <input type="text" name="text_report_oper_cq_qcs_training1" id="text_report_oper_cq_qcs_training1">
                    <input type="text" name="text_report_oper_cq_qcs_oa_result1" id="text_report_oper_cq_qcs_oa_result1">
                    <input type="text" name="text_report_oper_cq_qcs_disapproval_reason1" id="text_report_oper_cq_qcs_disapproval_reason1">
                    <input type="text" name="text_report_oper_cq_qcs_certifiedby1" id="text_report_oper_cq_qcs_certifiedby1">
                    <input type="text" name="text_report_oper_cq_qcs_date_time1" id="text_report_oper_cq_qcs_date_time1">
                    <input type="text" name="text_report_oper_cq_qcs_obs_result2" id="text_report_oper_cq_qcs_obs_result2">
                    <input type="text" name="text_report_oper_cq_qcs_sample2" id="text_report_oper_cq_qcs_sample2">
                    <input type="text" name="text_report_oper_cq_qcs_ok2" id="text_report_oper_cq_qcs_ok2">
                    <input type="text" name="text_report_oper_cq_qcs_ng2" id="text_report_oper_cq_qcs_ng2">
                    <input type="text" name="text_report_oper_cq_qcs_training2" id="text_report_oper_cq_qcs_training2">
                    <input type="text" name="text_report_oper_cq_qcs_oa_result2" id="text_report_oper_cq_qcs_oa_result2">
                    <input type="text" name="text_report_oper_cq_qcs_disapproval_reason2" id="text_report_oper_cq_qcs_disapproval_reason2">
                    <input type="text" name="text_report_oper_cq_qcs_certifiedby2" id="text_report_oper_cq_qcs_certifiedby2">
                    <input type="text" name="text_report_oper_cq_qcs_date_time2" id="text_report_oper_cq_qcs_date_time2">

                    <!-- FOR BACK PAGE -->

                    <input type="text" name="text_report_oper_cq_peqc_lot1" id="text_report_oper_cq_peqc_lot1">
                    <input type="text" name="text_report_oper_cq_peqc_inj1" id="text_report_oper_cq_peqc_inj1">
                    <input type="text" name="text_report_oper_cq_peqc_dej1" id="text_report_oper_cq_peqc_dej1">
                    <input type="text" name="text_report_oper_cq_peqc_oa_result1" id="text_report_oper_cq_peqc_oa_result1">
                    <input type="text" name="text_report_oper_cq_peqc_disapproval_reason1" id="text_report_oper_cq_peqc_disapproval_reason1">
                    <input type="text" name="text_report_oper_cq_peqc_prod_certified1" id="text_report_oper_cq_peqc_prod_certified1">
                    <input type="text" name="text_report_oper_cq_peqc_eng_certified1" id="text_report_oper_cq_peqc_eng_certified1">
                    <input type="text" name="text_report_oper_cq_peqc_qc_certified1" id="text_report_oper_cq_peqc_qc_certified1">
                    <input type="text" name="text_report_oper_cq_peqc_date_time1" id="text_report_oper_cq_peqc_date_time1">
                    <input type="text" name="text_report_oper_cq_peqc_lot2" id="text_report_oper_cq_peqc_lot2">
                    <input type="text" name="text_report_oper_cq_peqc_inj2" id="text_report_oper_cq_peqc_inj2">
                    <input type="text" name="text_report_oper_cq_peqc_dej2" id="text_report_oper_cq_peqc_dej2">
                    <input type="text" name="text_report_oper_cq_peqc_oa_result2" id="text_report_oper_cq_peqc_oa_result2">
                    <input type="text" name="text_report_oper_cq_peqc_disapproval_reason2" id="text_report_oper_cq_peqc_disapproval_reason2">
                    <input type="text" name="text_report_oper_cq_peqc_prod_certified2" id="text_report_oper_cq_peqc_prod_certified2">
                    <input type="text" name="text_report_oper_cq_peqc_eng_certified2" id="text_report_oper_cq_peqc_eng_certified2">
                    <input type="text" name="text_report_oper_cq_peqc_qc_certified2" id="text_report_oper_cq_peqc_qc_certified2">
                    <input type="text" name="text_report_oper_cq_peqc_date_time2" id="text_report_oper_cq_peqc_date_time2">
                    <input type="text" name="text_report_oper_cq_vpes_applicable" id="text_report_oper_cq_vpes_applicable">
                    <input type="text" name="text_report_oper_cq_vpes_validation_result" id="text_report_oper_cq_vpes_validation_result">
                    <input type="text" name="text_report_oper_cq_vpes_result1" id="text_report_oper_cq_vpes_result1">
                    <input type="text" name="text_report_oper_cq_vpes_validatedby1" id="text_report_oper_cq_vpes_validatedby1">
                    <input type="text" name="text_report_oper_cq_vpes_date1" id="text_report_oper_cq_vpes_date1">
                    <input type="text" name="text_report_oper_cq_vpes_result2" id="text_report_oper_cq_vpes_result2">
                    <input type="text" name="text_report_oper_cq_vpes_validatedby2" id="text_report_oper_cq_vpes_validatedby2">
                    <input type="text" name="text_report_oper_cq_vpes_date2" id="text_report_oper_cq_vpes_date2">
                    <input type="text" name="text_report_oper_cq_vpes_remarks" id="text_report_oper_cq_vpes_remarks">
                    <input type="text" name="text_report_oper_cq_vpqcs_validation_station" id="text_report_oper_cq_vpqcs_validation_station">
                    <input type="text" name="text_report_oper_cq_vpqcs_result1" id="text_report_oper_cq_vpqcs_result1">
                    <input type="text" name="text_report_oper_cq_vpqcs_validatedby1" id="text_report_oper_cq_vpqcs_validatedby1">
                    <input type="text" name="text_report_oper_cq_vpqcs_date1" id="text_report_oper_cq_vpqcs_date1">
                    <input type="text" name="text_report_oper_cq_vpqcs_result2" id="text_report_oper_cq_vpqcs_result2">
                    <input type="text" name="text_report_oper_cq_vpqcs_validatedby2" id="text_report_oper_cq_vpqcs_validatedby2">
                    <input type="text" name="text_report_oper_cq_vpqcs_date2" id="text_report_oper_cq_vpqcs_date2">
                    <input type="text" name="text_report_oper_cq_vpqcs_remarks" id="text_report_oper_cq_vpqcs_remarks">
                    <input type="text" name="text_report_oper_cq_vpqcs_sheet_checking" id="text_report_oper_cq_vpqcs_sheet_checking">
                    <input type="text" name="text_report_oper_cq_vpqcs_applicable" id="text_report_oper_cq_vpqcs_applicable">
                    <input type="text" name="text_report_oper_cq_vpqcs_sheet_result1" id="text_report_oper_cq_vpqcs_sheet_result1">
                    <input type="text" name="text_report_oper_cq_vpqcs_sheet_validatedby1" id="text_report_oper_cq_vpqcs_sheet_validatedby1">
                    <input type="text" name="text_report_oper_cq_vpqcs_sheet_date1" id="text_report_oper_cq_vpqcs_sheet_date1">
                    <input type="text" name="text_report_oper_cq_vpqcs_sheet_result2" id="text_report_oper_cq_vpqcs_sheet_result2">
                    <input type="text" name="text_report_oper_cq_vpqcs_sheet_validatedby2" id="text_report_oper_cq_vpqcs_sheet_validatedby2">
                    <input type="text" name="text_report_oper_cq_vpqcs_sheet_date2" id="text_report_oper_cq_vpqcs_sheet_date2">
                    <input type="text" name="text_report_oper_cq_vpqcs_sheet_remarks" id="text_report_oper_cq_vpqcs_sheet_remarks">
                    <input type="text" name="text_report_oper_cq_vvo_docno1" id="text_report_oper_cq_vvo_docno1">
                    <input type="text" name="text_report_oper_cq_vvo_sequence_details1" id="text_report_oper_cq_vvo_sequence_details1">
                    <input type="text" name="text_report_oper_cq_vvo_result1" id="text_report_oper_cq_vvo_result1">
                    <input type="text" name="text_report_oper_cq_vvo_validatedby1" id="text_report_oper_cq_vvo_validatedby1">
                    <input type="text" name="text_report_oper_cq_vvo_date1" id="text_report_oper_cq_vvo_date1">
                    <input type="text" name="text_report_oper_cq_vvo_docno2" id="text_report_oper_cq_vvo_docno2">
                    <input type="text" name="text_report_oper_cq_vvo_sequence_details2" id="text_report_oper_cq_vvo_sequence_details2">
                    <input type="text" name="text_report_oper_cq_vvo_result2" id="text_report_oper_cq_vvo_result2">
                    <input type="text" name="text_report_oper_cq_vvo_validatedby2" id="text_report_oper_cq_vvo_validatedby2">
                    <input type="text" name="text_report_oper_cq_vvo_date2" id="text_report_oper_cq_vvo_date2">
                    <input type="text" name="text_report_oper_cq_approved_confirmedby" id="text_report_oper_cq_approved_confirmedby">

                    <input type="submit" class="btn btn-warning float-start pdf" id="pdf" name="pdf" value="Generate PDF">
                </form>

            </div>
        </div>
    </div>
</div>
