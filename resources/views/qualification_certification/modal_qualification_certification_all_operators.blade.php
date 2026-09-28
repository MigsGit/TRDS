<!-- FORMAT 1 MH -->
<form id="formSubmitOperators">
        <h3 class="mt-5 mb-3 text-center">Operators OPERATOR'S TRAINING / CERTIFICATION AND VALIDATION SLIP</h3>
    
        <div class="accordion" id="accordionExampleOperators">
            @include('qualification_certification.operator_training_items_table', [
                'tableId'        => 'tblTrainingItemsOperators',
                'accordionParent' => '#accordionExampleOperators',
            ])
            <div class="accordion-item">
                <h2 class="card-header">
                <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOneOperators" aria-expanded="true" aria-controls="collapseOneOperators">
                    <h5>2) PRODUCT ORIENTATION AND SAMPLE CHECK (QC)</h5>
                </button>
                </h2>
                <div id="collapseOneOperators" class="accordion-collapse collapse show" data-parent="#accordionExampleOperators">
                    <div class="card-body">
                        <!-- ------------------------------------------------ -->

                        <label class="mb-3">QUALITY CONTROL SECTION</label>

                            <div class="row">
                                <div class="col-md-6">
                                    <p class="" for="">First Take:</p>
                                    <p class="" for="">1. Actual Product Orientation</p>
                                </div>

                                <div class="col-md-6">
                                    <p class="" for="">Second Take:</p>
                                    <p class="" for="">1. Actual Product Orientation</p>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p class="ms-5" for="">2. Sample Checking:</p>
                                    <select class="form-control select2bs4" style="width: 100%;" name="text_sel_result1_operator" id="text_sel_result1_operator">
                                        <option value="" selected disabled>Select Result</option>
                                        <option value="OK">OK</option>
                                        <option value="NG">NG</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <p class="ms-5" for="">2. Sample Checking:</p>
                                    <select class="form-control select2bs4" style="width: 100%;" name="text_sel_result2_operator" id="text_sel_result2_operator">
                                        <option value="" selected disabled>Select Result</option>
                                        <option value="OK">OK</option>
                                        <option value="NG">NG</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="">CONDUCTED BY:</label>
                                    <select class="form-control select2bs4" style="width: 100%;" id="text_sec1_certified_operator" name="text_sec1_certified_operator"></select>
                                </div>
                                <div class="col-md-6">
                                    <label for="">CONDUCTED BY:</label>
                                    <select class="form-control select2bs4" style="width: 100%;" id="text_sec2_certified_operator" name="text_sec2_certified_operator"></select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="">Date:</label>
                                    <input class="form-control" type="date" id="text_sec1_date_operator" name="text_sec1_date_operator">
                                </div>
                                <div class="col-md-6">
                                    <label for="">Date:</label>
                                    <input class="form-control" type="date" id="text_sec2_date_operator" name="text_sec2_date_operator">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="">Time:</label>
                                    <input class="form-control" type="time" id="text_sec1_time_operator" name="text_sec1_time_operator">
                                </div>  
                                <div class="col-md-6">
                                    <label for="">Time:</label>
                                    <input class="form-control" type="time" id="text_sec2_time_operator" name="text_sec2_time_operator">
                                </div>  
                            </div>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="card-header">
                <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOneOperators" aria-expanded="true" aria-controls="collapseOneOperators">
                    <h5>3) PRODUCT VALIDATION THROUGH GRR (FOR VISUAL STATIONS)</h5>
                </button>
                </h2>
                <div id="collapseOneOperators" class="accordion-collapse collapse show" data-parent="#accordionExampleOperators">
                    <div class="card-body">
                        <!-- ------------------------------------------------ -->

                        <label class="mb-3">QUALITY CONTROL SECTION</label>

                            <div class="row">
                                <div class="col-md-6">
                                    <p class="" for="">First Take:</p>
                                </div>

                                <div class="col-md-6">
                                    <p class="" for="">Second Take:</p>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <select class="form-control select2bs4" style="width: 100%;" name="text_pv_result1_operator" id="text_pv_result1_operator">
                                        <option value="" selected disabled>Select Result</option>
                                        <option value="PASSED">PASSED</option>
                                        <option value="FAILED">FAILED</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <select class="form-control select2bs4" style="width: 100%;" name="text_pv_result2_operator" id="text_pv_result2_operator">
                                        <option value="" selected disabled>Select Result</option>
                                        <option value="PASSED">PASSED</option>
                                        <option value="FAILED">FAILED</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="">CONDUCTED BY:</label>
                                    <select class="form-control select2bs4" style="width: 100%;" id="text_pv1_certified_operator" name="text_pv1_certified_operator"></select>
                                </div>
                                <div class="col-md-6">
                                    <label for="">CONDUCTED BY:</label>
                                    <select class="form-control select2bs4" style="width: 100%;" id="text_pv2_certified_operator" name="text_pv2_certified_operator"></select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="">Date:</label>
                                    <input class="form-control" type="date" id="text_pv1_date_operator" name="text_pv1_date_operator">
                                </div>
                                <div class="col-md-6">
                                    <label for="">Date:</label>
                                    <input class="form-control" type="date" id="text_pv2_date_operator" name="text_pv2_date_operator">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="">Time:</label>
                                    <input class="form-control" type="time" id="text_pv1_time_operator" name="text_pv1_time_operator">
                                </div>  
                                <div class="col-md-6">
                                    <label for="">Time:</label>
                                    <input class="form-control" type="time" id="text_pv2_time_operator" name="text_pv2_time_operator">
                                </div>  
                            </div>
                    </div>
                </div>
            </div>
        </div>

        <hr style="height: 5px; background-color: black; border: none;">

        <div class="col-md-6">
            <label for="">Approved / Confirmed by:</label> 
             <select class="form-control select2bs4" style="width: 100%;" name="text_visual_approved_confirmed_by" id="text_visual_approved_confirmed_by" multiple>
            </select>
            {{-- nmodify <input class="form-control" type="text" id="text_mh_approved_confirmed_by" name="text_mh_approved_confirmed_by" list="list_display_empno" placeholder="Select CONDUCTED BY">
            <datalist id="list_display_empno"></datalist>

            <label for="" class="mt-1">Prodn / PPC-WHSE Sec. Head</label>

            <input type="hidden" id="text_mh_approved_confirmed_by_username" name="text_mh_approved_confirmed_by_username">
            <input type="hidden" id="text_mh_approved_confirmed_by_email" name="text_mh_approved_confirmed_by_email"> --}}
        </div>

        <div class="modal-footer btnMh d-none">
            <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa-solid fa-xmark me-2" style="color: white"></i>CLOSE</button>
            <button type="submit" class="btn btn-success" id="addNew"><i class="fa-solid fa-file-import me-2" style="color: white"></i>MH SUBMIT</button>
        </div>

    </form>