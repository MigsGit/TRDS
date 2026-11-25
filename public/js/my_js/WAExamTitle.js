const CreateUpdateExamTitle = () => {
    let form = $('#formCreateUpdateExamTitle')
    let formData = new FormData(form[0]);
    const ajaxGetCreateUpdateExamTitle = {
        url: "create_update_exam_title",
        method: "POST",
        data: formData,
        dataType: "json",

        beforeSendCallback: function(){
            console.log('Request sending...');
        },

        successCallback: function(response){

            if(response['result'] == 1){
                alert('Exam title already exists!');
            }else{
                console.log('Done!')
                $('#modalCreateUpdateExamTitle').modal('hide');
                $('#formCreateUpdateExamTitle')[0].reset();
                toastr.success('Saved!');
                dataTableExamTitle.draw();
            }
        },

        errorCallback: function(xhr, status, error){
            console.log('Ajax Error:', xhr.responseText);
        }
    };

    ajaxRequest(ajaxGetCreateUpdateExamTitle);
};

const GetExamTitleById = (examTitleId) => {
    const ajaxGetExamTitleById = {
        url: "get_exam_title_by_id",
        method: "GET",
        data: {
            examTitleId: examTitleId,
        },
        dataType: "json",

        beforeSendCallback: function(){
        },

        successCallback: function(response){
            let getExamTitleData = response;
            console.log('getExamTitleData:', getExamTitleData);

            $('#examTitle').val(getExamTitleData[0].exam_title);
        },

        errorCallback: function(xhr, status, error){
            console.log('Ajax Error:', xhr.responseText);
        }
    };

    ajaxRequest(ajaxGetExamTitleById);
};

const ChangeExamTitleStatus = (examTitleId) => {
    let formData = $('#formChangeExamTitleStatus').serialize() + '&examTitleId=' + examTitleId;

    const ajaxChangeExamTitleStatus = {
        url: "change_exam_title_status",
        method: "POST",
        data: formData,
        dataType: "json",

        beforeSendCallback: function(){
            $("#iBtnChangeExamTitleStatusIcon").addClass('fa fa-spinner fa-pulse');
            $("#btnChangeExamTitleStatus").prop('disabled', 'disabled');
        },

        successCallback: function(response){
            let getExamTitleData = response;
            console.log('getExamTitleData:', getExamTitleData);

            if(response['hasError'] == '1'){
                toastr.error('Exam title activation failed!');
            }else{
                if($("#txtChangeExamTitleStatus").val() == 0){
                    toastr.success('Exam title activation success!');
                    $("#txtChangeExamTitleStatus").val(1);
                }
                else{
                    toastr.success('Exam title deactivation success!');
                    $("#txtChangeExamTitleStatus").val(0);
                }
                $("#modalChangeExamTitleStatus").modal('hide');
                $("#formChangeExamTitleStatus")[0].reset();
                dataTableExamTitle.draw();
            }

            $("#iBtnChangeExamTitleStatusIcon").removeClass('fa fa-spinner fa-pulse');
            $("#btnChangeExamTitleStatus").removeAttr('disabled');
            $("#iBtnChangeExamTitleStatusIcon").addClass('fa fa-check');
        },

        errorCallback: function(xhr, status, error){
            console.log('Ajax Error:', xhr.responseText);
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
            $("#iBtnChangeExamTitleStatusIcon").removeClass('fa fa-spinner fa-pulse');
            $("#btnChangeExamTitleStatus").removeAttr('disabled');
            $("#iBtnChangeExamTitleStatusIcon").addClass('fa fa-check');
        }
    };

    ajaxRequest(ajaxChangeExamTitleStatus);
};