<?php
namespace W3TC;

/**
 * Attaches to wp actions related to content change, which should fire
 * flushes of html content
 */
class Util_AttachToActions {
	static function flush_posts_on_actions() {
		static $attached = false;
		if ( $attached )
			return;

		$attached = true;

		$o = new Util_AttachToActions();

		add_action( 'clean_post_cache', array(
				$o,
				'on_post_change'
			), 0, 2 );
		add_action( 'publish_post', array(
				$o,
				'on_post_change'
			), 0, 2 );

		add_action( 'switch_theme', array(
				$o,
				'on_change'
			), 0 );

		add_action( 'wp_update_nav_menu', array(
				$o,
				'on_change'
			), 0 );

		add_action( 'edit_user_profile_update', array(
				$o,
				'on_change'
			), 0 );

		if ( Util_Environment::is_wpmu() ) {
			add_action( 'delete_blog', array(
					$o,
					'on_change'
				), 0 );
		}

		add_action( 'edited_term', array(
				$o,
				'on_change'
			), 0 );
	}



	/**
	 * Post changed action
	 *
	 * @param integer $post_id
	 * @param null    $post
	 * @return void
	 */
	function on_post_change( $post_id, $post = null ) {
		if ( is_null( $post ) )
			$post = get_post( $post_id );

		// if attachment changed - parent post has to be flushed
		// since there are usually attachments content like title
		// on the page (gallery)
		if ( $post->post_type == 'attachment' ) {
			$post_id = $post->post_parent;
			$post = get_post( $post_id );
		}

		if ( !Util_Environment::is_flushable_post( $post, 'posts',
				Dispatcher::config() ) )
			return;

		$cacheflush = Dispatcher::component( 'CacheFlush' );
		$cacheflush->flush_post( $post_id );
	}



	/**
	 * Change action
	 */
	function on_change() {
		$cacheFlush = Dispatcher::component( 'CacheFlush' );
		$cacheFlush->flush_posts();
	}
}
