const GetEmployeeDetails = (element, searchTerm, successCallback, errorCallback) => {

    ajaxRequest({
        url: 'get_systemone_employee_training_details',
        method: 'GET',
        dataType: 'json',
        data: {
            search: searchTerm
        },

        successCallback: function (response) {

            successCallback(
                response.map(item => ({
                    id: item.pkid,
                    employeeNo: item.EmpNo,
                    position: item.Position,
                    department: item.Department,
                    division: item.Division,
                    section: item.Section,
                    employmentStatus: item.EmpStatus,
                    hiringStatus: item.HiringStatus,
                    dateHired: item.DateHired,
                    text: `${item.EmpNo} - ${item.EmpName}`,
                }))
            );

        },

        errorCallback: errorCallback
    });

};