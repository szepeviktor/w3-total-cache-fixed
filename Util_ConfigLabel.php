<?php
namespace W3TC;

class Util_ConfigLabel {
	static public function get( $key ) {
		static $keys = null;
		if ( is_null( $keys ) ) {
			$keys = array(
				'memcached.servers' => __( 'Memcached hostname:port / <acronym title="Internet Protocol">IP</acronym>:port:', 'w3-total-cache' ),
				'memcached.persistent' => __( 'Persistent connection', 'w3-total-cache' ),
				'memcached.username' => __( 'Memcached username:', 'w3-total-cache' ),
				'memcached.password' => __( 'Memcached password:', 'w3-total-cache' ),
				'redis.servers' => __( 'Redis hostname:port / <acronym title="Internet Protocol">IP</acronym>:port:', 'w3-total-cache' ),
				'redis.persistent' => __( 'Persistent connection', 'w3-total-cache' ),
				'redis.dbid' => __( 'Redis Database ID:', 'w3-total-cache' ),
				'redis.password' => __( 'Redis password:', 'w3-total-cache' ),
			);
		}

		return $keys[$key];
	}
}
