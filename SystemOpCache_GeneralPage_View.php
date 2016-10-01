<?php
namespace W3TC;

if ( !defined( 'W3TC' ) )
	die();

?>
<?php Util_Ui::postbox_header( __( 'Opcode Cache', 'w3-total-cache' ), '', 'system_opcache' ); ?>

<table class="form-table">
    <?php
Util_Ui::config_item( array(
		'key' => 'opcache.engine',
		'label' => 'Opcode Cache',
		'control' => 'selectbox',
		'value' => $opcode_engine,
		'selectbox_values' => array(
			'Not Available' => array(
				'disabled' => ( $opcode_engine !== 'Not Available' ),
				'label' => __( 'Not Available', 'w3-total-cache' ),
			),
			'OPcache' => array(
				'disabled' => ( $opcode_engine !== 'OPcache' ),
				'label' => __( 'Opcode: Zend Opcache', 'w3-total-cache' ),
			),
			'APC' => array(
				'disabled' => ( $opcode_engine !== 'APC' ),
				'label' => __( 'Opcode: Alternative PHP Cache (APC / APCu)', 'w3-total-cache' ),
			),
		),
	) );

Util_Ui::config_item( array(
		'key' => 'opcache.validate_timestamps',
		'label' => 'Validate timestamps:',
		'control' => 'checkbox',
		'disabled' => true,
		'value' => $validate_timestamps,
		'checkbox_label' => __( 'Enable', 'w3-total-cache' ),
		'description' => __( 'Once enabled, each file request will update the cache with the latest version.'
			. 'When this setting is off, the Opcode Cache will not check, instead PHP must be restarted in order for setting changes to be reflected.', 'w3-total-cache' )
	) );
?>

</table>
<?php
Util_Ui::button_config_save( 'general_opcache', '<input type="submit" name="w3tc_opcache_flush" value="' .
	__( 'Empty cache', 'w3-total-cache' ) . '"' .
	( ( ( $opcode_engine !== 'Not Available' ) ) ? '' : ' disabled="disabled" ' ) .
	' class="button" />' );
?>

<?php Util_Ui::postbox_footer(); ?>
