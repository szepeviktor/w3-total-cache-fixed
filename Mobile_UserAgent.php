<?php
namespace W3TC;

/**
 * W3TC Mobile detection
 */

/**
 * class Mobile
 */
class Mobile_UserAgent extends Mobile_Base {
	/**
	 * PHP5-style constructor
	 */
	function __construct() {
		parent::__construct( 'mobile.rgroups', 'agents' );
	}

	function group_verifier( $group_compare_value ) {
		return isset( $_SERVER['HTTP_USER_AGENT'] ) && preg_match( '~' . $group_compare_value . '~i', $_SERVER['HTTP_USER_AGENT'] );
	}
}
