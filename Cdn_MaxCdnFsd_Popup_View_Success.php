<?php
namespace W3TC;

if ( !defined( 'W3TC' ) )
	die();
?>
<form class="w3tc_popup_form">
    <div class="metabox-holder">
        <?php Util_Ui::postbox_header(
	__( 'Succeeded', 'w3-total-cache' ) ); ?>

        <div style="text-align: center">
            Pull Zone <?php echo $details['name'] ?> was successfully configured.<br />
            Now you need to change DNS records of your domain
            <strong><?php echo $details['home_domain'] ?></strong> and CNAME it to
            <strong><?php echo $details['dns_cname_target'] ?></strong> to make caching work.
        </div>

        <p class="submit">
            <input type="button"
                class="w3tc_cdn_maxcdn_fsd_done w3tc-button-save button-primary"
                value="<?php _e( 'Done', 'w3-total-cache' ); ?>" />
        </p>
        <?php Util_Ui::postbox_footer(); ?>
    </div>
</form>
