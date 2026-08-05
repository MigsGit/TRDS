// $(document).ready(function () {

    // Add User
    function AddUser(){
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
        "timeOut": "3000",
        "extendedTimeOut": "3000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut",
        };

        $.ajax({
            url: "add_user",
            method: "post",
            data: $('#formAddUser').serialize(),
            dataType: "json",
            beforeSend: function(){
                $("#iBtnAddUserIcon").addClass('fa fa-spinner fa-pulse');
                $("#btnAddUser").prop('disabled', 'disabled');
            },
            success: function(JsonObject){
                if(JsonObject['result'] == 1){
                    $("#modalAddUser").modal('hide');
                    $("#formAddUser")[0].reset();
                    $("#selAddUserLevel").select2('val', '0');
                    $("#txtAddUserEmail").removeAttr('disabled');
                    $("#txtAddUserOQCStamp").prop('disabled', 'disabled');
                    // $("#chkAddUserSendEmail").removeAttr('disabled');
                    // $("#chkAddUserSendEmail").prop('checked', 'checked');
                    $("#chkAddUserWithEmail").prop('checked', 'checked');

                    dataTableUsers.draw();
                    toastr.success('User was succesfully saved!');

                    if(JsonObject['has_email'] == 0){
                        toastr.options = {
                        "closeButton": true,
                        "debug": false,
                        "newestOnTop": true,
                        "progressBar": true,
                        "positionClass": "toast-top-right",
                        "preventDuplicates": false,
                        "showDuration": "0",
                        "hideDuration": "0",
                        "timeOut": "0",
                        "extendedTimeOut": "0",
                        "showEasing": "swing",
                        "hideEasing": "linear",
                        "showMethod": "fadeIn",
                        "hideMethod": "fadeOut",
                        "tapToDismiss": false
                        };

                        // toastr.info("<center><b>USER INFO</b></center> " + "<b>Username: </b> " + JsonObject['username']  + "<br>" + "<b>Password: </b> " + JsonObject['password']);
                    }
                }
                else{
                    toastr.error('Saving User Failed!');

                    if(JsonObject['error']['name'] === undefined){
                        $("#txtAddUserName").removeClass('is-invalid');
                        $("#txtAddUserName").attr('title', '');
                    }
                    else{
                        $("#txtAddUserName").addClass('is-invalid');
                        $("#txtAddUserName").attr('title', JsonObject['error']['name']);
                    }

                    if(JsonObject['error']['username'] === undefined){
                        $("#txtAddUserUserName").removeClass('is-invalid');
                        $("#txtAddUserUserName").attr('title', '');
                    }
                    else{
                        $("#txtAddUserUserName").addClass('is-invalid');
                        $("#txtAddUserUserName").attr('title', JsonObject['error']['username']);
                    }

                    if(JsonObject['error']['employee_id'] === undefined){
                        $("#txtAddUserEmpId").removeClass('is-invalid');
                        $("#txtAddUserEmpId").attr('title', '');
                    }
                    else{
                        $("#txtAddUserEmpId").addClass('is-invalid');
                        $("#txtAddUserEmpId").attr('title', JsonObject['error']['employee_id']);
                    }

                    if(JsonObject['error']['user_level_id'] === undefined){
                        $("#selAddUserLevel").removeClass('is-invalid');
                        $("#selAddUserLevel").attr('title', '');
                    }
                    else{
                        $("#selAddUserLevel").addClass('is-invalid');
                        $("#selAddUserLevel").attr('title', JsonObject['error']['user_level_id']);
                    }

                    if(JsonObject['error']['email'] === undefined){
                        $("#txtAddUserEmail").removeClass('is-invalid');
                        $("#txtAddUserEmail").attr('title', '');
                    }
                    else{
                        $("#txtAddUserEmail").addClass('is-invalid');
                        $("#txtAddUserEmail").attr('title', JsonObject['error']['email']);
                    }
                }

                $("#iBtnAddUserIcon").removeClass('fa fa-spinner fa-pulse');
                $("#btnAddUser").removeAttr('disabled');
                $("#iBtnAddUserIcon").addClass('fa fa-check');
            },
            error: function(data, xhr, status){
                     $("#iBtnAddUserIcon").removeClass('fa fa-spinner fa-pulse');
                $("#btnAddUser").removeAttr('disabled');
                $("#iBtnAddUserIcon").addClass('fa fa-check');
                if( data.status === 422 ){
                    Swal.fire({ icon: 'error', title: 'Error', text: ('Please check the required fields.')});
                    toastr.error(data.responseJSON.message);
                    errorHandler(errorResponse.errors['user_level_id'], $('#selAddUserLevel'));
                        return;
                }
                toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);

            }
        });
    }

    // Edit User
    function GetUserByIdToEdit(userId){
    let data = {
            user_id: userId
    };
    call_ajax(data,'get_user_by_id',function(response){
            let userCollection = response['userCollection'][0];
            let rapidxUser = userCollection.users;
            let empNo = rapidxUser.rapidx_emp_no;
            let usersId = rapidxUser.id;
            let userDetails = userCollection.userDetails;
            console.log(userCollection);

            if(userDetails.EmpNo != null){
                $("#txtAddUserEmpId").val(empNo);
                $("#userId").val(rapidxUser.id);
                getEmpIdData(empNo);
                GetUserLevel($(".selectUserLevel"),rapidxUser.user_level_id);
            }
            else{
                toastr.warning('No User Record Found!');
            }
    })
    }

    function EditUser(){
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
        "timeOut": "3000",
        "extendedTimeOut": "3000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut",
        };

        $.ajax({
            url: "edit_user",
            method: "post",
            data: $('#formEditUser').serialize(),
            dataType: "json",
            beforeSend: function(){
                $("#iBtnEditUserIcon").addClass('fa fa-spinner fa-pulse');
                $("#btnEditUser").prop('disabled', 'disabled');
            },
            success: function(JsonObject){
                if(JsonObject['result'] == 1){
                    $("#modalEditUser").modal('hide');
                    $("#formEditUser")[0].reset();
                    $("#selEditUserLevel").select2('val', '0');
                    $("#txtEditUserEmail").removeAttr('disabled');
                    // $("#chkEditUserSendEmail").removeAttr('disabled');
                    // $("#chkEditUserSendEmail").prop('checked', 'checked');
                    $("#chkEditUserWithEmail").prop('checked', 'checked');

                    dataTableUsers.draw();
                    toastr.success('User was succesfully saved!');

                    if(JsonObject['has_email'] == 0){
                        toastr.options = {
                        "closeButton": true,
                        "debug": false,
                        "newestOnTop": true,
                        "progressBar": true,
                        "positionClass": "toast-top-right",
                        "preventDuplicates": false,
                        "showDuration": "0",
                        "hideDuration": "0",
                        "timeOut": "0",
                        "extendedTimeOut": "0",
                        "showEasing": "swing",
                        "hideEasing": "linear",
                        "showMethod": "fadeIn",
                        "hideMethod": "fadeOut",
                        "tapToDismiss": false
                        };

                        // toastr.info("<center><b>USER INFO</b></center> " + "<b>Username: </b> " + JsonObject['username']  + "<br>" + "<b>Password: </b> " + JsonObject['password']);
                    }
                }
                else{
                    toastr.error('Updating User Failed!');

                    if(JsonObject['error']['name'] === undefined){
                        $("#txtEditUserName").removeClass('is-invalid');
                        $("#txtEditUserName").attr('title', '');
                    }
                    else{
                        $("#txtEditUserName").addClass('is-invalid');
                        $("#txtEditUserName").attr('title', JsonObject['error']['name']);
                    }

                    if(JsonObject['error']['username'] === undefined){
                        $("#txtEditUserUserName").removeClass('is-invalid');
                        $("#txtEditUserUserName").attr('title', '');
                    }
                    else{
                        $("#txtEditUserUserName").addClass('is-invalid');
                        $("#txtEditUserUserName").attr('title', JsonObject['error']['username']);
                    }

                    if(JsonObject['error']['employee_id'] === undefined){
                        $("#txtEditUserEmpId").removeClass('is-invalid');
                        $("#txtEditUserEmpId").attr('title', '');
                    }
                    else{
                        $("#txtEditUserEmpId").addClass('is-invalid');
                        $("#txtEditUserEmpId").attr('title', JsonObject['error']['employee_id']);
                    }

                    if(JsonObject['error']['user_level_id'] === undefined){
                        $("#selEditUserLevel").removeClass('is-invalid');
                        $("#selEditUserLevel").attr('title', '');
                    }
                    else{
                        $("#selEditUserLevel").addClass('is-invalid');
                        $("#selEditUserLevel").attr('title', JsonObject['error']['user_level_id']);
                    }

                    if(JsonObject['error']['email'] === undefined){
                        $("#txtEditUserEmail").removeClass('is-invalid');
                        $("#txtEditUserEmail").attr('title', '');
                    }
                    else{
                        $("#txtEditUserEmail").addClass('is-invalid');
                        $("#txtEditUserEmail").attr('title', JsonObject['error']['email']);
                    }
                }

                $("#iBtnEditUserIcon").removeClass('fa fa-spinner fa-pulse');
                $("#btnEditUser").removeAttr('disabled');
                $("#iBtnEditUserIcon").addClass('fa fa-check');
            },
            error: function(data, xhr, status){
                toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
                $("#iBtnEditUserIcon").removeClass('fa fa-spinner fa-pulse');
                $("#btnEditUser").removeAttr('disabled');
                $("#iBtnEditUserIcon").addClass('fa fa-check');
            }
        });
    }

    function PrintBatchUser(selectedUsers){
    // console.log(selectedUsers);
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
        "timeOut": "3000",
        "extendedTimeOut": "3000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut",
        };

        $.ajax({
            url: "get_user_by_batch",
            method: "get",
            data: {
            user_id: selectedUsers
            },
            dataType: "json",
            beforeSend: function(){
                // $("#iBtnEditUserIcon").addClass('fa fa-spinner fa-pulse');
                // $("#btnEditUser").prop('disabled', 'disabled');
            },
            success: function(JsonObject){
                if(JsonObject['users'].length > 0){
                    // dataTableUsers.draw();
                    // toastr.success('Success!');

                    popup = window.open();
                    let content = '';
                    content += '<html>';
                    content += '<head>';
                        content += '<title></title>';
                        content += '<link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">';
                        content += '<script type="text/javascript" src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>';
                        content += '<script type="text/javascript" src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>';
                        content += '<style type="text/css">';
                        content += '.divBorder{';
                            content += 'border: 2px solid black;';
                                content += 'min-width: 225px;';
                                content += 'margin-top: 10px;';
                        content += '}';
                        content += '</style>';
                    content += '</head>';
                    content += '<body>';
                        content += '<div class="container-fluid">';
                        content += '<div class="row">';

                            for(let index = 1; index <= JsonObject['users'].length; index++) {
                            content += '<div class="col-sm-4">';
                                content += '<div class="divBorder">';
                                // content += '<center>';
                                    content += '<table>';
                                    content += '<tr>';
                                        content += '<td>';
                                        // content += '<center>';
                                            content += '<img src="' + JsonObject['qrcode'][index - 1] + '" style="max-width: 120px;">';
                                        // content += '</center>';
                                        content += '</td>';
                                        content += '<td>';
                                        content += '<label style="text-align: left; font-weight: bold; font-family: Arial; font-size: 18px;">' + JsonObject['users'][index - 1].employee_id + '</label>';
                                        content += '<br>';
                                        content += '<label style="text-align: left; font-family: Arial Narrow; font-size: 18px;">' + JsonObject['users'][index - 1].firstname +' '+ JsonObject['users'][index - 1].lastname + '</label>';
                                        content += '</td>';
                                    content += '</tr>';
                                    content += '</table>';
                                // content += '</center>';
                                content += '</div>';
                            content += '</div>';

                            // if(index % 3 == 0){
                            //   content += '<div class="col-sm-3">';
                            //   content += '</div>';
                            // }
                            }

                        content += '</div>';
                        content += '</div>';
                    content += '</body>';
                    content += '</html>';
                    popup.document.write(content);
                    // popup.focus(); //required for IE
                    // popup.print();
                    // popup.close();
                }
                else{
                    // toastr.error('Failed!');
                }

                // $("#iBtnEditUserIcon").removeClass('fa fa-spinner fa-pulse');
                // $("#btnEditUser").removeAttr('disabled');
                // $("#iBtnEditUserIcon").addClass('fa fa-check');
            },
            error: function(data, xhr, status){
                toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
                // $("#iBtnEditUserIcon").removeClass('fa fa-spinner fa-pulse');
                // $("#btnEditUser").removeAttr('disabled');
                // $("#iBtnEditUserIcon").addClass('fa fa-check');
            }
        });
    }

    // Get User By Status
    function CountUserByStatForDashboard(status){
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
        "timeOut": "3000",
        "extendedTimeOut": "3000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut",
        };
        $.ajax({
            url: "get_user_by_stat",
            method: "get",
            data: {
                status: status
            },
            dataType: "json",
            beforeSend: function(){

            },
            success: function(JsonObject){
                if(JsonObject['user'].length > 0){
                    $("#h3TotalNoOfUsers").text(JsonObject['user'].length);
                }
                else{
                    toastr.warning('No User Record Found!');
                }
            },
            error: function(data, xhr, status){
                toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
                return totalNoOfUsers;
            }
        });
    }

    // Generate User QR Code
    function GenerateUserQRCode(qrcode, action, userId){
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
            "timeOut": "3000",
            "extendedTimeOut": "3000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut",
        };

        $.ajax({
            url: "generate_user_qrcode",
            method: "get",
            data: {
                qrcode: qrcode,
                action: action,
                user_id: userId,
            },
            dataType: "json",
            beforeSend: function(){

            },
            success: function(JsonObject){
                if(action == 1){
                if(JsonObject['result'] == '1'){
                    $("#imgAddUserBarcode").attr("src", JsonObject['qrcode']);
                    $("#lblAddUserQRCodeVal").text(qrcode);
                }
                else if(JsonObject['result'] == '0'){
                    toastr.error('Generating QR Code Failed!');
                    $("#imgAddUserBarcode").attr("src", JsonObject['qrcode']);
                    $("#lblAddUserQRCodeVal").text('0');
                }
                else if(JsonObject['result'] == '2'){
                    toastr.warning('Cannot Generate Duplicate Employee ID!');
                    $("#imgAddUserBarcode").attr("src", JsonObject['qrcode']);
                    $("#lblAddUserQRCodeVal").text('0');
                }
                }
                else if(action == 2){
                if(JsonObject['result'] == '1'){
                    $("#imgEditUserBarcode").attr("src", JsonObject['qrcode']);
                    $("#lblEditUserQRCodeVal").text(qrcode);
                }
                else if(JsonObject['result'] == '0'){
                    toastr.error('Generating QR Code Failed!');
                }
                else if(JsonObject['result'] == '2'){
                    toastr.warning('Cannot Generate Duplicate Employee ID!');
                }
                }
            },
            error: function(data, xhr, status){
                alert('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
            }
        });
    }

    function GetUserList(cboElement,userId=null){
        let result = '<option value="">N/A</option>';
        $.ajax({
            url: 'get_user_list',
            method: 'get',
            dataType: 'json',
            beforeSend: function(){
                result = '<option value=""> -- Loading -- </option>';
                cboElement.html(result);
            },
            success: function(JsonObject){
                let userCollection = JsonObject['userCollection'];
                result = '';
                if(userCollection.length > 0){
                    result = '<option value="">N/A</option>';
                    for(let index = 0; index < userCollection.length; index++){
                        let disabled = '';
                        let empNo = userCollection[index].users.rapidx_emp_o;
                        let usersId = userCollection[index].users.id;
                        let userDetails = userCollection[index].userDetails;
                        let fullName = userDetails['FirstName']+' '+userDetails['LastName'];
                        result += '<option emp-no="' + empNo + '" value="' + usersId + '">' + fullName + '</option>';
                    }
                }
                else{
                    result = '<option value=""> -- No record found -- </option>';
                }

                cboElement.html(result);
                if(userId != null){
                    cboElement.val(userId).attr('readonly',true);
                }
            },
            error: function(data, xhr, status){
                result = '<option value=""> -- Reload Again -- </option>';
                cboElement.html(result);
                console.log('Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
            }
        });
    }

    const getSystemOneEmployeeDetailsRev1 = (comboId) => {
        comboId.select2({
            theme: 'bootstrap-5',
            placeholder: 'Search Employee Name or ID...',
            minimumInputLength: 2, // Only search after typing 2 characters to save performance
            ajax: {
                url: "get_system_one_employee_details",
                dataType: 'json',
                delay: 250, // Wait 250ms after typing stops before hitting the server
                data: function (params) {
                    return {
                        search: params.term, // Search keyword
                        page: params.page || 1 // Current page (defaults to 1)
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;

                    return {
                        results: data.results, // Must be formatted as [{id: 1, text: 'Name'}]
                        pagination: {
                            more: data.pagination.more // True if there are more records to load
                        }
                    };
                },
                cache: true
            }
       });
    }



    function getSystemOneEmployeeDetailstest(cboElement,userId=null){
        let result = '<option value="">N/A</option>';
        $.ajax({
            url: 'get_system_one_employee_details',
            method: 'get',
            dataType: 'json',
            beforeSend: function(){
                result = '<option value=""> -- Loading -- </option>';
                cboElement.html(result);
            },
            success: function(JsonObject){

                let userCollection = JsonObject['userCollection'];
                result = '';
                if(userCollection.length > 0){
                    result = '<option value="">N/A</option>';
                    for(let index = 0; index < userCollection.length; index++){
                        let disabled = '';
                        let EmpNo = userCollection[index].EmpNo;
                        let EmpName = userCollection[index].EmpName;
                        result += '<option value="' + EmpNo + '">' + EmpName + '</option>';
                    }
                }
                else{
                    result = '<option value=""> -- No record found -- </option>';
                }

                cboElement.html(result);
                if(userId != null){
                    cboElement.val(userId).attr('readonly',true);
                }
            },
            error: function(data, xhr, status){
                result = '<option value=""> -- Reload Again -- </option>';
                cboElement.html(result);
                console.log('Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
            }
        });
    }

    const getEmpIdData = (id) => {
        $.ajax({
            type: "get",
            url: "get_emp_details_by_id",
            data: {
                "empId" : id
            },
            dataType: "json",
            success: function (response) {
                console.log(response);
                if(response['empInfo'].length == 0){
                    let params = {
                        frmId : $('#formAddUser')
                    }
                    resetFormValues(params);
                    selAddUserLevel
                    $('#selAddUserLevel').val('');
                }
                $('#systemoneEmpId').val(response['empInfo'][0]['pkid']);
                $('#txtAddfirstName').val(response['empInfo'][0]['FirstName']);
                $('#txtAddMiddleName').val(response['empInfo'][0]['MiddleName']);
                $('#txtAddLastName').val(response['empInfo'][0]['LastName']);
                $('#txtAddLastName').val(response['empInfo'][0]['LastName']);
                $('#txtAddUserPosition').val(response['empInfo'][0]['Position']);
                $('#txtAddUserSection').val(response['empInfo'][0]['Section']);
                $('#rapidxEmpId').val(response['rapidxUser']['id']??'');
                $('#txtAddUserEmail').val(response['rapidxUser']['email']);

                // $username = strtolower(substr($fname, 0, 1).substr($mname, 0,1).$lname);
                if(/^[a-zA-Z0-9]*$/.test(response['empInfo'][0]['MiddleName'].substring(0,1)) == true) {
                    middlename = response['empInfo'][0]['MiddleName'].substring(0,1);
                }
                let username = response['empInfo'][0]['FirstName'].substring(0,1)+middlename+response['empInfo'][0]['LastName'];
                $('#txtAddUserUserName').val(username.toLowerCase())
            }
        });
    }

    const GetUserLevel = (cboElement,userLevelId=null) => {
        let result = '<option value="0" selected disabled> -- -- </option>';
        $.ajax({
            url: 'get_user_levels',
            method: 'get',
            dataType: 'json',
            beforeSend: function(){
                result = '<option value="0" selected disabled> -- Loading -- </option>';
                cboElement.html(result);
            },
            success: function(response){
                result = '';
                if(response['user_levels'].length > 0){ // true
                    result = '<option value="0" selected disabled> </option>';
                    for(let index = 0; index < response['user_levels'].length; index++){
                        result += '<option value="' + response['user_levels'][index].id + '">' + response['user_levels'][index].user_level + '</option>';
                    }
                }
                else{
                    result = '<option value="0" selected disabled> No record found </option>';
                }
                cboElement.html(result);
                if(userLevelId != null){
                    cboElement.val(userLevelId);
                }
            }
        });
    }
    const GetUserModuleAccess = (userId) =>{
        let data = {
            user_id : userId
        }
        call_ajax(data,'get_user_module_access',function(response){
            console.log(response);

        })
    }


// });
