const GetEmployeeExamResultById = (examResultDetailsId) => {
    ajaxRequest({
        url: "get_employee_exam_result_by_id",
        method: "GET",
        data: { examResultDetailsId: examResultDetailsId },
        dataType: "json",

        successCallback: function(response) {
            const container = document.querySelector('.exam-scroll-container');
            container.innerHTML = '';

            let employeeInfo = response.exam_result_info;
            console.log('object: ', employeeInfo);
            let examResult = response.exam_result;

            $('#txtExamTrainingRequestCtrlNo').val(employeeInfo.training_request_ctrl_no);
            $('#txtExamTrainingRequestEmployeeNo').val(employeeInfo.employee_no);
            $('#txtExamTrainingRequestName').val(employeeInfo.employee_name);
            $('#txtExamTrainingRequestDateHired').val(employeeInfo.date_hired);
            $('#txtExamTrainingRequestDateExamination').val(response.date_examination);
            if (typeof examResult === 'string') {
                try {
                    examResult = JSON.parse(examResult);
                } catch (e) {
                    console.error("Failed to parse exam_result JSON", e);
                    container.innerHTML = `<div class="alert alert-danger">Invalid exam result format.</div>`;
                    return;
                }
            }

            // Parse questionnaire_details (contains the correct answers)
            let questionnaireDetails = response.questionnaire_details;

            if (typeof questionnaireDetails === 'string') {
                try {
                    questionnaireDetails = JSON.parse(questionnaireDetails);
                } catch (e) {
                    console.error("Failed to parse questionnaire_details JSON", e);
                    questionnaireDetails = {};
                }
            }

            if (!examResult) {
                container.innerHTML = `<div class="alert alert-warning">No exam result found.</div>`;
                return;
            }

            const questions = Object.keys(examResult)
                .filter(key => key !== 'summary')
                .sort((a, b) => parseInt(a) - parseInt(b))
                .map(key => examResult[key]);

            questions.forEach(q => {
                const items = Array.isArray(q.answer_choices_question) ? q.answer_choices_question : [];
                let questionText = q.category_type === 2 ? (q.description || 'No description text') : (items[0]?.question || 'No question text');

                let html = `<div class="card mb-3"><div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <strong>${q.exam_no}. ${questionText}</strong>
                                    <span class="text-muted">${q.points} ${q.points === 1 ? 'pt' : 'pts'}</span>
                                </div>`;

                const protocol = window.location.protocol;
                const hostname = window.location.hostname;
                const pathname = window.location.pathname;
                const firstSegment = pathname.split('/').filter(Boolean)[0];
                if (q.image && q.image.trim() !== '') {
                    html += `<div class="text-center mb-2">
                                <img src="${protocol}//${hostname}/${firstSegment}/storage/app/public/questionnaire_attachment/${q.image}"
                                    style="max-width:200px; cursor:pointer;"
                                    data-toggle="modal" data-target="#imageModal" class="previewImage">
                            </div>`;
                }

                // MULTIPLE CHOICE
                if (q.category_type === 0 && items.length > 0) {
                    const item = items[0];
                    const qData = questionnaireDetails[q.exam_no];
                    const correctItem = qData.answer_choices_question[0];

                    const userAnswerRaw = item.user_answer ? String(item.user_answer).trim() : '';

                    const userAnswers = userAnswerRaw
                        ? userAnswerRaw.split(',').map(x => x.trim())
                        : [];

                    const correctAnswers = String(correctItem.answer || '')
                        .split(',')
                        .map(x => x.trim());

                    (item.choices || []).forEach(choice => {
                        const value = String(choice).trim();
                        const isChecked = userAnswers.includes(value);
                        const isCorrect = correctAnswers.includes(value);

                        let icon = '';

                        if (isChecked && isCorrect) {
                            icon = '<i class="fa fa-check text-success ml-1"></i>';
                        }

                        else if (isChecked && !isCorrect) {
                            icon = '<i class="fa fa-times text-danger ml-1"></i>';
                        }

                        else if (!userAnswerRaw && isCorrect) {
                            icon = '<i class="fa fa-times text-danger ml-1"></i>';
                        }

                        html += `
                            <div class="form-check">
                                <input class="form-check-input"
                                    type="${q.points > 1 ? 'checkbox' : 'radio'}"
                                    disabled
                                    ${isChecked ? 'checked' : ''}>

                                <label class="form-check-label">
                                    ${choice}
                                    ${icon}
                                </label>
                            </div>
                        `;
                    });
                }

                // IDENTIFICATION / ESSAY
                if (q.category_type === 1 && items.length > 0) {
                    const item = items[0];
                    const ans = item.user_answer || '';
                    if (q.type === 'Identification') {
                        html += `<input type="text" class="form-control" value="${ans}" readonly>`;
                    } else if (q.type === 'Essay') {
                        html += `<textarea class="form-control" rows="4" readonly>${ans}</textarea>`;
                    }

                    const maxScore = q.points || 0;
                    const qId = q.exam_no;

                    html += `<input type="number" class="form-control score-input"
                                data-question="${qId}"
                                max="${maxScore}" min="0" value="0"
                                style="width:80px; display:inline-block;"> / ${maxScore}`;
                }

                // // IDENTIFICATION / ESSAY
                // if (q.category_type === 1 && items.length > 0) {
                //     const item = items[0];
                //     const qData = questionnaireDetails[q.exam_no];
                //     const correctItem = qData.answer_choices_question[0];
                //     const userAnswer = item.user_answer
                //         ? String(item.user_answer).trim()
                //         : '';
                //     const correctAnswer = String(correctItem.answer || '').trim();
                //     const isCorrect =
                //         userAnswer &&
                //         userAnswer.toLowerCase() === correctAnswer.toLowerCase();
                //     let icon = '';

                //     if(isCorrect){
                //         icon = '<i class="fa fa-check text-success ml-1"></i>';
                //     }else{
                //         icon = '<i class="fa fa-times text-danger ml-1"></i>';
                //     }

                //     if(q.type === 'Identification'){
                //         html += `
                //             <div class="input-group mb-2">
                //                 <input type="text"
                //                     class="form-control"
                //                     value="${userAnswer || ''}"
                //                     readonly>

                //                 <div class="input-group-append">
                //                     <span class="input-group-text">
                //                         ${icon}
                //                     </span>
                //                 </div>
                //             </div>
                //         `;

                //         if(!isCorrect){
                //             html += `
                //                 <small class="text-success">
                //                     Correct Answer: ${correctAnswer}
                //                 </small>
                //             `;
                //         }
                //     }else{
                //         html += `
                //             <div class="mb-2">
                //                 <textarea class="form-control" rows="4" readonly>${userAnswer}</textarea>
                //                 <div class="mt-1">${icon}</div>
                //             </div>
                //         `;
                //     }
                // }

                // GRID / TABLE
                if (q.category_type === 2 && items.length > 0) {
                    const qData = questionnaireDetails[q.exam_no];

                    html += `
                    <div class="table-responsive">
                        <table class="table table-bordered text-center">
                        <thead>
                        <tr>
                        <th>Process</th>`;

                    (items[0].choices || []).forEach(col => {
                        html += `<th>${col}</th>`;
                    });

                    html += `
                            </tr>
                            </thead>
                            <tbody>`;

                    items.forEach(row => {
                        const correctRow = qData.answer_choices_question
                            .find(x => x.question === row.question);
                        const correctAnswer = correctRow
                            ? String(correctRow.answer).trim()
                            : '';
                        const userAnswer = row.user_answer
                            ? String(row.user_answer).trim()
                            : '';

                        html += `<tr>`;
                        html += `<td class="text-start">${row.question}</td>`;
                        (row.choices || []).forEach(choice => {
                            const value = String(choice).trim();
                            const isChecked = userAnswer === value;
                            const isCorrect = correctAnswer === value;
                            let icon = '';

                            if(isChecked && isCorrect){
                                icon = '<i class="fa fa-check text-success ml-1"></i>';
                            }else if (isChecked && !isCorrect){
                                icon = '<i class="fa fa-times text-danger ml-1"></i>';
                            }else if(!userAnswer && isCorrect){
                                icon = '<i class="fa fa-times text-danger ml-1"></i>';
                            }else if(!userAnswer && !isCorrect){
                                icon = '';
                            }

                            html += `
                                <td>
                                    <div class="d-flex justify-content-center align-items-center">
                                        <input type="radio"
                                            disabled
                                            ${isChecked ? 'checked' : ''}>
                                        <span class="ml-1">${icon}</span>
                                    </div>
                                </td>
                            `;
                        });

                        html += `</tr>`;
                    });

                    html += `
                            </tbody>
                            </table>
                            </div>`;
                }

                html += `</div></div>`;
                container.insertAdjacentHTML('beforeend', html);
            });

            // SUMMARY
            if (examResult.summary) {
                const s = examResult.summary;
                const badge = s.result === 'Passed' ? 'badge-success' : 'badge-danger';
                const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                container.insertAdjacentHTML('beforeend',
                    `<form method="post" id="formUpdateScoreForIdentificationEssay">
                        <input type="hidden" name="_token" value="${csrf}">
                        <input type="hidden" name="exam_result_details_id" id="getExamResultDetailsId" value="${response.id}">
                        <div class="card mt-3">
                            <div class="card-body text-center">
                                <h4 name="remarkDisplay" class="badge ${badge} p-2">${s.result}</h4>
                                <input type="hidden" name="remark" id="remarkInput" value="${s.result}">

                                <p name="scoreDisplay"><strong>Score:</strong> ${s.total_score} / ${s.total_points}</p>
                                <input type="hidden" name="score" id="scoreInput" value="${s.total_score}">

                                <p name="ratingDisplay"><strong>Percentage:</strong> ${s.percentage}%</p>
                                <input type="hidden" name="rating" id="ratingInput" value="${s.percentage}">

                                <p><strong>Passing Score:</strong> <span id="passingScore">${s.passing_score}</span></p>
                                <input type="hidden" name="passing_score" id="passingScoreInput" value="${s.passing_score}">

                                <p name="manual_scoreDisplay"><strong>Manual Score:</strong> <span id="manualTotal">0</span></p>
                                <input type="hidden" name="manual_score" id="manualScoreInput" value="0">
                            </div>

                            <div class="d-flex justify-content-center mb-3">
                                <button type="submit" class="btn btn-dark w-25">
                                    Submit Result
                                </button>
                            </div>
                        </div>
                    </form>`);
            }
        },

        errorCallback: function(xhr, status, error) {
            console.log('Ajax Error:', xhr.responseText);
        }
    });
};

