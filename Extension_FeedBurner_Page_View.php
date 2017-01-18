<?php
namespace W3TC;

if ( !defined( 'W3TC' ) )
	die();

?>
<p>
    Jump to:
    <a href="admin.php?page=w3tc_general"><?php _e( 'Main Menu', 'w3-total-cache' ); ?></a> |
    <a href="admin.php?page=w3tc_extensions"><?php _e( 'Extensions', 'w3-total-cache' ); ?></a>
</p>
<p>
    FeedBurner extension is currently <?php
if ( $config->is_extension_active_frontend( 'feedburner' ) )
	echo '<span class="w3tc-enabled">enabled</span>';
else
	echo '<span class="w3tc-disabled">disabled</span>';
?>.
<p>

<div class="metabox-holder">
    <?php Util_Ui::postbox_header( __( 'Google FeedBurner', 'w3-total-cache' ) ); ?>
    <table class="form-table">
        <?php
Util_Ui::config_item( array(
		'key' => array( 'feedburner', 'urls' ),
		'control' => 'textarea',
		'label' => __( 'Additional <acronym title="Uniform Resource Locator">URL</acronym>s:', 'w3-total-cache' ),
		'description' => __( 'Specify any additional feed <acronym title="Uniform Resource Locator">URL</acronym>s to ping on FeedBurner.',
			'w3-total-cache' )
	) )
?>
    </table>
    <?php Util_Ui::button_config_save( 'extension_feedburner' ); ?>
    <?php Util_Ui::postbox_footer(); ?>
</div>
