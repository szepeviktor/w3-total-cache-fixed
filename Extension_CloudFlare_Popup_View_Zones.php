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
                <th>Zone:</td>
                <td>
                    <?php foreach ( $details['zones'] as $z ): ?>
                        <label>
                            <input name="zone_id" type="radio" class="w3tc-ignore-change"
                                value="<?php echo $z['id'] ?>" />
                            <?php echo htmlspecialchars( $z['name'] ) ?>
                        </label><br />
                    <?php endforeach ?>
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
