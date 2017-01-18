jQuery(document).ready(function($) {
    function w3tc_ustats_load() {
        top_object = $('.ustats_top');
        $('.ustats_loading').removeClass('w3tc_hidden');
        $('.ustats_content').addClass('w3tc_hidden');
        $('.ustats_error').addClass('w3tc_none');
        $('.ustats_nodata').addClass('w3tc_none');

        $.getJSON(ajaxurl + '?action=w3tc_ajax&_wpnonce=' + w3tc_nonce + 
            '&w3tc_action=ustats_get',
            function(data) {
                w3tc_ustats_set_values(data, 'ustats_');
                
                if (data.period.seconds)
                    $('.ustats_content').removeClass('w3tc_hidden');
                else
                    $('.ustats_nodata').removeClass('w3tc_none');

                $('.ustats_loading').addClass('w3tc_hidden');

                w3tc_ustats_set_refresh(
                    (data && data.period ? data.period.to_update_secs : 0));
            }
        ).fail(function() {
            $('.ustats_error').removeClass('w3tc_none');
            $('.ustats_content').addClass('w3tc_hidden');
            $('.ustats_loading').addClass('w3tc_hidden');
        });
    }



    function w3tc_ustats_set_values(data, css_class_prefix) {
        for (p in data) {
            var v = data[p];
            if (typeof(v) != 'string' && typeof(v) != 'number')
                w3tc_ustats_set_values(v, css_class_prefix + p + '_');
            else {
                jQuery('.' + css_class_prefix + p).html(v);
            }
        }
    }



    var seconds_timer_id;
    function w3tc_ustats_set_refresh(new_seconds_till_refresh) {
        clearTimeout(seconds_timer_id);
        var seconds_till_refresh = new_seconds_till_refresh;

        seconds_timer_id = setInterval(function() {
            seconds_till_refresh--;
            if (seconds_till_refresh <= 0) {
                jQuery('.ustats_reload').text('Refresh');
                clearTimeout(seconds_timer_id);
                seconds_timer_id = null;
                return;
            }

            jQuery('.ustats_reload').text('Will be recalculated in ' + 
                seconds_till_refresh + ' second' + 
                (seconds_till_refresh > 1 ? 's' : ''));
        }, 1000);
    }

    w3tc_ustats_load();

    $('.ustats_reload').click(function(e) {
        event.preventDefault();
        w3tc_ustats_load();
    })
});
