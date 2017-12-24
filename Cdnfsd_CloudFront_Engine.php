<?php
namespace W3TC;



class Cdnfsd_CloudFront_Engine {
	private $access_key;
	private $secret_key;
	private $distribution_id;



	function __construct( $config = array() ) {
		$this->access_key = $config['access_key'];
		$this->secret_key = $config['secret_key'];
		$this->distribution_id = $config['distribution_id'];
	}



	function flush_urls( $urls ) {
		if ( empty( $this->access_key ) || empty( $this->secret_key ) ||
			empty( $this->distribution_id ) )
			throw new \Exception( __( 'Access key not specified.', 'w3-total-cache' ) );

		$api = new Cdnfsd_CloudFront_Api( $this->access_key, $this->secret_key );
		$uris = array();
		foreach ( $urls as $url ) {
			$parsed = parse_url( $url );
			$relative_url =
				( isset( $parsed['path'] ) ? $parsed['path'] : '/' ) .
				( isset( $parsed['query'] ) ? '?' . $parsed['query'] : '' );
			$uris[] = $relative_url;
		}

		$api->invalidation_create( $this->distribution_id, $uris );
	}



	/**
	 * Flushes CDN completely
	 */
	function flush_all() {
		if ( empty( $this->access_key ) || empty( $this->secret_key ) ||
			empty( $this->distribution_id ) )
			throw new \Exception( __( 'Access key not specified.', 'w3-total-cache' ) );

		$api = new Cdnfsd_CloudFront_Api( $this->access_key, $this->secret_key );
		$uris = array( '/*' );

		$api->invalidation_create( $this->distribution_id, $uris );
	}
}
