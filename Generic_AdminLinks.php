<?php
namespace W3TC;

/**
 * class Environment
 */
class Generic_AdminLinks {
	/**
	 * Update plugin link
	 *
	 * @return void
	 */
	static public function link_update( $config ) {
		self::link_delete();
		self::link_insert( $config );
	}

	/**
	 * Insert plugin link into Blogroll
	 *
	 * @return void
	 */
	static private function link_insert( $config ) {
		$support = $config->get_string( 'common.support' );
		$matches = null;
		if ( $support != '' && preg_match( '~^link_category_(\d+)$~', $support, $matches ) ) {
			require_once ABSPATH . 'wp-admin/includes/bookmark.php';

			wp_insert_link( array(
					'link_url' => W3TC_LINK_URL,
					'link_name' => W3TC_LINK_NAME,
					'link_category' => array(
						(int) $matches[1]
					),
					'link_rel' => 'nofollow'
				) );
		}
	}

	/**
	 * Deletes plugin link from Blogroll
	 *
	 * @return void
	 */
	static public function link_delete() {
		require_once ABSPATH . 'wp-admin/includes/bookmark.php';
		$bookmarks = get_bookmarks();
		$link_id = 0;
		foreach ( $bookmarks as $bookmark ) {
			if ( $bookmark->link_url == W3TC_LINK_URL ) {
				wp_delete_link( $bookmark->link_id );
			}
		}
	}
}
