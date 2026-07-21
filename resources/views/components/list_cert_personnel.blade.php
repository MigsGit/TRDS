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
                    {{-- <div class="col-12">
                        <div class="form-group">
                            <label>Series:</label>
                            <select class="form-control" id="selCertPersonnelSeries"></select>
                        </div>
                    </div> --}}
                    <div class="col-12">
                        <div class="form-group">
                            <label>Product Line:</label>
                            <select class="form-control" id="selCertPersonnelProductLine"></select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-sm btn-success" id="btnExportListCertPersonnel">Export</button>
            </div>
        </div>
    </div>
</div>
