<?php
namespace W3TC;

/**
 * W3 ObjectCache plugin
 */
class ObjectCache_Plugin {
	/**
	 * Config
	 */
	private $_config = null;

	function __construct() {
		$this->_config = Dispatcher::config();
	}

	/**
	 * Runs plugin
	 */
	function run() {
		add_filter( 'cron_schedules', array(
				$this,
				'cron_schedules'
			) );

		add_filter( 'w3tc_footer_comment', array(
				$this,
				'w3tc_footer_comment'
			) );

		if ( $this->_config->get_string( 'objectcache.engine' ) == 'file' ) {
			add_action( 'w3_objectcache_cleanup', array(
					$this,
					'cleanup'
				) );
		}

		if ( $this->_do_flush() ) {
			add_action( 'clean_post_cache', array(
					$this,
					'on_post_change'
				), 0, 2 );
		}

		if ( $this->_do_flush() ) {
			add_action( 'comment_post', array(
					$this,
					'on_comment_change'
				), 0 );

			add_action( 'edit_comment', array(
					$this,
					'on_comment_change'
				), 0 );

			add_action( 'delete_comment', array(
					$this,
					'on_comment_change'
				), 0 );

			add_action( 'wp_set_comment_status', array(
					$this,
					'on_comment_status'
				), 0, 2 );

			add_action( 'trackback_post', array(
					$this,
					'on_comment_change'
				), 0 );

			add_action( 'pingback_post', array(
					$this,
					'on_comment_change'
				), 0 );
		}

		add_action( 'switch_theme', array(
				$this,
				'on_change'
			), 0 );

		if ( $this->_do_flush() ) {
			add_action( 'updated_option', array(
					$this,
					'on_change_option'
				), 0, 1 );
			add_action( 'added_option', array(
					$this,
					'on_change_option'
				), 0, 1 );

			add_action( 'delete_option', array(
					$this,
					'on_change_option'
				), 0, 1 );
		}

		add_action( 'edit_user_profile_update', array(
				$this,
				'on_change_profile'
			), 0 );

		add_filter( 'w3tc_admin_bar_menu',
			array( $this, 'w3tc_admin_bar_menu' ) );

		// usage statistics handling
		add_action( 'w3tc_usage_statistics_of_request', array(
				$this, 'w3tc_usage_statistics_of_request' ), 10, 1 );
		add_filter( 'w3tc_usage_statistics_metrics', array(
				$this, 'w3tc_usage_statistics_metrics' ) );

		if ( Util_Environment::is_wpmu() ) {
			add_action( 'delete_blog', array(
					$this,
					'on_change'
				), 0 );

			add_action( 'switch_blog', array(
					$this,
					'switch_blog'
				), 0, 2 );
		}
	}

	/**
	 * Does disk cache cleanup
	 *
	 * @return void
	 */
	function cleanup() {
		$w3_cache_file_cleaner = new Cache_File_Cleaner( array(
				'cache_dir' => Util_Environment::cache_blog_dir( 'object' ),
				'clean_timelimit' => $this->_config->get_integer( 'timelimit.cache_gc' )
			) );

		$w3_cache_file_cleaner->clean();
	}

	/**
	 * Cron schedules filter
	 *
	 * @param array   $schedules
	 * @return array
	 */
	function cron_schedules( $schedules ) {
		$gc = $this->_config->get_integer( 'objectcache.file.gc' );

		return array_merge( $schedules, array(
				'w3_objectcache_cleanup' => array(
					'interval' => $gc,
					'display' => sprintf( '[W3TC] Object Cache file GC (every %d seconds)', $gc )
				)
			) );
	}

	/**
	 * Change action
	 */
	function on_change() {
		static $flushed = false;

		if ( !$flushed ) {
			$flush = Dispatcher::component( 'CacheFlush' );
			$flush->objectcache_flush();
			$flushed = true;
		}
	}

