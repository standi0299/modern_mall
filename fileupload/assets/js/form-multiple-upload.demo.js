/*
Template Name: Color Admin - Responsive Admin Dashboard Template build with Twitter Bootstrap 3.3.7
Version: 2.1.0
Author: Sean Ngu
Website: http://www.seantheme.com/color-admin-v2.1/admin/html/
*/
var handleJqueryFileUpload = function() {
     // Initialize the jQuery File Upload widget:
    $('#fileupload').fileupload({
        autoUpload: false,
        //disableImageResize: /Android(?!.*Chrome)|Opera/.test(window.navigator.userAgent), //리사이즈 사용안함.
        maxFileSize: 157286400, //157286400 //5000000
        //acceptFileTypes: /(\.|\/)(gif|jpe?g|png|zip|pdf)$/i,
        acceptFileTypes: /(\.|\/)(gif|jpg|jpeg|png|zip|pdf|xlsx|xls|ai|eps|psd|indd)$/i,
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials: true},     
    });

    // Enable iframe cross-domain access via redirect option:
    $('#fileupload').fileupload(
        'option',
        'redirect',
        window.location.href.replace(
            /\/[^\/]*$/,
            '/cors/result.html?%s'
        )
    );

    // Upload server status check for browsers with CORS support:
    if ($.support.cors) {
        $.ajax({
            type: 'HEAD'
        }).fail(function () {
            $('<div class="alert alert-danger"/>').text('Upload server currently unavailable - ' + new Date()).appendTo('#fileupload');
        });
    }

    // Load & display existing files:
    $('#fileupload').addClass('fileupload-processing');
    $.ajax({
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials: true},
        url: $('#fileupload').fileupload('option', 'url'),
        dataType: 'json',
        context: $('#fileupload')[0]
    }).always(function () {
        $(this).removeClass('fileupload-processing');
    }).done(function (result) {
        $(this).fileupload('option', 'done')
        .call(this, $.Event('done'), {result: result});
    });
};


var FormMultipleUpload = function () {
	"use strict";
    return {
        //main function
        init: function () {
            handleJqueryFileUpload();            
        }
    };
}();