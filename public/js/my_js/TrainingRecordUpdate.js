const getTypeofTraining = (cboElement) => {
    $.ajax({
        type: "GET",
        url: "get_type_of_trainings",
        // data: "data",
        dataType: "json",
        beforeSend: function(){
            result = '<option value=""> -- Loading -- </option>';
            cboElement.html(result);

        },
        success: function (response) {
            let result = "";
            result += `<option value="" selected disabled> -- Select Type of Training -- </option>`;
            response.forEach(element => {
                result += `<option value="${element.id}">${element.dropdown_masters_details}</option>`;
            });
            cboElement.html(result);
        },
        error: function(xhr, status, error){
            console.log('xhr: ' + xhr + "\n" + "status: " + status + "\n" + "error: " + error);
        }
    });
}

const getVenue = (cboElement) => {
    $.ajax({
        type: "GET",
        url: "get_venues",
        // data: "data",
        dataType: "json",
        beforeSend: function(){
            result = '<option value=""> -- Loading -- </option>';
            cboElement.html(result);
        },
        success: function (response) {
            let result = "";
            result += `<option value="" selected disabled> -- Select Venue -- </option>`;
    
            response.forEach(element => {
                result += `<option value="${element.id}">${element.dropdown_masters_details}</option>`;
            });
            cboElement.html(result);
        },
        error: function(xhr, status, error){
            console.log('xhr: ' + xhr + "\n" + "status: " + status + "\n" + "error: " + error);
        }
    });
}

const getEmployees = (cboElement) => {
    $.ajax({
        type: "GET",
        url: "get_employees",
        // data: "data",
        dataType: "json",
        beforeSend: function(){
            result = '<option value=""> -- Loading -- </option>';
            cboElement.html(result);
        },
        success: function (response) {
            let result = "";
            // result += `<option value="" selected disabled> -- Select Trainer -- </option>`;
            response.forEach(element => {
                result += `<option value="${element.EmpNo}">${element.FirstName} ${element.LastName}</option>`;
            });
            cboElement.html(result);
        },
        error: function(xhr, status, error){
            console.log('xhr: ' + xhr + "\n" + "status: " + status + "\n" + "error: " + error);
        }
    });
}

const saveTrainingRecord = (serializeData) => {
    
    $.ajax({
        type: "POST",
        url: "save_training_record",
        data: serializeData,
        processData: false,
        contentType: false,
        dataType: "json",
        beforeSend: function(){
            showSwalLoading();
        },
        success: function (response) {
            if(!response.success){
                toastr.error('Failed to save training record.');
                Swal.close();
                return;
            }
            toastr.success('Training record saved successfully.');
            $('#modalTrainingRecord').modal('hide');
            dtTrainingRecords.draw();
            Swal.close();
        },
        error: function(xhr, status, error){
            if(xhr.responseJSON){
                toastr.error(xhr.responseJSON.message || 'Failed to save training record.');
            }
            console.log('xhr: ' + xhr + "\n" + "status: " + status + "\n" + "error: " + error);
            Swal.close();

        }
    });
}

