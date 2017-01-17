<?php
namespace W3TCExample;

if ( !defined( 'W3TC' ) )
	die();

?>
<p>
	Jump to:
	<a href="admin.php?page=w3tc_general"><?php _e( 'Main Menu', 'w3-total-cache' ); ?></a> |
	<a href="admin.php?page=w3tc_extensions"><?php _e( 'Extensions', 'w3-total-cache' ); ?></a>
</p>
<p>Example extension is currently <span class="w3tc-enabled">enabled</span></p>

<div class="metabox-holder">
<?php
// render settings box header
\W3TC\Util_Ui::postbox_header( 'Example extension' );
?>
<table class="form-table">
<?php
// render controls showing content of w3tc configuration options
\W3TC\Util_Ui::config_item( array(
		'key' => array( 'example', 'is_title_postfix' ),
		'control' => 'checkbox',
		'label' => 'Add postfix to page titles',
		'checkbox_label' => 'Enable',
		'description' => 'Check if you want to add postfix to each post title.'
	) );
\W3TC\Util_Ui::config_item( array(
		'key' => array( 'example', 'title_postfix' ),
		'control' => 'textbox',
		'label' => 'Postfix to page titles'
	) );
?>
</table>
<?php
// render save button for ::config_item controls
\W3TC\Util_Ui::button_config_save( 'extension_example' );
// render settings box footer
\W3TC\Util_Ui::postbox_footer();
?>
</div>
