const GetExamTrainingRequestControlNo = (element) => {
    try {
        const folderPath = window.location.pathname.split('/').filter(Boolean);
        const systemName = folderPath.length > 1 ? folderPath[0] : '';

        const baseUrl = systemName
            ? `${window.location.origin}/${systemName}`
            : window.location.origin;

        const link = `${baseUrl}/get_exam_training_request_control_no`;

        const ajaxGetExamTrainingRequestControlNo = {
            url: link,
            method: 'GET',
            dataType: 'json',

            successCallback: function (response) {
                const data = Array.isArray(response) ? response : [];
                let html = '';

                if (data.length) {
                    html += '<option value="" disabled selected>Select Control No</option>';
                    html += data.map(item => {
                        const controlNo = item?.ctrl_number ?? 'No Control No';
                        return `<option value="${controlNo}">${controlNo}</option>`;
                    }).join('');
                } else {
                    html = '<option value="" disabled selected>Not found</option>';
                }

                element.html(html);
            },

            errorCallback: function (xhr, status, error) {
                console.error('STATUS:', status);
                console.error('ERROR:', error);
                console.error('RESPONSE:', xhr.responseText);

                element.html('<option value="" disabled selected>Error loading data</option>');
            }
        };

        ajaxRequest(ajaxGetExamTrainingRequestControlNo);

    } catch (err) {
        console.error('Unexpected error:', err);
        element.html('<option value="" disabled selected>Error loading data</option>');
    }
};

const GetExamTrainingRequestEmployeeNo = (element, selectedControlNo) => {
    try {
        const folderPath = window.location.pathname.split('/').filter(Boolean);
        const systemName = folderPath.length > 1 ? folderPath[0] : '';
        const baseUrl = systemName
            ? `${window.location.origin}/${systemName}`
            : window.location.origin;

        const link = `${baseUrl}/get_exam_training_request_employee_no`;

        const ajaxGetExamTrainingRequestEmployeeNo = {
            url: link,
            data: { controlNo: selectedControlNo },
            method: 'GET',
            dataType: 'json',

            successCallback: function (response) {
                const examTrainingRequestEmployeeNo = response[0]?.training_request_details || [];
                let result = '';

                if (examTrainingRequestEmployeeNo.length > 0) {
                    result += '<option value="" disabled selected>Select Employee No</option>';

                    examTrainingRequestEmployeeNo.forEach(item => {
                        const employeeNo = item?.emp_no ?? 'No Employee No';
                        const employeeName = item?.name ?? 'No Employee Name';
                        const dateHired = item?.date_hired ?? 'No Date Hired';

                        result += `<option value="${employeeNo}" data-name="${employeeName}" data-date-hired="${dateHired}">${employeeNo}</option>`;
                    });

                    element.html(result);

                    // element.off('change').on('change', function () {
                    //     const selectedOption = $(this).find('option:selected');
                    //     const name = selectedOption.data('name') || '';
                    //     const dateHired = selectedOption.data('date-hired') || '';
                    //     $('#txtExamTrainingRequestName').val(name);
                    //     $('#txtExamTrainingRequestDateHired').val(dateHired);
                    // });

                } else {
                    element.html('<option value="" disabled selected>Not found</option>');
                }
            },

            errorCallback: function (xhr, status, error) {
                console.error('STATUS:', status);
                console.error('ERROR:', error);
                console.error('RESPONSE:', xhr.responseText);
                element.html('<option value="" disabled selected>Error loading data</option>');
            }
        };

        ajaxRequest(ajaxGetExamTrainingRequestEmployeeNo);

    } catch (err) {
        console.error('Unexpected error:', err);
        element.html('<option value="" disabled selected>Error loading data</option>');
    }
};

