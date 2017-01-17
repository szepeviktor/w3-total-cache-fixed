<?php
namespace W3TC;



/* todo - sort by name
function extensions_sort_cmp_name($a, $b)
{
    if ($a['name'] == $b['name']) {
        return 0;
    }
    return ($a['name'] < $b['name']) ? -1 : 1;
}*/



class Extensions_Page extends Base_Page_Settings {
	/**
	 * Current page
	 *
	 * @var string
	 */
	protected $_page = 'w3tc_extensions';
	protected $_active_tab;
	protected $_config_settings = array();

	/**
	 * Extensions view
	 *
	 * @return void
	 */
	function render_content() {
		$extension = '';
		$extension_status = 'all';

		if ( isset( $_GET['extension_status'] ) ) {
			if ( in_array( $_GET['extension_status'], array( 'all', 'active', 'inactive', 'core' ) ) )
				$extension_status = $_GET['extension_status'];
		}

		if ( isset( $_GET['extension'] ) ) {
			$extension = $_GET['extension'];
		}

		$view = ( isset( $_GET['action'] ) && $_GET['action'] == 'view' );

		$extensions_active = Extensions_Util::get_active_extensions( $this->_config );

		if ( $extension && $view ) {
			$all_settings = $this->_config->get_array( 'extensions.settings' );
			$meta = $extensions_active[$extension];
			$sub_view = 'settings';
		} else {
			$extensions_all = Extensions_Util::get_extensions( $this->_config );
			$extensions_inactive = Extensions_Util::get_inactive_extensions( $this->_config );
			$var = "extensions_{$extension_status}";
			$extensions = $$var;
			$extension_keys = array_keys($extensions);
			sort($extension_keys);

			$sub_view = 'list';
			$page = 1;
		}

		$config = Dispatcher::config();
		include W3TC_INC_OPTIONS_DIR . '/extensions.php';
	}

	/**
	 * Sets default values for lacking extension meta keys
	 *
	 * @param unknown $meta
	 * @return array
	 */
	function default_meta( $meta ) {
		$default = array (
			'name' => '',
			'author' => '',
			'description' => '',
			'author_uri' => '',
			'extension_uri' => '',
			'extension_id' => '',
			'version' => '',
			'enabled' => true,
			'requirements' => array(),
			'core' => false,
			'path' => ''
		);
		return array_merge( $default, $meta );
	}
}
