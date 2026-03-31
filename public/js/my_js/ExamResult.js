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
                let questionText = q.category_type === 2 ? (q.question_text || 'Grid Question') : (items[0]?.question || 'No question text');

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
                    
                    const userAnswers = item.user_answer ? item.user_answer.split(',').map(a => a.trim()) : [];
                    (item.choices || []).forEach(choice => {
                        const checked = userAnswers.includes(choice.trim()) ? 'checked' : '';
                        
                        html += `<div class="form-check">
                                    <input class="form-check-input" 
                                        type="${q.points > 1 ? 'checkbox' : 'radio'}" 
                                        disabled ${checked}>
                                    <label class="form-check-label">${choice}</label>
                                </div>`;
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

                // GRID / TABLE
                if (q.category_type === 2 && items.length > 0) {
                    html += `<div class="table-responsive"><table class="table table-bordered text-center">
                                <thead><tr><th>Process</th>`; 
                    (items[0].choices || []).forEach(col => html += `<th>${col}</th>`);
                    html += `</tr></thead><tbody>`;

                    items.forEach(row => {
                        html += `<tr><td class="text-start">${row.question}</td>`;
                        (row.choices || []).forEach(colChoice => {
                            const checked = row.user_answer === colChoice ? 'checked' : '';
                            html += `<td><input type="radio" disabled ${checked}></td>`;
                        });
                        html += `</tr>`;
                    });

                    html += `</tbody></table></div>`;
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

// 🔹 Real-time auto compute for manual scores
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
    const remark = percentage >= passingScore ? 'Passed' : 'Failed';

    // ✅ Update badge text AND color
    const $remarkBadge = $('h4[name="remarkDisplay"]');
    $remarkBadge
        .text(remark)
        .removeClass('badge-success badge-danger')
        .addClass(remark === 'Passed' ? 'badge-success' : 'badge-danger');

    // ✅ Update hidden inputs for form submission
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
