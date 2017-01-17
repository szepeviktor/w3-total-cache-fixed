<?php
namespace W3TC;

if ( !defined( 'W3TC' ) )
	die();

?>
<p id="w3tc-options-menu">
	Jump to:
	<a href="admin.php?page=w3tc_general"><?php _e( 'Main Menu', 'w3-total-cache' ); ?></a> |
	<a href="admin.php?page=w3tc_extensions"><?php _e( 'Extensions', 'w3-total-cache' ); ?></a> |
	<a href="#overview"><?php _e( 'Overview', 'w3-total-cache' ); ?></a> |
	<a href="#advanced"><?php _e( 'Advanced', 'w3-total-cache' ); ?></a>
</p>
<p>
	Fragment caching via <strong><?php
echo Cache::engine_name( $config->get_string( array( 'fragmentcache', 'engine' ) ) )
?></strong> is currently <?php
if ( $config->is_extension_active_frontend( 'fragmentcache' ) )
	echo '<span class="w3tc-enabled">enabled</span>';
else {
	echo '<span class="w3tc-disabled">disabled</span>';
	$ext = Extensions_Util::get_extension( $config, 'fragmentcache' );

	if ( !empty( $ext['requirements'] ) ) {
		echo ' (<span class="description">' .
			$ext['requirements'] .
			'</span>)';
	}
}
?>.
<p>

<form action="admin.php?page=w3tc_fragmentcache" method="post">
	<p>
		<?php echo Util_Ui::nonce_field( 'w3tc' ); ?>
		<input type="submit" name="w3tc_flush_fragmentcache" value="<?php _e( 'Empty the entire cache', 'w3-total-cache' ) ?>" class="button" />
		<?php _e( 'if needed.', 'w3-total-cache' ) ?>
	</p>
</form>

<form action="admin.php?page=w3tc_fragmentcache" method="post">
	<div class="metabox-holder">
		<?php Util_Ui::postbox_header( __( 'Overview', 'w3-total-cache' ), '', 'overview' ); ?>
		<table class="form-table">
		<tr>
			<th><?php _e( 'Registered fragment groups:', 'w3-total-cache' ); ?></th>
			<td>
				<?php if ( $registered_groups ): ?>
				<ul>
					<?php
	foreach ( $registered_groups as $group => $descriptor )
		echo '<li>', $group,
		' (', $descriptor['expiration'], ' secs): ',
		implode( ',', $descriptor['actions'] ), '</li>';
?>
				</ul>
				<span class="description"><?php _e( 'The groups above will be flushed upon setting changes.', 'w3-total-cache' ); ?></span>
				<?php else: ?>
				<span class="description"><?php _e( 'No groups have been registered.', 'w3-total-cache' ); ?></span>
				<?php endif ?>
			</td>
		</tr>
		</table>
		<?php Util_Ui::postbox_footer(); ?>

		<?php Util_Ui::postbox_header( __( 'Advanced', 'w3-total-cache' ), '', 'advanced' ); ?>
		<table class="form-table">
			<?php
if ( $config->get_string( array( 'fragmentcache', 'engine' ) ) == 'memcached' ) {
	$module = 'fragmentcache';
	include W3TC_INC_DIR . '/options/parts/memcached_extension.php';
} elseif ( $config->get_string( array( 'fragmentcache', 'engine' ) ) == 'redis' ) {
	$module = 'fragmentcache';
	include W3TC_INC_DIR . '/options/parts/redis_extension.php';
}
?>

			<tr>
				<th style="width: 250px;"><label for="fragmentcache_lifetime"><?php _e( 'Default lifetime of cached fragments:', 'w3-total-cache' ) ?></label></th>
				<td>
					<input id="fragmentcache_lifetime" type="text" <?php Util_Ui::sealing_disabled( 'fragmentcache.' ) ?> name="fragmentcache___lifetime" value="<?php echo esc_attr( $config->get_integer( array( 'fragmentcache', 'lifetime' ) ) ) ?>" size="8" /><?php _e( 'seconds', 'w3-total-cache' ) ?>
					<br /><span class="description"><?php _e( 'Determines the natural expiration time of unchanged cache items. The higher the value, the larger the cache.', 'w3-total-cache' ); ?></span>
				</td>
			</tr>
			<tr>
				<th><label for="fragmentcache_file_gc"><?php _e( 'Garbage collection interval:', 'w3-total-cache' ) ?></label></th>
				<td>
					<input id="fragmentcache_file_gc" type="text" <?php Util_Ui::sealing_disabled( 'fragmentcache.' ) ?> name="fragmentcache___file__gc" value="<?php echo esc_attr( $config->get_integer( array( 'fragmentcache', 'file.gc' ) ) ) ?>" size="8" /> <?php _e( 'seconds', 'w3-total-cache' ) ?>
					<br /><span class="description"><?php _e( 'If caching to disk, specify how frequently expired cache data is removed. For busy sites, a lower value is best.', 'w3-total-cache' ); ?></span>
				</td>
			</tr>
			<tr>
				<th><label for="fragmentcache_groups"><?php _e( 'Manual fragment groups:', 'w3-total-cache' ) ?></label></th>
				<td>
					<textarea id="fragmentcache_groups" name="fragmentcache___groups"
						<?php Util_Ui::sealing_disabled( 'fragmentcache.' ) ?>
							  cols="40" rows="5"><?php echo esc_textarea( implode( "\r\n", $config->get_array( array( 'fragmentcache', 'groups' ) ) ) ); ?></textarea><br />
					<span class="description"><?php _e( 'Specify fragment groups that should be managed by W3 Total Cache. Enter one action per line comma delimited, e.g. (group, action1, action2). Include the prefix used for a transient by a theme or plugin.', 'w3-total-cache' ); ?></span>
				</td>
			</tr>
		</table>

		<?php Util_Ui::button_config_save( 'extension_fragmentcache' ); ?>
		<?php Util_Ui::postbox_footer(); ?>
	</div>
</form>
