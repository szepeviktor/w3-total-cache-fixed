<?php
namespace W3TC;

if ( !defined( 'W3TC' ) )
	die();

?>
<tr>
	<th style="width: 300px;"><label><?php _e( 'Authorize:', 'w3-total-cache' ); ?></label></th>
	<td>
		<?php if ( !$authorized ): ?>
			<input class="w3tc_cdn_rackspace_authorize button" type="button"
				value="<?php _e( 'Authorize', 'w3-total-cache' ); ?>" />
		<?php else: ?>
			<input class="w3tc_cdn_rackspace_authorize button" type="button"
				value="<?php _e( 'Reauthorize', 'w3-total-cache' ); ?>" />
		<?php endif ?>
	</td>
</tr>

<?php if ( $authorized ): ?>
<tr>
	<th><?php _e( 'Username:', 'w3-total-cache' ); ?></th>
	<td class="w3tc_config_value_text">
		<?php echo $config->get_string( 'cdn.rscf.user' ) ?>
	</td>
</tr>
<tr>
	<th><?php _e( 'Region:', 'w3-total-cache' ); ?></th>
	<td class="w3tc_config_value_text">
		<?php echo $config->get_string( 'cdn.rscf.location' ) ?>
	</td>
</tr>
<tr>
	<th><?php _e( 'Container:', 'w3-total-cache' ); ?></th>
	<td class="w3tc_config_value_text">
		<?php echo $config->get_string( 'cdn.rscf.container' ) ?>
	</td>
</tr>
<tr>
	<th><label><?php _e( 'CDN host (CNAME target):', 'w3-total-cache' ); ?></label></th>
	<td class="w3tc_config_value_text">
		http: <?php echo $cdn_host_http ?><br />
		https: <?php echo $cdn_host_https ?>
	</td>
</tr>
<tr>
	<th><label for="cdn_rackspace_ssl"><?php _e( '<acronym title="Secure Sockets Layer">SSL</acronym> support:</label>', 'w3-total-cache' ); ?></th>
	<td>
		<select id="cdn_rackspace_ssl" name="cdn__rscf__ssl">
			<option value="auto"<?php selected( $config->get_string( 'cdn.rscf.ssl' ), 'auto' ); ?>><?php _e( 'Auto (determine connection type automatically)', 'w3-total-cache' ); ?></option>
			<option value="enabled"<?php selected( $config->get_string( 'cdn.rscf.ssl' ), 'enabled' ); ?>><?php _e( 'Enabled (always use SSL)', 'w3-total-cache' ); ?></option>
			<option value="disabled"<?php selected( $config->get_string( 'cdn.rscf.ssl' ), 'disabled' ); ?>><?php _e( 'Disabled (always use HTTP)', 'w3-total-cache' ); ?></option>
		</select>
        <br /><span class="description"><?php _e( 'Some <acronym>CDN</acronym> providers may or may not support <acronym title="Secure Sockets Layer">SSL</acronym>, contact your vendor for more information.', 'w3-total-cache' ); ?></span>
	</td>
</tr>
<tr>
    <th><?php _e( 'Replace site\'s hostname with:', 'w3-total-cache' ); ?></th>
    <td>
		<?php $cnames = $config->get_array( 'cdn.rscf.cname' ); include W3TC_INC_DIR . '/options/cdn/common/cnames.php'; ?>
        <br />
        <span class="description">
        	<?php _e( 'Enter hostname mapped to <acronym>CDN</acronym> host, this value will replace your site\'s hostname in the <acronym title="Hypertext Markup Language">HTML</acronym>.', 'w3-total-cache' ); ?>
        </span>
    </td>
</tr>
<tr>
	<th colspan="2">
        <input id="cdn_test"
        	class="button {type: 'highwinds', nonce: '<?php echo wp_create_nonce( 'w3tc' ); ?>'}"
        	type="button"
        	value="<?php _e( 'Test', 'w3-total-cache' ); ?>" />
        <span id="cdn_test_status" class="w3tc-status w3tc-process"></span>
    </th>
</tr>
<?php endif ?>
