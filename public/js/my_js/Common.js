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