<?php
namespace W3TC;



class Mobile_Page_ReferrerGroups extends Base_Page_Settings {
	/**
	 * Current page
	 *
	 * @var string
	 */
	protected $_page = 'w3tc_referrer';

	/**
	 * Referrer tab
	 *
	 * @return void
	 */
	function view() {
		$groups = $this->_config->get_array( 'referrer.rgroups' );

		$w3_referrer = Dispatcher::component( 'Mobile_Referrer' );

		$themes = $w3_referrer->get_themes();

		include W3TC_INC_DIR . '/options/referrer.php';
	}
}
