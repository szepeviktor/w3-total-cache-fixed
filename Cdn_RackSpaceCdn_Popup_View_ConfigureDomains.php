<?php
namespace W3TC;

if ( !defined( 'W3TC' ) )
	die();
?>
<form action="admin.php?page=w3tc_cdn" method="post" style="padding: 20px" class="w3tc_cdn_rackspace_form">
    <?php
if ( !empty( $details['error_message'] ) )
	echo '<div class="error">' . $details['error_message'] . '</div>';
?>
    <div class="metabox-holder">
        <?php Util_Ui::postbox_header( __( 'CNAMEs to use', 'w3-total-cache' ) ); ?>
        <?php $cname_class = 'w3tc-ignore-change'; include W3TC_INC_DIR . '/options/cdn/common/cnames.php'; ?>
        <br />
        <span class="description"><?php _e( 'Enter hostname mapped to <acronym>CDN</acronym> host, this value will replace your site\'s hostname in the <acronym title="Hypertext Markup Language">HTML</acronym>.', 'w3-total-cache' ); ?></span>

        <p class="submit">
            <input type="button"
                class="w3tc_cdn_rackspace_configure_domains_done w3tc-button-save button-primary"
                value="<?php _e( 'Apply', 'w3-total-cache' ); ?>" />
        </p>
        <?php Util_Ui::postbox_footer(); ?>
    </div>
</form>
