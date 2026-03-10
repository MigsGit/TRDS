
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
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">User</h3>
              </div>

              <!-- Start Page Content -->
              <div class="card-body">
                  <div style="float: right;">

                    <button class="btn btn-primary" data-toggle="modal" data-target="#modalAddUser" id="btnShowAddUserModal"><i class="fa fa-user-plus"></i> Add User</button>
                  </div> <br><br>
                  <div class="table responsive">
                    <table id="tblUsers" class="table table-sm table-bordered table-striped table-hover" style="width: 100%;">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>Name</th>
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
  <!-- /.content-wrapper -->

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
            <div class="row">
              <div class="col-sm-12">
                <div class="form-group">
                        <label>Employee ID</label>
                        <input type="text" class="form-control" name="employee_id" id="txtAddUserEmpId" oninput="this.value = this.value.toUpperCase()">
                </div>
                <div class="form-group">
                  <label>Name</label>
                    <input type="text" class="form-control" name="name" id="frmSaveUserUserName">
                </div>

                <div class="form-group">
                  <label>Username</label>
                    <input type="text" class="form-control" name="username" id="frmSaveUserUserUserName">
                </div>

                <div class="form-group">
                    <div class="row">
                      <div class="col-sm-6">
                        <input type="checkbox" name="with_email" id="chkAddUserWithEmail" checked="checked">
                        <label>Email</label>
                      </div>
                      <div class="col-sm-6">
                        <input type="checkbox" name="send_email" id="chkAddUserSendEmail" checked="checked">
                        <label>Send Password to Email</label>
                      </div>
                    </div>

                    <input type="text" class="form-control" name="email" id="frmSaveUserUserEmail">
                </div>

                <div class="form-group">
                  <label>User Level</label>
                    <select class="form-control select2bs4 selectUserLevel" name="user_level_id" id="selAddUserLevel" style="width: 100%;">
                      <!-- Code generated -->
                    </select>
                </div>


              </div>
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" id="btnAddUser" class="btn btn-primary"><i id="iBtnAddUserIcon" class="fa fa-check"></i> Save</button>
          </div>
        </form>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
  <!-- /.modal -->

  <div class="modal fade" id="modalEditUser">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title"><i class="fa fa-user"></i> Edit User</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form method="post" id="formEditUser">
          @csrf
          <div class="modal-body">
            <div class="row">
              <div class="col-sm-12">
                <input type="hidden" class="form-control" name="user_id" id="txtEditUserId">
                <div class="form-group">
                  <label>Name</label>
                    <input type="text" class="form-control" name="name" id="txtEditUserName">
                </div>

                <div class="form-group">
                  <label>Username</label>
                    <input type="text" class="form-control" name="username" id="txtEditUserUserName">
                </div>

                <div class="form-group">
                    <input type="checkbox" name="with_email" id="chkEditUserWithEmail" checked="checked">
                    <label>Email</label>
                    <input type="text" class="form-control" name="email" id="txtEditUserEmail">
                    <input type="hidden" class="form-control" name="current_email" id="txtEditUserCurrEmail">
                </div>

                <div class="form-group">
                  <label>User Level</label>
                    <select class="form-control select2bs4 selectUserLevel" name="user_level_id" id="selEditUserLevel" style="width: 100%;">
                      <!-- Code generated -->
                    </select>
                </div>

              </div>
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" id="btnEditUser" class="btn btn-primary"><i id="iBtnEditUserIcon" class="fa fa-check"></i> Save</button>
          </div>
        </form>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
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
            <button type="submit" id="btnChangeUserStat" class="btn btn-primary"><i id="iBtnChangeUserStatIcon" class="fa fa-check"></i> Yes</button>
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
            <button type="submit" id="btnResetUserPass" class="btn btn-primary"><i id="iBtnResetUserPassIcon" class="fa fa-check"></i> Yes</button>
          </div>
        </form>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
  <!-- /.modal -->

  <!-- MODALS -->
  <div class="modal fade" id="modalGenUserBarcode" modal-id="">
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
          <button type="submit" id="btnPrintUserBarcode" class="btn btn-primary"><i id="iBtnPrintUserBarcodeIcon" class="fa fa-print"></i> Print</button>
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

   <!-- Modal -->
   <div class="modal fade" id="mdl_qrcode_scanner" data-formid="" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header border-bottom-0 pb-0">
          <!-- <h5 class="modal-title" id="exampleModalLongTitle"></h5> -->
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body pt-0">
          <div class="text-center text-secondary">
            Please Scan Employee No
            <br>
            <br>
            <h1><i class="fa fa-qrcode fa-lg"></i></h1>
          </div>
          <input type="text" id="txt_qrcode_scanner" class="hidden_scanner_input" autocomplete="off">
        </div>
      </div>
    </div>
  </div>

  @endsection

  @section('js_content')
  <script type="text/javascript">
    let dataTableUsers;
    let genUserqrcode = "";
    let imgResultUserQrCode = '';
    let qrCodeName = '';
    let arrSelectedUsers = [];
