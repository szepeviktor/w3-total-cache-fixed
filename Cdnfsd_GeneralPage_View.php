<?php
namespace W3TC;

if ( !defined( 'W3TC' ) )
	die();

?>
<p><?php _e( 'Host whole website with your full site content delivery network provider to reduce page load time.', 'w3-total-cache' ); ?>
<?php if ( !$cdnfsd_enabled ): ?>
<?php printf( __( ' If you do not have a <acronym title="Content Delivery Network">CDN</acronym> provider try MaxCDN. <a href="%s" target="_blank">Sign up and save 25&#37;</a>.', 'w3-total-cache' ), wp_nonce_url( Util_Ui::admin_url( 'admin.php?page=w3tc_dashboard&w3tc_cdn_maxcdn_signup' ), 'w3tc' ) ); ?>
<?php endif ?>
</p>
<table class="form-table">
    <?php
Util_Ui::config_item( array(
		'key' => 'cdnfsd.enabled',
		'label' => __( '<acronym title="Full Site Delivery">FSD</acronym> <acronym title="Content Delivery Network">CDN</acronym>:', 'w3-total-cache' ),
		'control' => 'checkbox',
		'checkbox_label' => __( 'Enable', 'w3-total-cache' ),
		'disabled' => ( $is_pro ? null : true ),
		'description' => __( 'Whole website will appear to load instantly for site visitors.',
			'w3-total-cache' ) .
			( $is_pro ? '' : __( ' <strong>Available after upgrade.</strong>', 'w3-total-cache' ) )
	) );

Util_Ui::config_item( array(
		'key' => 'cdnfsd.engine',
		'label' => __( '<acronym title="Full Site Delivery">FSD</acronym> <acronym title="Content Delivery Network">CDN</acronym> Type:', 'w3-total-cache' ),
		'control' => 'selectbox',
		'selectbox_values' => $cdnfsd_engine_values,
		'value' => $cdnfsd_engine,
		'disabled' => ( $is_pro ? null : true ),
		'description' => __( 'Select the <acronym title="Content Delivery Network">CDN</acronym> type you wish to use.',
			'w3-total-cache' ) . $cdnfsd_engine_extra_description
	) );
?>
</table>
