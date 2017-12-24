<?php
namespace W3TC;

if ( !defined( 'W3TC' ) )
	die();
?>
<form class="w3tc_popup_form" method="post">
    <?php
Util_Ui::hidden( '', 'api_key', $details['api_key'] );
Util_Ui::hidden( '', 'zone_id', $details['zone_id'] );
Util_Ui::hidden( '', 'name', $details['name'] );
?>

    <div class="metabox-holder">
        <?php Util_Ui::postbox_header( __( 'Configure zone', 'w3-total-cache' ) ); ?>
        <table class="form-table">
            <tr>
                <th>Name:</th>
                <td><?php echo $details['name'] ?></td>
            </tr>
            <tr>
                <th>Origin URL:</th>
                <td><?php $this->render_zone_value_change( $details, 'url' ) ?></td>
            </tr>
            <tr>
                <th>Origin IP:</th>
                <td><?php $this->render_zone_ip_change( $details, 'ip' ) ?><br />
                    <span class="description">IP of your WordPress host</span>
                </td>
            </tr>
            <tr>
                <th>Origin IP Resolution:</th>
                <td><?php $this->render_zone_boolean_change( $details, 'dns_check' ) ?></td>
            </tr>
            <tr>
                <th>Ignore Cache Control:</th>
                <td><?php $this->render_zone_boolean_change( $details, 'dns_check' ) ?></td>
            </tr>
            <tr>
                <th><acronym title="Content Delivery Network">CDN</acronym> Domain:</th>
                <td>
                    <?php $this->render_zone_value_change( $details, 'custom_domain' ) ?><br />
                    <span class="description">Domain <acronym title="Content Delivery Network">CDN</acronym> will handle</span>
                </td>
            </tr>
        </table>

        <p class="submit">
            <input type="button"
                class="w3tc_cdn_maxcdn_fsd_configure_zone w3tc-button-save button-primary"
                value="<?php _e( 'Apply', 'w3-total-cache' ); ?>" />
            <input type="button"
                class="w3tc_cdn_maxcdn_fsd_configure_zone_skip w3tc-button-save button"
                value="<?php _e( 'Don\'t reconfigure, I know what I\'m doing', 'w3-total-cache' ); ?>" />

        </p>
        <?php Util_Ui::postbox_footer(); ?>
    </div>
</form>
