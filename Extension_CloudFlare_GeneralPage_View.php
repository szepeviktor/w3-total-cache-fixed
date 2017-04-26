<?php
namespace W3TC;

if ( !defined( 'W3TC' ) )
	die();

?>
<?php Util_Ui::postbox_header( __( 'Network Performance &amp; Security powered by CloudFlare', 'w3-total-cache' ), '', 'cloudflare' ); ?>
<?php
Util_Ui::config_overloading_button( array(
		'key' => 'cloudflare.configuration_overloaded'
	) );
?>
<p>
    <?php _e( 'CloudFlare protects and accelerates websites.', 'w3-total-cache' ) ?>
</p>

<table class="form-table">
    <?php
Util_Ui::config_item( array(
		'key' => array( 'cloudflare', 'widget_cache_mins' ),
		'label' => __( 'Cache time:', 'w3-total-cache' ),
		'control' => 'textbox',
		'description' =>
		'How many minutes data retrieved from CloudFlare ' .
		'should be stored. Minimum is 1 minute.'
	) );

Util_Ui::config_item( array(
		'key' => array( 'cloudflare', 'pagecache' ),
		'label' => __( 'Page Caching:', 'w3-total-cache' ),
		'control' => 'checkbox',
		'checkbox_label' => 'Flush CloudFlare on Post Modifications',
		'description' =>
		'Enable when you have html pages cached on CloudFlare level.'
	) );
?>
</table>

<?php
Util_Ui::button_config_save( 'general_cloudflare',
	'<input type="submit" name="w3tc_cloudflare_flush" value="' .
	__( 'Empty cache', 'w3-total-cache' ) . '"' .
	' class="button" />' );
?>
<?php Util_Ui::postbox_footer(); ?>
