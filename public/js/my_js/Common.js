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

            console.log('errorResponse',errorResponse);
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
                toastr.error(errorResponse.message);
            }

        }
    });
}

const resetFormValues = (params) => {
    // Reset values
    params.frmId[0].reset();
    
    // params.frmId[0].reset();

    // Reset hidden input fields
    // $("select[name='user_level']", $('#formAddUser')).val(0).trigger('change');

    // Remove invalid & title validation
    $('div').find('input').removeClass('is-invalid');
    $("div").find('input').attr('title', '');
    $('div').find('select').removeClass('is-invalid');
    $("div").find('select').attr('title', '');
}
