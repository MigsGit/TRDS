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
                console.log('Done!')

                $('#modalCreateUpdateQuestionnaireDetails').modal('hide');
                $('#singleMultipleAnswer').empty();
                $('#identificationEssay').empty();
                $('#multipleGrid').empty();
                $('#formCreateUpdateQuestionnaireDetails')[0].reset();
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

const GetQuestionnaireDetailsById = (questionnaireDetailId,questionnaireDetailRevision) => {
    const ajaxGetQuestionnaireDetailsById = {
        url: "get_questionnaire_details_by_id",
        method: "GET",
        data: {
            questionnaireDetailId: questionnaireDetailId,
            questionnaireDetailRevision: questionnaireDetailRevision
        },
        dataType: "json",

        beforeSendCallback: function(){
        },

        successCallback: function(response){
            let getQuestionnaireDetials = response[0];
            console.log('getQuestionnaireDetails:', getQuestionnaireDetials);

            if(getQuestionnaireDetials.length === 0){
                return;
            }
            
            $('#slctQuestionnaireCategoryType').val(getQuestionnaireDetials.category_type).trigger('change')
            $('#nmbrQuestionnairePoints').val(getQuestionnaireDetials.points)

            let getData  = JSON.parse(getQuestionnaireDetials.answer_choices_question)
            let question    = getData[0].question
            let choices     = getData[0].choices
            let answer      = getData[0].answer
            let image       = getQuestionnaireDetials.image

            if(!image){
                $('#txtAttachment').addClass('d-none')
                $('#fileAttachment').removeClass('d-none')
            }else{
                $('#fileAttachment').addClass('d-none')
                $('#txtAttachment').removeClass('d-none')
            }

            $('#txteUploadImage').val(image)

            switch (getQuestionnaireDetials.category_type) {
                case 0:
                    $('#txtQuestionnaireQuestion').val(question);
                    $('.divChoices').empty();
                    $('#btnAddChoice').click();

                    for(let i = 1; i < choices.length; i++){
                        $('#btnAddChoice').click();
                    }

                    $('.divChoices .input-group').each(function(index){
                        let choiceValue = choices[index];

                        $(this).find("input[name='choices[]']").val(choiceValue);

                        if(choiceValue === answer){
                            $(this)
                                .find('.chkAnswer')
                                .prop('checked', true)
                                .trigger('change');
                        }
                    });
                    break;

                case 1:
                    $('#txtQuestionnaireQuestion').val(question);
                    $('#txtQuestionType').val(getQuestionnaireDetials.type).trigger('change');
                    $('#txtIdentification').val(answer)
                    break;

                case 2:
                    $('#txtQuestionnaireDescription').val(getQuestionnaireDetials.description)
                    let rawData = getQuestionnaireDetials.answer_choices_question;
                
                    if (!rawData) return;
                
                    let getData = JSON.parse(rawData);
                
                    getQuestions = [];
                    getOptions = [];
                    getSelectedAnswers = [];
                
                    if (getData.length > 0) {
                        getOptions = getData[0].choices;
                    }
                
                    getData.forEach((item, index) => {
                        getQuestions.push(item.question);
                        getSelectedAnswers[index] = item.answer;
                    });
                
                    renderTable();
                
                    getSelectedAnswers.forEach((ans, rowIndex) => {
                        if (ans !== null) {
                            let radio = $(`input[data-row="${rowIndex}"][data-column="${ans}"]`);
                            radio.prop('checked', true);
                        }
                    });
                
                    $('#gridAnswerHidden').val(JSON.stringify(getSelectedAnswers));
                
                    break;
                default:
                    console.log('IRRORMAN');
                    break;
            }
        },

        errorCallback: function(xhr, status, error){
            console.log('Ajax Error:', xhr.responseText);
        }
    };

    ajaxRequest(ajaxGetQuestionnaireDetailsById);
};

// const GetExamTrainingRequestControlNo = (element) => { 
//     const ajaxGetExamTrainingRequestControlNo = {
//         url: 'get_exam_training_request_control_no',
//         method: 'GET',
        
//         successCallback: (response) => {
//             console.log('object');
//             const examTrainingRequestControlNo = response || [];
//             let result = '';

//             if (examTrainingRequestControlNo.length > 0) {
//                 result += '<option value="" disabled selected>Select Control No</option>';

//                 examTrainingRequestControlNo.forEach(item => {
//                     const controlNo = item?.ctrl_number ?? 'No Control No';
//                     result += `<option value="${controlNo}">${controlNo}</option>`;
//                 });
//             } else {
//                 result = '<option value="" disabled selected>Not found</option>';
//             }

//             element.html(result);
//         },

//         errorCallback(xhr, status, error) {
//             console.log("STATUS:", status);
//             console.log("ERROR:", error);
//             console.log("RESPONSE:", xhr.responseText); // 🔥 importante
//             // result.errorCallback(xhr, status, error);
//         }
//     };

//     ajaxRequest(ajaxGetExamTrainingRequestControlNo);
// };