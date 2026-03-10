// =================================================================================================================================
// ======================================================== QUESTIONNAIRES =========================================================
// =================================================================================================================================
const GetSystemOneHrisDepartment = (element) => {
    const ajaxGetSystemOneHrisDepartment = {
        url: 'get_systemone_hris_department',
        method: 'GET',

        successCallback: (response) => {
            const systemOneHrisDepartment = response || [];
            let result = '';

            if (systemOneHrisDepartment.length > 0) {
                result += '<option value="" disabled selected>Select Department</option>';

                systemOneHrisDepartment.forEach(item => {
                    const department = item?.Department ?? 'No Department';
                    result += `<option value="${department}">${department}</option>`;
                });
            } else {
                result = '<option value="" disabled selected>Not found</option>';
            }

            element.html(result);
        },

        errorCallback: () => {
            element.html('<option value="" disabled selected>Reload Again</option>');
        }
    };

    ajaxRequest(ajaxGetSystemOneHrisDepartment);
};

const GetSystemOneHrisPosition = (element) => {
    const ajaxGetSystemOneHrisPosition = {
        url: 'get_systemone_hris_position',
        method: 'GET',

        successCallback: (response) => {
            const systemOneHrisPosition = response || [];
            let result = '';

            if (systemOneHrisPosition.length > 0) {
                result += '<option value="" disabled selected>Select Position</option>';

                systemOneHrisPosition.forEach(item => {
                    const position = item?.Position ?? 'No Position';
                    result += `<option value="${position}">${position}</option>`;
                });
            } else {
                result = '<option value="" disabled selected>Not found</option>';
            }

            element.html(result);
        },

        errorCallback: () => {
            element.html('<option value="" disabled selected>Reload Again</option>');
        }
    };

    ajaxRequest(ajaxGetSystemOneHrisPosition);
};

const GetSystemOneHrisSection = (element) => {
    const ajaxGetSystemOneHrisSection = {
        url: 'get_systemone_hris_section',
        method: 'GET',

        successCallback: (response) => {
            const systemOneHrisSection = response || [];
            let result = '';

            if (systemOneHrisSection.length > 0) {
                result += '<option value="" disabled selected>Select Section</option>';

                systemOneHrisSection.forEach(item => {
                    const section = item?.Section ?? 'No Section';
                    result += `<option value="${section}">${section}</option>`;
                });
            } else {
                result = '<option value="" disabled selected>Not found</option>';
            }

            element.html(result);
        },

        errorCallback: () => {
            element.html('<option value="" disabled selected>Reload Again</option>');
        }
    };

    ajaxRequest(ajaxGetSystemOneHrisSection);
};

const CreateUpdateQuestionnaire = () => {
    let formData = $('#formCreateUpdateQuestionnaire').serialize();

    const ajaxGetCreateUpdateQuestionnaire = {
        url: "create_update_questionnaire",
        method: "POST",
        data: formData,
        dataType: "json",

        beforeSendCallback: function(){
            console.log('Request sending...');
        },

        successCallback: function(response){
            if(response['result'] == 1){
                alert('Questionnaire already exists!');
            }else if(response['hasError'] == 1){
                alert('Saving failed!');
            }else{
                $('#modalCreateUpdateQuestionnaire').modal('hide');
                toastr.success('Saved!');
                dataQuestionnaire.draw();
            }
        },

        errorCallback: function(xhr, status, error){
            console.log('Ajax Error:', xhr.responseText);
        }
    };

    ajaxRequest(ajaxGetCreateUpdateQuestionnaire);
};

