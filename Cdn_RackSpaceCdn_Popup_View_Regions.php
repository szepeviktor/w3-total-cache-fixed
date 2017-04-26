<?php
namespace W3TC;

if ( !defined( 'W3TC' ) )
	die();
?>
<form action="admin.php?page=w3tc_cdn" method="post" style="padding: 20px"
    class="w3tc_cdn_rackspace_form">
    <?php
Util_Ui::hidden( '', 'w3tc_action', 'cdn_rackspace_regions_done' );
Util_Ui::hidden( '', 'user_name', $details['user_name'] );
Util_Ui::hidden( '', 'api_key', $details['api_key'] );
Util_Ui::hidden( '', 'access_token', $details['access_token'] );
Util_Ui::hidden( '', 'region_descriptors', $details['region_descriptors_serialized'] );
echo Util_Ui::nonce_field( 'w3tc' );

?>
    <?php
if ( isset( $details['error_message'] ) )
	echo '<div class="error">' . $details['error_message'] . '</div>';
?>
    <div class="metabox-holder">
        <?php Util_Ui::postbox_header( __( 'Select region', 'w3-total-cache' ) ); ?>
        <table class="form-table">
            <tr>
                <th>Region:</td>
                <td>
                    <?php foreach ( $details['region_descriptors'] as $region => $region_details ): ?>
                        <label>
                            <input name="region" type="radio" class="w3tc-ignore-change"
                                value="<?php echo $region ?>" />
                            <?php echo $region_details['name'] ?>
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
