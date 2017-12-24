<?php
namespace W3TC;



class PgCache_Page_CookieGroups {
	static public function admin_init_w3tc_pgcache_cookiegroups() {
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'w3tc_pgcache_page_cookiegroups',
			plugins_url( 'PgCache_Page_CookieGroups_View.js', W3TC_FILE ),
			array( 'jquery', 'jquery-ui-sortable' ), '1.0' );
	}



	static public function w3tc_settings_page_w3tc_pgcache_cookiegroups() {
		$c = Dispatcher::config();

		$groups = array(
			'value' => $c->get_array( 'pgcache.cookiegroups.groups' ),
			'disabled' => $c->is_sealed( 'pgcache.cookiegroups.groups' )
		);

		$groups = apply_filters( 'w3tc_ui_config_item_pgcache.cookiegroups.groups', $groups );
		$config = Dispatcher::config();

		include W3TC_DIR . '/PgCache_Page_CookieGroups_View.php';
	}



	static public function w3tc_config_ui_save_w3tc_pgcache_cookiegroups( $config ) {
		$groups = Util_Request::get_array( 'cookiegroups' );

		$mobile_groups = array();
		$cached_mobile_groups = array();

		foreach ( $groups as $group => $group_config ) {
			$group = strtolower( $group );
			$group = preg_replace( '~[^0-9a-z_]+~', '_', $group );
			$group = trim( $group, '_' );

			if ( $group ) {
				$enabled = ( isset( $group_config['enabled'] ) ?
					(boolean) $group_config['enabled'] : false );
				$cache = ( isset( $group_config['cache'] ) ?
					(boolean) $group_config['cache'] : false );
				$cookies = ( isset( $group_config['cookies'] ) ?
					explode( "\r\n", trim( $group_config['cookies'] ) ) :
					array() );

				$cookies = array_unique( $cookies );
				sort( $cookies );

				$cookiegroups[$group] = array(
					'enabled' => $enabled,
					'cache' => $cache,
					'cookies' => $cookies
				);
			}
		}

		/**
		 * Allow plugins modify W3TC mobile groups
		 */
		$cookiegroups = apply_filters( 'w3tc_pgcache_cookiegroups', $cookiegroups );

		$enabled = false;
		foreach ( $cookiegroups as $group_config ) {
			if ( $group_config['enabled'] ) {
				$enabled = true;
				break;
			}
		}
		$config->set( 'pgcache.cookiegroups.enabled', $enabled );
		$config->set( 'pgcache.cookiegroups.groups', $cookiegroups );
	}
}
