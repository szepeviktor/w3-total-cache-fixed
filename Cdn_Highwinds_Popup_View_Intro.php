<?php
namespace W3TC;

if ( !defined( 'W3TC' ) )
	die();
?>
<form class="w3tc_cdn_highwinds_form" method="post" style="padding: 20px">
	<?php
if ( isset( $details['error_message'] ) )
	echo '<div class="error">' . $details['error_message'] . '</div>';
?>
	<div class="metabox-holder">
		<?php Util_Ui::postbox_header(
	__( 'Your Highwinds API Token', 'w3-total-cache' ) ); ?>
		<table class="form-table">
			<tr>
				<td>API Token:</td>
				<td>
					<input name="api_token" type="text" class="w3tc-ignore-change"
						value="" style="width: 550px" />
				</td>
			</tr>
		</table>

		<p class="submit">
			<input type="button"
				class="w3tc_cdn_highwinds_select_host w3tc-button-save button-primary"
				value="<?php _e( 'Next', 'w3-total-cache' ); ?>" />
		</p>
		<?php Util_Ui::postbox_footer(); ?>
	</div>
</form>
