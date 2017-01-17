<?php
namespace W3TC;

if ( !defined( 'W3TC' ) )
	die();

/*
 * Requires $module variable
 */
?>
<tr>
    <th><label for="redis_servers"><?php echo Util_ConfigLabel::get( 'redis.servers' ) ?></label></th>
    <td>
        <input id="redis_servers" type="text"
            name="<?php echo $module ?>__redis__servers"
            <?php Util_Ui::sealing_disabled( $module ) ?>
            value="<?php echo esc_attr( implode( ',', $this->_config->get_array( $module . '.redis.servers' ) ) ); ?>"
            size="100" />
        <input class="w3tc_common_redis_test button {nonce: '<?php echo wp_create_nonce( 'w3tc' ); ?>'}"
            <?php Util_Ui::sealing_disabled( $module ) ?>
            type="button" value="<?php esc_attr_e( 'Test', 'w3-total-cache' ); ?>" />
        <span class="w3tc_common_redis_test_result w3tc-status w3tc-process"></span>
        <br /><span class="description"><?php _e( 'Multiple servers may be used and seperated by a comma; e.g. 192.168.1.100:11211, domain.com:22122', 'w3-total-cache' ); ?></span>
    </td>
</tr>
<tr>
    <th><label><?php _e( 'Use persistent connection:', 'w3-total-cache' ); ?></label></th>
    <td>
        <?php $this->checkbox( $module . '.redis.persistent' ) ?> <?php echo Util_ConfigLabel::get( 'redis.persistent' ) ?></label><br />
        <span class="description"><?php _e( 'Using persistent connection doesn\'t reinitialize redis driver on each request', 'w3-total-cache' ); ?></span>
    </td>
</tr>
<tr>
    <th style="width: 250px;"><label for="redis_dbid"><?php echo Util_ConfigLabel::get( 'redis.dbid' ) ?></label></th>
    <td>
        <input id="redis_dbid" type="text" name="<?php echo $module ?>__redis__dbid"
            <?php Util_Ui::sealing_disabled( $module ) ?>
            value="<?php echo esc_attr( $this->_config->get_integer( $module . '.redis.dbid' ) ); ?>"
            size="8" />
        <br /><span class="description"><?php _e( 'Database ID to use', 'w3-total-cache' ); ?></span>
    </td>
</tr>
<tr>
    <th><label for="redis_password"><?php echo Util_ConfigLabel::get( 'redis.password' ) ?></label></th>
    <td>
        <input id="redis_password" name="<?php echo $module ?>__redis__password" type="text"
            <?php Util_Ui::sealing_disabled( $module ) ?>
            <?php
$this->value_with_disabled( $module . '.redis.password',
	false, '' )
?> /><br />
        <span class="description"><?php _e( 'Specify redis password, when <acronym title="Simple Authentication and Security Layer">SASL</acronym> authentication used', 'w3-total-cache' )?></span>
    </td>
</tr>
