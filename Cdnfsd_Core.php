<?php
namespace W3TC;

/**
 * Core for FSD CDN
 */
class Cdnfsd_Core {
	/**
	 * Returns CDN object
	 */
	function get_engine() {
		static $engine_object = null;

		if ( is_null( $engine_object ) ) {
			$c = Dispatcher::config();
			$engine = $c->get_string( 'cdnfsd.engine' );

			switch ( $engine ) {
			case 'cloudflare':
				$engine_object = null;   // extension handles everything
				break;

			case 'cloudfront':
				$engine_object = new Cdnfsd_CloudFront_Engine( array(
						'access_key' => $c->get_string( 'cdnfsd.cloudfront.access_key' ),
						'secret_key' => $c->get_string( 'cdnfsd.cloudfront.secret_key' ),
						'distribution_id' => $c->get_string( 'cdnfsd.cloudfront.distribution_id' )
					) );
				break;

			case 'limelight':
				$engine_object = new Cdnfsd_LimeLight_Engine( array(
						'short_name' => $c->get_string( 'cdnfsd.limelight.short_name' ),
						'username' => $c->get_string( 'cdnfsd.limelight.username' ),
						'api_key' => $c->get_string( 'cdnfsd.limelight.api_key' ),
						'debug' => $c->get_string( 'cdnfsd.debug' )
					) );
				break;

			case 'maxcdn':
				$engine_object = new Cdnfsd_MaxCdn_Engine( array(
						'api_key' => $c->get_string( 'cdnfsd.maxcdn.api_key' ),
						'zone_id' => $c->get_integer( 'cdnfsd.maxcdn.zone_id' )
					) );
				break;

			case 'stackpath':
				$engine_object = new Cdnfsd_StackPath_Engine( array(
						'api_key' => $c->get_string( 'cdnfsd.stackpath.api_key' ),
						'zone_id' => $c->get_integer( 'cdnfsd.stackpath.zone_id' )
					) );
				break;

			default:
				throw new \Exception( 'unknown engine ' . $engine );
			}
		}

		return $engine_object;
	}
}
