<?php
namespace W3TC;

class Extensions_Util {
	/**
	 * Get registered extensions
	 *
	 * @param unknown $config
	 * @return array
	 */
	static public function get_extensions( $config ) {
		return apply_filters( "w3tc_extensions", __return_empty_array(), $config );
	}

	/**
	 * Get registered extension
	 *
	 * @param unknown $extension
	 * @param unknown $config
	 * @return array
	 */
	static public function get_extension( $config, $extension ) {
		$exts = self::get_extensions( $config );
		if ( !isset( $exts[$extension] ) )
			return null;

		return $exts[$extension];
	}

	/**
	 * Returns the inactive extensions
	 *
	 * @param unknown $config
	 * @return array
	 */
	static public function get_inactive_extensions( $config ) {
		$extensions = Extensions_Util::get_extensions( $config );
		$config = Dispatcher::config();
		$active_extensions = $config->get_array( 'extensions.active' );
		return array_diff_key( $extensions, $active_extensions );
	}

	/**
	 * Returns the active extensions
	 *
	 * @param unknown $config
	 * @return array
	 */
	static public function get_active_extensions( $config ) {
		$extensions = Extensions_Util::get_extensions( $config );
		$extensions_keys = array_keys( $extensions );
		$config = Dispatcher::config();
		$active_extensions = $config->get_array( 'extensions.active' );
		return array_intersect_key( $extensions, $active_extensions );
	}

	/**
	 *
	 *
	 * @param unknown $extension
	 * @param Config  $w3_config
	 * @return bool
	 */
	static public function activate_extension( $extension, $w3_config ) {
		$all_extensions = Extensions_Util::get_extensions( $w3_config );
		$extensions = $w3_config->get_array( 'extensions.active' );

		if ( !$w3_config->is_extension_active( $extension ) ) {
			$meta = $all_extensions[$extension];

			$filename = W3TC_EXTENSION_DIR . '/' . trim( $meta['path'], '/' );
			if ( !file_exists( $filename ) )
				return false;

			include $filename;

			$extensions[$extension] = $meta['path'];
			ksort( $extensions, SORT_STRING );
			$w3_config->set( 'extensions.active', $extensions );

			// if extensions doesnt want to control frontend activity -
			// activate it there too
			if ( !isset( $meta['active_frontend_own_control'] ) ||
				!$meta['active_frontend_own_control'] ) {
				$w3_config->set_extension_active_frontend( $extension, true );
			}

			try {
				$w3_config->save();
				return true;
			} catch ( \Exception $ex ) {
			}
		}

		return false;
	}


	/**
	 *
	 *
	 * @param unknown $extension
	 * @param Config  $config
	 * @param bool    $dont_save_config
	 * @return bool
	 */
	static public function deactivate_extension( $extension, $config, $dont_save_config = false ) {
		$extensions = $config->get_array( 'extensions.active' );
		if ( array_key_exists( $extension, $extensions ) ) {
			unset( $extensions[$extension] );
			ksort( $extensions, SORT_STRING );
			$config->set( 'extensions.active', $extensions );
		}

		$config->set_extension_active_frontend( $extension, false );

		try {
			if ( !$dont_save_config )
				$config->save();
			do_action( "w3tc_deactivate_extension_{$extension}" );
			return true;
		} catch ( \Exception $ex ) {}

		return false;
	}
}
