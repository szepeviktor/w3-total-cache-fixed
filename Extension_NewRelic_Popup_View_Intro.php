<?php
namespace W3TC;

if ( !defined( 'W3TC' ) )
	die();
?>
<form style="padding: 20px">
    <?php
if ( isset( $details['error_message'] ) )
	echo '<div class="error">' . $details['error_message'] . '</div>';
?>

    <div class="metabox-holder">
        <?php Util_Ui::postbox_header(
	__( 'Specify API Key', 'w3-total-cache' ) ); ?>
        <table class="form-table">
            <tr>
                <th>
                    <label for="newrelic_api_key"><?php
_e( '<acronym title="Application Programming Interface">API</acronym> key:', 'w3-total-cache' )
?></label>
                </th>
                <td>
                    <input name="api_key" class="w3tcnr_api_key" type="text"
                        value="<?php echo esc_attr( $details['api_key'] ) ?>" size="45"/>
                </td>
            </tr>
        </table>

        <p class="submit">
            <input type="button"
                class="w3tcnr_list_applications w3tc-button-save button-primary"
                value="<?php _e( 'Apply', 'w3-total-cache' ); ?>" />
        </p>
        <?php Util_Ui::postbox_footer(); ?>
    </div>
</form>
