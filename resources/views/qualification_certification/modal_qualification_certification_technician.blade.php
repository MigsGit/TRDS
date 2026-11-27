<form id="formSubmit_Tech">
    <h3 class="mt-5 mb-3 text-center">TECHNICIAN TRAINING / CERTIFICATION AND VALIDATION SLIP</h3>
    <div class="accordion" id="accordionExampleTech">
        <div class="accordion-item">

                <h4 class="mt-5 mb-3 text-center">PRACTICAL ORIENTATION AND VALIDATION</h4>
            <div class="accordion" id="accordionExampleTechOperator">
                @include('qualification_certification.operator_prodn_training_items_table', [
                    'tableId'        => 'tblTrainingItemsTechOperator',
                    'accordionParent' => '#accordionExampleTechOperator',
                    'position'       => 'Production',
                ])

            </div>
            <div class="accordion" id="accordionExampleTechEngg">
            @include('qualification_certification.operator_engg_training_items_table', [
                    'tableId'        => 'tblTrainingItemsTechEngg',
                    'accordionParent' => '#accordionExampleTechEngg',
                    'position'       => 'Engineer',
                ])
            </div>
            <div class="accordion" id="accordionExampleTechQC">
            @include('qualification_certification.operator_qc_training_items_table', [
                    'tableId'        => 'tblTrainingItemsTechQC',
                    'accordionParent' => '#accordionExampleTechQC',
                    'position'       => 'QC',
                ])
            </div>
        </div>
    </div>
    <hr style="height: 5px; background-color: black; border: none;">
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="">Approved by:</label>
            <!-- original input replaced with select2 -->
            <!-- <input class="form-control" type="text" id="text_tech_approved_by" name="text_tech_approved_by" list="list_display_empno" placeholder="Select Approved by"> -->
            <!-- <datalist id="list_display_empno"></datalist> -->
            <select class="form-control select2bs4" style="width: 100%;" id="text_tech_approved_by" name="text_tech_approved_by">
                <option value="" selected disabled>Select Approved by</option>
            </select>
        </div>
        <div class="col-md-6"></div>
    </div>

    <!-- <hr style="height: 1px; background-color: grey; border: none;"> -->

    <div class="modal-footer techSave">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa-solid fa-xmark me-2" style="color: white"></i>Close</button>
        <button type="submit" class="btn btn-success" id="addNewTechnician"><i class="fa-solid fa-file-import me-2" style="color: white"></i>Save Tech</button>
    </div>

</form>