$(document).ready(function () {
      //Initialize Select2 Elements
      $('.select2').select2();

      //Initialize Select2 Elements
      $('.select2bs4').select2({
        theme: 'bootstrap4'
      });

      $(document).on('click','#tblUsers tbody tr',function(e){
        $(this).closest('tbody').find('tr').removeClass('table-active');
        $(this).closest('tr').addClass('table-active');
      });

      //GetUserLevel($(".selectUserLevel"));

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
            { "data" : "id" },
            { "data" : "rapidx_emp_no" },
            { "data" : "user_level.user_level" },
            { "data" : "label1" },
            { "data" : "action1", orderable:false, searchable:false }
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
        $("#formAddUser").submit(function(event){
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
          $("#chkAddUserSendEmail").removeAttr('disabled');
          $("#chkAddUserSendEmail").prop('checked', 'checked');
          $("#chkAddUserWithEmail").prop('checked', 'checked');
        });

        $("#chkAddUserWithEmail").click(function(){
          if($(this).prop('checked')) {
            $("#txtAddUserEmail").removeAttr('disabled');
            $("#chkAddUserSendEmail").removeAttr('disabled');
            $("#chkAddUserSendEmail").prop('checked', 'checked');
          }
          else{
            $("#txtAddUserEmail").prop('disabled', 'disabled');
            $("#txtAddUserEmail").val('');
            $("#chkAddUserSendEmail").prop('disabled', 'disabled');
            $("#chkAddUserSendEmail").removeAttr('checked');
          }
        });

        $("#chkAddUserWithOQCStamp").click(function(){
          if($(this).prop('checked')) {
            $("#txtAddUserOQCStamp").removeAttr('disabled');
          }
          else{
            $("#txtAddUserOQCStamp").prop('disabled', 'disabled');
            $("#txtAddUserOQCStamp").val('');
          }
        });

        // Edit User
        $("#btnEditUserGenBarcode").click(function(){
          let qrcode = $("#txtEditUserEmpId").val();
          GenerateUserQRCode(qrcode, 2, $("#txtEditUserId").val()); // For Edit
        });

        $("#chkEditUserWithOQCStamp").click(function(){
          if($(this).prop('checked')) {
            $("#txtEditUserOQCStamp").removeAttr('disabled');
          }
          else{
            $("#txtEditUserOQCStamp").prop('disabled', 'disabled');
          }
        });

        // Edit User
        $(document).on('click', '.aEditUser', function(){
          let userId = $(this).attr('user-id');
          $("#txtEditUserId").val(userId);
          GetUserByIdToEdit(userId);
          $("#txtEditUserName").removeClass('is-invalid');
          $("#txtEditUserName").attr('title', '');
          $("#txtEditUserUserName").removeClass('is-invalid');
          $("#txtEditUserUserName").attr('title', '');
          $("#txtEditUserEmail").removeClass('is-invalid');
          $("#txtEditUserEmail").attr('title', '');
          $("#txtEditUserEmpId").removeClass('is-invalid');
          $("#txtEditUserEmpId").attr('title', '');
          $("#selEditUserLevel").removeClass('is-invalid');
          $("#selEditUserLevel").attr('title', '');
          $("#txtEditUserName").focus();
          // $("#selEditUserLevel").select2('val', '0');
          $("#chkEditUserWithEmail").prop('checked', 'checked');
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

        // Change User Status
        $(document).on('click', '.aChangeUserStat', function(){
          let userStat = $(this).attr('status');
          let userId = $(this).attr('user-id');

          $("#txtChangeUserStatUserId").val(userId);
          $("#txtChangeUserStatUserStat").val(userStat);

          if(userStat == 1){
            $("#lblChangeUserStatLabel").text('Are you sure to activate?');
            $("#h4ChangeUserTitle").html('<i class="fa fa-user"></i> Activate User');
          }
          else{
            $("#lblChangeUserStatLabel").text('Are you sure to deactivate?');
            $("#h4ChangeUserTitle").html('<i class="fa fa-user"></i> Deactivate User');
          }
        });

        $("#formChangeUserStat").submit(function(event){
          event.preventDefault();
          ChangeUserStatus();
        });

        // Reset User Password
        $(document).on('click', '.aResetUserPass', function(){
          let userId = $(this).attr('user-id');

          $("#txtResetUserPassUserId").val(userId);
        });

        $("#formResetUserPass").submit(function(event){
          event.preventDefault();
          ResetUserPass();
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
                qrCodeName = JsonObject['user'][0].name;
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
      });
  </script>
  @endsection
{{-- @endauth --}}