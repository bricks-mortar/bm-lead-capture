var pluginPath = 'wp-content/plugins/bm-lead-capture';

jQuery(function($) {

//    if (!readCookie('bm_pitched')) {

        setTimeout(function() {
            loadConfig('wp-content/plugins/bm-lead-capture/config.json', function(config) {

                setCookie('bm_pitched', true, config.cookie_length);

                $(document).bind("DOMNodeInserted", function() {
                    $("#capture-body").css('background', "url('" + config.popup_background + "')");
                    $("#popup-heading").html(config.popup_heading);
                    $("#popup-subheading").html(config.popup_subheading);
                    $("#yes-msg").html(config.yes_message);
                    $("#no-msg").html(config.no_message);
                    $("#final-heading").html(config.offer_heading);
                    $("#final-message").html(config.offer_message);
                });

                $('body').append($('<div id="bm-lead-capture"></div>'));
                $('#bm-lead-capture').load(pluginPath + "/assets/popover.html");
            });
        }, 1);
//    }


    $(document).on('submit', '#emailCapture', ajaxSubmit);
    function ajaxSubmit() {
        var emailCaptureForm = jQuery(this).serialize();
        $.ajax({
            type:"POST",
            url: "wp-admin/admin-ajax.php",
            data: emailCaptureForm,
            success:function(data) {
                $(".status").html(data);
            }
        });
        return false;
    }

    $(document).on('click', '#yes', function() {
        $('#step-one').fadeOut('fast', function() {
            $('#step-two').fadeIn('fast');
        });
    });

    $(document).on('click', '#no', function() {
        $('#bm-lead-capture').fadeOut("slow", function() {
            $(this).remove();
        });
    });
    $(document).on('click', '#lc-close', function() {
        $('#bm-lead-capture').fadeOut("slow", function() {
            $(this).remove();
        });
    });

});


/**
 * Helper functions
 */

function loadConfig(addr, callback) {
    jQuery.getJSON(addr, function(data) {
        var items = {}
        jQuery.each(data, function(key, val) {
            items[key] = val;
        });

        callback(items);
    });
}

function setCookie(cookieName, cookieValue, days) {
    var cookie = cookieName + '=' + cookieValue;

    // make sure you don't replace a cookie with the same value
    var allcookies = document.cookie;
    allcookies  = allcookies.split(';');

    for (var i=0; i<allcookies.length; i++) {
        if (cookie == allcookies[i]) {
            return;
        }
    }
    // set our cookie for a month
    document.cookie = cookie + ';' + 'path=/;' + 'max-age=' + 60*60*24*days + ';';
}

function readCookie(cookieName) {
    var allcookies = document.cookie;
    allcookies  = allcookies.split(';');

    for (var i=0; i<allcookies.length; i++) {
        var name = jQuery.trim(allcookies[i].split('=')[0]);
        if (name == cookieName) {
            var value = jQuery.trim(allcookies[i].split('=')[1]);
            return value;
        }
    }
    return false;
}

function deleteCookie(cookieName) {
    document.cookie = cookieName + '=; expires=Thu, 01-Jan-70 00:00:01 GMT;';
}