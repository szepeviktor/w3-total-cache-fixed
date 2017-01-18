<?php
namespace W3TC;

if ( !defined( 'W3TC' ) )
	die();
?>
<form action="admin.php?page=w3tc_cdn" method="post" style="padding: 20px"
    class="w3tc_cdn_highwinds_form">
    <?php
Util_Ui::hidden( '', 'account_hash', $details['account_hash'] );
Util_Ui::hidden( '', 'api_token', $details['api_token'] );
echo Util_Ui::nonce_field( 'w3tc' );

?>
    <?php
if ( isset( $details['error_message'] ) )
	echo '<div class="error">' . $details['error_message'] . '</div>';
?>
    <div class="metabox-holder">
        <?php Util_Ui::postbox_header( __( 'Select host to use', 'w3-total-cache' ) ); ?>
        <table class="form-table">
            <tr>
                <td>Host:</td>
                <td>
                    <?php foreach ( $details['hosts'] as $host ): ?>
                        <label>
                            <input name="host" type="radio" class="w3tc-ignore-change"
                                value="<?php echo $host['hashCode'] ?>" />
                            <?php echo $host['name'] ?>
                            (<?php echo $host['hashCode'] ?>)
                        </label><br />
                    <?php endforeach ?>

                    <label>
                        <input name="host" type="radio" class="w3tc-ignore-change" value=""
                            />
                        Add new host:
                    </label>
                    <input name="host_new" type="text" class="w3tc-ignore-change" />
            </tr>
        </table>

        <p class="submit">
            <input type="button"
                class="w3tc_cdn_highwinds_configure_host w3tc-button-save button-primary"
                value="<?php _e( 'Apply', 'w3-total-cache' ); ?>" />
        </p>
        <?php Util_Ui::postbox_footer(); ?>
    </div>
</form>