$(document).on('input', '.score-input', function () {
    let manualTotal = 0;

    $('.score-input').each(function () {
        let val = parseFloat($(this).val()) || 0;
        const max = parseFloat($(this).attr('max')) || 0;

        if (val > max) val = max;
        if (val < 0) val = 0;
        $(this).val(val);

        manualTotal += val;
    });

    const scoreText = $('p[name="scoreDisplay"]').text();
    const match = scoreText.match(/Score:\s*(\d+\.?\d*)\s*\/\s*(\d+\.?\d*)/);
    let existingScore = 0;
    let totalPoints = 0;

    if (match) {
        existingScore = parseFloat(match[1]) || 0;
        totalPoints = parseFloat(match[2]) || 0;
    }

    const combinedScore = existingScore + manualTotal;
    const percentage = totalPoints ? ((combinedScore / totalPoints) * 100).toFixed(2) : 0;

    $('#manualTotal').text(manualTotal);
    $('p[name="ratingDisplay"]').html(`<strong>Percentage:</strong> ${percentage}%`);

    const passingScore = parseFloat($('#passingScore').text()) || 0;
    const remark = percentage == 100 ? 'Passed' : 'Failed';

    const $remarkBadge = $('h4[name="remarkDisplay"]');
    $remarkBadge
        .text(remark)
        .removeClass('badge-success badge-danger')
        .addClass(remark === 'Passed' ? 'badge-success' : 'badge-danger');

    $('#remarkInput').val(remark);
    $('#manualScoreInput').val(manualTotal);
    $('#scoreInput').val(existingScore);
    $('#ratingInput').val(percentage);
});

