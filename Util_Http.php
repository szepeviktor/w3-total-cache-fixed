<?php
namespace W3TC;

class Util_Http {
	/**
	 * Filter handler for use_curl_transport.
	 * Workaround to not use curl for extra http methods
	 *
	 * @param unknown $result boolean
	 * @param unknown $args   array
	 * @return boolean
	 */
	static public function use_curl_transport( $result, $args ) {
		if ( isset( $args['method'] ) && $args['method'] != 'GET' && $args['method'] != 'POST' )
			return false;

		return $result;
	}

	/**
	 * Sends HTTP request
	 *
	 * @param unknown $url  string
	 * @param unknown $args array
	 * @return WP_Error|array
	 */
	static public function request( $url, $args = array() ) {
		static $filter_set = false;
		if ( !$filter_set ) {
			add_filter( 'use_curl_transport',
				array( '\W3TC\Util_Http', 'use_curl_transport' ), 10, 2 );
			$filter_set = true;
		}

		$args = array_merge( array(
				'user-agent' => W3TC_POWERED_BY
			), $args );

		return wp_remote_request( $url, $args );
	}

	/**
	 * Sends HTTP GET request
	 *
	 * @param string  $url
	 * @param array   $args
	 * @return array|WP_Error
	 */
	static public function get( $url, $args = array() ) {
		$args = array_merge( $args, array(
				'method' => 'GET'
			) );

		return self::request( $url, $args );
	}

	/**
	 * Downloads URL into a file
	 *
	 * @param string  $url
	 * @param string  $file
	 * @return boolean
	 */
	static public function download( $url, $file ) {
		if ( strpos( $url, '//' ) === 0 ) {
			$url = ( Util_Environment::is_https() ? 'https:' : 'http:' ) . $url;
		}

		$response = self::get( $url );

		if ( !is_wp_error( $response ) && $response['response']['code'] == 200 ) {
			return @file_put_contents( $file, $response['body'] );
		}

		return false;
	}

	/**
	 * Returns upload info
	 *
	 * @return array
	 */
	static public function upload_info() {
		static $upload_info = null;

		if ( $upload_info === null ) {
			$upload_info = Util_Environment::wp_upload_dir();

			if ( empty( $upload_info['error'] ) ) {
				$parse_url = @parse_url( $upload_info['baseurl'] );

				if ( $parse_url ) {
					$baseurlpath = ( !empty( $parse_url['path'] ) ? trim( $parse_url['path'], '/' ) : '' );
				} else {
					$baseurlpath = 'wp-content/uploads';
				}

				$upload_info['baseurlpath'] = '/' . $baseurlpath . '/';
			} else {
				$upload_info = false;
			}
		}

		return $upload_info;
	}
}
