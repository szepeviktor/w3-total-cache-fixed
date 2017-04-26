<?php
namespace W3TC;

if ( !defined( 'W3TC' ) )
	die();

?>
<tr>
    <th><label for="minify_yuicss_path_java"><?php Util_Ui::e_config_label( 'minify.yuicss.path.java' ) ?></label></th>
    <td>
        <input class="css_enabled" id="minify_yuicss_path_java" type="text"
           <?php Util_Ui::sealing_disabled( 'minify.' ) ?> name="minify__yuicss__path__java" value="<?php echo esc_attr( $this->_config->get_string( 'minify.yuicss.path.java' ) ); ?>" size="100" /></td>
</tr>
<tr>
    <th><label for="minify_yuicss_path_jar"><?php Util_Ui::e_config_label( 'minify.yuicss.path.jar' ) ?></label></th>
    <td>
        <input class="css_enabled" id="minify_yuicss_path_jar" type="text"
           <?php Util_Ui::sealing_disabled( 'minify.' ) ?> name="minify__yuicss__path__jar" value="<?php echo esc_attr( $this->_config->get_string( 'minify.yuicss.path.jar' ) ); ?>" size="100" /></td>
</tr>
<tr>
    <th>&nbsp;</th>
    <td>
        <input class="minifier_test button css_enabled {type: 'yuicss', nonce: '<?php echo wp_create_nonce( 'w3tc' ); ?>'}" type="button" value="<?php _e( 'Test YUI Compressor', 'w3-total-cache' ); ?>" />
        <span class="minifier_test_status w3tc-status w3tc-process"></span>
    </td>
</tr>
<tr>
    <th><label for="minify_yuicss_options_line-break"><?php Util_Ui::e_config_label( 'minify.yuicss.options.line-break' ) ?></label></th>
    <td>
        <input class="css_enabled" id="minify_yuicss_options_line-break"
           <?php Util_Ui::sealing_disabled( 'minify.' ) ?> type="text" name="minify__yuicss__options__line-break" value="<?php echo esc_attr( $this->_config->get_integer( 'minify.yuicss.options.line-break' ) ); ?>" size="8" style="text-align: right;" /> symbols (set to 0 to disable)</td>
</tr>
