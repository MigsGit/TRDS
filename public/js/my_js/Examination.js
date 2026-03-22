const GetExamTrainingRequestControlNo = (element) => { 
    const ajaxGetExamTrainingRequestControlNo = {
        url: 'get_exam_training_request_control_no',
        method: 'GET',

        successCallback: (response) => {
            const examTrainingRequestControlNo = response || [];
            let result = '';

            if (examTrainingRequestControlNo.length > 0) {
                result += '<option value="" disabled selected>Select Control No</option>';

                examTrainingRequestControlNo.forEach(item => {
                    const controlNo = item?.ctrl_number ?? 'No Control No';
                    result += `<option value="${controlNo}">${controlNo}</option>`;
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

    ajaxRequest(ajaxGetExamTrainingRequestControlNo);
};