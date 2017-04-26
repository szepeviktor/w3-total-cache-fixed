<?php
namespace W3TC;

/**
 * component of shared code used by dbcache
 */
class DbCache_Core {
	public function get_usage_statistics_cache_config() {
		$c = Dispatcher::config();
		$engine = $c->get_string( 'dbcache.engine' );

		switch ( $engine ) {
		case 'memcached':
			$engineConfig = array(
				'servers' => $c->get_array( 'dbcache.memcached.servers' ),
				'persistent' => $c->get_boolean( 'dbcache.memcached.persistent' ),
				'aws_autodiscovery' => $c->get_boolean( 'dbcache.memcached.aws_autodiscovery' ),
				'username' => $c->get_string( 'dbcache.memcached.username' ),
				'password' => $c->get_string( 'dbcache.memcached.password' )
			);
			break;

		case 'redis':
			$engineConfig = array(
				'servers' => $c->get_array( 'dbcache.redis.servers' ),
				'persistent' => $c->get_boolean( 'dbcache.redis.persistent' ),
				'dbid' => $c->get_integer( 'dbcache.redis.dbid' ),
				'password' => $c->get_string( 'dbcache.redis.password' )
			);
			break;

		default:
			$engineConfig = array();
		}

		$engineConfig['engine'] = $engine;
		return $engineConfig;
	}
}
