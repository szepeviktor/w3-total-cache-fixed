<?php
namespace W3TC;



class Cdn_MaxCdnFsd_Engine {
	private $api_key;
	private $zone_id;



	function __construct( $config = array() ) {
		$this->api_key = $config['api_key'];
		$this->zone_id = $config['zone_id'];
	}



	function flush_urls( $urls ) {
		if ( empty( $this->api_key ) || empty( $this->zone_id ) )
			throw new \Exception( __( 'API key not specified.', 'w3-total-cache' ) );

		if ( !class_exists( 'NetDNA' ) )
			require_once W3TC_LIB_NETDNA_DIR . '/NetDNA.php';
		$api = \NetDNA::create( $this->api_key );

		$files = array();
		foreach ( $urls as $url ) {
			$parsed = parse_url( $url );
			$relative_url =
				( isset( $parsed['path'] ) ? $parsed['path'] : '/' ) .
				( isset( $parsed['query'] ) ? '?' . $parsed['query'] : '' );
			$files[] = $relative_url;
		}

		$api->cache_delete( $this->zone_id, $files );
	}



	/**
	 * Flushes CDN completely
	 */
	function flush_all() {
		if ( empty( $this->api_key ) || empty( $this->zone_id ) )
			throw new \Exception( __( 'API key not specified.', 'w3-total-cache' ) );

		if ( !class_exists( 'NetDNA' ) )
			require_once W3TC_LIB_NETDNA_DIR . '/NetDNA.php';
		$api = \NetDNA::create( $this->api_key );

		$api->cache_delete( $this->zone_id );
	}
}
