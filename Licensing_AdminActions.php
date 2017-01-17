<?php
namespace W3TC;



class Licensing_AdminActions {

	private $_config = null;

	function __construct() {
		$this->_config = Dispatcher::config();
	}

	/**
	 *  test action
	 */
	function w3tc_licensing_buy_plugin() {
		include W3TC_INC_DIR . '/lightbox/purchase.php';
	}

	/**
	 * Self test action
	 */
	function w3tc_licensing_upgrade() {
		include W3TC_INC_DIR . '/lightbox/upgrade.php';
	}

	function w3tc_licensing_check_key() {
		delete_transient( 'w3tc_license_status' );
		Licensing_Core::activate_license( $this->_config->get_string( 'plugin.license_key' ), W3TC_VERSION );
		Util_Admin::redirect( array(), true );
	}

	function w3tc_licensing_reset_rooturi() {
		$license_key = $this->_config->get_string( 'plugin.license_key' );

		delete_transient( 'w3tc_license_status' );
		Licensing_Core::activate_license( $license_key, W3TC_VERSION );

		$license = Licensing_Core::check_license( $license_key, W3TC_VERSION );
		if ( $license ) {
			$status = $license->license_status;
			if ( substr( $status . '.', 0, 7 ) == 'active.' ) {
				Util_Admin::redirect_with_custom_messages2( array(
						'notes' => array( 'Your license has been reset already. Activated for this website now.' )
					), true );
			}
		}

		$r = Licensing_Core::reset_rooturi(
			$this->_config->get_string( 'plugin.license_key' ), W3TC_VERSION );

		if ( isset( $r->status ) && $r->status == 'done' ) {
			Util_Admin::redirect_with_custom_messages2( array(
					'notes' => array( 'Email with a link for license reset was sent to you' )
				), true );
		} else {
			Util_Admin::redirect_with_custom_messages2( array(
					'errors' => array( 'Failed to reset license' )
				), true );
		}
	}
}
