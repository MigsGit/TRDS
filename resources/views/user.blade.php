
@php $layout = 'layouts.super_user_layout'; @endphp
{{-- @auth
  @php
    if(Auth::user()->user_level_id == 1){
      $layout = 'layouts.super_user_layout';
    }
    else if(Auth::user()->user_level_id == 2){
      $layout = 'layouts.admin_layout';
    }
    else if(Auth::user()->user_level_id == 3){
      $layout = 'layouts.user_layout';
    }
  @endphp
@endauth --}}

{{-- @auth --}}
  @extends($layout)

  @section('title', 'User')

  @section('content_page')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>User</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
              <li class="breadcrumb-item active">User</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <!-- left column -->
          <div class="col-md-12">
            <!-- general form elements -->
            <div class="card card-dark">
              <div class="card-header">
                <h3 class="card-title">User</h3>
              </div>

               <!-- Start Page Content -->
               <div class="card-body">

                <div style="float: right;">
                  <button class="btn btn-primary" data-toggle="modal" data-target="#modalAddUser" id="btnShowAddUserModal"><i class="fa fa-user-plus"></i> Add User</button>

                  <button class="btn btn-outline-primary" data-toggle="modal" data-target="#modalAddUserModuleAccess" id="btnAddUserModuleAccess"><i class="fa fa-user-plus"></i> Add Module Access</button>
                </div> <br><br>
                <div class="table responsive">
                  <table id="tblUsers" class="table table-sm table-bordered table-striped table-hover" style="width: 100%;">
                    <thead>
                      <tr>
                        <th>Emp No</th>
                        <th>Full Name</th>
                        <th>User Level</th>
                        <th>Status</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                  </table>
                </div>
            </div>
              <!-- !-- End Page Content -->

            </div>
            <!-- /.card -->
          </div>
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>

  <!-- /.modal -->
  <div class="modal fade" id="modalChangeUserStat">
    <div class="modal-dialog">
      <div class="modal-content modal-sm">
        <div class="modal-header">
          <h4 class="modal-title" id="h4ChangeUserTitle"><i class="fa fa-user"></i> Change Status</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form method="post" id="formChangeUserStat">
          @csrf
          <div class="modal-body">
            <label id="lblChangeUserStatLabel">Are you sure to ?</label>
            <input type="hidden" name="user_id" placeholder="User Id" id="txtChangeUserStatUserId">
            <input type="hidden" name="status" placeholder="Status" id="txtChangeUserStatUserStat">
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
            <button type="submit" id="btnChangeUserStat" class="btn btn-dark"><i id="iBtnChangeUserStatIcon" class="fa fa-check"></i> Yes</button>
          </div>
        </form>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
  <!-- /.modal -->

  <div class="modal fade" id="modalResetUserPass">
    <div class="modal-dialog">
      <div class="modal-content modal-sm">
        <div class="modal-header">
          <h4 class="modal-title"><i class="fa fa-user"></i> Reset User Password</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form method="post" id="formResetUserPass">
          @csrf
          <div class="modal-body">
            <label>Are you sure to reset password?</label>
            <input type="hidden" name="user_id" placeholder="User Id" id="txtResetUserPassUserId">
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
            <button type="submit" id="btnResetUserPass" class="btn btn-dark"><i id="iBtnResetUserPassIcon" class="fa fa-check"></i> Yes</button>
          </div>
        </form>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
  <!-- /.modal -->

  <!-- MODALS -->
  <div class="modal fade" id="modalGenUserBarcode">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title"><i class="fa fa-qrcode"></i> Generate QR Code</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <center>
              <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')
                        ->size(150)->errorCorrection('H')
                        ->generate('0')) !!}" id="imgGenUserBarcode" style="max-width: 200px;">
              <br>
              <label id="lblGenUserBarcodeVal">...</label>
            </center>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" id="btnPrintUserBarcode" class="btn btn-dark"><i id="iBtnPrintUserBarcodeIcon" class="fa fa-print"></i> Print</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
  <!-- /.modal -->

  <div class="modal fade" id="modalImportUser">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title"><i class="fa fa-file-excel"></i> Import User</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form method="post" id="formImportUser" enctype="multipart/form-data">
          @csrf
          <div class="modal-body">
            <div class="row">
              <div class="col-sm-12">
                <div class="form-group">
                  <label>File</label>
                    <input type="file" class="form-control" name="import_file" id="fileImportUser">
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" id="btnImportUser" class="btn btn-primary"><i id="iBtnImportUserIcon" class="fa fa-check"></i> Import</button>
          </div>
        </form>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
  <!-- /.modal -->

  <!-- MODALS -->
  <div class="modal fade" id="modalAddUser">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title"><i class="fa fa-user-plus"></i> Add User</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form method="post" id="formAddUser">
          @csrf
          <div class="modal-body">
            <div class="form-group">
              <label>Employee ID</label>
              <input type="text" class="form-control" name="rapidx_emp_no" id="txtAddUserEmpId" oninput="this.value = this.value.toUpperCase()">
            </div>
            <div class="row">
              <div class="col-sm-12">
                <div class="form-group d-none">
                    <input type="number" class="form-control" name="user_id" id="userId" readonly>
                    <input type="number" class="form-control" name="rapidx_emp_id" id="rapidxEmpId" readonly>
                    <input type="number" class="form-control" name="systemone_emp_id" id="systemoneEmpId" readonly>
                </div>
                <div class="row">
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label>Firstname</label>
                        <input type="text" class="form-control" name="fname" id="txtAddfirstName" readonly>
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label>Middlename</label>
                        <input type="text" class="form-control" name="mname" id="txtAddMiddleName" readonly>
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label>Lastname</label>
                      <input type="text" class="form-control" name="lname" id="txtAddLastName" readonly>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label>Username</label>
                    <input type="text read-only" class="form-control" name="username" id="txtAddUserUserName" readonly>
                </div>

                <div class="form-group d-none">
                    <div class="row">
                      <div class="col-sm-6">
                        <input type="checkbox" name="with_email" id="chkAddUserWithEmail" checked="checked">
                        <label>Email</label>
                      </div>
                    </div>
                    <input type="text" class="form-control read-only" name="email" id="txtAddUserEmail">
                </div>

                <div class="form-group">
                  <label>User Level</label>
                    <select class="form-control select2bs4 selectUserLevel" name="user_level_id" id="selAddUserLevel" style="width: 100%;" multiple="false">
                      <!-- Code generated -->
                    </select>
                </div>

                <div class="form-group">
                    <label>Position</label>
                      <input type="text" class="form-control read-only" name="position"  id="txtAddUserPosition" readonly>
                  </div>

                  <div class="form-group">
                      <label>Section</label>
                      <input type="text" class="form-control read-only" name="section" id="txtAddUserSection" readonly>
                  </div>
              </div>
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="button" id="btnAddUser" class="btn btn-dark"><i id="iBtnAddUserIcon" class="fa fa-check"></i> Save</button>
          </div>
        </form>
      </div>
      <!-- /.modal-content -->
    </div>
  </div>
  <div class="modal fade" id="modalAddUserModuleAccess">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title"><i class="fa fa-user-plus"></i> Add Module Access</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
                <div class="form-group">
                    <label>Employee Number</label>
                    <select class="form-control select2bs4" name="selected_employee_number[]" id="selectedEmployeeNumber" style="width: 100%;" multiple>
                    </select>
                </div>
            <br><br>
            <table id="tblUserModuleAccess" class="table table-sm table-bordered table-striped table-hover" style="width: 100%;">
                <thead>
                  <tr>
                    <th><center> <input class="" type="checkbox" id="checkBulkUserModuleSelectAll"> </center></th>
                    <th>Module Name</th>
                  </tr>
                </thead>
              </table>
        </div>
        <div class="modal-footer">
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" id="btnSubmitUserModuleAccess" class="btn btn-dark"><i id="ibtnSubmitUserModuleAccess" class="fa fa-check"></i> Save</button>
            </div>
        </div>
      </div>
    </div>
  </div>
  <!-- /.modal -->
  @endsection

  @section('js_content')
  <script type="text/javascript">
    let dataTableUsers;
    let genUserqrcode = "";
    let imgResultUserQrCode = '';
    let qrCodeName = '';
    let arrSelectedUsers = [];
    globalVar = {
        arrUserModulesId : []
    }
    tbl = {
        tblUserModuleAccess:'#tblUserModuleAccess',
    }
    $(document).ready(function () {
        //Initialize Select2 Elements
        $('.select2bs4').each(function () {
            $(this).select2({
                theme: 'bootstrap-5',
                multiple: true,
                dropdownParent: $(this).parent(),
            });
        });

        $(document).on('click','#tblUsers tbody tr',function(e){
            $(this).closest('tbody').find('tr').removeClass('table-active');
            $(this).closest('tr').addClass('table-active');
        });

        dataTableUsers = $("#tblUsers").DataTable({
        "processing" : false,
            "serverSide" : true,
            "ajax" : {
            url: "view_users",
            // data: function (param){
            //     param.status = $("#selEmpStat").val();
            // }
            },

            "columns":[
                { "data" : "rapidx_emp_no" },
                { "data" : "fullname" },
                { "data" : "user_level.user_level" },
                { "data" : "label1" },
                { "data" : "action1", orderable:false, searchable:false }
            ],

            "order": [[ 1, "asc" ]],
        });//end of dataTableUsers

        dtUserModuleAccess = $("#tblUserModuleAccess").DataTable({
        "processing" : false,
            "serverSide" : true,
            "ajax" : {
            url: "view_user_module_access",
            // data: function (param){
            //     param.status = $("#selEmpStat").val();
            // }
            },

            "columns":[
                { "data" : "rawBulkCheckBox", orderable:false, searchable:false },
                { "data" : "module_name" },
            ],

        //   "columnDefs": [
        //     {
        //       "targets": [3, 5],
        //       "data": null,
        //       "defaultContent": "N/A"
        //     },
        //     // { "visible": false, "targets": 1 }
        //   ],
            "order": [[ 1, "asc" ]],
            "drawCallback": function(settings) {
                // Look for all checkboxes in the table
                $('.checkBulkUserModule').each(function() {
                    if ($(this).is(':checked')) {
                        // If checked, find the closest <tr> and add the highlight class
                        // $(this).closest('tr').css('background-color', '#d4edda'); // Light green
                        // $(this).closest('tr').css('color', '#155724'); // Dark green text
                        $(this).closest('tr').attr('style', 'background:#90EE90;');
                        globalVar.arrUserModulesId.push($(this).attr('pkid-received'));

                    } else {
                        // If not checked, ensure it has the default background
                        $(this).closest('tr').css('background-color', '');
                        $(this).closest('tr').css('color', '');
                    }
                });
            },
        });//end of dataTableUsers

        $(document).on('click', '.chkUser', function(){
            let userId = $(this).attr('user-id');

            if($(this).prop('checked')){
                // Checked
                if(!arrSelectedUsers.includes(userId)){
                    arrSelectedUsers.push(userId);
                }
            }
            else{
                // Unchecked
                let index = arrSelectedUsers.indexOf(userId);
                arrSelectedUsers.splice(index, 1);
            }
        $("#lblNoOfPrintBatchSelUser").text(arrSelectedUsers.length);
            if(arrSelectedUsers.length <= 0){
                $("#btnShowModalPrintBatchUser").prop('disabled', 'disabled');
                $("#btnSendTUVBatchEmail").prop('disabled', 'disabled');

            }
            else{
                $("#btnShowModalPrintBatchUser").removeAttr('disabled');
                $("#btnSendTUVBatchEmail").removeAttr('disabled');

            }
        });

        // Add User
        $("#btnAddUserGenBarcode").click(function(){
            $('#modalGenUserBarcode').modal('show').attr('modal-id','searchByEmpNo');
            //   let qrcode = $("#txtAddUserEmpId").val();
            //   GenerateUserQRCode(qrcode, 1, 0); // For Add
        });
        // Add User
        $("#btnAddUserGenBarcode").click(function(){
            if(e.keyCode == 13){
                let qrcode = $("#txtAddUserEmpId").val();
                GenerateUserQRCode(qrcode, 1, 0); // For Add
            }
        });

        $("#btnShowModalPrintBatchUser").click(function(){
          PrintBatchUser(arrSelectedUsers);
          // console.log(arrSelectedUsers);
        });

        $("#chkSelAllUsers").click(function(){
          if($(this).prop('checked')) {
              $(".chkUser").prop('checked', 'checked');
              $("#btnShowModalPrintBatchUser").removeAttr('disabled');
              $("#lblNoOfPrintBatchSelUser").text('All');
              arrSelectedUsers = 0;
          }
          else{
              // $(".chkUser").removeAttr('checked');
              dataTableUsers.draw();
              arrSelectedUsers = [];
              $("#btnShowModalPrintBatchUser").prop('disabled', 'disabled');
              $("#lblNoOfPrintBatchSelUser").text('0');
          }
        });

        // Add User
        $("#btnAddUser").on('click', function(event){
          event.preventDefault();
          AddUser();
        });

        $("#btnShowAddUserModal").click(function(){
          $("#txtAddUserName").removeClass('is-invalid');
          $("#txtAddUserName").attr('title', '');
          $("#txtAddUserUserName").removeClass('is-invalid');
          $("#txtAddUserUserName").attr('title', '');
          $("#txtAddUserEmail").removeClass('is-invalid');
          $("#txtAddUserEmail").attr('title', '');
          $("#txtAddUserEmpId").removeClass('is-invalid');
          $("#txtAddUserEmpId").attr('title', '');
          $("#selAddUserLevel").removeClass('is-invalid');
          $("#selAddUserLevel").attr('title', '');
          $("#txtAddUserName").focus();
          $("#selAddUserLevel").select2('val', '0');
          $("#txtAddUserEmail").removeAttr('disabled');
          // $("#chkAddUserSendEmail").removeAttr('disabled');
          // $("#chkAddUserSendEmail").prop('checked', 'checked');
          $("#chkAddUserWithEmail").prop('checked', 'checked');
          GetUserLevel($(".selectUserLevel"));
        });

        // Edit User
        $(document).on('click', '.aEditUser', function(){
          let userId = $(this).attr('user-id');
          $("#txtEditUserId").val(userId);
          GetUserByIdToEdit(userId);
        });
        $(document).on('click', '.aEditModuleAccess', function(){
          let userId = $(this).attr('user-id');
          let rapidxEmpNo = $(this).attr('rapidx-emp-no');

          $("#txtEditUserId").val(userId);
          GetUserList( $('#selectedEmployeeNumber'),userId);
          dtUserModuleAccess.ajax.url('view_user_module_access?users_id='+userId).draw();
        });

        $("#chkEditUserWithEmail").click(function(){
          if($(this).prop('checked')) {
            $("#txtEditUserEmail").removeAttr('disabled');
            // $("#chkEditUserSendEmail").removeAttr('disabled');
            // $("#chkEditUserSendEmail").prop('checked', 'checked');
            $("#txtEditUserEmail").val($("#txtEditUserCurrEmail").val());
          }
          else{
            $("#txtEditUserEmail").prop('disabled', 'disabled');
            $("#txtEditUserEmail").val('');
            // $("#chkEditUserSendEmail").prop('disabled', 'disabled');
            // $("#chkEditUserSendEmail").removeAttr('checked');
          }
        });

        $("#formEditUser").submit(function(event){
          event.preventDefault();
          EditUser();
        });

        $(document).on('click', '.aGenUserBarcode', function(){
          let employeeId = $(this).attr('employee-id');
            $.ajax({
                url: "generate_user_qrcode",
                method: "get",
                data: {
                  qrcode: employeeId
                },
                // dataType: "json",
                beforeSend: function(){

                },
                success: function(JsonObject){
              if(JsonObject['result'] == 1){
                $("#imgGenUserBarcode").attr("src", JsonObject['qrcode']);
                imgResultUserQrCode = JsonObject['qrcode'];
                qrCodeName = JsonObject['user'][0].firstname +" "+JsonObject['user'][0].lastname;
                genUserqrcode = JsonObject['user'][0].employee_id;
              }
              $("#lblGenUserBarcodeVal").text(employeeId);
                },
                error: function(data, xhr, status){
                    alert('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);

                }
            });
        });

        $("#formImportUser").submit(function(event){
            event.preventDefault();
            $.ajax({
                url: 'import_user',
                method: 'post',
                data: new FormData(this),
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function(){
                    // alert('Loading...');
                },
                success: function(JsonObject){
                    if(JsonObject['result'] == 1){
                      toastr.success('Importing Success!');
                      dataTableUsers.draw();
                      $("#modalImportUser").modal('hide');
                    }
                    else{
                      toastr.error('Importing Failed!');
                    }
                },
                error: function(data, xhr, status){
                    console.log('Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
                }
            });
        });

        $("#btnPrintUserBarcode").click(function(){
          popup = window.open();
          // popup.document.write('<br><br><div style="border: 2px solid black; padding: 1px 1px; max-width: 100px;" class="rotated"><img src="' + imgResultUserQrCode + '" style="max-width: 100px;"><br><center><label style="text-align: center; font-weight: bold; font-family: Arial;">' + qrcode + '</label></center></div>');
          let content = '';
          content += '<html>';
          content += '<head>';
            content += '<title></title>';
            content += '<style type="text/css">';
              content += '.rotated {';
                // content += 'transform: rotate(270deg); /* Equal to rotateZ(45deg) */';
                content += 'border: 2px solid black;';
                content += 'width: 150px;';
                content += 'position: absolute;';
                content += 'left: 17.5px;';
                content += 'top: 15px;';
              content += '}';
            content += '</style>';
          content += '</head>';
          content += '<body>';
            //content += '<br><br><br>';
            content += '<center>';
            content += '<div class="rotated">';
            content += '<table>';
            content += '<tr>';
            content += '<td>';
            content += '<center>';
            content += '<img src="' + imgResultUserQrCode + '" style="max-width: 70px;">';
            // content += '<br>';
            // content += '<label style="text-align: center; font-weight: bold; font-family: Arial;">' + genUserqrcode + '</label>';
            content += '</center>';
            content += '</td>';
            content += '<td>';
            content += '<label style="text-align: center; font-weight: bold; font-family: Arial; font-size: 10px;"> E.N.: ' + genUserqrcode + '</label>';
            content += '<br>';
            content += '<label style="text-align: center; font-weight: bold; font-family: Arial Narrow; font-size: 8px;">' + qrCodeName + ' <br> </label>';
            content += '</td>';
            content += '</tr>';
            content += '</table>';
            content += '</div>';
            content += '</center>';
          content += '</body>';
          content += '</html>';
          popup.document.write(content);
          popup.focus(); //required for IE
          popup.print();
          popup.close();
        });

        $('#txtAddUserEmpId').on('keyup', function(e){
          if(e.keyCode == 13){
            e.preventDefault();
            getEmpIdData($(this).val());
          }
        });

        $('#btnAddUserModuleAccess').click(function (e) {
            e.preventDefault();
            // getUserDetails();
            GetUserList( $('#selectedEmployeeNumber'));
        });

        $(tbl.tblUserModuleAccess).on('click','#checkBulkUserModule','tr', function () {
            let row = $(this).closest('tr'); // Get the parent row of the checkbox
            let pkidReceived = $(this).attr('pkid-received');
            if ($(this).prop('checked')) {
                row.attr('style', 'background:#90EE90;');
                $(this).each(function () {
                    globalVar.arrUserModulesId.push(pkidReceived);
                    console.log('arrUserModulesId',globalVar.arrUserModulesId);
                });
            }else{
                row.attr('style', 'background:white;');
                $(this).each(function () {
                    let indexPkidReceived = globalVar.arrUserModulesId.indexOf(pkidReceived);
                    globalVar.arrUserModulesId.splice(indexPkidReceived, 1);
                    console.log('arrSplice_fkid_document',globalVar.arrUserModulesId);
                });
            }
            $('#countBulkIqcInspection').text(`${globalVar.arrUserModulesId.length}`);
            console.log(globalVar.arrUserModulesId);
        });

        $('#checkBulkUserModuleSelectAll').on('change', function() {
            let isChecked = this.checked;
            $('.checkBulkUserModule').prop('checked', isChecked).trigger('change');; // Toggle all row checkboxes
            if (isChecked) {
                $('.checkBulkUserModule').each(function() {
                    let row = $(this).closest('tr');
                    row.attr('style', 'background:#90EE90;');
                    globalVar.arrUserModulesId.push($(this).attr('pkid-received'));
                });
            } else {
                // dataTable.iqcTsWhsPackaging.page.len(10).draw();
                globalVar.arrUserModulesId = [];
            }
            $('#countBulkIqcInspection').text(`${globalVar.arrUserModulesId.length}`);
            console.log(globalVar.arrUserModulesId);
        });

        // Individual row checkbox selection
        $(tbl.tblUserModuleAccess).on('change', '.checkBulkUserModule', function() {
            let pkid = $(this).attr('pkid-received'); // Get ID
            let row = $(this).closest('tr'); // Get the row
            if (this.checked) {
                row.attr('style', 'background:#90EE90;');
            } else {
                row.attr('style', 'background:white;'); // Remove highlight class
            }
        });
        $('#btnSubmitUserModuleAccess').click(function (e) {
            e.preventDefault();
            let data = {
                arrUserModulesId : globalVar.arrUserModulesId.toSorted((a, b) => a - b),
                selectedEmployeeNumber : $('#selectedEmployeeNumber').val()
            }
            let serializedData = {}
            console.log(data);

            call_ajax_serialize(data,serializedData , 'save_user_module_access', function(response){
                console.log(response);
                $('#modalAddUserModuleAccess').modal('hide');
            });
        });
        $('#modalAddUser').on('hidden.bs.modal', function (e) {
            let params = {
                frmId : $('#formAddUser')
            }
            resetFormValues(params);
        });
        $('#modalAddUserModuleAccess').on('hidden.bs.modal', function (e) {
            globalVar.arrUserModulesId = [];
            $('#selectedEmployeeNumber').val('');
            dtUserModuleAccess.ajax.url('view_user_module_access?users_id='+'').draw();
        });
    });
  </script>
  @endsection
{{-- @endauth --}}
