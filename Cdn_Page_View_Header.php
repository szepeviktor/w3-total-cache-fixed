<?php
namespace W3TC;

if ( !defined( 'W3TC' ) )
	die();

?>
<?php include W3TC_INC_DIR . '/options/common/header.php'; ?>

<p>
    <?php echo sprintf(
	__( 'Content Delivery Network support via %1$s is currently %2$s.', 'w3-total-cache' ),
	'<strong>'.Cache::engine_name( $config->get_string( 'cdn.engine' ) ).'</strong>',
	'<span class="w3tc-' . ( $config->get_boolean( 'cdn.enabled' ) ? 'enabled">' . __( 'enabled', 'w3-total-cache' ) : 'disabled">' . __( 'disabled', 'w3-total-cache' ) ) . '</span>'
); ?>
</p>
