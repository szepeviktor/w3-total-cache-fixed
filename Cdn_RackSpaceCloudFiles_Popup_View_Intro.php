<?php
namespace W3TC;

if ( !defined( 'W3TC' ) )
	die();
?>
<form class="w3tc_cdn_rackspace_form" method="post" style="padding: 20px">
<?php
Util_Ui::hidden( '', 'w3tc_action', 'cdn_rackspace_intro_done' );

if ( isset( $details['error_message'] ) )
	echo '<div class="error">' . $details['error_message'] . '</div>';
?>
	<div class="metabox-holder">
		<?php Util_Ui::postbox_header(
	__( 'Your RackSpace API key', 'w3-total-cache' ) ); ?>
		<table class="form-table">
			<tr>
				<td><?php _e( 'Username:', 'w3-total-cache' ) ?></td>
				<td>
					<input name="user_name" type="text" class="w3tc-ignore-change"
						style="width: 100px"
						value="<?php echo esc_attr( $details['user_name'] ) ?>" />
				</td>
			</tr>
			<tr>
				<td><?php _e( '<acronym title="Application Programming Interface">API</acronym> key:', 'w3-total-cache' ); ?></td>
				<td>
					<input name="api_key" type="text" class="w3tc-ignore-change"
						style="width: 550px"
						value="<?php echo esc_attr( $details['api_key'] ) ?>" />
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
