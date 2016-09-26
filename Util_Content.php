<?php
namespace W3TC;

class Util_Content {
	/**
	 * Check if content is HTML or XML
	 *
	 * @param string  $content
	 * @return boolean
	 */
	static public function is_html( $content ) {
		if ( strlen( $content ) > 1000 ) {
			$content = substr( $content, 0, 1000 );
		}

		if ( strstr( $content, '<!--' ) !== false ) {
			$content = preg_replace( '~<!--.*?-->~s', '', $content );
		}

		$content = ltrim( $content, "\x00\x09\x0A\x0D\x20\xBB\xBF\xEF" );

		return stripos( $content, '<?xml' ) === 0 || stripos( $content, '<html' ) === 0 || stripos( $content, '<!DOCTYPE' ) === 0;
	}

	/**
	 * If content can handle HTML comments, can disable printout per request using filter 'w3tc_can_print_comment'
	 *
	 * @param unknown $buffer
	 * @return bool
	 */
	static public function can_print_comment( $buffer ) {
		if ( function_exists( 'apply_filters' ) )
			return apply_filters( 'w3tc_can_print_comment', Util_Content::is_html( $buffer ) && !defined( 'DOING_AJAX' ) );
		return Util_Content::is_html( $buffer ) && !defined( 'DOING_AJAX' );
	}



	/**
	 * Check if there was database error
	 *
	 * @param string  $content
	 * @return boolean
	 */
	static public function is_database_error( &$content ) {
		return stristr( $content, '<title>Database Error</title>' ) !== false;
	}

	/**
	 * Returns GMT date
	 *
	 * @param integer $time
	 * @return string
	 */
	static public function http_date( $time ) {
		return gmdate( 'D, d M Y H:i:s \G\M\T', $time );
	}

	/**
	 * Escapes HTML comment
	 *
	 * @param string  $comment
	 * @return mixed
	 */
	static public function escape_comment( $comment ) {
		while ( strstr( $comment, '--' ) !== false ) {
			$comment = str_replace( '--', '- -', $comment );
		}

		return $comment;
	}
}
