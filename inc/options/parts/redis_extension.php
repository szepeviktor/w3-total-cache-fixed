<?php
namespace W3TC;

if ( !defined( 'W3TC' ) )
	die();

/*
 * Requires $module variable
 */
$config = Dispatcher::config();

?>
<tr>
    <th><label for="redis_servers"><?php echo Util_ConfigLabel::get( 'redis.servers' ) ?></label></th>
    <td>
        <input id="redis_servers" type="text"
            name="<?php echo $module ?>___redis__servers"
            <?php Util_Ui::sealing_disabled( $module ) ?>
            value="<?php echo esc_attr( implode( ',', $config->get_array( array( $module, 'redis.servers' ) ) ) ); ?>"
            size="100" />
        <input class="w3tc_common_redis_test button {nonce: '<?php echo wp_create_nonce( 'w3tc' ); ?>'}"
            <?php Util_Ui::sealing_disabled( $module ) ?>
            type="button" value="<?php esc_attr_e( 'Test', 'w3-total-cache' ); ?>" />
        <span class="w3tc_common_redis_test_result w3tc-status w3tc-process"></span>
        <br /><span class="description"><?php _e( 'Multiple servers may be used and seperated by a comma; e.g. 192.168.1.100:11211, domain.com:22122', 'w3-total-cache' ); ?></span>
    </td>
</tr>
<?php

Util_Ui::config_item( array(
		'key' => array( $module, 'redis.persistent' ),
		'label' => __( 'Use persistent connection:', 'w3-total-cache' ),
		'control' => 'checkbox',
		'checkbox_label' => Util_ConfigLabel::get( 'redis.persistent' ),
		'description' =>
		'Using persistent connection doesn\'t reinitialize memcached driver on each request'
	) );

Util_Ui::config_item( array(
		'key' => array( $module, 'redis.dbid' ),
		'label' => Util_ConfigLabel::get( 'redis.dbid' ),
		'control' => 'textbox',
		'description' =>
		__( 'Database ID to use', 'w3-total-cache' )
	) );

Util_Ui::config_item( array(
		'key' => array( $module, 'redis.password' ),
		'label' => Util_ConfigLabel::get( 'redis.password' ),
		'control' => 'textbox',
		'description' =>
		__( 'Specify redis password', 'w3-total-cache' )
	) );
