<div class="modal fade" id="modalListOfCertPersonnel" data-backdrop="static" data-formid="" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
            <div class="modal-header text-white border-0"
                 style="background: linear-gradient(135deg, #173b67, #2878b5); padding: 22px 26px;">
                <div class="d-flex align-items-center">
                    <div class="mr-3 d-flex align-items-center justify-content-center"
                         style="width: 48px; height: 48px; background: rgba(255,255,255,.15); border-radius: 12px;">
                        <i class="fas fa-id-card fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="modal-title font-weight-bold mb-1">List of Certified Personnel</h5>
                        <small style="opacity: .85;">Generate a list of certified personnel</small>
                    </div>
                </div>
                <button id="close" type="button" class="close text-white ml-3" data-dismiss="modal" aria-label="Close"
                        style="text-shadow: none; opacity: .9;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4" style="background: #f7f9fc;">
                <div class="mb-3">
                    <h6 class="font-weight-bold text-dark mb-1">
                        <i class="fas fa-sliders-h text-primary mr-2"></i>
                        Export Filters
                    </h6>
                </div>
                <div class="card border-0 shadow-sm" style="border-radius: 10px;">
                <div class="card-body p-4">
                <div class="row">
                    {{-- <div class="col-12">
                        <div class="form-group">
                            <label>Position:</label>
                            <select class="form-control select2bs4" id="selCertPersonnelPosition"></select>
                        </div>
                    </div> --}}
                    <div class="col-12">
                        <div class="form-group">
                            <label class="font-weight-bold text-dark">
                                <i class="fas fa-sitemap text-muted mr-1"></i>
                                Section
                            </label>
                            <select class="form-control select2bs4" id="selCertPersonnelSection"></select>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label class="font-weight-bold text-dark">
                                <i class="fas fa-industry text-muted mr-1"></i>
                                Product Area
                            </label>
                            <select class="form-control select2bs4" id="selCertPersonnelProductLine"></select>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group mb-0">
                            <label class="font-weight-bold text-dark">
                                <i class="fas fa-layer-group text-muted mr-1"></i>
                                Series
                            </label>
                            <select class="form-control select2bs4" id="selCertPersonnelSeries" multiple></select>
                        </div>
                    </div>
                </div>
                </div>
                </div>

                <div class="mt-3 px-3 py-2"
                     style="background: #eaf3ff; border-left: 4px solid #2878b5; border-radius: 5px;">
                    <small class="text-dark">
                        <i class="fas fa-info-circle text-primary mr-1"></i>
                        <strong>Note:</strong> All dropdown options are synced with the <strong>Qualification & Certification</strong> module. If a required selection is not listed, please add it in that module first.
                    </small>
                </div>
            </div>
            <div class="modal-footer bg-white border-top px-4 py-3">
                <button type="button" class="btn btn-primary font-weight-bold px-4" id="btnExportListCertPersonnel"
                        style="border-radius: 7px;">
                    <i class="fas fa-file-excel mr-2"></i>
                    Export
                </button>
                <button type="button" class="btn btn-light border px-4" data-dismiss="modal"
                        style="border-radius: 7px;">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
