<?php
namespace W3TC;

if ( !defined( 'W3TC' ) )
	die();

?>
<?php _e( 'Metrics are not available for browser applications', 'w3-total-cache' )?>
<p>
    <a href="<?php echo esc_attr( NEWRELIC_SIGNUP_URL ); ?>" target="_blank">
        <?php _e( 'Upgrade your New Relic account to enable more metrics.', 'w3-total-cache' )?>
    </a>
</p>