const getTrainingRecord = (trainingId) => {
    $.ajax({
        type: "GET",
        url: "get_training_record_by_id",
        data: { id: trainingId },
        dataType: "json",
        success: function (response) {
            if(response.success){
                traineeArray = [];

                let record = response.data;
                $('#editingRecordId').val(record.id);
                $('#startDate').val(record.start_date);
                $('#endDate').val(record.end_date);
                $('#trainingTitle').val(record.training_title);
                $('#objective').val(record.objective);
                $('#remarks').val(record.remarks);
                $('#trainer').val(record.trainer ? record.trainer.split(',') : []).trigger('change');
                $('#venue').val(record.venue).trigger('change');
                $('#typeOfTraining').val(record.type_of_training).trigger('change');
                
                if(record.attachments != null){
                    // editing with an existing attachment: show read-only info + reupload checkbox, hide file input
                    $('#attachments_current_wrapper').removeClass('d-none');
                    // $('#attachments_text').val(record.attachments);
                    $('#attachments_text').val(
                        record.attachments
                            ? record.attachments.split(',').join('\n')
                            : ''
                    );
                    $('#attachments_checkbox_reupload').prop('checked', false);
                    $('#attachments').addClass('d-none').val('');
                }
                else{
                    // no existing attachment: behave like "add" mode, show the file input
                    $('#attachments_current_wrapper').addClass('d-none');
                    $('#attachments_text').val('');
                    $('#attachments_checkbox_reupload').prop('checked', false);
                    $('#attachments').removeClass('d-none').val('');
                }
                record.trainee_details.map(td => {
                    return {
                        action: `<center><button type="button" class="btn btn-sm btn-danger btnRemoveTrainee"><i class="fas fa-times"></i></button></center>`,
                        empNo: td.employee_no,
                        empName: ` ${td.employee_details.LastName}, ${td.employee_details.FirstName} ${td.employee_details.MiddleName}`,
                        empDept: td.employee_details.Deparment,
                        qcSlipStation: td.station,
                        qcSlipSeries: td.series
                    }
                }).forEach(item => {
                    traineeArray.push(item);
                });
                dtTrainees.clear().rows.add(traineeArray).draw();
                countTotalTrainees = traineeArray.length;
                $('#totalTrainees').text(countTotalTrainees);
                
                $('#modalTrainingRecordTitle').text('Edit Training Record');
                $('#modalTrainingRecord').modal('show');
            } else {
                toastr.error('Failed to fetch training record.');
            }
        },
        error: function(xhr, status, error){
            console.log('xhr: ' + xhr + "\n" + "status: " + status + "\n" + "error: " + error);
        }
    });
}

const getTraineeDetails = (EmpId) => {
    $.ajax({
        type: "GET",
        url: "get_trainee_details",
        data: { EmpId: EmpId },
        dataType: "json",
        success: function (response) {
            let data = {
                action       : "",
                empNo        : "",
                empName      : "",
                empDept      : "",
                qcSlipStation: "",
                qcSlipSeries : ""
            }

            if(response.success){
                data.action        = `<center><button type="button" class="btn btn-sm btn-danger btnRemoveTrainee"><i class="fas fa-times"></i></button></center>`;
                data.empNo         = response.qc_slip.employee_info.EmpNo || "";
                data.empName       = response.qc_slip.employee_info.EmpName || "";
                data.empDept       = response.qc_slip.employee_info.Department || "";
                data.qcSlipStation = response.qc_slip.get_station_to.dropdown_masters_details || "";
                data.qcSlipSeries  = response.qc_slip.qc_slip.series_name || "";

                traineeArray.push(data);

                countTotalTrainees = traineeArray.length;
                $('#totalTrainees').text(countTotalTrainees);
                dtTrainees.clear().rows.add(traineeArray).draw();

            } else {
                toastr.error('Employee not found or has no Qualification/Validation data.');
            }

            $('#trainee').val('');
        },
        error: function(xhr, status, error){
            console.log('xhr: ' + xhr + "\n" + "status: " + status + "\n" + "error: " + error);
        }
    });
}

const deleteTrainingRecord = (trainingId) => {
    $.ajax({
        type: "POST",
        url: "delete_training_record",
        data: { 
            trainingId: trainingId,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        dataType: "json",
        success: function (response) {
            if(response.success){
                toastr.success('Training record deleted successfully.');
                // Optionally, refresh the training records table here
            } else {
                toastr.error('Failed to delete training record.');
            }
        },
        error: function(xhr, status, error){
            console.log('xhr: ' + xhr + "\n" + "status: " + status + "\n" + "error: " + error);
        }
    });
}

const proceedImportTrainees = (file) => {
    let formData = new FormData();
    formData.append('file', file);
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

    $.ajax({
        type: "POST",
        url: "import_trainees",
        data: formData,
        processData: false,
        contentType: false,
        dataType: "json",
        success: function (response) {
            if(response.success){
                traineeArray = response.data;
                countTotalTrainees = traineeArray.length;
                $('#totalTrainees').text(countTotalTrainees);
                dtTrainees.clear().rows.add(traineeArray).draw();
            } else {
                toastr.error('Failed to import trainees.');
            }
        },
        error: function(xhr, status, error){
            toastr.error('Failed to import trainees.');
            console.log('xhr: ' + xhr + "\n" + "status: " + status + "\n" + "error: " + error);
        }
    });
}