const GetExamTrainingRequestEmployeeInfo = (employeeNo, controlNo) => {
    const folderPath = window.location.pathname.split('/').filter(Boolean);
        const systemName = folderPath.length > 1 ? folderPath[0] : '';
        const baseUrl = systemName
            ? `${window.location.origin}/${systemName}`
            : window.location.origin;

    const link = `${baseUrl}/get_exam_training_request_employee_no`;
    
    const ajaxGetExamTrainingRequestEmployeeInfo = {
        url: link,
        method: "GET",
        data: {
            employeeNo: employeeNo,
            controlNo: controlNo
        },
        dataType: "json",

        beforeSendCallback: function(){
            console.log('Request sending...');
        },

        successCallback: function(response){
            if(response.length > 0){
                if (response[0].training_request_details.length > 0) {
                    for (let index = 0; index < response[0].training_request_details.length; index++) {
                        if(response[0].training_request_details[index].emp_no === employeeNo){
                            const employeeInfo = response[0].training_request_details[index];
                            $('#txtExamTrainingRequestName').val(employeeInfo.name)
                            $('#txtExamTrainingRequestDateHired').val(employeeInfo.date_hired)
                        }
                    }
                }else{
                console.log('No data found for employeeNo:', employeeNo, 'and controlNo:', controlNo);
            }
            }else{
                console.log('No data found for employeeNo:', employeeNo, 'and controlNo:', controlNo);
            }
        },

        errorCallback: function(xhr, status, error){
            console.log('Ajax Error:', xhr.responseText);
        }
    };

    ajaxRequest(ajaxGetExamTrainingRequestEmployeeInfo);
};

const CountExamTrainingRequestExaminationTake = (employeeNo, controlNo) => {
    const folderPath = window.location.pathname.split('/').filter(Boolean);
        const systemName = folderPath.length > 1 ? folderPath[0] : '';
        const baseUrl = systemName
            ? `${window.location.origin}/${systemName}`
            : window.location.origin;

    const link = `${baseUrl}/count_exam_training_request_examination_take`;

    const ajaxCountExamTrainingRequestExaminationTake = {
        url: link,
        method: "GET",
        data: {
            employeeNo: employeeNo,
            controlNo: controlNo
        },
        dataType: "json",

        beforeSendCallback: function(){
            console.log('Request sending...');
        },

        successCallback: function(response){
            console.log('qwe:', response);
            let count = response;
            if(count > 0){
                $('#txtExamTrainingRequestExaminationTake').val(count);
            }
        },

        errorCallback: function(xhr, status, error){
            console.log('Ajax Error:', xhr.responseText);
        }
    };

    ajaxRequest(ajaxCountExamTrainingRequestExaminationTake);
};

const LinkForIdAndRevision = (linkIdRevision) => {
    try {
        const folderPath = window.location.pathname.split('/').filter(Boolean);
        const systemName = folderPath.length > 1 ? folderPath[0] : '';
        const baseUrl = systemName
            ? `${window.location.origin}/${systemName}`
            : window.location.origin;

        const link = `${baseUrl}/get_exam_training_request_details_by_id_revision`;

        const ajaxGetExamTrainingRequestDetailsByIdRevision = {
            url: link,
            method: "GET",
            data: {
                idRevision: linkIdRevision
            },
            dataType: "json",

            beforeSendCallback: function(){
                console.log('Request sending!...');
            },

            successCallback: function(response){
                const qwe = response?.qwe || {};
                let questionnaire = {
                    id: qwe.id || '',
                    revision: qwe.revision,
                    category: qwe.category || '',
                    exam_title: qwe.exam_title || '',
                    exam_instruction: qwe.exam_instruction || '',
                    purpose: qwe.purpose || '',
                    department: qwe.department || '',
                    position: qwe.position || '',
                    product_line: qwe.product_line || '',
                    passing_score: qwe.passing_score || 0
                };
                console.log('qweqwe: ', questionnaire);


                $('#txtExaminationQuestionnaire').val(JSON.stringify(questionnaire));

                let details = qwe?.questionnaire_details || [];

                details.sort((a, b) => a.exam_no - b.exam_no);                
                const fieldsToKeep = [
                    "id",
                    "category_type",
                    "points",
                    "type",
                    "exam_no",
                    "image",
                    "description",
                    "answer_choices_question"
                ];

                let cleanedQuestionDetails = details.reduce((count, item) => {
                    const examNo = item.exam_no;

                    // Parse answer_choices_question safely
                    let parsedAnswer = [];
                    try {
                        parsedAnswer = item.answer_choices_question
                            ? JSON.parse(item.answer_choices_question)
                            : [];
                    } catch (e) {
                        console.error('Invalid JSON in answer_choices_question for exam_no', examNo);
                    }

                    // Pick only selected fields
                    let selected = {};
                    fieldsToKeep.forEach(field => {
                        if (field === 'answer_choices_question') {
                            selected[field] = parsedAnswer;
                        } else {
                            selected[field] = item[field];
                        }
                    });

                    count[examNo] = selected;
                    return count;
                }, {});

                $('#txtExaminationQuestionnaireDetails').val(JSON.stringify(cleanedQuestionDetails));

                console.log('Exam metadata:', questionnaire);
                console.log('Cleaned questions:', cleanedQuestionDetails);
            },

            errorCallback: function(xhr, status, error){
                console.log('Ajax Error:', xhr.responseText);
            }
        };

        ajaxRequest(ajaxGetExamTrainingRequestDetailsByIdRevision);

    } catch (err) {
        console.error('Unexpected error:', err);
    }
};

