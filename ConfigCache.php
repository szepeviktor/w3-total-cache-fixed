<?php
namespace W3TC;

/**
 * Provides access to config cache, used mostly when config is stored in
 * database to not issue config loading database queries on each http request
 */
class ConfigCache {
	/**
	 * Reads config from config cache
	 */
	static public function util_array_from_storage( $blog_id, $preview ) {
		$cache = self::get_cache();

		$config = $cache->get( self::get_key( $blog_id, $preview ) );
		if ( is_array( $config ) ) {
			return $config;
		}

		return null;
	}



	/**
	 * Removes config cache entry so that it can be read from original source
	 * on next attempt
	 */
	static public function remove_item( $blog_id, $preview ) {
		$cache = self::get_cache();

		$cache->hard_delete( self::get_key( $blog_id, false ) );
		$cache->hard_delete( self::get_key( $blog_id, true ) );
	}



	static public function save_item( $blog_id, $preview, $data ) {
		$cache = self::get_cache();

		$cache->set( self::get_key( $blog_id, $preview ), $data );
	}



	static private function get_cache() {
		static $cache = null;

		if ( !is_null( $cache ) ) {
			return $cache;
		}

		switch ( W3TC_CONFIG_CACHE_ENGINE ) {
		case 'memcached':
			$engineConfig = array(
				'servers' => explode( ',', W3TC_CONFIG_CACHE_MEMCACHED_SERVERS ),
				'persistent' =>
					( defined( 'W3TC_CONFIG_CACHE_MEMCACHED_PERSISTENT' ) ?
						W3TC_CONFIG_CACHE_MEMCACHED_PERSISTENT : true ),
				'aws_autodiscovery' =>
					( defined( 'W3TC_CONFIG_CACHE_MEMCACHED_AWS_AUTODISCOVERY' ) ?
						W3TC_CONFIG_CACHE_MEMCACHED_AWS_AUTODISCOVERY : false ),
				'username' =>
					( defined( 'W3TC_CONFIG_CACHE_MEMCACHED_USERNAME' ) ?
						W3TC_CONFIG_CACHE_MEMCACHED_USERNAME : '' ),
				'password' =>
					( defined( 'W3TC_CONFIG_CACHE_MEMCACHED_PASSWORD' ) ?
						W3TC_CONFIG_CACHE_MEMCACHED_PASSWORD : '' ),
				'key_version_mode' => 'disabled'
			);
			break;

		case 'redis':
			$engineConfig = array(
				'servers' => explode( ',', W3TC_CONFIG_CACHE_REDIS_SERVERS ),
				'persistent' =>
					( defined( 'W3TC_CONFIG_CACHE_REDIS_PERSISTENT' ) ?
						W3TC_CONFIG_CACHE_REDIS_PERSISTENT : true ),
				'dbid' =>
					( defined( 'W3TC_CONFIG_CACHE_REDIS_DBID' ) ?
						W3TC_CONFIG_CACHE_REDIS_DBID : 0 ),
				'password' =>
					( defined( 'W3TC_CONFIG_CACHE_REDIS_PASSWORD' ) ?
						W3TC_CONFIG_CACHE_REDIS_PASSWORD : '' ),
				'key_version_mode' => 'disabled'
			);
			break;

		default:
			$engineConfig = array();
		}

		$engineConfig['blog_id'] = '0';
		$engineConfig['module'] = 'config';
		$engineConfig['host'] = '';
		$engineConfig['instance_id'] =
			( defined( 'W3TC_INSTANCE_ID' ) ? W3TC_INSTANCE_ID : 0 );

		$cache = Cache::instance( W3TC_CONFIG_CACHE_ENGINE, $engineConfig );
		return $cache;
	}



	static private function get_key( $blog_id, $preview ) {
		return 'w3tc_config_' . $blog_id . ( $preview ? '_preview' : '' );
	}
}
