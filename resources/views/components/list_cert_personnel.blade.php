<div class="modal fade" id="modalListOfCertPersonnel" data-backdrop="static" data-formid="" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"><i class="fas fa-file-excel fa-sm"></i> List of Certified Personnel</h3>
                <button id="close" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label>Section:</label>
                            <select class="form-control" id="selCertPersonnelSection"></select>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group mb-2">
                            <label>Product Line:</label>
                            <select class="form-control" id="selCertPersonnelProductLine"></select>
                        </div>
                    </div>
                </div>

                <!-- Added Note -->
                <div class="alert alert-info border-0 shadow-sm mt-3 py-2 px-3 mb-0" role="alert">
                    <small>
                        <i class="fas fa-info-circle mr-1"></i>
                        <strong>Note:</strong> All dropdown options are synced with the <strong>Qualification & Certification</strong> module. If a required selection is not listed, please add it in that module first.
                    </small>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-sm btn-success" id="btnExportListCertPersonnel">Export</button>
            </div>
        </div>
    </div>
</div>
