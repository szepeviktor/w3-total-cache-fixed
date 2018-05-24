<?php
namespace W3TC;

if ( !defined( 'W3TC' ) )
	die();

$api_key = $config->get_string( 'cdn.limelight.api_key' );
?>
<tr>
	<th style="width: 300px;"><label><?php _e( 'Authorize:', 'w3-total-cache' ); ?></label></th>
	<td>
		<?php if ( empty( $api_key ) ): ?>
			<input class="w3tc_cdn_limelight_authorize button" type="button"
				value="<?php _e( 'Authorize', 'w3-total-cache' ); ?>" />
		<?php else: ?>
			<input class="w3tc_cdn_limelight_authorize button" type="button"
				value="<?php _e( 'Reauthorize', 'w3-total-cache' ); ?>" />
		<?php endif ?>
	</td>
</tr>

<?php if ( !empty( $api_key ) ): ?>
<tr>
	<th><label for="cdn_limelight_ssl"><?php _e( '<acronym title="Secure Sockets Layer">SSL</acronym> support:</label>', 'w3-total-cache' ); ?></th>
	<td>
		<select id="cdn_limelight_ssl" name="cdn__limelight__ssl">
			<option value="auto"<?php selected( $config->get_string( 'cdn.limelight.ssl' ), 'auto' ); ?>><?php _e( 'Auto (determine connection type automatically)', 'w3-total-cache' ); ?></option>
			<option value="enabled"<?php selected( $config->get_string( 'cdn.limelight.ssl' ), 'enabled' ); ?>><?php _e( 'Enabled (always use SSL)', 'w3-total-cache' ); ?></option>
			<option value="disabled"<?php selected( $config->get_string( 'cdn.limelight.ssl' ), 'disabled' ); ?>><?php _e( 'Disabled (always use HTTP)', 'w3-total-cache' ); ?></option>
		</select>
        <br /><span class="description"><?php _e( 'Some <acronym title="Content Delivery Network">CDN</acronym> providers may or may not support <acronym title="Secure Sockets Layer">SSL</acronym>, contact your vendor for more information.', 'w3-total-cache' ); ?></span>
	</td>
</tr>
<tr>
    <th><?php _e( 'Replace site\'s hostname with:', 'w3-total-cache' ); ?></th>
    <td>
		<?php $cnames = $config->get_array( 'cdn.limelight.host.domains' ); include W3TC_INC_DIR . '/options/cdn/common/cnames-readonly.php'; ?>
        <br />
        <span class="description"><?php _e( 'Hostname provided by your <acronym title="Content Delivery Network">CDN</acronym> provider, this value will replace your site\'s hostname in the <acronym title="Hypertext Markup Language">HTML</acronym>.', 'w3-total-cache' ); ?></span>
    </td>
</tr>
<tr>
	<th colspan="2">
        <input id="cdn_test"
        	class="button {type: 'limelight', nonce: '<?php echo wp_create_nonce( 'w3tc' ); ?>'}"
        	type="button"
        	value="<?php _e( 'Test', 'w3-total-cache' ); ?>" />
        <span id="cdn_test_status" class="w3tc-status w3tc-process"></span>
    </th>
</tr>
<?php endif ?>
