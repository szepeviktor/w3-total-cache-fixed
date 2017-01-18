<?php
namespace W3TC;

if ( !defined( 'W3TC' ) )
	die();
?>
<form action="admin.php?page=w3tc_cdn" method="post" style="padding: 20px">
    <?php
Util_Ui::hidden( '', 'client_id', $client_id );
Util_Ui::hidden( '', 'access_token', $access_token );
Util_Ui::hidden( '', 'refresh_token', $refresh_token );
echo Util_Ui::nonce_field( 'w3tc' );
?>
    <br /><br />
    <div class="metabox-holder">
        <?php Util_Ui::postbox_header( __( 'Select folder', 'w3-total-cache' ) ); ?>
        <table class="form-table">
            <tr>
                <td>Folder:</td>
                <td>
                    <?php foreach ( $folders as $folder ): ?>
                        <label>
                            <input name="folder" type="radio" class="w3tc-ignore-change"
                                value="<?php echo $folder->id ?>" />
                            <?php echo $folder->title ?>
                        </label><br />
                    <?php endforeach ?>

                    <label>
                        <input name="folder" type="radio" class="w3tc-ignore-change" value=""
                            />
                        Add new folder:
                    </label>
                    <input name="folder_new" type="text" class="w3tc-ignore-change" />
            </tr>
        </table>

        <p class="submit">
            <input type="submit" name="w3tc_cdn_google_drive_auth_set"
                class="w3tc-button-save button-primary"
                value="<?php _e( 'Apply', 'w3-total-cache' ); ?>" />
        </p>
        <?php Util_Ui::postbox_footer(); ?>
    </div>
</form>
