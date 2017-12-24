<?php
namespace W3TC;

if ( !defined( 'W3TC' ) )
	die();
?>
<form action="admin.php?page=w3tc_cdn" method="post" style="padding: 20px"
	class="w3tc_extension_cloudflare_form">
	<?php
Util_Ui::hidden( '', 'w3tc_action', 'extension_cloudflare_zones_done' );
Util_Ui::hidden( '', 'email', $details['email'] );
Util_Ui::hidden( '', 'key', $details['key'] );
Util_Ui::hidden( '', 'page', '' );
echo Util_Ui::nonce_field( 'w3tc' );

?>
	<?php
if ( isset( $details['error_message'] ) )
	echo '<div class="error">' . $details['error_message'] . '</div>';

?>
	<div class="metabox-holder">
		<?php Util_Ui::postbox_header( __( 'Select zone', 'w3-total-cache' ) ); ?>
		<table class="form-table">
			<tr>
				<td>Zone:</td>
				<td>
					<?php foreach ( $details['zones'] as $z ): ?>
						<label>
							<input name="zone_id" type="radio" class="w3tc-ignore-change"
								value="<?php echo $z['id'] ?>" />
							<?php echo htmlspecialchars( $z['name'] ) ?>
						</label><br />
					<?php endforeach ?>
			</tr>
			<tr>
				<td></td>
				<td>
					<?php
					if ( $details['total_pages'] > 1 ):
						for ( $page = 1; $page <= $details['total_pages']; $page++ ):
							if ( $page == $details['page']):
								echo $page;
							else: ?>
								<a href="#" class="w3tc_cloudflare_zone_page" data-page="<?php echo $page ?>"><?php echo $page ?></a>
							<?php endif ?>
							&nbsp;
						<?php endfor ?>
					<?php endif ?>
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
