/* Select 2 Attr */
$('.select2bs4').each(function () {
    $(this).select2({
        theme: 'bootstrap-5',
        dropdownParent: $(this).parent(),
    });
});

/**
 * Reusable function for using Ajax Request
 *
 * @param {object} options
 */
const ajaxRequest = (options) => {
    var defaults = {
        url: '',
        method: 'GET',
        data: {},
        headers: {},
        dataType: 'json',
        processData: true,
        contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
        beforeSendCallback: null,
        successCallback: () => {},
        errorCallback: () => {}
    };

    // Merge default options with user-provided options
    options = $.extend({}, defaults, options);

    if (options.data instanceof FormData) {
        options.processData = false;
        options.contentType = false;
    }

    $.ajax({
        url: options.url,
        method: options.method,
        data: options.data,
        headers: options.headers,
        dataType: options.dataType,
        processData: options.processData,
        contentType: options.contentType,
        beforeSend(xhr) {
            if (typeof options.beforeSendCallback === 'function') {
                options.beforeSendCallback(xhr);
            }
        },
        success(response) {
            options.successCallback(response);
        },
        error(xhr, status, error) {
            options.errorCallback(xhr, status, error);
        }
    });
};

/* Call basic ajax for submit */
const  call_ajax = (data = null, handler, fn,elFormId =null) => {
    data = $.param(data);
    $.ajax({
        type: "GET",
        dataType: "json",
        data: data,
        url: handler,
        beforeSend: function(){
            // console.log('call_ajax elFormId',elFormId);
            // return
            showSwalLoading();
            Swal.close();

            if(elFormId !=null){
                elFormId[0].reset();
            }
        },
        success: function (result) {
            fn(result);
            $('#modal-loading').modal('hide');

        },
        error: function (result) {
            fn(result);
            $('#modal-loading').modal('hide');
        }
    });
}

const applyValidationState = (errors, formSelector) => {
    const $form = formSelector;

    // reset
    $form.find('.form-control, .form-select').each(function () {
        $(this).removeClass('is-invalid is-valid').attr('title', '');
    });

    if (!errors) return;
    Object.keys(errors).forEach(field => {
        const el = $form.find(`#${field}`);
        if (el) {
            $(el).addClass('is-invalid').attr('title', errors[field][0]);
        }
    });
}


const errorHandler = function (errors,formInput){
    if(errors === undefined){
        formInput.removeClass('is-invalid')
        formInput.addClass('is-valid')
        formInput.attr('title', '')
    }else {
        formInput.removeClass('is-valid')
        formInput.addClass('is-invalid');
        formInput.attr('title', errors[0])
    }
}

const  call_ajax_serialize = (data = null, serialized_data, handler, fn,elFormId =null) => {
    data = $.param(data) + '&' + serialized_data;
	$.ajax({
        type: "post",
        dataType: "json",
        data: data,
        url: handler,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function(){
            // $('#modal-loading').modal('show');
            showSwalLoading();
        },
        success: function (result) {
            Swal.close();
            fn(result);
            $('#modal-loading').modal('hide');
            // if(elFormId !=null){
            //     elFormId[0].reset();
            // }

        },
        error: function (result) {
            Swal.close();
            let errorResponse = result.responseJSON;
            let status = result.status;

            // console.log('errorResponse',errorResponse);
            // console.log(errorResponse.msg);
            // console.log(errorResponse.trainingAttendanceIsExists);
            // console.log(errorResponse.isSuccess);
            // console.log(result.status);
            // console.log(result.statusText);
            // $('#modal-loading').modal('hide');
            if( status === 500){
                toastr.error('Internal Server Error! Please Contact ISS');
                // toastr.error(errorResponse.message ?? '');
                // Swal.fire({ icon: 'error', title: 'Error', text: (errorResponse.message) ? errorResponse.message : 'Internal Server Error.'});
            }
            if( result.status === 409 ){
                toastr.error(errorResponse.message ?? '');
                Swal.fire({ icon: 'error', title: 'Error', text: (errorResponse.message) ? errorResponse.message : 'Internal Server Error.'});
            }
            if( result.status === 422 ){
                Swal.fire({ icon: 'error', title: 'Error', text: ('Please check the required fields.')});
                toastr.error(errorResponse.message);
                applyValidationState(errorResponse.errors, elFormId); // <-- replaces all if-else

                errorHandler(errorResponse.errors['text_oper_approved_confirmed_by'], $('#text_oper_approved_confirmed_by')); // <-- replaces all if-else
                errorHandler(errorResponse.errors['text_alert_prod_sec'], $('#text_alert_prod_sec'));
                errorHandler(errorResponse.errors['text_alert_prod_cc_sec'], $('#text_alert_prod_cc_sec'));
                errorHandler(errorResponse.errors['text_qcs_station_1st_oper'], $('#text_qcs_station_1st_oper'));
                errorHandler(errorResponse.errors['text_series_operator'], $('#text_series_operator'));
                // errorHandler(errorResponse.errors['text_section_operator'], $('#text_section_operator'));
                errorHandler(errorResponse.errors['text_operator_product_line'], $('#text_operator_product_line'));
                errorHandler(errorResponse.errors['text_certification_operator'], $('#text_certification_operator'));


                // text_qcs_station_1st_oper checkbox
            }

        }
    });
}


const showSwalLoading = (params) => {
    Swal.fire({
        width: '20rem',
        html: '<em>Loading..</em>',
        allowOutsideClick: false,
        onRender: function () {
            $('.swal2-content').prepend('<div class="spinner-border text-dark" role="status" style="width: 3rem; height: 3rem;"><span class="sr-only">Loading...</span></div>');
        },
        showConfirmButton: false,
    });
}

const resetFormValues = (params) => {
    // Reset values
    params.frmId[0].reset();

    // Remove invalid & title validation
    $('div').find('input').removeClass('is-invalid');
    $("div").find('input').attr('title', '');
    $('div').find('select').removeClass('is-invalid');
    $("div").find('select').attr('title', '');
}

/**
 * SweetAlert confirmation
 */
const  swalConfirmation =(message, callback) => {
    Swal.fire({
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes'
    }).then((result) => {
        if (result.isConfirmed) callback();
    });
}


const handleValidatorErrors = (errors) => {

    document.querySelectorAll('div input').forEach(function(input) {
        input.classList.remove('is-invalid');
    });
    document.querySelectorAll('div select').forEach(function(input) {
        input.classList.remove('is-invalid');
    });
    document.querySelectorAll('div textarea').forEach(function(input) {
        input.classList.remove('is-invalid');
    });
    // Loop through each field in the errors object
    for (let field in errors) {
        if (errors.hasOwnProperty(field)) {
            // Extract the error messages for the field
            let fieldErrorMessage = errors[field];

            // Add invalid class & title validation
            if(field){
                // document.querySelector(`[name="${field}"]`).classList.add('is-invalid');
                document.querySelectorAll(`[name="${field}"], [name="${field}[]"]`).forEach(function(el) {
                    el.classList.add('is-invalid');
                });
                // document.querySelector(`[name="${field}"]`).classList.add('is-invalid');
                // document.querySelector(`[name="${field}"]`).classList.add('is-invalid');

            }
        }
    }
}
