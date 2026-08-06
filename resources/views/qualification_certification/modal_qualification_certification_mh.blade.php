<!-- FORMAT 1 MH -->
<div class="d-none" id="divMH">
    <form id="formSubmit_MH">
        <h3 class="mt-5 mb-3 text-center">MATERIAL HANDLER'S TRAINING / QUALIFICATION / CERTIFICATION SLIP</h3>

        <div class="row mb-5">
            <div class="col-md-3">
                <label for="">Control No.:</label>
                <input class="form-control" type="hidden" class="form-control d-none" id="text_mh_new_conno" name="text_mh_new_conno" placeholder="Select section to generate Control No." readonly>
                <input class="form-control" type="text" class="form-control" id="" name="text_mh_new_conno" placeholder="Auto Generated" readonly>
                <input class="form-control" type="hidden" class="form-control" id="text_mh_conno" name="text_mh_conno" readonly>
            </div>

            <div class="col-md-3">
                <label for="">Production Section:</label>
                <select class="form-control select2bs4" style="width: 100%;" name="text_mh_section" id="text_mh_section"></select>
                </select>
            </div>

            <div class="col-md-3">
                <label for="">Series Name:</label>
                <input class="form-control" type="text" id="text_mh_series" name="text_mh_series" placeholder="Enter series name here">
            </div>

            <div class="col-md-3">
                <label for="">Product Line:</label>
                    <select class="form-control select2bs4" style="width: 100%;" name="text_mh_product_line" id="text_mh_product_line" placeholder="product line here">
                </select>
                </datalist>
            </div>
        </div>

        <div class="row mt-2 mb-5">
            <div class="col-md-12">
                <button type="button" class="btn btn-primary" id="" data-target="#select_Employee" data-toggle="modal" ><i class="fa-solid fa-user-plus me-3"></i>Add Employee</button>
            </div>
        </div>

        <div class="table-responsive mt-3 mb-5">
            <table id="tbl_certified_list_MH" class="table table-bordered table-hover nowrap">
                <thead class="table-primary">
                    <tr>
                        <th>Action</th>
                        <th>Employee No.</th>
                        <th>Employee Name</th>
                        <th>Station From (Series)</th>
                        <th>Station To</th>
                    </tr>
                </thead>

                <tbody>
                </tbody>
            </table>
        </div>

        <div class="row mb-5">
            <div class="col-md-12">
                <label for="">Reason for Certification:</label>
                <select class="form-control select2bs4" style="width: 100%;" name="text_mh_certification" id="text_mh_certification">
                    <option value="" selected disabled>Select Reason</option>
                    <option value="Newly hired employees">1 Newly hired employees</option>
                    <option value="Newly promoted employees">2 Newly promoted employees</option>
                    <option value="Lateral transfer">3 Lateral transfer</option>
                    <option value="Flexibility1">4 Flexibility</option>
                    <option value="Transfer to another station">5 Transfer to another station (E.g: final visual, insertion,IQC, IPQC, OQC, etc)</option>
                    <option value="Transfer to other production section">6 Transfer to other production section (E.g: TS,PPS,CN, YF)</option>
                    <option value="Transfer to other product line">7 Transfer to other product line (E.g: TS: BGA-FP, QFP; CN: FMS, PJS; YF: EOL, FOL; PPS: Molding CN, Molding TS, Grinding, Stamping; MH-WHS, MH-Prodn)</option>
                    <option value="New Product">8 New Product</option>
                    <option value="Re-certification from disqualification">9 Re-certification from disqualification</option>
                    <option value="Leave reached at least 1 month (ML/ VL/SL)">10 Leave reached at least 1 month (ML/ VL/SL)</option>
                    <option value="Company shutdown at least 1 month">11 Company shutdown at least 1 month</option>
                    <option value="Re-certification">12 Re-certification</option>
                </select>
            </div>
        </div>

        <div class="accordion" id="accordionExampleMH">
            <div class="accordion-item">
                <h2 class="accordion-header">
                <button class="accordion-button" type="button" data-toggle="collapse" data-target="#collapseOneMH" aria-expanded="true" aria-controls="collapseOneMH">
                    <h5>PRODUCTION / WAREHOUSE SUPERVISOR (Training/Orientation)</h5>
                </button>
                </h2>
                <div id="collapseOneMH" class="accordion-collapse collapse show" data-parent="#accordionExampleMH">
                    <div class="accordion-body">

                        <!-- ------------------------------------------------ -->

                        <div class="row mb-2">
                            <div class="col-md-1"></div>

                            <div class="col-md-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="text_mh_training_orientation1" name="text_mh_training_orientation" value="SOP" style="width: 1.2rem; height: 1.2rem; border: 2px solid black; accent-color: #007bff;">
                                    <label class="fs-5  " for="text_mh_training_orientation1"  style="font-weight: normal;">1) SOP</label>
                                </div>
                            </div>

                            <div class="col-md-5"></div>

                            <div class="col-md-5">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="text_mh_training_orientation6" name="text_mh_training_orientation" value="Work Instruction and Point Panel"  style="width: 1.2rem; height: 1.2rem; border: 2px solid black; accent-color: #007bff;">
                                    <label class="fs-5  " for="text_mh_training_orientation6" style="font-weight: normal;">6) Work Instruction and Point Panel</label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-md-1"></div>

                            <div class="col-md-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="text_mh_training_orientation2" name="text_mh_training_orientation" value="Process Flow" style="width: 1.2rem; height: 1.2rem; border: 2px solid black; accent-color: #007bff;">
                                    <label class="fs-5  " for="text_mh_training_orientation2" style="font-weight: normal;">2) Process Flow</label>
                                </div>
                            </div>

                            <div class="col-md-4"></div>

                            <div class="col-md-5">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="text_mh_training_orientation7" name="text_mh_training_orientation" value="Production Abnormality Control (IMS-PMI-025)" style="width: 1.2rem; height: 1.2rem; border: 2px solid black; accent-color: #007bff;">
                                    <label class="fs-5" for="text_mh_training_orientation7" style="font-weight: normal;">7) Production Abnormality Control (IMS-PMI-025)</label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-md-1"></div>

                            <div class="col-md-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="text_mh_training_orientation3" name="text_mh_training_orientation" value="Product Drawing" style="width: 1.2rem; height: 1.2rem; border: 2px solid black; accent-color: #007bff;">
                                    <label class="fs-5  " for="text_mh_training_orientation3" style="font-weight: normal;">3) Product Drawing</label>
                                </div>
                            </div>

                            <div class="col-md-4"></div>

                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="text_mh_training_orientation8" name="text_mh_training_orientation" value="Rule when to escalate" style="width: 1.2rem; height: 1.2rem; border: 2px solid black; accent-color: #007bff;">
                                    <label class="fs-5  " for="text_mh_training_orientation8" style="font-weight: normal;">Rule when to escalate</label>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="text_mh_training_orientation9" name="text_mh_training_orientation" value="Filling-up of forms" style="width: 1.2rem; height: 1.2rem; border: 2px solid black; accent-color: #007bff;">
                                    <label class="fs-5  " for="text_mh_training_orientation9" style="font-weight: normal;">Filling-up of forms</label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-md-1"></div>

                            <div class="col-md-5">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="text_mh_training_orientation4" name="text_mh_training_orientation" value="Past Trouble History (claim, lot-out, yield, etc)" style="width: 1.2rem; height: 1.2rem; border: 2px solid black; accent-color: #007bff;">
                                    <label class="fs-5  " for="text_mh_training_orientation4" style="font-weight: normal;">4) Past Trouble History (claim, lot-out, yield, etc)</label>
                                </div>
                            </div>

                            <div class="col-md-1"></div>

                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="text_mh_training_orientation10" name="text_mh_training_orientation" value="Dropped on the floor" style="width: 1.2rem; height: 1.2rem; border: 2px solid black; accent-color: #007bff;">
                                    <label class="fs-5  " for="text_mh_training_orientation10" style="font-weight: normal;">7.1 Dropped on the floor</label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-1"></div>

                            <div class="col-md-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="text_mh_training_orientation5" name="text_mh_training_orientation" value="Defect escalation" style="width: 1.2rem; height: 1.2rem; border: 2px solid black; accent-color: #007bff;">
                                    <label class="fs-5  " for="text_mh_training_orientation5" style="font-weight: normal;">5) Defect escalation</label>
                                </div>
                            </div>

                            <div class="col-md-4"></div>

                            <div class="col-md-1" style="border: 2px solid black; padding: 8px; height: 48px;">
                                <div class="form-check">
                                    <label class="fs-5  " for="" style="font-weight: normal;">CN</label>
                                </div>
                            </div>

                            <div class="col-md-2" style="border: 2px solid black; padding: 8px; height: 48px;">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="text_mh_training_orientation11" name="text_mh_training_orientation" value="WI-CN-216" style="width: 1.2rem; height: 1.2rem; border: 2px solid black; accent-color: #007bff;">
                                    <label class="fs-5  " for="text_mh_training_orientation11" style="font-weight: normal;">WI-CN-216</label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-1"></div>

                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="text_mh_training_orientation5_1" name="text_mh_training_orientation" value="Rule when to escalate_2" style="width: 1.2rem; height: 1.2rem; border: 2px solid black; accent-color: #007bff;">
                                    <label class="fs-5  " for="text_mh_training_orientation5_1" style="font-weight: normal;">Rule when to escalate</label>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="text_mh_training_orientation5_2" name="text_mh_training_orientation" value="Filling-up of forms_2" style="width: 1.2rem; height: 1.2rem; border: 2px solid black; accent-color: #007bff;">
                                    <label class="fs-5  " for="text_mh_training_orientation5_2" style="font-weight: normal;">Filling-up of forms</label>
                                </div>
                            </div>

                            <div class="col-md-1"></div>

                            <div class="col-md-1" style="border: 2px solid black; padding: 8px; height: 48px;">
                                <div class="form-check">
                                    <label class="fs-5  " for="" style="font-weight: normal;">CN</label>
                                </div>
                            </div>

                            <div class="col-md-2" style="border: 2px solid black; padding: 8px; height: 48px;">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="text_mh_training_orientation12" name="text_mh_training_orientation" value="CN PP-CN-407" style="width: 1.2rem; height: 1.2rem; border: 2px solid black; accent-color: #007bff;">
                                    <label class="fs-5  " for="text_mh_training_orientation12" style="font-weight: normal;">PP-CN-407</label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-md-1"></div>

                            <div class="col-md-1" style="border: 2px solid black; padding: 8px; height: 48px;">
                                <div class="form-check">
                                    <!-- <input class="form-check-input" type="checkbox" id="text_training_orientation5_1" name="text_training_orientation" value=""> -->
                                    <label class="fs-5  " for="" style="font-weight: normal;">CN</label>
                                </div>
                            </div>

                            <div class="col-md-2" style="border: 2px solid black; padding: 8px; height: 48px;">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="text_mh_training_orientation17" name="text_mh_training_orientation" value="CN PP-CN-010" style="width: 1.2rem; height: 1.2rem; border: 2px solid black; accent-color: #007bff;">
                                    <label class="fs-5  " for="text_mh_training_orientation17" style="font-weight: normal;">PP-CN-010</label>
                                </div>
                            </div>

                            <div class="col-md-3"></div>

                            <div class="col-md-1" style="border: 2px solid black; padding: 8px; height: 48px;">
                                <div class="form-check">
                                    <label class="fs-5  " for="" style="font-weight: normal;">CN</label>
                                </div>
                            </div>

                            <div class="col-md-2" style="border: 2px solid black; padding: 8px; 5px; height: 48px;">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="text_mh_training_orientation13" name="text_mh_training_orientation" value="CN PP-CN-066" style="width: 1.2rem; height: 1.2rem; border: 2px solid black; accent-color: #007bff;">
                                    <label class="fs-5  " for="text_mh_training_orientation13" style="font-weight: normal;">PP-CN-066</label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-md-1"></div>

                            <div class="col-md-1" style="border: 2px solid black; padding: 8px; 5px; height: 48px;">
                                <div class="form-check">
                                    <label class="fs-5  " for="" style="font-weight: normal;">PPS</label>
                                </div>
                            </div>

                            <div class="col-md-2" style="border: 2px solid black; padding: 8px; 5px; height: 48px;">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="text_mh_training_orientation18" name="text_mh_training_orientation" value="PPS PP-MDGEN-135" style="width: 1.2rem; height: 1.2rem; border: 2px solid black; accent-color: #007bff;">
                                    <label class="fs-5  " for="text_mh_training_orientation18" style="font-weight: normal;">PP-MDGEN-135</label>
                                </div>
                            </div>

                            <div class="col-md-3"></div>

                            <div class="col-md-1" style="border: 2px solid black; padding: 8px; height: 48px;">
                                <div class="form-check">
                                    <label class="fs-5  " for="" style="font-weight: normal;">PPS</label>
                                </div>
                            </div>

                            <div class="col-md-2" style="border: 2px solid black; padding: 8px; height: 48px;">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="text_mh_training_orientation14" name="text_mh_training_orientation" value="PPS PP-MDGEN-136" style="width: 1.2rem; height: 1.2rem; border: 2px solid black; accent-color: #007bff;">
                                    <label class="fs-5  " for="text_mh_training_orientation14" style="font-weight: normal;">PP-MDGEN-136</label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-md-1"></div>

                            <div class="col-md-1" style="border: 2px solid black; padding: 8px; height: 48px;">
                                <div class="form-check">
                                    <label class="fs-5  " for="" style="font-weight: normal;">YF</label>
                                </div>
                            </div>

                            <div class="col-md-2" style="border: 2px solid black; padding: 8px; height: 48px;">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="text_mh_training_orientation19" name="text_mh_training_orientation" value="YF PP-YFLEX-296" style="width: 1.2rem; height: 1.2rem; border: 2px solid black; accent-color: #007bff;">
                                    <label class="fs-5  " for="text_mh_training_orientation19" style="font-weight: normal;">PP-YFLEX-296</label>
                                </div>
                            </div>

                            <div class="col-md-3"></div>

                            <div class="col-md-1" style="border: 2px solid black; padding: 8px; height: 48px;">
                                <div class="form-check">
                                    <label class="fs-5  " for="" style="font-weight: normal;">YF</label>
                                </div>
                            </div>

                            <div class="col-md-2" style="border: 2px solid black; padding: 8px; height: 48px;">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="text_mh_training_orientation15" name="text_mh_training_orientation" value="YF PP-YFLEX-448" style="width: 1.2rem; height: 1.2rem; border: 2px solid black; accent-color: #007bff;">
                                    <label class="fs-5  " for="text_mh_training_orientation15" style="font-weight: normal;">PP-YFLEX-448</label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-md-1"></div>

                            <div class="col-md-1" style="border: 2px solid black; padding: 8px; height: 48px;">
                                <div class="form-check">
                                    <label class="fs-5  " for="" style="font-weight: normal;">TS</label>
                                </div>
                            </div>

                            <div class="col-md-2" style="border: 2px solid black; padding: 8px; height: 48px;">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="text_mh_training_orientation20" name="text_mh_training_orientation" value="TS PP-TSDGEN-046" style="width: 1.2rem; height: 1.2rem; border: 2px solid black; accent-color: #007bff;">
                                    <label class="fs-5  " for="text_mh_training_orientation20" style="font-weight: normal;">PP-TSDGEN-046</label>
                                </div>
                            </div>

                            <div class="col-md-3"></div>

                            <div class="col-md-1" style="border: 2px solid black; padding: 8px; height: 48px;">
                                <div class="form-check">
                                    <label class="fs-5  " for="" style="font-weight: normal;">TS</label>
                                </div>
                            </div>

                            <div class="col-md-2" style="border: 2px solid black; padding: 8px; height: 48px;">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="text_mh_training_orientation16" name="text_mh_training_orientation" value="TS PP-TSDGEN-039" style="width: 1.2rem; height: 1.2rem; border: 2px solid black; accent-color: #007bff;">
                                    <label class="fs-5  " for="text_mh_training_orientation16" style="font-weight: normal;">PP-TSDGEN-039</label>
                                </div>
                            </div>
                        </div>

                        <h4 class="mt-3 mb-3">RESULT</h4>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="ms-3" for="">First Take:</label>
                                <select class="form-control select2bs4" style="width: 100%;" name="text_mh_first_result" id="text_mh_first_result">
                                    <option value="" selected disabled>Select Result</option>
                                    <option value="PASSED">PASSED</option>
                                    <option value="FAILED">FAILED</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="ms-3" for="">Second Take:</label>
                                <select class="form-control select2bs4" style="width: 100%;" name="text_mh_second_result" id="text_mh_second_result">
                                    <option value="" selected disabled>Select Result</option>
                                    <option value="PASSED">PASSED</option>
                                    <option value="FAILED">FAILED</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="ms-3" for="">Trained by:</label>
                                        <input class="form-control" type="text" id="text_mh_first_trained_by" name="text_mh_first_trained_by" list="list_display_empno" placeholder="Select Trained by">
                                        <datalist id="list_display_empno"></datalist>

                                        <input type="hidden" id="text_mh_first_trained_by_username" name="text_mh_first_trained_by_username">
                                        <input type="hidden" id="text_mh_first_trained_by_email" name="text_mh_first_trained_by_email">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="ms-3" for="">Mentored by:</label>
                                        <input class="form-control" type="text" id="text_mh_first_mentored_by" name="text_mh_first_mentored_by" list="list_display_empno" placeholder="Select Mentored by">
                                        <datalist id="list_display_empno"></datalist>
                                    </div>
                                </div>

                            </div>

                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="ms-3" for="">Trained by:</label>
                                        <input class="form-control" type="text" id="text_mh_second_trained_by" name="text_mh_second_trained_by" list="list_display_empno" placeholder="Select Trained by">
                                        <datalist id="list_display_empno"></datalist>

                                        <input type="hidden" id="text_mh_second_trained_by_username" name="text_mh_second_trained_by_username">
                                        <input type="hidden" id="text_mh_second_trained_by_email" name="text_mh_second_trained_by_email">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="ms-3" for="">Mentored by:</label>
                                        <input class="form-control" type="text" id="text_mh_second_mentored_by" name="text_mh_second_mentored_by" list="list_display_empno" placeholder="Select Mentored by">
                                        <datalist id="list_display_empno"></datalist>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="row mb-5">
                            <div class="col-md-3">
                                <label class="ms-3" for="">Date:</label>
                                <input class="form-control" type="date" id="text_mh_first_date" name="text_mh_first_date">
                            </div>

                            <div class="col-md-3">
                                <label class="ms-3" for="">Time:</label>
                                <input class="form-control" type="time" id="text_mh_first_time" name="text_mh_first_time">
                            </div>

                            <div class="col-md-3">
                                <label class="ms-3" for="">Date:</label>
                                <input class="form-control" type="date" id="text_mh_second_date" name="text_mh_second_date">
                            </div>

                            <div class="col-md-3">
                                <label class="ms-3" for="">Time:</label>
                                <input class="form-control" type="time" id="text_mh_second_time" name="text_mh_second_time">
                            </div>
                        </div>

                        <!-- <div class="row mb-2">
                            <div class="col-md-6"></div>
                            <div class="col-md-3">
                                <label class="" for="">Send Email Alert to:</label>
                                <input class="form-control" type="text" id="text_alert_pw_sec_mh" name="text_alert_pw_sec_mh" list="list_display_empno" placeholder="Send email to">
                                <datalist id="list_display_empno"></datalist>
                                <input type="hidden" id="text_alert_pw_sec_mh_username" name="text_alert_pw_sec_mh_username">
                                <input type="hidden" id="text_alert_pw_sec_mh_email" name="text_alert_pw_sec_mh_email">
                            </div>

                            <div class="col-md-3">
                                <label class="" for="">Add cc:</label>
                                <input class="form-control" type="text" id="text_alert_pw_cc_sec_mh" name="text_alert_pw_cc_sec_mh" list="list_display_empno" placeholder="Cc">
                                <datalist id="list_display_empno"></datalist>
                                <input type="hidden" id="text_alert_pw_cc_sec_mh_username" name="text_alert_pw_cc_sec_mh_username">
                                <input type="hidden" id="text_alert_pw_cc_sec_mh_email" name="text_alert_pw_cc_sec_mh_email">
                            </div>
                        </div> -->

                        <div class="row mb-2">
                            <div class="col-md-6"></div>

                            <!-- PW Section: To -->
                            <div class="col-md-3">
                                <label for="text_alert_pw_sec_mh">Send Email Alert to:</label>
                                <div id="selectedPwAlertRecipients" class="fs-5 mb-1"></div> <!-- Badge container -->
                                <input class="form-control" type="text"
                                    id="text_alert_pw_sec_mh"
                                    name="text_alert_pw_sec_mh"
                                    list="list_display_empno"
                                    placeholder="Send email to">
                                <datalist id="list_display_empno"></datalist>
                                <input type="hidden" id="text_alert_pw_sec_mh_username" name="text_alert_pw_sec_mh_username">
                                <input type="hidden" id="text_alert_pw_sec_mh_email" name="text_alert_pw_sec_mh_email">
                            </div>

                            <!-- PW Section: CC -->
                            <div class="col-md-3">
                                <label for="text_alert_pw_cc_sec_mh">Add cc:</label>
                                <div id="selectedPwAlertCcRecipients" class="fs-5 mb-1"></div> <!-- Badge container -->
                                <input class="form-control" type="text"
                                    id="text_alert_pw_cc_sec_mh"
                                    name="text_alert_pw_cc_sec_mh"
                                    list="list_display_empno"
                                    placeholder="Cc">
                                <datalist id="list_display_empno"></datalist>
                                <input type="hidden" id="text_alert_pw_cc_sec_mh_username" name="text_alert_pw_cc_sec_mh_username">
                                <input type="hidden" id="text_alert_pw_cc_sec_mh_email" name="text_alert_pw_cc_sec_mh_email">
                            </div>
                        </div>

                        <!-- ------------------------------------------------ -->

                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-toggle="collapse" data-target="#collapseTwoMH" aria-expanded="false" aria-controls="collapseTwoMH">
                    <h5>LINE QUALITY CONTROL SECTION (Certification)</h5>
                </button>
                </h2>
                <div id="collapseTwoMH" class="accordion-collapse collapse" data-parent="#accordionExampleMH">
                    <div class="accordion-body">

                        <!-- ------------------------------------------------ -->

                        <h4 class="mt-5 mb-3">QUALITY CONTROL SECTION (CERTIFICATION)</h4>

                        <p class="mb-3">1. Let the operator discuss the details of training/orientation conducted by concerned Supervisor and Eng'r as per check items specified.</p>

                        <div class="row mb-3">
                            <label for="">Result:</label>
                            <input class="form-control" type="text" id="text_mh_result_input" name="text_mh_result_input">
                        </div>

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
                                <p class="" for="">1.1 Observation / Interview Result</p>
                                <select class="form-control select2bs4" style="width: 100%;" name="text_mh_obs_first_result" id="text_mh_obs_first_result">
                                    <option value="" selected disabled>Select Result</option>
                                    <option value="PASSED">PASSED</option>
                                    <option value="FAILED">FAILED</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <p class="" for="">1.1 Observation / Interview Result</p>
                                <select class="form-control select2bs4" style="width: 100%;" name="text_mh_obs_second_result" id="text_mh_obs_second_result">
                                    <option value="" selected disabled>Select Result</option>
                                    <option value="PASSED">PASSED</option>
                                    <option value="FAILED">FAILED</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="" for="">2. Sample Checking:</p>
                                <input class="form-control" type="number" placeholder="Enter sample count" min="0" name="text_mh_first_sample" id="text_mh_first_sample">
                            </div>

                            <div class="col-md-6">
                                <p class="" for="">2. Sample Checking:</p>
                                <input class="form-control" type="number" placeholder="Enter sample count" min="0" name="text_mh_second_sample" id="text_mh_second_sample">
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-3">
                                <p class="" for="">OK:</p>
                                <input class="form-control" type="number" placeholder="Enter sample count" min="0" name="text_mh_first_ok" id="text_mh_first_ok">
                            </div>

                            <div class="col-md-3">
                                <p class="" for="">NG:</p>
                                <input class="form-control" type="number" placeholder="Enter sample count" min="0" name="text_mh_first_ng" id="text_mh_first_ng">
                            </div>

                            <div class="col-md-3">
                                <p class="" for="">OK:</p>
                                <input class="form-control" type="number" placeholder="Enter sample count" min="0" name="text_mh_second_ok" id="text_mh_second_ok">
                            </div>

                            <div class="col-md-3">
                                <p class="" for="">NG:</p>
                                <input class="form-control" type="number" placeholder="Enter sample count" min="0" name="text_mh_second_ng" id="text_mh_second_ng">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check ms-4">
                                    <input class="form-check-input" type="checkbox" id="text_mh_pdi_1t" name="qc_mh_chkbox" value="PRODUCT DRAWING INTERPRETATION" style="width: 1.2rem; height: 1.2rem; border: 2px solid black; accent-color: #007bff;">
                                    <label class="fs-5" for="text_mh_pdi_1t" style="font-weight: normal;">2.1 PRODUCT DRAWING INTERPRETATION</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check ms-4">
                                    <input class="form-check-input" type="checkbox" id="text_mh_pdi_2t" name="qc_mh_chkbox_2t" value="PRODUCT DRAWING INTERPRETATION" style="width: 1.2rem; height: 1.2rem; border: 2px solid black; accent-color: #007bff;">
                                    <label class="fs-5" for="text_mh_pdi_2t" style="font-weight: normal;">2.1 PRODUCT DRAWING INTERPRETATION</label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check ms-4">
                                    <input class="form-check-input" type="checkbox" id="text_mh_kma_1t" name="qc_mh_chkbox" value="KITTING / MH ACTIVITY (e.g Receiving)" style="width: 1.2rem; height: 1.2rem; border: 2px solid black; accent-color: #007bff;">
                                    <label class="fs-5" for="text_mh_kma_1t" style="font-weight: normal;"> 2.2 KITTING / MH ACTIVITY (e.g Receiving)</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check ms-4">
                                    <input class="form-check-input" type="checkbox" id="text_mh_kma_2t" name="qc_mh_chkbox_2t" value="KITTING / MH ACTIVITY (e.g Receiving)" style="width: 1.2rem; height: 1.2rem; border: 2px solid black; accent-color: #007bff;">
                                    <label class="fs-5" for="text_mh_kma_2t" style="font-weight: normal;"> 2.2 KITTING / MH ACTIVITY (e.g Receiving)</label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-check ms-4">
                                    <input class="form-check-input" type="checkbox" id="text_mh_fifo_1t" name="qc_mh_chkbox" value="FIFO system (WI)" style="width: 1.2rem; height: 1.2rem; border: 2px solid black; accent-color: #007bff;">
                                    <label class="fs-5" for="text_mh_fifo_1t" style="font-weight: normal;"> 2.3 FIFO system (WI)</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check ms-4">
                                    <input class="form-check-input" type="checkbox" id="text_mh_fifo_2t" name="qc_mh_chkbox_2t" value="FIFO system (WI)" style="width: 1.2rem; height: 1.2rem; border: 2px solid black; accent-color: #007bff;">
                                    <label class="fs-5" for="text_mh_fifo_2t" style="font-weight: normal;"> 2.3 FIFO system (WI)</label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="" for="">3. Overall Assessment:</label>
                                <select class="form-control select2bs4" style="width: 100%;" name="text_mh_oa_1st_result" id="text_mh_oa_1st_result">
                                    <option value="" selected disabled>Select Result</option>
                                    <option value="PASSED">PASSED</option>
                                    <option value="FAILED">FAILED</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="" for="">3. Overall Assessment:</label>
                                <select class="form-control select2bs4" style="width: 100%;" name="text_mh_oa_2nd_result" id="text_mh_oa_2nd_result">
                                    <option value="" selected disabled>Select Result</option>
                                    <option value="PASSED">PASSED</option>
                                    <option value="FAILED">FAILED</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="" for="">Reason for Disapproval:</label>
                                <input class="form-control" type="text" id="text_mh_1st_disapproval" name="text_mh_1st_disapproval">
                            </div>

                            <div class="col-md-6">
                                <label class="" for="">Reason for Disapproval:</label>
                                <input class="form-control" type="text" id="text_mh_2nd_disapproval" name="text_mh_2nd_disapproval">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="" for="">Certified by:</label>
                                <input class="form-control" type="text" id="text_mh_1st_certified" name="text_mh_1st_certified" list="list_display_empno" placeholder="Select Certified by">
                                <datalist id="list_display_empno"></datalist>

                                <input type="hidden" id="text_mh_1st_certified_username" name="text_mh_1st_certified_username">
                                <input type="hidden" id="text_mh_1st_certified_email" name="text_mh_1st_certified_email">
                            </div>

                            <div class="col-md-6">
                                <label class="" for="">Certified by:</label>
                                <input class="form-control" type="text" id="text_mh_2nd_certified" name="text_mh_2nd_certified" list="list_display_empno" placeholder="Select Certified by">
                                <datalist id="list_display_empno"></datalist>

                                <input type="hidden" id="text_mh_2nd_certified_username" name="text_mh_2nd_certified_username">
                                <input type="hidden" id="text_mh_2nd_certified_email" name="text_mh_2nd_certified_email">
                            </div>
                        </div>

                        <div class="row mb-5">
                            <div class="col-md-3">
                                <label class="" for="">Date:</label>
                                <input class="form-control" type="date" id="text_mh_qc_1st_date" name="text_mh_qc_1st_date">
                            </div>

                            <div class="col-md-3">
                                <label class="" for="">Time:</label>
                                <input class="form-control" type="time" id="text_mh_qc_1st_time" name="text_mh_qc_1st_time">
                            </div>

                            <div class="col-md-3">
                                <label class="" for="">Date:</label>
                                <input class="form-control" type="date" id="text_mh_qc_2nd_date" name="text_mh_qc_2nd_date">
                            </div>

                            <div class="col-md-3">
                                <label class="" for="">Time:</label>
                                <input class="form-control" type="time" id="text_mh_qc_2nd_time" name="text_mh_qc_2nd_time">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="table-responsive">
                                    <table id="" class="table table-bordered table-hover nowrap">
                                        <thead class="table table-warning">
                                            <tr class="text-center">
                                                <th>Designation</th>
                                                <th>Training Orientation</th>
                                                <th>Qualifier</th>
                                                <th>Certifier</th>
                                                <th>Approver / Confirmation</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <tr>
                                                <td rowspan="2" class="text-center align-middle">Material Handler</td>
                                                <td>Production / Warehouse</td>
                                                <td rowspan="2" class="text-center align-middle">n/a</td>
                                                <td></td>
                                                <td>- Production Head</td>
                                            </tr>

                                            <tr>
                                                <td><> Supervisor</td>
                                                <td>QC Inspector</td>
                                                <td>- PPC-Whse Head</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- ------------------------------------------------ -->

                    </div>
                </div>
            </div>
        </div>

        <hr style="height: 5px; background-color: black; border: none;">

        <div class="col-md-6">
            <label for="">Approved / Confirmed by:</label>
            <input class="form-control" type="text" id="text_mh_approved_confirmed_by" name="text_mh_approved_confirmed_by" list="list_display_empno" placeholder="Select Certified by">
            <datalist id="list_display_empno"></datalist>

            <label for="" class="mt-1">Prodn / PPC-WHSE Sec. Head</label>

            <input type="hidden" id="text_mh_approved_confirmed_by_username" name="text_mh_approved_confirmed_by_username">
            <input type="hidden" id="text_mh_approved_confirmed_by_email" name="text_mh_approved_confirmed_by_email">
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa-solid fa-xmark me-2" style="color: white"></i>CLOSE</button>
            <button type="submit" class="btn btn-success" id="addNew"><i class="fa-solid fa-file-import me-2" style="color: white"></i>SUBMIT</button>
        </div>

    </form>

</div>