const UpdateExamScoreForEmployee = () => {
    let formData = $('#formUpdateScoreForIdentificationEssay').serialize();

    const ajaxGetUpdateExamScoreForEmployee = {
        url: "update_exam_score_for_employee",
        method: "POST",
        data: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType: "json",

        beforeSendCallback: function(){
            console.log('Request sending...');
        },

        successCallback: function(response){
            if(response['hasError'] == 1){
                alert('Saving failed!');
            }else{
                $('#modalEmployeeExamResult').modal('hide');
                toastr.success('Saved!');
                examResultTableDetails.draw();
            }
        },

        errorCallback: function(xhr, status, error){
            console.log('Ajax Error:', xhr.responseText);
        }
    };

    ajaxRequest(ajaxGetUpdateExamScoreForEmployee);
};

const UpdateExaminationDate = () => {
    let form = $('#formChangeExaminationDate')
    let formData = new FormData(form[0]);
    const ajaxGetUpdateExaminationDate = {
        url: "update_examination_date",
        method: "POST",
        data: formData,
        processData: false,
        contentType: false,
        dataType: "json",

        beforeSendCallback: function(){
            console.log('Request sending...');
        },

        successCallback: function(response){
            if(response['hasError'] == 1){
                alert('Saving failed!');
            }else{
                console.log('Done!')

                $('#formChangeExaminationDate')[0].reset();
                $('#modalChangeExaminationDate').modal('hide');
                toastr.success('Saved!');
                examResultTableDetails.draw();
            }
        },

        errorCallback: function(xhr, status, error){
            console.log('Ajax Error:', xhr.responseText);
        }
    };

    ajaxRequest(ajaxGetUpdateExaminationDate);
};

