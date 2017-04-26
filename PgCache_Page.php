<?php
namespace W3TC;



class PgCache_Page extends Base_Page_Settings {
	/**
	 * Current page
	 *
	 * @var string
	 */
	protected $_page = 'w3tc_pgcache';


	/**
	 * Page cache tab
	 *
	 * @return void
	 */
	function view() {
		global $wp_rewrite;

		$feeds = $wp_rewrite->feeds;

		$feed_key = array_search( 'feed', $feeds );

		if ( $feed_key !== false ) {
			unset( $feeds[$feed_key] );
		}

		$default_feed = get_default_feed();
		$pgcache_enabled = $this->_config->get_boolean( 'pgcache.enabled' );
		$permalink_structure = get_option( 'permalink_structure' );

		$varnish_enabled = $this->_config->get_boolean( 'varnish.enabled' );
		$cdn_mirror_purge_enabled =
			Cdn_Util::is_engine_fsd( $this->_config->get_string( 'cdn.engine' ) ) &&
			$this->_config->get_boolean( 'cdn.enabled' );
		include W3TC_INC_DIR . '/options/pgcache.php';
	}
}
