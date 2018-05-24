<?php
namespace W3TC;

if ( !defined( 'W3TC' ) )
	die();

?>
<div id="maxcdn-widget" class="w3tcmaxcdn_signup">
    <p><?php _e( 'Dramatically increase website speeds in just a few clicks! Add the MaxCDN content delivery network service to your site.', 'w3-total-cache' )?></p>
    <h4 class="w3tcmaxcdn_signup_h4"><?php _e( 'New customers', 'w3-total-cache' )?></h4>
    <p><?php _e( 'MaxCDN works magically with W3 Total Cache.', 'w3-total-cache' )?></p>
    <a class="button-primary" href="<?php echo wp_nonce_url( Util_Ui::admin_url( 'admin.php?page=w3tc_dashboard&w3tc_cdn_maxcdn_signup' ), 'w3tc' )?>" target="_blank"><?php _e( 'Sign Up Now and Save 25%', 'w3-total-cache' )?></a>
    <p><span class="desc"><?php _e( '100% Money Back Guarantee (30 Days)', 'w3-total-cache' )?></span></p>
        <h4 class="w3tcmaxcdn_signup_h4"><?php _e( 'Current customers', 'w3-total-cache' )?></h4>
        <p><?php _e( "Existing MaxCDN customers, enable <acronym title='Content Delivery Network'>CDN</acronym> and:", 'w3-total-cache' )?></p>
        <a class="button-primary" href="<?php echo wp_nonce_url( Util_Ui::admin_url( 'admin.php?page=w3tc_cdn' ), 'w3tc' )?>" target="_blank"><?php _e( 'Authorize', 'w3-total-cache' )?></a>
</div>
