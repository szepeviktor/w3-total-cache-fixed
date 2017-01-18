<?php
namespace W3TC;

if ( !defined( 'W3TC' ) )
	die();
?>
<form class="w3tc_extension_cloudflare_form" method="post" style="padding: 20px">
	<?php Util_Ui::hidden( '', 'w3tc_action', 'extension_cloudflare_intro_done' ); ?>
	<?php
if ( isset( $details['error_message'] ) )
	echo '<div class="error">' . $details['error_message'] . '</div>';
?>
	<div class="metabox-holder">
		<?php Util_Ui::postbox_header(
	__( 'Your CloudFlare API key', 'w3-total-cache' ) ); ?>
		<table class="form-table">
			<tr>
				<th><?php _e( 'Email:', 'w3-total-cache' ) ?></td>
				<td>
					<input name="email" type="text" class="w3tc-ignore-change"
						style="width: 300px"
						value="<?php echo esc_attr( $details['email'] ) ?>" />
				</td>
			</tr>
			<tr>
				<th><?php _e( '<acronym title="Application Programming Interface">API</acronym> key:', 'w3-total-cache' ); ?></td>
				<td>
					<input name="key" type="text" class="w3tc-ignore-change"
						style="width: 550px"
						value="<?php echo esc_attr( $details['key'] ) ?>" />
				</td>
			</tr>
		</table>

		<p class="submit">
			<input type="button"
				class="w3tc_popup_submit w3tc-button-save button-primary"
				value="<?php _e( 'Next', 'w3-total-cache' ); ?>" />
		</p>
		<?php Util_Ui::postbox_footer(); ?>
	</div>
</form>
