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
            // return;
            $('#modal-loading').modal('show');
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
        // bootstrap.Tooltip.getInstance(this)?.dispose();
    });

    if (!errors) return;
    Object.keys(errors).forEach(field => {
        const el = $form.find(`[name="${field}"]`);
        if (el) {
            $(el).addClass('is-invalid').attr('title', errors[field][0]);
        }
    });
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
            $('#modal-loading').modal('show');
            if(elFormId !=null){
                elFormId[0].reset();
            }
        },
        success: function (result) {
            fn(result);
            $('#modal-loading').modal('hide');

        },
        error: function (result) {
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
                toastr.error(errorResponse.msg ?? '');
                toastr.error(errorResponse.message ?? '');
            }
            if( result.status === 409 ){

            }

            if( result.status === 422 ){
                
                    Swal.fire({ icon: 'error', title: 'Error', text: ('Please check the required fields.')});
                    toastr.error(errorResponse.message);
                    applyValidationState(errorResponse.errors, elFormId); // <-- replaces all if-else
                // toastr.error(errorResponse.message);

                // errorHandler( errors.first_molding_device_id,formModal.firstMolding.find('#first_molding_device_id') );
            }

        }
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
/**
 * SweetAlert confirmation
 */
const  swalConfirmAction =(message, callback) => {
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
