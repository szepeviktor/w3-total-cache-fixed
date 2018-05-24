jQuery(function() {
    jQuery('.button-vote').live('click', function() {
        window.open('https://wordpress.org/support/plugin/w3-total-cache/reviews/#new-post');
    });

    jQuery('.button-share').live('click', function() {
        window.open('https://plus.google.com/share?url=' +
             encodeURIComponent(w3tc_spread_the_word_product_url), '_blank');
    });

    jQuery('.button-tweet').live('click', function() {
        window.open('https://twitter.com/?status=' + 
            encodeURIComponent(w3tc_spread_the_word_tweet), '_blank');
    });

    jQuery('.button-like').live('click', function() {
        window.open('https://www.facebook.com/sharer.php?u=' +
            encodeURIComponent(w3tc_spread_the_word_product_url), '_blank');
    });

    var last_value = null;
    jQuery('#common_support').change(function() {
        var where = jQuery(this).val();
        if (last_value == where)
            return;

        last_value = where;

        jQuery.post(ajaxurl, {
            action:'w3tc_link_support', 
            w3tc_common_support_us: where
        }, function(data) {
            alert(data);
        });
    });

    jQuery('.button-rating').live('click', function() {
        window.open(w3tc_spread_the_word_rate_url, '_blank');
    });
});