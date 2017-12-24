<?php
namespace W3TC;

if ( !defined( 'W3TC' ) )
	die();

?>

<p>
	<?php _e( 'Cookie group support is always <span class="w3tc-enabled">enabled</span>.', 'w3-total-cache' ); ?>
</p>

<form action="admin.php?page=w3tc_pgcache_cookiegroups" method="post">
	<div class="metabox-holder">
		<?php Util_Ui::postbox_header( __( 'Manage Cookie Groups', 'w3-total-cache' ), '', 'manage' ); ?>
		<p>
			<input id="w3tc_cookiegroup_add" type="button" class="button"
				<?php disabled( $groups['disabled'] ) ?>
				value="<?php _e( 'Create a group', 'w3-total-cache' ); ?>" />
			<?php _e( 'of Cookies by specifying names in the Cookies field. Assign a set of Cookies to ensure that a unique cache is created for each Cookie group. Drag and drop groups into order (if needed) to determine their priority (top -&gt; down).', 'w3-total-cache' ); ?>
		</p>

		<ul id="cookiegroups" class="w3tc_cachegroups">
			<?php $index = 0; foreach ( $groups['value'] as $group => $group_config ): $index++; ?>
			<li id="cookiegroup_<?php echo esc_attr( $group ); ?>">
				<table class="form-table">
					<tr>
						<th>
							<?php _e( 'Group name:', 'w3-total-cache' ); ?>
						</th>
						<td>
							<span class="cookiegroup_number"><?php echo $index; ?>.</span>
							<span class="cookiegroup_name"><?php echo htmlspecialchars( $group ); ?></span>
							<input type="button" class="button w3tc_cookiegroup_delete"
								value="Delete group"
								<?php disabled( $groups['disabled'] ) ?> />
						</td>
					</tr>
					<tr>
						<th>
							<label for="cookiegroup_<?php echo esc_attr( $group ); ?>_enabled">
								<?php _e( 'Enabled:', 'w3-total-cache' ); ?>
							</label>
						</th>
						<td>
							<input id="cookiegroup_<?php echo esc_attr( $group ); ?>_enabled"
								type="checkbox"
								name="cookiegroups[<?php echo esc_attr( $group ); ?>][enabled]"
								<?php disabled( $groups['disabled'] ) ?> value="1"
								<?php checked( $group_config['enabled'], true ); ?> />
						</td>
					</tr>
					<tr>
						<th>
							<label for="cookiegroup_<?php echo esc_attr( $group ); ?>_cache">
								<?php _e( 'Cache:', 'w3-total-cache' ); ?>
							</label>
						</th>
						<td>
							<input id="cookiegroup_<?php echo esc_attr( $group ); ?>_cache"
								type="checkbox"
								name="cookiegroups[<?php echo esc_attr( $group ); ?>][cache]"
								<?php disabled( $groups['disabled'] ) ?> value="1"
								<?php checked( $group_config['cache'], true ); ?> />
						</td>
					</tr>
					<tr>
						<th>
							<label for="cookiegroup_<?php echo esc_attr( $group ); ?>_cookies">
								<?php _e( 'Cookies:', 'w3-total-cache' ); ?>
							</label>
						</th>
						<td>
							<textarea id="cookiegroup_<?php echo esc_attr( $group ); ?>_cookies"
								name="cookiegroups[<?php echo esc_attr( $group ); ?>][cookies]"
								rows="10" cols="50" <?php disabled( $groups['disabled'] ) ?>><?php echo esc_textarea( implode( "\r\n", (array) $group_config['cookies'] ) ); ?></textarea>
							<br />
							<span class="description">
								<?php _e( 'Specify the cookies for this group. Values like \'cookie\', \'cookie=value\', and cookie[a-z]+=value[a-z]+ are supported. Remember to escape special characters like spaces, dots or dashes with a backslash. Regular expressions are also supported.', 'w3-total-cache' ); ?>
							</span>
						</td>
					</tr>
				</table>
			</li>
			<?php endforeach; ?>
		</ul>
		<div id="cookiegroups_empty" style="display: none;"><?php _e( 'No groups added. All Cookies recieve the same page and minify cache results.', 'w3-total-cache' ) ?></div>

		<?php
if ( !$groups['disabled'] )
	Util_Ui::button_config_save( 'pgcache_cookiegroups' );
Util_Ui::postbox_footer();

Util_Ui::postbox_header( __( 'Note(s):', 'w3-total-cache' ), '',
	'notes' );
?>
		<table class="form-table">
			<tr>
				<th colspan="2">
					<ul>
						<li>
							<?php _e( 'Content is cached for each group separately.', 'w3-total-cache' ) ?>
						</li>
						<li>
							<?php _e( 'Per the above, make sure that visitors are notified about the cookie as per any regulations in your market.', 'w3-total-cache' ) ?>
						</li>
					</ul>
				</th>
			</tr>
		</table>
		<?php Util_Ui::postbox_footer(); ?>
	</div>
</form>