	/**
	 * Change post action
	 */
	function on_post_change( $post_id = 0, $post = null ) {
		static $flushed = false;

		if ( !$flushed ) {
			if ( is_null( $post ) )
				$post = $post_id;

			if ( $post_id> 0 && !Util_Environment::is_flushable_post(
					$post, 'objectcache', $this->_config ) ) {
				return;
			}

			$flush = Dispatcher::component( 'CacheFlush' );
			$flush->objectcache_flush();
			$flushed = true;
		}
	}

	/**
	 * Change action
	 */
	function on_change_option( $option ) {
		static $flushed = false;
/*
		if ( !$flushed ) {
			if ( $option != 'cron' ) {
				$flush = Dispatcher::component( 'CacheFlush' );
				$flush->objectcache_flush();
				$flushed = true;
			}
		}*/
	}

	/**
	 * Flush cache when user profile is updated
	 *
	 * @param int     $user_id
	 */
	function on_change_profile( $user_id ) {
		static $flushed = false;

		if ( !$flushed ) {
			if ( Util_Environment::is_wpmu() ) {
				$blogs = get_blogs_of_user( $user_id, true );
				if ( $blogs ) {
					global $w3_multisite_blogs;
					$w3_multisite_blogs = $blogs;
				}
			}

			$flush = Dispatcher::component( 'CacheFlush' );
			$flush->objectcache_flush();

			$flushed = true;
		}
	}

	/**
	 * Switch blog action
	 */
	function switch_blog( $blog_id, $previous_blog_id ) {
		$o = Dispatcher::component( 'ObjectCache_WpObjectCache_Regular' );
		$o->switch_blog( $blog_id );
	}


	/**
	 * Comment change action
	 *
	 * @param integer $comment_id
	 */
	function on_comment_change( $comment_id ) {
		$post_id = 0;

		if ( $comment_id ) {
			$comment = get_comment( $comment_id, ARRAY_A );
			$post_id = ( !empty( $comment['comment_post_ID'] ) ?
				(int) $comment['comment_post_ID'] : 0 );
		}

		$this->on_post_change( $post_id );
	}

	/**
	 * Comment status action
	 *
	 * @param integer $comment_id
	 * @param string  $status
	 */
	function on_comment_status( $comment_id, $status ) {
		if ( $status === 'approve' || $status === '1' ) {
			$this->on_comment_change( $comment_id );
		}
	}

	public function w3tc_admin_bar_menu( $menu_items ) {
		$menu_items['20410.objectcache'] = array(
			'id' => 'w3tc_flush_objectcache',
			'parent' => 'w3tc_flush',
			'title' => __( 'Object Cache', 'w3-total-cache' ),
			'href' => wp_nonce_url( network_admin_url(
					'admin.php?page=w3tc_dashboard&amp;w3tc_flush_objectcache' ), 'w3tc' )
		);

		return $menu_items;
	}

	public function w3tc_footer_comment( $strings ) {
		$o = Dispatcher::component( 'ObjectCache_WpObjectCache_Regular' );
		$strings = $o->w3tc_footer_comment( $strings );

		return $strings;
	}

	public function w3tc_usage_statistics_of_request( $storage ) {
		$o = Dispatcher::component( 'ObjectCache_WpObjectCache_Regular' );
		$o->w3tc_usage_statistics_of_request( $storage );
	}

	public function w3tc_usage_statistics_metrics( $metrics ) {
		return array_merge( $metrics, array(
				'objectcache_calls_total', 'objectcache_calls_hits' ) );
	}

	/**
	 *
	 *
	 * @return bool
	 */
	private function _do_flush() {
		//TODO: Requires admin flush until OC can make changes in Admin backend
		return $this->_config->get_boolean( 'cluster.messagebus.enabled' )
			|| $this->_config->get_boolean( 'objectcache.purge.all' )
			|| defined( 'WP_ADMIN' );
	}
}
