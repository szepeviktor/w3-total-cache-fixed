<?php
namespace W3TC;

if ( !defined( 'W3TC' ) )
	die();

?>
<div id="maxcdn-widget" class="sign-up maxcdn-netdna-widget-base">
    <?php if ( $error ): ?>
    <?php Util_Ui::error_box( '<p>' . sprintf( __( 'MaxCDN encountered an error trying to retrieve data, make sure your host support cURL and outgoing requests: %s', 'w3-total-cache' ), $error ) . '</p>' ) ?>
    <?php endif; ?>
    <?php if ( !$authorized ): ?>
    <p><?php _e( 'Add the MaxCDN content delivery network to increase website speeds dramatically in just a few minutes!', 'w3-total-cache' )?></p>
    <h4><?php _e( 'New customers', 'w3-total-cache' )?></h4>
    <p><?php _e( 'MaxCDN is a service that lets you speed up your site even more with W3 Total Cache.', 'w3-total-cache' )?></p>
    <a class="button-primary" href="<?php echo wp_nonce_url( Util_Ui::admin_url( 'admin.php?page=w3tc_dashboard&w3tc_cdn_maxcdn_signup' ), 'w3tc' )?>" target="_blank"><?php _e( 'Sign Up Now and Save 25%', 'w3-total-cache' )?></a>
    <p><span class="desc"><?php _e( '100% Money Back Guarantee (30 Days)', 'w3-total-cache' )?></span></p>
     <?php endif ?>
        <h4><?php _e( 'Current customers', 'w3-total-cache' )?></h4>
        <p><?php _e( "Once you've signed up or if you're an existing MaxCDN customer, to enable CDN:", 'w3-total-cache' )?></p>
        <a class="button-primary" href="<?php echo wp_nonce_url( Util_Ui::admin_url( 'admin.php?page=w3tc_cdn' ), 'w3tc' )?>" target="_blank"><?php _e( 'Authorize', 'w3-total-cache' )?></a>
</div>
