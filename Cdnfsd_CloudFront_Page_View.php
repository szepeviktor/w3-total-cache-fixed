<?php
namespace W3TC;

if ( !defined( 'W3TC' ) )
	die();

$key = $config->get_string( 'cdnfsd.cloudfront.access_key' );
$authorized = !empty( $key );

?>
<form id="cdn_form" action="admin.php?page=w3tc_cdn" method="post">
    <div class="metabox-holder">
        <?php Util_Ui::postbox_header( __( 'Configuration: Full-Site Delivery', 'w3-total-cache' ),
	'', 'configuration' ); ?>
        <table class="form-table">
			<tr>
				<th style="width: 300px;">
					<label>
						<?php
_e( 'Specify account credentials:',
	'w3-total-cache' );
?>
					</label>
				</th>
				<td>
					<?php if ( $authorized ): ?>
						<input class="w3tc_cdn_cloudfront_fsd_authorize button-primary"
							type="button"
							value="<?php _e( 'Reauthorize', 'w3-total-cache' ); ?>"
							/>
					<?php else: ?>
						<input class="w3tc_cdn_cloudfront_fsd_authorize button-primary"
							type="button"
							value="<?php _e( 'Authorize', 'w3-total-cache' ); ?>"
							/>
					<?php endif ?>
				</td>
			</tr>

			<?php if ( $authorized ): ?>
			<tr>
				<th>
					<label><?php _e( '<acronym title="Content Delivery Network">CDN</acronym> CNAME:', 'w3-total-cache' ); ?></label>
				</th>
				<td class="w3tc_config_value_text">
					<?php
echo $config->get_string( 'cdnfsd.cloudfront.distribution_domain' )
?><br />
					<span class="description">
						This website domain has to be CNAME pointing to this
						<acronym title="Content Delivery Network">CDN</acronym> domain
					</span>
				</td>
			</tr>
			<?php endif ?>
        </table>

        <?php Util_Ui::button_config_save( 'cdn_configuration' ); ?>
        <?php Util_Ui::postbox_footer(); ?>
    </div>
</form>
