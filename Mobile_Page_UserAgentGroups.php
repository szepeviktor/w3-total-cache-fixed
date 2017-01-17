<?php
namespace W3TC;



class Mobile_Page_UserAgentGroups extends Base_Page_Settings {
	/**
	 * Current page
	 *
	 * @var string
	 */
	protected $_page = 'w3tc_mobile';


	/**
	 * Mobile tab
	 *
	 * @return void
	 */
	function view() {
		$c = Dispatcher::config();

		$groups = array(
			'value' => $c->get_array( 'mobile.rgroups' ),
			'disabled' => $c->is_sealed( 'mobile.rgroups' ),
			'description' =>
			'<li>' .
			__( 'Enabling even a single user agent group will set a cookie called "w3tc_referrer." It is used to ensure a consistent user experience across page views. Make sure any reverse proxy servers etc respect this cookie for proper operation.',
				'w3-total-cache' ) .
			'</li>' .
			'<li>' .
			__( 'Per the above, make sure that visitors are notified about the cookie as per any regulations in your market.',
				'w3-total-cache' ) .
			'</li>'
		);

		$groups = apply_filters( 'w3tc_ui_config_item_mobile.rgroups', $groups );

		$w3_mobile = Dispatcher::component( 'Mobile_UserAgent' );
		$themes = $w3_mobile->get_themes();

		include W3TC_INC_DIR . '/options/mobile.php';
	}

}