const GetQuestionnaireById = (questionnaireId) => {
    const ajaxGetQuestionnaireById = {
        url: "get_questionnaire_by_id",
        method: "GET",
        data: {
            questionnaireId: questionnaireId,
        },
        dataType: "json",

        beforeSendCallback: function(){
        },

        successCallback: function(response){
            let getQuestionnaireData = response;
            console.log('getQuestionnaireData:', getQuestionnaireData);

            $('#slctQuestionnaireCategory').val(getQuestionnaireData[0].category);
            $('#nmbrQuestionnairePassingScore').val(getQuestionnaireData[0].passing_score);
            $('#txtQuestionnaireTitle').val(getQuestionnaireData[0].exam_title);
            $('#txtQuestionnaireInstruction').val(getQuestionnaireData[0].exam_instruction);
            $('#txtQuestionnairePurpose').val(getQuestionnaireData[0].purpose);
            $('#slctQuestionnaireDepartment').val(getQuestionnaireData[0].department).trigger('change');
            $('#slctQuestionnairePosition').val(getQuestionnaireData[0].position).trigger('change');
            $('#slctQuestionnaireProductLine').val(getQuestionnaireData[0].product_line).trigger('change');
        },

        errorCallback: function(xhr, status, error){
            console.log('Ajax Error:', xhr.responseText);
        }
    };

    ajaxRequest(ajaxGetQuestionnaireById);
};

const ChangeQuestionnaireStatus = (questionnaireId) => {
    let formData = $('#formChangeQuestionnaireStatus').serialize() + '&questionnaireId' + questionnaireId;

    const ajaxGetQuestionnaireById = {
        url: "change_questionnaire_status",
        method: "POST",
        data: formData,
        dataType: "json",

        beforeSendCallback: function(){
            $("#iBtnChangeQuestionnaireStatusIcon").addClass('fa fa-spinner fa-pulse');
            $("#btnChangeQuestionnaireStatus").prop('disabled', 'disabled');
        },

        successCallback: function(response){
            let getQuestionnaireData = response;
            console.log('getQuestionnaireData:', getQuestionnaireData);

            if(response['hasError'] == '1'){
                toastr.error('Questionnaire activation failed!');
            }else{
                if($("#txtChangeQuestionnaireStatus").val() == 0){
                    toastr.success('Questionnaire activation success!');
                    $("#txtChangeQuestionnaireStatus").val() == 1;
                }
                else{
                    toastr.success('Questionnaire deactivation success!');
                    $("#txtChangeQuestionnaireStatus").val() == 0;
                }
                $("#modalChangeQuestionnaireStatus").modal('hide');
                $("#formChangeQuestionnaireStatus")[0].reset();
                dataQuestionnaire.draw();
            }

            $("#iBtnChangeQuestionnaireStatusIcon").removeClass('fa fa-spinner fa-pulse');
            $("#btnChangeQuestionnaireStatus").removeAttr('disabled');
            $("#iBtnChangeQuestionnaireStatusIcon").addClass('fa fa-check');
        },

        errorCallback: function(xhr, status, error){
            console.log('Ajax Error:', xhr.responseText);
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
            $("#iBtnChangeQuestionnaireStatusIcon").removeClass('fa fa-spinner fa-pulse');
            $("#btnChangeQuestionnaireStatus").removeAttr('disabled');
            $("#iBtnChangeQuestionnaireStatusIcon").addClass('fa fa-check');
        }
    };

    ajaxRequest(ajaxGetQuestionnaireById);
};

// =================================================================================================================================
// ===================================================== QUESTIONNAIRE DETAILS =====================================================
// =================================================================================================================================
const CreateUpdateQuestionnaireDetails = () => {
    let form = $('#formCreateUpdateQuestionnaireDetails')
    let formData = new FormData(form[0]);
    const ajaxGetCreateUpdateQuestionnaireDetails = {
        url: "create_update_questionnaire_details",
        method: "POST",
        data: formData,
        dataType: "json",

        beforeSendCallback: function(){
            console.log('Request sending...');
        },

        successCallback: function(response){

            if(response['result'] == 1){
                alert('Questionnaire Details already exists!');
            }else if(response['hasError'] == 1){
                alert('Saving failed!');
            }else{
                $('#modalCreateUpdateQuestionnaireDetails').modal('hide');
                toastr.success('Saved!');
                dataQuestionnaireDetails.draw();
            }
        },

        errorCallback: function(xhr, status, error){
            console.log('Ajax Error:', xhr.responseText);
        }
    };

    ajaxRequest(ajaxGetCreateUpdateQuestionnaireDetails);
};