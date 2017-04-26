<?php
namespace W3TC;

if ( !defined( 'W3TC' ) )
	die();
?>
<form action="admin.php?page=w3tc_cdn" method="post" style="padding: 20px"
    class="w3tc_cdn_rackspace_form">
    <?php
Util_Ui::hidden( '', 'user_name', $details['user_name'] );
Util_Ui::hidden( '', 'api_key', $details['api_key'] );
Util_Ui::hidden( '', 'access_token', $details['access_token'] );
Util_Ui::hidden( '', 'access_region_descriptor', $details['access_region_descriptor_serialized'] );
Util_Ui::hidden( '', 'region', $details['region'] );
echo Util_Ui::nonce_field( 'w3tc' );

?>
    <?php
if ( isset( $details['error_message'] ) )
	echo '<div class="error">' . $details['error_message'] . '</div>';
?>
    <div class="metabox-holder">
        <?php Util_Ui::postbox_header( __( 'Create new service', 'w3-total-cache' ) ); ?>
        <table class="form-table" style="width: 100%">
            <tr>
                <th style="width: 150px">Name:</td>
                <td>
                    <input name="name" type="text" class="w3tc-ignore-change"
                        style="width: 100px"
                        value="<?php echo esc_attr( $details['name'] ) ?>" />
                </td>
            </tr>
            <tr>
                <th style="white-space: nowrap">Traffic Type:</td>
                <td>
                    <label>
                        <input name="protocol" type="radio"
                            class="w3tc-ignore-change w3tc_cdn_rackspace_protocol"
                            value="http"
                            <?php checked( $details['protocol'], 'http' ) ?> />
                        http://
                    </label>
                    <br />
                    <label>
                        <input name="protocol" type="radio"
                            class="w3tc-ignore-change w3tc_cdn_rackspace_protocol"
                            value="https"
                            <?php checked( $details['protocol'], 'https' ) ?> />
                        https://
                    </label>
                </td>
            </tr>
            <tr>
                <th>Origin:</td>
                <td>
                    <?php echo $details['origin'] ?>
                </td>
            </tr>
            <tr class="w3tc_cdn_rackspace_cname_http"
                style="<?php echo $details['cname_http_style'] ?>">
                <th style="white-space: nowrap">Primary CNAME:</td>
                <td>
                    <input name="cname_http" type="text" class="w3tc-ignore-change"
                        style="width: 200px"
                        value="<?php echo esc_attr( $details['cname_http'] ) ?>" />
                    <br />
                    <span class="description">
                        <?php _e( 'The domain name through which visitors retrieve content. You will be provided with a target domain to use as an alias for this CNAME', 'w3-total-cache' ); ?>
                    </span>
                </td>
            </tr>
            <tr class="w3tc_cdn_rackspace_cname_https"
                style="<?php echo $details['cname_https_style'] ?>">
                <th style="white-space: nowrap">Primary CNAME:</td>
                <td>
                    <input name="cname_https_prefix" type="text" class="w3tc-ignore-change"
                        style="width: 100px"
                        value="<?php echo esc_attr( $details['cname_https_prefix'] ) ?>" />
                    <input name="" type="text" readonly="readonly"
                        value=".xxxx.secure.raxcdn.com" />
                    <br />
                    <span class="description">
                        <?php _e( 'The name should be a single word, and cannot contain any dots (.).', 'w3-total-cache' ) ?>
                    </span>
                </td>
            </tr>
        </table>

        <p class="submit">
            <input type="button"
                class="w3tc_cdn_rackspace_service_create_done w3tc-button-save button-primary"
                value="<?php _e( 'Next', 'w3-total-cache' ); ?>" />
        </p>
        <?php Util_Ui::postbox_footer(); ?>
    </div>
</form>
