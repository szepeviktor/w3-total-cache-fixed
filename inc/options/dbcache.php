<?php
namespace W3TC;

if ( !defined( 'W3TC' ) )
	die();

?>
<?php include W3TC_INC_DIR . '/options/common/header.php'; ?>

<form action="admin.php?page=<?php echo $this->_page; ?>" method="post">
    <p>
		<?php echo sprintf( __( 'Database caching via %s is currently %s.', 'w3-total-cache' ), Cache::engine_name( $this->_config->get_string( 'dbcache.engine' ) ) , '<span class="w3tc-' . ( $dbcache_enabled ? 'enabled">' . __( 'enabled', 'w3-total-cache' ) : 'disabled">' . __( 'disabled', 'w3-total-cache' ) ) . '</span>' ); ?>
    </p>
    <p>
        <?php _e( 'To rebuild the database cache use the', 'w3-total-cache' ) ?>
        <?php echo Util_Ui::nonce_field( 'w3tc' ); ?>
        <input type="submit" name="w3tc_flush_dbcache" value="<?php _e( 'empty cache', 'w3-total-cache' ); ?>"<?php if ( ! $dbcache_enabled ): ?> disabled="disabled"<?php endif; ?> class="button" />
			<?php _e( 'operation.', 'w3-total-cache' ); ?>
    </p>
</form>

<form action="admin.php?page=<?php echo $this->_page; ?>" method="post">
    <div class="metabox-holder">
        <?php Util_Ui::postbox_header( __( 'General', 'w3-total-cache' ), '', 'general' ); ?>
        <table class="form-table">
            <tr>
                <th>
                    <?php $this->checkbox( 'dbcache.reject.logged' ) ?> <?php Util_Ui::e_config_label( 'dbcache.reject.logged' ) ?></label>
                    <br /><span class="description"><?php _e( 'Enabling this option is recommended to maintain default WordPress behavior.', 'w3-total-cache' ); ?></span>
                </th>
            </tr>
        </table>

        <?php Util_Ui::button_config_save( 'dbcache_general' ); ?>
        <?php Util_Ui::postbox_footer(); ?>

        <?php Util_Ui::postbox_header( __( 'Advanced', 'w3-total-cache' ), '', 'advanced' ); ?>
        <table class="form-table">
            <?php
if ( $this->_config->get_string( 'dbcache.engine' ) == 'memcached' ) {
	$module = 'dbcache';
	include W3TC_INC_DIR . '/options/parts/memcached.php';
} elseif ( $this->_config->get_string( 'dbcache.engine' ) == 'redis' ) {
	$module = 'dbcache';
	include W3TC_INC_DIR . '/options/parts/redis.php';
}
?>
            <tr>
                <th style="width: 250px;"><label for="dbcache_lifetime"><?php Util_Ui::e_config_label( 'dbcache.lifetime' ) ?></label></th>
                <td>
                    <input id="dbcache_lifetime" type="text" name="dbcache__lifetime"
                        <?php Util_Ui::sealing_disabled( 'dbcache.' ) ?>
                        value="<?php echo esc_attr( $this->_config->get_integer( 'dbcache.lifetime' ) ); ?>" size="8" /> <?php _e( 'seconds', 'w3-total-cache' ); ?>
                    <br /><span class="description"><?php _e( 'Determines the natural expiration time of unchanged cache items. The higher the value, the larger the cache.', 'w3-total-cache' ); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="dbcache_file_gc"><?php Util_Ui::e_config_label( 'dbcache.file.gc' ) ?></label></th>
                <td>
                    <input id="dbcache_file_gc" type="text" name="dbcache__file__gc"
					<?php Util_Ui::sealing_disabled( 'dbcache.' ) ?> value="<?php echo esc_attr( $this->_config->get_integer( 'dbcache.file.gc' ) ); ?>" size="8" /> <?php _e( 'seconds', 'w3-total-cache' ); ?>
                    <br /><span class="description"><?php _e( 'If caching to disk, specify how frequently expired cache data is removed. For busy sites, a lower value is best.', 'w3-total-cache' ); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="dbcache_reject_uri"><?php Util_Ui::e_config_label( 'dbcache.reject.uri' ) ?></label></th>
                <td>
                    <textarea id="dbcache_reject_uri" name="dbcache__reject__uri"
                        <?php Util_Ui::sealing_disabled( 'dbcache.' ) ?> cols="40" rows="5"><?php echo esc_textarea( implode( "\r\n", $this->_config->get_array( 'dbcache.reject.uri' ) ) ); ?></textarea><br />
						<span class="description">
							<?php echo sprintf( __( 'Always ignore the specified pages / directories. Supports regular expressions (See <a href="%s">FAQ</a>).', 'w3-total-cache' ), network_admin_url( 'admin.php?page=w3tc_faq#q82' ) ); ?>
						</span>
                </td>
            </tr>
            <tr>
                <th><label for="dbcache_reject_sql"><?php Util_Ui::e_config_label( 'dbcache.reject.sql' ) ?></label></th>
                <td>
                    <textarea id="dbcache_reject_sql" name="dbcache__reject__sql"
                        <?php Util_Ui::sealing_disabled( 'dbcache.' ) ?> cols="40" rows="5"><?php echo esc_textarea( implode( "\r\n", $this->_config->get_array( 'dbcache.reject.sql' ) ) ); ?></textarea><br />
                    <span class="description"><?php _e( 'Do not cache queries that contain these terms. Any entered prefix (set in wp-config.php) will be replaced with current database prefix (default: wp_). Query stems can be identified using debug mode.', 'w3-total-cache' ); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="dbcache_reject_words"><?php Util_Ui::e_config_label( 'dbcache.reject.words' ) ?></label></th>
                <td>
                    <textarea id="dbcache_reject_words" name="dbcache__reject__words"
                        <?php Util_Ui::sealing_disabled( 'dbcache.' ) ?> cols="40" rows="5"><?php echo esc_textarea( implode( "\r\n", $this->_config->get_array( 'dbcache.reject.words' ) ) ); ?></textarea><br />
                    <span class="description"><?php _e( 'Do not cache queries that contain these words or regular expressions.', 'w3-total-cache' ); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="dbcache_reject_constants"><?php _e( 'Reject constants:' ) ?></label></th>
                <td>
                    <textarea id="dbcache_reject_constants" name="dbcache__reject__constants"
                        <?php Util_Ui::sealing_disabled( 'dbcache.' ) ?> cols="40" rows="5"><?php echo esc_textarea( implode( "\r\n", $this->_config->get_array( 'dbcache.reject.constants' ) ) ); ?></textarea><br />
                    <span class="description"><?php _e( 'Disable caching once specified constants defined.', 'w3-total-cache' ); ?></span>
                </td>
            </tr>
        </table>

        <?php Util_Ui::button_config_save( 'dbcache_advanced' ); ?>
        <?php Util_Ui::postbox_footer(); ?>
    </div>
</form>

<?php include W3TC_INC_DIR . '/options/common/footer.php'; ?>
