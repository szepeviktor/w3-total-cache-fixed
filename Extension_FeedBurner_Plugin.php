<?php
namespace W3TC;

class Extension_FeedBurner_Plugin {
	private $_messagebus_do_ping = false;

	public function run() {
		add_action( 'w3tc_messagebus_message_received', array(
				$this,
				'messagebus_message_received'
			), 20 );

		// that is called after all flushes happen
		add_action( 'publish_post', array(
				$this,
				'publish_post'
			), 1000, 1 );
	}



	/**
	 * flush the registered FB feeds
	 */
	public function publish_post( $post_id ) {
		$config = Dispatcher::config();

		if ( Util_Environment::is_flushable_post( $post_id, 'pgcache', $config ) )
			$this->do_ping();
	}



	public function messagebus_message_received() {
		// if we process messagebus message - check if some post has been flushed
		// meaning that real content of responses has changed
		add_action( 'w3tc_flush_post',
			array( $this, 'w3tc_flush_post' ),
			10100, 1 );

		add_action( 'w3tc_messagebus_message_processed', array(
				$this,
				'messagebus_message_processed'
			), 20 );
	}



	public function w3tc_flush_post( $post_id ) {
		$config = Dispatcher::config();
		if ( Util_Environment::is_flushable_post( $post_id, 'pgcache', $config ) )
			$this->_messagebus_do_ping = true;
	}



	public function messagebus_message_processed() {
		if ( $this->_messagebus_do_ping )
			$this->do_ping();
	}



	private function do_ping() {
		$c = Dispatcher::config();

		$fb_urls = $c->get_array( array( 'feedburner', 'urls' ) );

		$fb_urls[] = home_url();
		foreach ( $fb_urls as $url ) {
			if ( !empty( $url ) )
				wp_remote_get( 'http://feedburner.google.com/fb/a/pingSubmit?bloglink=' . urlencode( $url ) );
		}
	}
}


$p = new Extension_FeedBurner_Plugin();
$p->run();

if ( is_admin() ) {
	$p = new Extension_FeedBurner_Plugin_Admin();
	$p->run();
}
