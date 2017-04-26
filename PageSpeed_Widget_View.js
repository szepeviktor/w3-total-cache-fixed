jQuery(document).ready(function($) {
    function w3tcps_load(nocache) {
        $('.w3tcps_loading').removeClass('w3tc_hidden');
        $('.w3tcps_content').addClass('w3tc_hidden');
        $('.w3tcps_error').addClass('w3tc_none');

        $.getJSON(ajaxurl + '?action=w3tc_ajax&_wpnonce=' + w3tc_nonce + 
            '&w3tc_action=pagespeed_widgetdata' + (nocache ? '&cache=no' : ''),
            function(data) {
                $('.w3tcps_loading').addClass('w3tc_hidden');

                if (data.error) {
                    $('.w3tcps_error').removeClass('w3tc_none');
                    return;
                }

                jQuery('.w3tcps_score').html(data.score);
                jQuery('.w3tcps_list').html(data.details);
                
                $('.w3tcps_content').removeClass('w3tc_hidden');
            }
        ).fail(function() {
            $('.w3tcps_error').removeClass('w3tc_none');
            $('.w3tcps_content').addClass('w3tc_hidden');
            $('.w3tcps_loading').addClass('w3tc_hidden');
        });
    }



    jQuery('.w3tc-widget-ps-view-all').click(function() {
        window.open('admin.php?page=w3tc_dashboard&w3tc_test_pagespeed_results&_wpnonce=' + jQuery(this).metadata().nonce, 'pagespeed_results', 'width=800,height=600,status=no,toolbar=no,menubar=no,scrollbars=yes');
        return false;
    });

    jQuery('.w3tc-widget-ps-refresh').click(function() {
        w3tcps_load(true);
    });



    w3tcps_load();
});
