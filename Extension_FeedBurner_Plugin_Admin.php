<?php
namespace W3TC;



class Extension_FeedBurner_Plugin_Admin {
	function run() {
		add_action( 'w3tc_environment_fix_on_wpadmin_request', array(
				'\W3TC\Extension_FeedBurner_Environment', 'fix_on_wpadmin_request' ),
			10, 2 );
		add_action( 'w3tc_environment_fix_after_deactivation', array(
				'\W3TC\Extension_FeedBurner_Environment', 'fix_after_deactivation' ) );
		add_filter( 'w3tc_environment_get_required_rules', array(
				'\W3TC\Extension_FeedBurner_Environment', 'get_required_rules' ),
			10, 2 );

		add_action( 'w3tc_deactivate_extension_feedburner', array(
				'\W3TC\Extension_FeedBurner_Environment', 'deactivate_extension' ) );

		add_action( 'w3tc_extension_page_feedburner', array(
				'\W3TC\Extension_FeedBurner_Page',
				'w3tc_extension_page_feedburner'
			) );
	}



	/**
	 *
	 *
	 * @param unknown $extensions
	 * @param Config  $config
	 * @return mixed
	 */
	static public function w3tc_extensions( $extensions, $config ) {
		$message = array();
		$message[] = 'FeedBurner';

		$extensions['feedburner'] = array (
			'name' => 'Google FeedBurner',
			'author' => 'W3 EDGE',
			'description' => sprintf( __( 'Automatically ping (purge) FeedBurner feeds when pages / posts are modified. Default URL: %s', 'w3-total-cache' ),
				!is_network_admin() ? home_url() : __( 'Network Admin has no main URL.', 'w3-total-cache' ) ),
			'author_uri' => 'https://www.w3-edge.com/',
			'extension_uri' => 'https://www.w3-edge.com/',
			'extension_id' => 'feedburner',
			'settings_exists' => true,
			'version' => '0.1',
			'enabled' => true,
			'requirements' => implode( ', ', $message ),
			'path' => 'w3-total-cache/Extension_FeedBurner_Plugin.php'
		);

		return $extensions;
	}
}
