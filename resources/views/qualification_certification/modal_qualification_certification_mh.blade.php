<form id="formSubmit_MH">
    <h3 class="mt-5 mb-3 text-center">MH TRAINING / CERTIFICATION AND VALIDATION SLIP</h3>
    <div class="accordion" id="accordionExampleMh">
        <div class="accordion-item">

                <h4 class="mt-5 mb-3 text-center">PRACTICAL ORIENTATION AND VALIDATION</h4>
            <div class="accordion" id="accordionExampleMhOperator">
                @include('qualification_certification.operator_prodn_training_items_table', [
                    'tableId'        => 'tblTrainingItemsMhOperator',
                    'accordionParent' => '#accordionExampleMhOperator',
                    'position'       => 'Production',
                ])

            </div>
            <div class="accordion" id="accordionExampleMhEngg">
            @include('qualification_certification.operator_engg_training_items_table', [
                    'tableId'        => 'tblTrainingItemsMhEngg',
                    'accordionParent' => '#accordionExampleMhEngg',
                    'position'       => 'Engineer',
                ])
            </div>
            <div class="accordion" id="accordionExampleMhQC">
            @include('qualification_certification.operator_qc_training_items_table', [
                    'tableId'        => 'tblTrainingItemsMhQC',
                    'accordionParent' => '#accordionExampleMhQC',
                    'position'       => 'QC',
                ])
            </div>
        </div>
    </div>
    <hr style="height: 5px; background-color: black; border: none;">
    <div class="col-md-6">
            <label for="">Approved / Confirmed by:</label>
             <select class="form-control select2bs4" style="width: 100%;" name="text_mh_approved_confirmed_by" id="text_mh_approved_confirmed_by" multiple>
            </select>
    </div>
    <div class="modal-footer btnMh d-none">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa-solid fa-xmark me-2" style="color: white"></i>CLOSE</button>
        <button type="submit" class="btn btn-success" id="addNew"><i class="fa-solid fa-file-import me-2" style="color: white"></i>MH SUBMIT</button>
    </div>

</form>

