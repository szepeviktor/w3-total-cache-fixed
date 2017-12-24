jQuery(function($) {
	function w3tc_maxcdn_resize(o) {
		o.options.height = jQuery('.w3tc_cdn_maxcdn_form').height();
		o.resize();
	}

	$('body')
		.on('click', '.w3tc_cdn_maxcdn_authorize', function() {
		    W3tc_Lightbox.open({
		        id:'w3tc-overlay',
		        close: '',
		        width: 800,
		        height: 300,
		        url: ajaxurl + '?action=w3tc_ajax&_wpnonce=' + w3tc_nonce +
            		'&w3tc_action=cdn_maxcdn_intro',
		        callback: w3tc_maxcdn_resize
		    });
		})



		.on('click', '.w3tc_cdn_maxcdn_list_zones', function() {
			var url = ajaxurl + '?action=w3tc_ajax&_wpnonce=' + w3tc_nonce +
        		'&w3tc_action=cdn_maxcdn_list_zones';

			W3tc_Lightbox.load_form(url, '.w3tc_cdn_maxcdn_form', w3tc_maxcdn_resize);
	    })



	    .on('click', '.w3tc_cdn_maxcdn_view_zone', function() {
			var url = ajaxurl + '?action=w3tc_ajax&_wpnonce=' + w3tc_nonce +
        		'&w3tc_action=cdn_maxcdn_view_zone';

			W3tc_Lightbox.load_form(url, '.w3tc_cdn_maxcdn_form', w3tc_maxcdn_resize);
	    })



	    .on('click', '.w3tc_cdn_maxcdn_configure_zone', function() {
			var url = ajaxurl + '?action=w3tc_ajax&_wpnonce=' + w3tc_nonce +
        		'&w3tc_action=cdn_maxcdn_configure_zone';

    		W3tc_Lightbox.load_form(url, '.w3tc_cdn_maxcdn_form', w3tc_maxcdn_resize);
	    })



	    .on('click', '.w3tc_cdn_maxcdn_configure_zone_skip', function() {
			var url = ajaxurl + '?action=w3tc_ajax&_wpnonce=' + w3tc_nonce +
        		'&w3tc_action=cdn_maxcdn_configure_zone_skip';

			W3tc_Lightbox.load_form(url, '.w3tc_cdn_maxcdn_form', w3tc_maxcdn_resize);
	    })



	    .on('click', '.w3tc_cdn_maxcdn_done', function() {
			// refresh page
	    	window.location = window.location + '&';
	    })



	    .on('size_change', '#cdn_cname_add', function() {
	    	w3tc_maxcdn_resize(W3tc_Lightbox);
	    })
});
