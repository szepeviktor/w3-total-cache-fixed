<?php
namespace W3TC;

/**
 * Core for FSD CDN
 */
class Cdn_Fsd_Core {
	/**
	 * Returns CDN object
	 */
	function get_engine() {
		static $engine_object = null;

		if ( is_null( $engine_object ) ) {
			$c = Dispatcher::config();
			$engine = $c->get_string( 'cdn.engine' );

			switch ( $engine ) {
			case 'cloudfront_fsd':
				$engine_object = new Cdn_CloudFrontFsd_Engine( array(
						'access_key' => $c->get_string( 'cdn.cloudfront_fsd.access_key' ),
						'secret_key' => $c->get_string( 'cdn.cloudfront_fsd.secret_key' ),
						'distribution_id' => $c->get_string( 'cdn.cloudfront_fsd.distribution_id' )
					) );
				break;

			case 'maxcdn_fsd':
				$engine_object = new Cdn_MaxCdnFsd_Engine( array(
						'api_key' => $c->get_string( 'cdn.maxcdn_fsd.api_key' ),
						'zone_id' => $c->get_integer( 'cdn.maxcdn_fsd.zone_id' )
					) );
				break;

			default:
				throw new \Exception( 'unknown engine ' . $engine );
			}
		}

		return $engine_object;
	}
}
