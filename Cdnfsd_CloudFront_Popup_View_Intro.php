<?php
namespace W3TC;

if ( !defined( 'W3TC' ) )
	die();
?>
<form class="w3tc_popup_form">
    <?php
if ( isset( $details['error_message'] ) )
	echo '<div class="error">' . $details['error_message'] . '</div>';
?>
    <div class="metabox-holder">
        <?php Util_Ui::postbox_header(
	__( 'Your CloudFront Account credentials', 'w3-total-cache' ) ); ?>
        <table class="form-table">
            <tr>
                <td>Access Key:</td>
                <td>
                    <input name="access_key" type="text" class="w3tc-ignore-change"
                        style="width: 550px"
                        value="<?php echo $config->get_string( 'cdnfsd.cloudfront.access_key' ) ?>" />
                </td>
            </tr>
            <tr>
                <td>Access Secret:</td>
                <td>
                    <input name="secret_key" type="text" class="w3tc-ignore-change"
                        style="width: 550px"
                        value="<?php echo $config->get_string( 'cdnfsd.cloudfront.secret_key' ) ?>" />
                </td>
            </tr>
        </table>

        <p class="submit">
            <input type="button"
                class="w3tc_cdn_cloudfront_fsd_list_distributions w3tc-button-save button-primary"
                value="<?php _e( 'Next', 'w3-total-cache' ); ?>" />
        </p>
        <?php Util_Ui::postbox_footer(); ?>
    </div>
</form>
