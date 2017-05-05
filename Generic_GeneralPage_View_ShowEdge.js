
jQuery(function() {
    W3tc_Lightbox.open({
        id:'w3tc-overlay',
        close: '',
        width: 800,
        height: 210,
        url: ajaxurl + '?action=w3tc_ajax&_wpnonce=' + w3tc_nonce + 
            '&w3tc_action=generic_edge'
    });
});