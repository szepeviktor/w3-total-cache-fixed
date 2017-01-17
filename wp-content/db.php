<?php

/**
 * W3 Total Cache Database module
 */
if ( !defined( 'ABSPATH' ) ) {
	die();
}

if ( !defined( 'W3TC_DIR' ) ) {
	define( 'W3TC_DIR', ( defined( 'WP_PLUGIN_DIR' ) ? WP_PLUGIN_DIR : WP_CONTENT_DIR . '/plugins' ) . '/w3-total-cache' );
}

/**
 * Abort W3TC loading if WordPress is upgrading
 */
if ( !@is_dir( W3TC_DIR ) || !file_exists( W3TC_DIR . '/w3-total-cache-api.php' ) ) {
	if ( !defined( 'WP_ADMIN' ) ) { // lets don't show error on front end
		require_once ABSPATH . WPINC . '/wp-db.php';
	} else {
		echo sprintf( '<strong>W3 Total Cache Error:</strong> some files appear to be missing or out of place. Please re-install plugin or remove <strong>%s</strong>. <br />', __FILE__ );
	}
} else {
	require_once W3TC_DIR . '/w3-total-cache-api.php';

	// no caching during activation
	$is_installing = ( defined( 'WP_INSTALLING' ) && WP_INSTALLING );

	$config = \W3TC\Dispatcher::config();
	if ( ( !$is_installing && $config->get_boolean( 'dbcache.enabled' ) ) || \W3TC\Util_Environment::is_dbcluster() ) {
		if ( defined( 'DB_TYPE' ) ) {
			$db_driver_path = sprintf( '%s/Db/%s.php', W3TC_LIB_DIR, DB_TYPE );

			if ( file_exists( $db_driver_path ) ) {
				require_once $db_driver_path;
			} else {
				die( sprintf( '<strong>W3 Total Cache Error:</strong> database driver doesn\'t exist: %s.', $db_driver_path ) );
			}
		}

		$GLOBALS['wpdb'] = \W3TC\DbCache_Wpdb::instance();
	}
}
