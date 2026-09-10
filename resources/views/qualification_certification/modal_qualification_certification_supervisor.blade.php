<!-- FORMAT 3 Supervisor, Engineer, Planner -->
<form id="formSubmit_SEP">
    <h3 class="mt-5 mb-3 text-center">SUPERVISOR/ENGINEER/PLANNER TRAINING / QUALIFICATION / CERTIFICATION SLIP</h3>

    <div class="accordion" id="accordionExampleSEP">
          {{-- <div class="accordion-item">
            <h2 class="card-header">
            <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseTwoInsp" aria-expanded="false" aria-controls="collapseTwoInsp">
                <h5>B LINE QUALITY CONTROL SECTION (Certification)</h5>
            </button>
            </h2>
            <div id="collapseTwoInsp" class="accordion-collapse collapse" data-parent="#accordionExampleInsp">
            <div class="card-body">

            </div>
            </div>
        </div> --}}
        <div class="accordion-item">
            <div class="card">
                <h2 class="card-header">
                    <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseTwoSEP" aria-expanded="false" aria-controls="collapseTwoSEP">
                        <h5>A. TRAINING / ORIENTATION (Duration: 1 week)</h5>
                    </button>
                </h2>
                <div id="collapseTwoSEP" class="accordion-collapse collapse" data-parent="#accordionExampleSEP">
                    <div class="card-body">

                        <div class="table-responsive mb-4">
                            <table class="table table-bordered nowrap" style="width: 100%;">
                                <thead>
                                    <tr class="text-center">
                                        <th>Designation</th>
                                        <th>Trainer</th>
                                        <th>Approver</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr>
                                        <td>Jr Supvr / Jr Engr/ Jr Planner</td>
                                        <td>Sr Supvr / Sr Engr / Sr Planner</td>
                                        <td>Section Head</td>
                                    </tr>

                                    <tr>
                                        <td>Sr Supvr / Sr Engr / Sr Planner</td>
                                        <td>Section Head</td>
                                        <td>Vice President</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="" for="">1. General Systems / Procedures:</label>
                                <select class="form-control select2bs4" style="width: 100%;" id="text_sep_training_general" name="text_sep_training_general[]" multiple>
                                    <option value="Production Abnormality Control (IMS-PMI-025)">1) Production Abnormality Control (IMS-PMI-025)</option>
                                    <option value="Flow of nonconforming products">2) Flow of nonconforming products (PQS-J01-004)</option>
                                    <option value="Defect escalation rule">3) Defect escalation rule</option>
                                    <option value="New Product Evaluation Report (PPS-I01-004)">4) New Product Evaluation Report (PPS-I01-004)</option>
                                    <option value="Handling product with long period of no production (PQS-I01-029)">5) Handling product with long period of no production (PQS-I01-029)</option>
                                    <option value="PMI change control procedure (PPS-I01-018)">6) PMI change control procedure (PPS-I01-018)</option>
                                    <option value="Pilot run system (TS/CN); Product Qualification (YF); Die set runcard/evaluation (PPS)">7) Pilot run system (TS/CN); Product Qualification (YF); Die set runcard/evaluation (PPS)</option>
                                    <option value="Urgent Direction transaction flow (PP-OPNGEN-025)">8) Urgent Direction transaction flow (PP-OPNGEN-025)</option>
                                    <option value="Product Traceability System">9) Product Traceability System</option>
                                    <option value="Problem Solving Methodology (PQS-I01-020)">10) Problem Solving Methodology (PQS-I01-020)</option>
                                    <option value="Production layout (if transfer is to other production section)">11) Production layout (if transfer is to other production section)</option>
                                    <option value="Past Trouble History (claim, lot-out, yield, etc)">12) Past Trouble History (claim, lot-out, yield, etc)</option>
                                </select>
                                <small class="text-muted">Note: Item # 1 &amp; 3, no need if transfer is within section only</small>
                            </div>

                            <div class="col-md-6">
                                <label class="" for="">2. Product Familiarization:</label>
                                <select class="form-control select2bs4" style="width: 100%;" id="text_sep_training_product" name="text_sep_training_product[]" multiple>
                                    <option value="Product Process Flow">1) Product Process Flow</option>
                                    <option value="Product Drawing interpretation">2) Product Drawing interpretation</option>
                                    <option value="Defects criteria per product">3) Defects criteria per product</option>
                                    <option value="Machine (for QC Supvr & Engineer)">4) Machine (for QC Supvr &amp; Engineer)</option>
                                    <option value="Product measurement (QC Supvr & Engineer)">5) Product measurement (QC Supvr &amp; Engineer)</option>
                                </select>
                                <small class="text-muted">Note: Check box depending on the section</small>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="" for="">3. Specific System per Section:</label>
                                <select class="form-control select2bs4" style="width: 100%;" id="text_sep_training_specific" name="text_sep_training_specific[]" multiple>
                                    <option value="Production System">Production System</option>
                                    <option value="Engineering System">Engineering System</option>
                                    <option value="QC System">QC System</option>
                                    <option value="PPC System">PPC System</option>
                                    <option value="Warehouse System">Warehouse System</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <div class="card shadow-sm h-100">
                                    <div class="card-header bg-light border-bottom-0 pt-3">
                                        <span class="fw-medium">Defect Escalation Rule - Reference Documents</span>
                                    </div>
                                    <div class="table-responsive p-3">
                                        <table class="table table-bordered table-hover align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th scope="col" style="width: 25%;">Category</th>
                                                    <th scope="col">Document / Code Reference</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><span class="badge bg-secondary text-uppercase px-2 py-1">CN</span></td>
                                                    <td>
                                                        <div class="form-check m-0">
                                                            <input class="form-check-input" type="checkbox" id="text_sep_training_orientation_13" name="text_sep_training_orientation[]" value="CN PP-CN-010">
                                                            <label class="form-check-label fw-medium ms-1" for="text_sep_training_orientation_13">PP-CN-010</label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge bg-secondary text-uppercase px-2 py-1">PPS</span></td>
                                                    <td>
                                                        <div class="form-check m-0">
                                                            <input class="form-check-input" type="checkbox" id="text_sep_training_orientation_14" name="text_sep_training_orientation[]" value="PPS PP-MDGEN-135">
                                                            <label class="form-check-label fw-medium ms-1" for="text_sep_training_orientation_14">PP-MDGEN-135</label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge bg-secondary text-uppercase px-2 py-1">YF</span></td>
                                                    <td>
                                                        <div class="form-check m-0">
                                                            <input class="form-check-input" type="checkbox" id="text_sep_training_orientation_15" name="text_sep_training_orientation[]" value="YF PP-YFLEX-296">
                                                            <label class="form-check-label fw-medium ms-1" for="text_sep_training_orientation_15">PP-YFLEX-296</label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge bg-secondary text-uppercase px-2 py-1">TS</span></td>
                                                    <td>
                                                        <div class="form-check m-0">
                                                            <input class="form-check-input" type="checkbox" id="text_sep_training_orientation_16" name="text_sep_training_orientation[]" value="TS PP-TSDGEN-046">
                                                            <label class="form-check-label fw-medium ms-1" for="text_sep_training_orientation_16">PP-TSDGEN-046</label>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <h2 class="card-header">
                <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseThreeSEP" aria-expanded="false" aria-controls="collapseThreeSEP">
                    <h5>B. CERTIFICATION (Duration: 2 weeks)</h5>
                </button>
            </h2>
            <div id="collapseThreeSEP" class="accordion-collapse collapse" data-parent="#accordionExampleSEP">
                <div class="card-body">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="" for="">Theoretical Examination</label>
                            <select class="form-control select2bs4" style="width: 100%;" name="text_sep_theoretical_result" id="text_sep_theoretical_result">
                                <option value="" selected disabled>Select Result</option>
                                <option value="PASSED">PASSED</option>
                                <option value="FAILED">FAILED</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="" for="">Hands-on</label>
                            <select class="form-control select2bs4" style="width: 100%;" name="text_sep_handson_result" id="text_sep_handson_result">
                                <option value="" selected disabled>Select Result</option>
                                <option value="PASSED">PASSED</option>
                                <option value="FAILED">FAILED</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="" for="">Trained / Certified By:</label>
                            <select class="form-control select2bs4" style="width: 100%;" id="text_sep_trained_certified_by" name="text_sep_trained_certified_by[]" multiple>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="" for="">Date:</label>
                            <input class="form-control" type="date" id="text_sep_date" name="text_sep_date">
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <label class="mt-4 mb-2" for="">Important Notations:</label>
    <p>1. The transfer of Supervisor, Engineer & Planner shall be performed if he/she reached at least 1 year on the current job assignment.</p>
    <p>2. Duration of ceritification activity should be done within 1 month period. (Training and orientation, theoretical exam and hands-on)</p>
    <p>3. Certification slip shall be accomplished by Section Head and submitted to TU along with the theoretical exam at the end of training for updating of ETR.</p>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa-solid fa-xmark me-2" style="color: white"></i>CLOSE</button>
        <button type="submit" class="btn btn-success" id="addNew"><i class="fa-solid fa-file-import me-2" style="color: white"></i>SUPVR SUBMIT</button>
    </div>

</form>
