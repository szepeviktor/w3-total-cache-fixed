<?php
namespace W3TC;

class Generic_Plugin_AdminNotifications {

	private $_config;

	/**
	 *
	 *
	 * @var string
	 */
	private $_page;

	function __construct() {
		$this->_config = Dispatcher::config();
	}

	/**
	 * Runs plugin
	 */
	function run() {
		if ( Util_Admin::is_w3tc_admin_page() ) {
			add_action( 'admin_head', array(
					$this,
					'admin_head'
				) );

			add_action( 'w3tc_message_action_generic_support_us', array(
					$this,
					'w3tc_message_action_generic_support_us'
				) );
			add_action( 'w3tc_ajax_generic_support_us', array(
					$this,
					'w3tc_ajax_generic_support_us'
				) );

			add_action( 'w3tc_message_action_generic_edge', array(
					$this,
					'w3tc_message_action_generic_edge'
				) );
			add_action( 'w3tc_ajax_generic_edge', array(
					$this,
					'w3tc_ajax_generic_edge'
				) );
		}
	}

	/**
	 * Print JS required by the support nag.
	 */
	function admin_head() {
		$state = Dispatcher::config_state_master();

		// support us
		$support_reminder =
			$state->get_integer( 'common.support_us_invitations' ) < 3 &&
			( $state->get_integer( 'common.install' ) <
			( time() - W3TC_SUPPORT_US_TIMEOUT ) ) &&
			( $state->get_integer( 'common.next_support_us_invitation' ) <
			time() ) &&
			$this->_config->get_string( 'common.support' ) == '' &&
			!$this->_config->get_boolean( 'common.tweeted' );

		if ( $support_reminder ) {
			$state->set( 'common.next_support_us_invitation',
				time() + W3TC_SUPPORT_US_TIMEOUT );
			$state->set( 'common.support_us_invitations',
				$state->get_integer( 'common.support_us_invitations' ) + 1 );
			$state->save();

			do_action( 'w3tc_message_action_generic_support_us' );
		}


		// edge mode
		$edge_reminder = !$support_reminder &&
			!Util_Environment::is_w3tc_edge( $this->_config ) &&
			$state->get_integer( 'common.edge_invitations' ) < 3 &&
			( $state->get_integer( 'common.install' ) <
			( time() - W3TC_EDGE_TIMEOUT ) ) &&
			( $state->get_integer( 'common.next_edge_invitation' ) < time() );

		if ( $edge_reminder ) {
			if ( $state->get_integer( 'common.edge_invitations' ) > 1 )
				$next = time() + 30 * 24 * 60 * 60;
			else
				$next = time() + W3TC_EDGE_TIMEOUT;

			$state->set( 'common.next_edge_invitation', $next );
			$state->set( 'common.edge_invitations',
				$state->get_integer( 'common.edge_invitations' ) + 1 );
			$state->save();

			do_action( 'w3tc_message_action_generic_edge' );
		}
	}

	/**
	 * Display the support us nag
	 */
	public function w3tc_message_action_generic_support_us() {
		wp_enqueue_script( 'w3tc-generic_support_us',
			plugins_url( 'Generic_GeneralPage_View_ShowSupportUs.js', W3TC_FILE ),
			array(), W3TC_VERSION );
	}



	public function w3tc_ajax_generic_support_us() {
		$supports = $this->get_supports();
		global $current_user;
		wp_get_current_user();
		$email = $current_user->user_email;
		include W3TC_INC_DIR . '/lightbox/support_us.php';
	}



	private function get_supports() {
		$supports = array(
			'footer' => 'page footer'
		);

		$link_categories = get_terms( 'link_category', array(
				'hide_empty' => 0
			) );

		foreach ( $link_categories as $link_category ) {
			$supports['link_category_' . $link_category->term_id] =
				strtolower( $link_category->name );
		}

		return $supports;
	}



	/**
	 * Display the support us nag
	 */
	public function w3tc_message_action_generic_edge() {
		wp_enqueue_script( 'w3tc-generic_edge',
			plugins_url( 'Generic_GeneralPage_View_ShowEdge.js', W3TC_FILE ),
			array(), W3TC_VERSION );
	}



	public function w3tc_ajax_generic_edge() {
		include W3TC_INC_LIGHTBOX_DIR . '/edge.php';
	}
}
