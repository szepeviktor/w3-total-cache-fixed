<?php
namespace W3TC;

/**
 * Purge using AmazonSNS object
 */
class Enterprise_CacheFlush_MakeSnsEvent extends Enterprise_SnsBase {

	private $messages = array();
	private $messages_by_signature = array();

	/**
	 * Flushes DB caches
	 *
	 */
	function dbcache_flush() {
		$this->_prepare_message( array( 'action' => 'dbcache_flush' ) );
	}

	/**
	 * Flushes minify caches
	 *
	 */
	function minifycache_flush() {
		$this->_prepare_message( array( 'action' => 'minifycache_flush' ) );
	}

	/**
	 * Flushes object caches
	 *
	 */
	function objectcache_flush() {
		$this->_prepare_message( array( 'action' => 'objectcache_flush' ) );
	}

	/**
	 * Flushes fragment caches
	 *
	 */
	function fragmentcache_flush() {
		$this->_prepare_message( array( 'action' => 'fragmentcache_flush' ) );
	}

	/**
	 * Flushes fragment cache based on group
	 *
	 */
	function fragmentcache_flush_group( $group ) {
		$this->_prepare_message( array( 'action' => 'fragmentcache_flush_group',
				'group' => $group ) );
	}

	/**
	 * Flushes query string
	 *
	 */
	function browsercache_flush() {
		$this->_prepare_message( array( 'action' => 'browsercache_flush' ) );
	}

	/**
	 * Purges Files from Varnish (If enabled) and CDN
	 *
	 */
	function cdn_purge_files( $purgefiles ) {
		$this->_prepare_message( array( 'action' => 'cdn_purge_files', 'purgefiles' => $purgefiles ) );
	}

	/**
	 * Performs garbage collection on the pgcache
	 */
	function pgcache_cleanup() {
		$this->_prepare_message( array( 'action' => 'pgcache_cleanup' ) );
	}

	/**
	 * Flushes the system APC
	 *
	 * @return bool
	 */
	function opcache_flush() {
		$this->_prepare_message( array( 'action' => 'opcache_flush' ) );
	}

	/**
	 * Reloads/compiles a PHP file.
	 *
	 * @param string  $filename
	 * @return mixed
	 */
	function opcache_flush_file( $filename ) {
		return $this->_prepare_message( array(
				'action' => 'opcache_flush_file',
				'filename' => $filename ) );
	}

	/**
	 * Purges/Flushes post page
	 *
	 * @param unknown $post_id
	 * @return boolean
	 */
	function flush_post( $post_id, $extras = null ) {
		return $this->_prepare_message( array(
			'action' => 'flush_post',
			'post_id' => $post_id,
			'extras' => $extras ) );
	}

	/**
	 * Purges/Flushes posts from caches
	 *
	 * @param unknown $extras
	 * @return boolean
	 */
	function flush_posts( $extras ) {
		return $this->_prepare_message( array(
				'action' => 'flush_posts',
				'extras' => $extras ) );
	}

	/**
	 * Purges/Flushes all enabled caches
	 *
	 * @return boolean
	 */
	function flush_all( $extras ) {
		return $this->_prepare_message( array(
				'action' => 'flush_all',
				'extras' => $extras
			) );
	}

	/**
	 * Purges/Flushes url
	 *
	 * @param string  $url
	 * @return boolean
	 */
	function flush_url( $url, $extras ) {
		return $this->_prepare_message( array(
			'action' => 'flush_url',
			'url' => $url,
			'extras' => $extras ) );
	}

	/**
	 * Makes get request to url specific to post, ie permalinks
	 *
	 * @param unknown $post_id
	 * @return mixed
	 */
	function prime_post( $post_id ) {
		return $this->_prepare_message( array( 'action' => 'prime_post', 'post_id' => $post_id ) );
	}

	/**
	 * Setups message list and if it should be combined or separate
	 *
	 * @param unknown $message
	 * @return boolean
	 */
	private function _prepare_message( $message ) {
		$message_signature = json_encode( $message );
		if ( isset( $this->messages_by_signature[$message_signature] ) )
			return true;
		$this->messages_by_signature[$message_signature] = '*';
		$this->messages[] = $message;

		$action = $this->_get_action();
		if ( !$action ) {
			$this->execute_delayed_operations();
			return true;
		}

		return true;
	}

	/**
	 * Sends messages stored in $messages
	 *
	 * @return boolean
	 */
	public function execute_delayed_operations() {
		if ( count( $this->messages ) <= 0 )
			return true;

		$this->_log( $this->_get_action() . ' sending messages' );

		$message = array();
		$message['actions'] = $this->messages;
		$message['blog_id'] = Util_Environment::blog_id();
		$message['host'] = isset( $_SERVER['HTTP_HOST'] ) ? $_SERVER['HTTP_HOST'] : null;
		$message['hostname'] = @gethostname();
		$v = json_encode( $message );

		try {
			$api = $this->_get_api();
			if ( defined( 'WP_CLI' ) && WP_CLI )
				$origin = 'WP CLI';
			else
				$origin = 'WP';
			$this->_log( $origin . ' sending message ' . $v );
			$this->_log( 'Host: ' . $message['host'] );
			if ( isset( $_SERVER['REQUEST_URI'] ) )
				$this->_log( 'URL: ' . $_SERVER['REQUEST_URI'] );
			if ( function_exists( 'current_filter' ) )
				$this->_log( 'Current WP hook: ' . current_filter() );

			$backtrace = debug_backtrace();
			$backtrace_optimized = array();
			foreach ( $backtrace as $b ) {
				$opt = isset( $b['function'] ) ? $b['function'] . ' ' : '';
				$opt .= isset( $b['file'] ) ? $b['file'] . ' ' : '';
				$opt .= isset( $b['line'] ) ?  '#' . $b['line'] . ' ' : '';
				$backtrace_optimized[] = $opt;

			}
			$this->_log( 'Backtrace ', $backtrace_optimized );

			$r = $api->publish( $this->_topic_arn, $v );
			if ( $r->status != 200 ) {
				$this->_log( "Error: {$r->body->Error->Message}" );
				return false;
			}
		} catch ( \Exception $e ) {
			$this->_log( 'Error ' . $e->getMessage() );
			return false;
		}

		// on success - reset messages array, but not hash (not resent repeatedly the same messages)
		$this->messages = array();

		return true;
	}

	/**
	 * Gets the current running WP action if any. Returns empty string if not found.
	 *
	 * @return string
	 */
	private function _get_action() {
		$action = '';
		if ( function_exists( 'current_filter' ) )
			$action = current_filter();
		return $action;
	}
}
