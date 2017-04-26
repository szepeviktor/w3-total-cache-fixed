<?php
namespace W3TC;

if ( !defined( 'W3TC' ) )
	die();

Util_Ui::config_item( array(
		'key' => 'cdn.s3_compatible.api_host',
		'label' => __( 'API host:', 'w3-total-cache' ),
		'control' => 'textbox',
		'textbox_size' => 30,
		'description' => __( 'Host of API endpoint, comptabile with Amazon S3 API', 'w3-total-cache' )
	) );
Util_Ui::config_item( array(
		'key' => 'cdn.s3.key',
		'label' => __( 'Access key ID:', 'w3-total-cache' ),
		'control' => 'textbox',
		'textbox_size' => 30,
		'description' => __( 'Theme files, media library attachments, <acronym title="Cascading Style Sheet">CSS</acronym>, <acronym title="JavaScript">JS</acronym> files etc will appear to load instantly for site visitors.', 'w3-total-cache' )
	) );

?>
<tr>
	<th><label for="cdn_s3_secret"><?php _e( 'Secret key:', 'w3-total-cache' ); ?></label></th>
	<td>
		<input id="cdn_s3_secret" class="w3tc-ignore-change"
                   <?php Util_Ui::sealing_disabled( 'cdn.' ) ?> type="password" name="cdn__s3__secret" value="<?php echo esc_attr( $this->_config->get_string( 'cdn.s3.secret' ) ); ?>" size="60" />
	</td>
</tr>
<tr>
	<th><label for="cdn_s3_bucket"><?php _e( 'Bucket:', 'w3-total-cache' ); ?></label></th>
	<td>
		<input id="cdn_s3_bucket" type="text" name="cdn__s3__bucket"
                   <?php Util_Ui::sealing_disabled( 'cdn.' ) ?> value="<?php echo esc_attr( $this->_config->get_string( 'cdn.s3.bucket' ) ); ?>" size="30" />
	</td>
</tr>
<tr>
	<th><label for="cdn_s3_ssl"><?php _e( '<acronym title="Secure Sockets Layer">SSL</acronym> support:', 'w3-total-cache' ); ?></label></th>
	<td>
		<select id="cdn_s3_ssl" name="cdn__s3__ssl" <?php Util_Ui::sealing_disabled( 'cdn.' ) ?>>
			<option value="auto"<?php selected( $this->_config->get_string( 'cdn.s3.ssl' ), 'auto' ); ?>><?php _e( 'Auto (determine connection type automatically)', 'w3-total-cache' ); ?></option>
			<option value="enabled"<?php selected( $this->_config->get_string( 'cdn.s3.ssl' ), 'enabled' ); ?>><?php _e( 'Enabled (always use SSL)', 'w3-total-cache' ); ?></option>
			<option value="disabled"<?php selected( $this->_config->get_string( 'cdn.s3.ssl' ), 'disabled' ); ?>><?php _e( 'Disabled (always use HTTP)', 'w3-total-cache' ); ?></option>
		</select>
        <br /><span class="description"><?php _e( 'Some <acronym>CDN</acronym> providers may or may not support <acronym title="Secure Sockets Layer">SSL</acronym>, contact your vendor for more information.', 'w3-total-cache' ); ?></span>
	</td>
</tr>
<tr>
	<th><?php _e( 'Replace site\'s hostname with:', 'w3-total-cache' ); ?></th>
	<td>
		<?php $cnames = $this->_config->get_array( 'cdn.s3.cname' ); include W3TC_INC_DIR . '/options/cdn/common/cnames.php'; ?>
        <br /><span class="description"><?php _e( 'If you have already added a <a href="http://docs.amazonwebservices.com/AmazonS3/latest/DeveloperGuide/VirtualHosting.html#VirtualHostingCustomURLs" target="_blank">CNAME</a> to your <acronym title="Domain Name System">DNS</acronym> Zone, enter it here.', 'w3-total-cache' ); ?></span>
	</td>
</tr>
<tr>
	<th colspan="2">
        <input id="cdn_test" class="button {type: 's3_compatible', nonce: '<?php echo wp_create_nonce( 'w3tc' ); ?>'}" type="button" value="<?php _e( 'Test S3 upload', 'w3-total-cache' ); ?>" /> <span id="cdn_test_status" class="w3tc-status w3tc-process"></span>
    </th>
</tr>