const ChangeExaminationResultStatus = (examResultDetailsId) => {
    let formData = $('#formChangeExamResultStatus').serialize() + '&examResultDetailsId=' + examResultDetailsId;

    const ajaxChangeExamResultStatus = {
        url: "change_exam_result_status",
        method: "POST",
        data: formData,
        dataType: "json",

        beforeSendCallback: function(){
            $("#iBtnChangeExamResultStatusIcon").addClass('fa fa-spinner fa-pulse');
            $("#btnChangeExamResultStatus").prop('disabled', 'disabled');
        },

        successCallback: function(response){
            let getExamResultData = response;
            console.log('getExamResultData:', getExamResultData);

            if(response['hasError'] == '1'){
                toastr.error('Exam result activation failed!');
            }else{
                if($("#txtChangeExamResultStatus").val() == 0){
                    toastr.success('The exam result has been activated successfully.');
                    $("#txtChangeExamResultStatus").val(1);
                }
                else{
                    toastr.success('The exam result has been deleted successfully.');
                    $("#txtChangeExamResultStatus").val(0);
                }
                $("#modalChangeExamResultStatus").modal('hide');
                $("#formChangeExamResultStatus")[0].reset();
                examResultTableDetails.draw();
            }

            $("#iBtnChangeExamResultStatusIcon").removeClass('fa fa-spinner fa-pulse');
            $("#btnChangeExamResultStatus").removeAttr('disabled');
            $("#iBtnChangeExamResultStatusIcon").addClass('fa fa-check');
        },

        errorCallback: function(xhr, status, error){
            console.log('Ajax Error:', xhr.responseText);
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
            $("#iBtnChangeExamResultStatusIcon").removeClass('fa fa-spinner fa-pulse');
            $("#btnChangeExamResultStatus").removeAttr('disabled');
            $("#iBtnChangeExamResultStatusIcon").addClass('fa fa-check');
        }
    };

    ajaxRequest(ajaxChangeExamResultStatus);
};
