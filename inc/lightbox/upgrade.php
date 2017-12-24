<?php
namespace W3TC;

if ( !defined( 'W3TC' ) )
	die();

?>
<div id="w3tc-upgrade">
    <div class="w3tc-overlay-logo"></div>
    <div class="w3tc_overlay_upgrade_header">
        <div>
            <div class="w3tc_overlay_upgrade_left_h">
                W3 Total Cache Pro unlocks more performance options for any website!
            </div>
            <div class="w3tc_overlay_upgrade_right_h">
                only $99 <span class="w3tc_overlay_upgrade_right_text">/year</span>
            </div>
        </div>
        <div class="w3tc_overlay_upgrade_description">
            <div class="w3tc_overlay_upgrade_content_l">
                <img src="<?php echo plugins_url( 'pub/img/overlay/w3-meteor.png', W3TC_FILE ) ?>"
                    width="238" height="178" />
            </div>
            <div class="w3tc_overlay_upgrade_content_r">
                <ul>
                    <li>
                        <strong>Full Site Delivery (FSD)</strong><br>
                        Provide the best user experience possible by enhancing by hosting HTML pages and RSS feeds with (supported) <acronym title="Content Delivery Network">CDN</acronym>'s high speed global networks.</li>
                    <li><strong>Fragment Caching Module</strong><br>
                        Unlocking the fragment caching module delivers enhanced performance for plugins and themes that use the WordPress Transient API. StudioPress' Genesis Framework is up to 60% faster with W3TC Pro.</li>
                    <li>
                        <strong>WPML Extension</strong><br>
                        Improve the performance of your WPML-powered site by unlocking W3TC Pro.</li>
                </ul>
            </div>
        </div>
        <div style="clear: both"></div>
    </div>
    <div class="w3tc_overlay_content"></div>
    <div class="w3tc_overlay_footer">
        <input id="w3tc-purchase" type="button" class="btn w3tc-size image btn-default palette-turquoise secure" value="<?php _e( 'Subscribe to Go Faster Now', 'w3-total-cache' ) ?> " />
    </div>
    <div style="clear: both"></div>
</div>
