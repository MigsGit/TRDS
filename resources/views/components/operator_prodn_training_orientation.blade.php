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
                    <button type="button" id= "closeBtn" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="formSendEmail" class="btn btn-success btn-sm"><font-awesome-icon class="nav-icon" icon="fas fa-save" />&nbsp; Save</button>
                </div>
        </div>
    </div>
</div>

