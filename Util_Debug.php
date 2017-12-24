<?php
namespace W3TC;

class Util_Debug {
	/**
	 * Returns current microtime
	 *
	 * @return double
	 */
	static public function microtime() {
		list ( $usec, $sec ) = explode( ' ', microtime() );

		return (double) $usec + (double) $sec;
	}

	/**
	 * Return full path to log file for module
	 * Path used in priority
	 * 1) W3TC_DEBUG_DIR
	 * 2) WP_DEBUG_LOG
	 * 3) W3TC_CACHE_DIR
	 *
	 * @param unknown $module
	 * @param null    $blog_id
	 * @return string
	 */
	static public function log_filename( $module, $blog_id = null ) {
		if ( is_null( $blog_id ) )
			$blog_id = Util_Environment::blog_id();

		$postfix = sprintf( '%06d', $blog_id );

		if ( defined( 'W3TC_BLOG_LEVELS' ) ) {
			for ( $n = 0; $n < W3TC_BLOG_LEVELS; $n++ )
				$postfix = substr( $postfix, strlen( $postfix ) - 1 - $n, 1 ) . '/' .
					$postfix;
		}
		$from_dir = W3TC_CACHE_DIR;
		if ( defined( 'W3TC_DEBUG_DIR' ) && W3TC_DEBUG_DIR ) {
			$dir_path = W3TC_DEBUG_DIR;
			if ( !is_dir( W3TC_DEBUG_DIR ) )
				$from_dir = dirname( W3TC_DEBUG_DIR );
		} else
			$dir_path = Util_Environment::cache_dir( 'log' );
		$filename = $dir_path . '/' . $postfix . '/' . $module . '.log';
		if ( !is_dir( dirname( $filename ) ) ) {

			Util_File::mkdir_from_safe( dirname( $filename ), $from_dir );
		}

		return $filename;
	}



	static public function log( $module, $message ) {
		$message = strtr( $message, '<>', '..' );
		$filename = Util_Debug::log_filename( $module );

		return @file_put_contents( $filename, date( 'r' ) . ' ' . $message . "\n", FILE_APPEND );
	}
}
