<?php
namespace W3TC;

if ( !defined( 'W3TC' ) )
	die();

?>
<div id="w3tc-upgrade">
    <div class="w3tc-overlay-logo"></div>
    <header>
        <div>
        <div class="left" style="float:left">
            <h2>W3 Total Cache Pro unlocks more performance options for your website!</h2>
        </div>
        <div class="right" style="float:right">
            <h2>only $99 <span>/year</span></h2>
        </div>
        </div>
        <div class="description">
            <img src="<?php echo plugins_url( 'pub/img/overlay/w3-meteor.png', W3TC_FILE ) ?>"  />
            <ul>
                <li><strong>Fragment Caching Extension</strong><br>
                    Unlocking the fragment caching module delivers enhanced performance for plugins and themes that use the WordPress Transient API. StudioPress' Genesis Framework is up to 60% faster with W3TC Pro.</li>
                <li>
                    <strong>WPML Extension</strong><br>
                    Improve the performance of your WPML-powered site by unlocking W3TC Pro.                </li>
                <li>
                    <strong>Full Site Delivery</strong><br>
                    Provide the best user experience possible by enhancing by hosting HTML pages and RSS feeds with (supported) CDN's high speed global networks.                </li>
            </ul>
        </div>
    </header>
    <div class="content">
    </div>
    <div class="footer">
        <input id="w3tc-purchase" type="button" class="btn w3tc-size image btn-default palette-turquoise secure" value="<?php _e( 'Subscribe to Go Faster Now', 'w3-total-cache' ) ?> " />
    </div>
</div>
