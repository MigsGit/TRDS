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

                    element.off('change').on('change', function () {
                        const selectedOption = $(this).find('option:selected');
                        const name = selectedOption.data('name') || '';
                        const dateHired = selectedOption.data('date-hired') || '';
                        $('#txtExamTrainingRequestName').val(name);
                        $('#txtExamTrainingRequestDateHired').val(dateHired);
                    });

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

