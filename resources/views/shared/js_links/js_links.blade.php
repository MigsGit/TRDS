<!-- jQuery -->
<script src="{{ asset('public/template/plugins/jquery/jquery.min.js') }}"></script>

<!-- Bootstrap 4 -->
<script src="{{ asset('public/template/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

<!-- bs-custom-file-input -->
<script src="{{ asset('public/template/plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>

<!-- AdminLTE App -->
<script src="{{ asset('public/template/dist/js/adminlte.min.js') }}"></script>

<!-- AdminLTE for demo purposes -->
<script src="{{ asset('public/template/dist/js/demo.js') }}"></script>

<!-- DataTables -->
<script src="{{ asset('public/template/plugins/datatables/jquery.dataTables.js') }}"></script>
<script src="{{ asset('public/template/plugins/datatables-bs4/js/dataTables.bootstrap4.js') }}"></script>

<!-- Select2 -->
{{-- <script src="{{ asset('public/template/plugins/select2/js/select2.full.min.js') }}"></script> --}}
<script src="{{ asset('public/template/plugins/select2/js/select2.min.js') }}"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

<!-- Toastr -->
<script src="{{ asset('public/template/plugins/toastr/toastr.min.js') }}"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.13.0/moment.min.js"></script>
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script> --}}

<!-- Datepicker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

{{--
<script src="{{ asset('public/template/plugins/daterangepicker/moment.min.js') }}"></script>
<script src="{{ asset('public/template/plugins/daterangepicker/daterangepicker.js') }}"></script> --}}

<!-- <script type="text/javascript" src="https://res.cloudinary.com/dxfq3iotg/raw/upload/v1581152197/smartwizard/jquery.smartWizard.min.js"></script> -->

<script>
    toastr.options = {
        "closeButton": false,
        "debug": false,
        "newestOnTop": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "3000",
        "timeOut": "5000",
        "extendedTimeOut": "3000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut",
        "iconClass":  "toast-custom"
    };
</script>

<!-- Custom JS -->
<script src="{{ asset('public/js/my_js/Common.js') }}?<?=time()?>"></script>
<script src="{{ asset('public/js/my_js/Questionnaires.js') }}?<?=time()?>"></script>
<script src="{{ asset('public/js/my_js/Examination.js') }}?<?=time()?>"></script>
<script src="{{ asset('public/js/my_js/ExamResult.js') }}?<?=time()?>"></script>
<script src="{{ asset('public/js/my_js/User.js') }}?<?=time()?>"></script>
<script src="{{ asset('public/js/my_js/CustomerClaim.js') }}?<?=time()?>"></script>
<script src="{{ asset('public/js/my_js/RapidXUser.js') }}?<?=time()?>"></script>
<script src="{{ asset('public/js/my_js/EmailRecipient.js') }}?<?=time()?>"></script>
<script src="{{ asset('public/js/my_js/ProductClassification.js') }}?<?=time()?>"></script>
<script src="{{ asset('public/js/my_js/TrainingRequest.js') }}?<?=time()?>"></script>
<script src="{{ asset('public/js/my_js/TrainingEndorsement.js') }}?<?=time()?>"></script>
<script src="{{ asset('public/js/my_js/QualificationCertification.js') }}?<?=time()?>"></script>
<script src="{{ asset('public/js/my_js/PersonnelSkillMatrix.js') }}?<?=time()?>"></script>


{{-- <script src="https://cdn.jsdelivr.net/npm/@coreui/coreui@4.0.2/dist/js/coreui.bundle.min.js"></script> --}}
