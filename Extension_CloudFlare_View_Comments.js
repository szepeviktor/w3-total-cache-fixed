jQuery(function($) {
    $('.w3tc_cloudflare_ip_check').click(function(e) {
        var href = $(this).attr('href');
        if (href.substr(0, 4) != '#ip=')
            return;

        e.preventDefault();

        var ip = unescape(href.substr(4));
        var tr = $(this).parent().closest('tr');
        var ip_span = tr.find('.cloudflare_ip_check');
        if (ip_span.length <= 0) {
            tr.find('.column-author').append(
                '<span class="cloudflare_ip_check"></span>');
            ip_span = tr.find('.cloudflare_ip_check');
        }

        ip_span.empty();
        $('<img>')
            .attr('class', 'cloudflare_ip_check_img')
            .attr('src', 'images/wpspin_light.gif')
            .attr('alt', 'Resolving IP ' + ip)
            .appendTo(ip_span);

        jQuery.post(ajaxurl, {
            action:'w3tc_cloudflare_ip_check',
            ip: ip
        }, null, 'json')
        .done(function(data) {
            ip_span.empty();
            var className = (data.error ? 'cloudflare_ip_check_error' : 
                'cloudflare_ip_check_success');
            ip_span.html('<span class="' + className + '">' + 
                data.message + '</span>');
        })
        .fail(function() {
            ip_span.html('<span class="cloudflare_ip_check_error">check failed</span>');
        });
    });
});

function w3tc_cloudflare_api_request(action, value, nonce) {
    var email = jQuery('input[id="cloudflare.email"]');
    var key = jQuery('input[id="cloudflare.key"]');
    var zone = jQuery('input[id="cloudflare.zone"]');

    if (!email.val()) {
        alert('Please enter CloudFlare E-Mail.');
        email.focus();
        return false;
    }

    if (!key.val()) {
        alert('Please enter CloudFlare API key.');
        key.focus();
        return false;
    }

    if (!zone.val()) {
        alert('Please enter CloudFlare zone.');
        zone.focus();
        return false;
    }

}
