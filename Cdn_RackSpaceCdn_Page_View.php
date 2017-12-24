<?php
namespace W3TC;

if ( !defined( 'W3TC' ) )
	die();

?>
<tr>
	<th style="width: 300px;"><label><?php _e( 'Authorize:', 'w3-total-cache' ); ?></label></th>
	<td>
		<?php if ( $authorized ): ?>
			<input class="w3tc_cdn_rackspace_authorize button" type="button"
				value="<?php _e( 'Reauthorize', 'w3-total-cache' ); ?>" />
		<?php else: ?>
			<input class="w3tc_cdn_rackspace_authorize button" type="button"
				value="<?php _e( 'Authorize', 'w3-total-cache' ); ?>" />
		<?php endif ?>
	</td>
</tr>

<?php if ( $authorized ): ?>
<tr>
	<th><?php _e( 'Username:', 'w3-total-cache' ); ?></th>
	<td class="w3tc_config_value_text">
		<?php echo $config->get_string( 'cdn.rackspace_cdn.user_name' ) ?>
	</td>
</tr>
<tr>
	<th><?php _e( 'Region:', 'w3-total-cache' ); ?></th>
	<td class="w3tc_config_value_text">
		<?php echo $config->get_string( 'cdn.rackspace_cdn.region' ) ?>
	</td>
</tr>
<tr>
	<th><?php _e( 'Service:', 'w3-total-cache' ); ?></th>
	<td class="w3tc_config_value_text">
		<?php echo $config->get_string( 'cdn.rackspace_cdn.service.name' ) ?>
	</td>
</tr>
<tr>
	<th><label><?php _e( '<acronym title="Content Delivery Network">CDN</acronym> host (CNAME target):', 'w3-total-cache' ); ?></label></th>
	<td class="w3tc_config_value_text">
		<?php echo $access_url_full ?>
	</td>
</tr>
<?php if ( $config->get_string( 'cdn.rackspace_cdn.service.protocol' ) == 'http' ): ?>
<tr>
    <th><?php _e( 'Replace site\'s hostname with:', 'w3-total-cache' ); ?></th>
    <td>
		<?php $cnames = $config->get_array( 'cdn.rackspace_cdn.domains' ); include W3TC_INC_DIR . '/options/cdn/common/cnames-readonly.php'; ?>
		<input class="w3tc_cdn_rackspace_configure_domains button" type="button"
				value="<?php _e( 'Configure CNAMEs', 'w3-total-cache' ); ?>" />
        <br />
        <span class="description">
        	<?php _e( 'Enter hostname mapped to <acronym title="Content Delivery Network">CDN</acronym> host, this value will replace your site\'s hostname in the <acronym title="Hypertext Markup Language">HTML</acronym>.', 'w3-total-cache' ); ?>
        </span>
    </td>
</tr>
<?php else: ?>
<tr>
    <th><?php _e( 'Replace site\'s hostname with:', 'w3-total-cache' ); ?></th>
    <td>
		<?php $cnames = $config->get_array( 'cdn.rackspace_cdn.domains' ); include W3TC_INC_DIR . '/options/cdn/common/cnames-readonly.php'; ?>
		<input name="w3tc_cdn_rackspace_cdn_domains_reload"
                class="w3tc-button-save button" type="submit"
				value="<?php _e( 'Reload CNAMEs from RackSpace', 'w3-total-cache' ); ?>" />
        <br />
        <span class="description">
        	<?php _e( 'Hostname(s) mapped to <acronym title="Content Delivery Network">CDN</acronym> host, this value will replace your site\'s hostname in the <acronym title="Hypertext Markup Language">HTML</acronym>. You can manage them from RackSpace management console and load here afterwards.', 'w3-total-cache' ); ?>
        </span>
    </td>
</tr>
<?php endif ?>
<tr>
	<th colspan="2">
        <input id="cdn_test"
        	class="button {type: 'rackspace_cdn', nonce: '<?php echo wp_create_nonce( 'w3tc' ); ?>'}"
        	type="button"
        	value="<?php _e( 'Test', 'w3-total-cache' ); ?>" />
        <span id="cdn_test_status" class="w3tc-status w3tc-process"></span>
    </th>
</tr>
<?php endif ?>