const ExamSubmission = () => {
    try {
        const folderPath = window.location.pathname.split('/').filter(Boolean);
        const systemName = folderPath.length > 1 ? folderPath[0] : '';
        const baseUrl = systemName
            ? `${window.location.origin}/${systemName}`
            : window.location.origin;

        const link = `${baseUrl}/exam_submission`;

        // 1. Read the answer key (examination_questionnaire_details) 
        let questionDetails = JSON.parse($('#txtExaminationQuestionnaireDetails').val() || '{}');

        // 2. Collect user answers into employee_examination_result 
        let employeeExamResult = {};
        let totalScore = 0;
        let totalPoints = 0;

        Object.keys(questionDetails).forEach(examNo => {
            let qDetail = questionDetails[examNo];
            let categoryType = qDetail.category_type;
            let qId = qDetail.id; // DB question id used in form name attributes

            let resultEntry = {
                exam_no: qDetail.exam_no,
                category_type: categoryType,
                type: qDetail.type,
                points: qDetail.points,
                image: qDetail.image,
                description: qDetail.description,
                answer_choices_question: []
            };

            switch (categoryType) {
                case 0: {
                    // ── Multiple choice ──
                    let userAnswer = null;

                    if (qDetail.points > 1) {
                        let selectedValues = [];
                        $(`input[name="answers[${qId}][]"]:checked`).each(function() {
                            selectedValues.push($(this).val());
                        });
                        userAnswer = selectedValues.length > 0 ? selectedValues.join(',') : null;
                    } else {
                        let checkedRadio = $(`input[name="answers[${qId}]"]:checked`);
                        userAnswer = checkedRadio.length > 0 ? checkedRadio.val() : null;
                    }

                    let correctAnswer = qDetail.answer_choices_question[0]?.answer || null;
                    let correctArr = correctAnswer ? String(correctAnswer).split(',').sort() : [];
                    let userArr = userAnswer ? String(userAnswer).split(',').sort() : [];
                    let isCorrect = (correctArr.length === userArr.length) && correctArr.every((val, i) => val === userArr[i]);

                    if (isCorrect) totalScore += qDetail.points;
                    totalPoints += qDetail.points;

                    resultEntry.answer_choices_question = [{
                        question: qDetail.answer_choices_question[0]?.question || '',
                        choices: qDetail.answer_choices_question[0]?.choices || [],
                        user_answer: userAnswer,
                        is_correct: isCorrect
                    }];
                    break;
                }

                case 1: {
                    // Identification / Essay
                    let userAnswer = null;
                    let el = $(`[name="answers[${qId}]"]`);
                    if (el.length > 0) {
                        userAnswer = el.val() || null;
                    }

                    let correctAnswer = qDetail.answer_choices_question[0]?.answer || null;
                    let isCorrect = null; 

                    if (qDetail.type === 'Identification') {
                        isCorrect = (userAnswer !== null && correctAnswer !== null) &&
                            String(userAnswer).trim().toLowerCase() === String(correctAnswer).trim().toLowerCase();
                        if (isCorrect) totalScore += qDetail.points;
                    }

                    totalPoints += qDetail.points;

                    resultEntry.answer_choices_question = [{
                        question: qDetail.answer_choices_question[0]?.question || '',
                        choices: [],
                        user_answer: userAnswer,
                        is_correct: isCorrect
                    }];
                    break;
                }

                case 2: {
                    // Grid / Table type 
                    let items = qDetail.answer_choices_question;
                    let gridAnswers = [];
                    let gridCorrectCount = 0;

                    items.forEach((item, rowIndex) => {
                        let checkedRadio = $(`input[name="answers[${qId}][${rowIndex}]"]:checked`);
                        let userAnswer = checkedRadio.length > 0 ? checkedRadio.val() : null;

                        let correctAnswer = item.answer;
                        let isCorrect = false;
                        if (userAnswer !== null && correctAnswer !== null) {
                            isCorrect = String(userAnswer) === String(correctAnswer);
                        }

                        if (isCorrect) gridCorrectCount++;

                        gridAnswers.push({
                            question: item.question,
                            choices: item.choices,
                            user_answer: userAnswer,
                            is_correct: isCorrect
                        });
                    });

                    let pointsPerRow = items.length > 0 ? qDetail.points / items.length : 0;
                    totalScore += gridCorrectCount * pointsPerRow;
                    totalPoints += qDetail.points;

                    resultEntry.answer_choices_question = gridAnswers;
                    break;
                }
            }

            employeeExamResult[examNo] = resultEntry;
        });

        // 3. Build summary
        let passingScore = 0;
        try {
            let questionnaire = JSON.parse($('#txtExaminationQuestionnaire').val() || '{}');
            passingScore = questionnaire.passing_score || 0;
        } catch(e) {}

        let percentage = totalPoints > 0 ? Math.round((totalScore / totalPoints) * 100) : 0;
        let isPassed = percentage >= passingScore;

        employeeExamResult.summary = {
            total_score: parseFloat(totalScore.toFixed(2)),
            total_points: totalPoints,
            percentage: percentage,
            passing_score: passingScore,
            result: isPassed ? 'Passed' : 'Failed'
        };

        console.log('Employee Examination Result:', employeeExamResult);
        console.log('Summary:', employeeExamResult.summary);

        // 4. Set the hidden input
        $('#txtEmployeeExaminationResult').val(JSON.stringify(employeeExamResult));

        let formData = {
            _token: $('input[name="_token"]').val(),
            examination_user_info: $('#txtExaminationUserInfo').val(),
            examination_questionnaire: $('#txtExaminationQuestionnaire').val(),
            examination_questionnaire_details: $('#txtExaminationQuestionnaireDetails').val(),
            employee_examination_result: JSON.stringify(employeeExamResult)
        };

        const ajaxExamSSubmission = {
            url: link,
            method: "POST",
            data: formData,
            dataType: "json",

            beforeSendCallback: function(){
                console.log('Request sending...');
                $('#btnSubmitExam').prop('disabled', true).text('Submitting...');
            },

            successCallback: function(response){
                if(response['hasError'] == 1){
                    alert('Saving failed!');
                }else{
                    $('#btnSubmitExam').prop('disabled', true).text('Submitted');
                    let tist = employeeExamResult.summary
                    console.log('Exam Result Submitted:', tist);

                   // SweetAlert popup
                    Swal.fire({
                        title: '📝 Employee Exam Result',
                        confirmButtonColor: '#172838',
                        html: `
                            <p><strong>Total Score:</strong> ${tist.total_score}</p>
                            <p><strong>Total Points:</strong> ${tist.total_points}</p>
                            <p><strong>Percentage:</strong> ${tist.percentage}%</p>
                            <p><strong>Passing Score:</strong> ${tist.passing_score}</p>
                            <p><strong>Result:</strong> ${tist.result}</p>
                        `,
                        icon: tist.result === 'Passed' ? 'success' : 'error',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Build the redirect URL
                            const protocol = window.location.protocol;
                            const hostname = window.location.hostname; 
                            const pathname = window.location.pathname;
                            const segments = pathname.split('/').filter(Boolean);
                            const firstSegment = segments[0] || '';
                            const secondSegment = segments[1] || '';
                            const newUrl = `${protocol}//${hostname}/${firstSegment}/${secondSegment}/`;

                            // Redirect after OK click
                            window.location.href = newUrl;
                        }
                    });
                }
            },

            errorCallback: function(xhr, status, error){
                console.log('Ajax Error:', xhr.responseText);
                $('#btnSubmitExam').prop('disabled', true).text('Failed');
            }
        };

        ajaxRequest(ajaxExamSSubmission);

    } catch (err) {
        console.error('Unexpected error:', err);
    }
};

