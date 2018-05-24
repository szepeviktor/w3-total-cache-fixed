<?php
namespace W3TC;

/**
 * W3 Total Cache Root_AdminMenu
 */

class Root_AdminMenu {

	/**
	 * Current page
	 *
	 * @var string
	 */
	protected $_page = 'w3tc_dashboard';

	private $_config;

	function __construct() {
		$this->_config = Dispatcher::config();
	}

	function generate_menu_array() {
		$pages = array(
			'w3tc_dashboard' => array(
				'page_title' => __( 'Dashboard', 'w3-total-cache' ),
				'menu_text' => __( 'Dashboard', 'w3-total-cache' ),
				'visible_always' => true,
				'order' => 100
			),
			'w3tc_general' => array(
				'page_title' => __( 'General Settings', 'w3-total-cache' ),
				'menu_text' => __( 'General Settings', 'w3-total-cache' ),
				'visible_always' => false,
				'order' => 200
			),
			'w3tc_pgcache' => array(
				'page_title' => __( 'Page Cache', 'w3-total-cache' ),
				'menu_text' => __( 'Page Cache', 'w3-total-cache' ),
				'visible_always' => false,
				'order' => 300
			),
			'w3tc_minify' => array(
				'page_title' => __( 'Minify', 'w3-total-cache' ),
				'menu_text' => __( 'Minify', 'w3-total-cache' ),
				'visible_always' => false,
				'order' => 400
			),
			'w3tc_dbcache' => array(
				'page_title' => __( 'Database Cache', 'w3-total-cache' ),
				'menu_text' => __( 'Database Cache', 'w3-total-cache' ),
				'visible_always' => false,
				'order' => 500
			),
			'w3tc_objectcache' => array(
				'page_title' => __( 'Object Cache', 'w3-total-cache' ),
				'menu_text' => __( 'Object Cache', 'w3-total-cache' ),
				'visible_always' => false,
				'order' => 600
			),
			'w3tc_browsercache' => array(
				'page_title' => __( 'Browser Cache', 'w3-total-cache' ),
				'menu_text' => __( 'Browser Cache', 'w3-total-cache' ),
				'visible_always' => false,
				'order' => 700
			),
			'w3tc_mobile' => array(
				'page_title' => __( 'User Agent Groups', 'w3-total-cache' ),
				'menu_text' => __( 'User Agent Groups', 'w3-total-cache' ),
				'visible_always' => false,
				'order' => 800
			),
			'w3tc_referrer' => array(
				'page_title' => __( 'Referrer Groups', 'w3-total-cache' ),
				'menu_text' => __( 'Referrer Groups', 'w3-total-cache' ),
				'visible_always' => false,
				'order' => 900
			),
			'w3tc_cdn' => array(
				'page_title' => __( 'Content Delivery Network', 'w3-total-cache' ),
				'menu_text' => __( '<acronym title="Content Delivery Network">CDN</acronym>', 'w3-total-cache' ),
				'visible_always' => false,
				'order' => 1000
			),
			'w3tc_faq' => array(
				'page_title' => __( 'FAQ', 'w3-total-cache' ),
				'menu_text' => __( 'FAQ', 'w3-total-cache' ),
				'visible_always' => true,
				'order' => 2000,
				'redirect_faq' => '*'
			),
			'w3tc_support' => array(
				'page_title' => __( 'Support', 'w3-total-cache' ),
				'menu_text' => __( '<span style="color: red;">Support</span>', 'w3-total-cache' ),
				'visible_always' => true,
				'order' => 2100
			),
			'w3tc_install' => array(
				'page_title' => __( 'Install', 'w3-total-cache' ),
				'menu_text' => __( 'Install', 'w3-total-cache' ),
				'visible_always' => false,
				'order' => 2200
			),
			'w3tc_about' => array(
				'page_title' => __( 'About', 'w3-total-cache' ),
				'menu_text' => __( 'About', 'w3-total-cache' ),
				'visible_always' => true,
				'order' => 2300
			)
		);
		$pages = apply_filters( 'w3tc_admin_menu', $pages, $this->_config );

		return $pages;
	}

	function generate( $base_capability ) {
		$pages = $this->generate_menu_array();

		uasort( $pages, function($a, $b) {
    			return ($a['order'] - $b['order']);
			}
		);

		add_menu_page( __( 'Performance', 'w3-total-cache' ),
			__( 'Performance', 'w3-total-cache' ),
			apply_filters( 'w3tc_capability_menu_w3tc_dashboard',
				$base_capability ),
			'w3tc_dashboard', '', 'div' );

		$submenu_pages = array();
		$is_master = ( is_network_admin() || !Util_Environment::is_wpmu() );
		$remaining_visible = !$this->_config->get_boolean( 'common.force_master' );

		foreach ( $pages as $slug => $titles ) {
			if ( $is_master || $titles['visible_always'] || $remaining_visible ) {
				$hook = add_submenu_page( 'w3tc_dashboard',
					$titles['page_title'] . ' | W3 Total Cache',
					$titles['menu_text'],
					apply_filters( 'w3tc_capability_menu_' . $slug,
						$base_capability ),
					$slug,
					array( $this, 'options' )
				);
				$submenu_pages[] = $hook;

				if ( isset( $titles['redirect_faq'] ) ) {
					add_action( 'load-' . $hook, array( $this, 'redirect_faq' ) );
				}
			}
		}
		return $submenu_pages;
	}

	public function redirect_faq() {
		wp_redirect( W3TC_FAQ_URL );
		exit;
	}

	/**
	 * Options page
	 *
	 * @return void
	 */
	function options() {
		$this->_page = Util_Request::get_string( 'page' );
		if ( !Util_Admin::is_w3tc_admin_page() )
			$this->_page = 'w3tc_dashboard';

		/*
         * Hidden pages
         */
		if ( isset( $_REQUEST['w3tc_dbcluster_config'] ) ) {
			$options_dbcache = new DbCache_Page();
			$options_dbcache->dbcluster_config();
		}

		/**
		 * Show tab
		 */
		switch ( $this->_page ) {
		case 'w3tc_dashboard':
			$options_dashboard = new Generic_Page_Dashboard();
			$options_dashboard->options();
			break;

		case 'w3tc_general':
			$options_general = new Generic_Page_General();
			$options_general->options();
			break;

		case 'w3tc_pgcache':
			$options_pgcache = new PgCache_Page();
			$options_pgcache->options();
			break;

		case 'w3tc_minify':
			$options_minify = new Minify_Page();
			$options_minify->options();
			break;

		case 'w3tc_dbcache':
			$options_dbcache = new DbCache_Page();
			$options_dbcache->options();
			break;

		case 'w3tc_objectcache':
			$options_objectcache = new ObjectCache_Page();
			$options_objectcache->options();
			break;

		case 'w3tc_browsercache':
			$options_browsercache = new BrowserCache_Page();
			$options_browsercache->options();
			break;

		case 'w3tc_mobile':
			$options_mobile = new Mobile_Page_UserAgentGroups();
			$options_mobile->options();
			break;

		case 'w3tc_referrer':
			$options_referrer = new Mobile_Page_ReferrerGroups();
			$options_referrer->options();
			break;

		case 'w3tc_cdn':
			$options_cdn = new Cdn_Page();
			$options_cdn->options();
			break;

		case 'w3tc_support':
			$options_support = new Support_Page();
			$options_support->options();
			break;

		case 'w3tc_install':
			$options_install = new Generic_Page_Install();
			$options_install->options();
			break;

		case 'w3tc_about':
			$options_about = new Generic_Page_About();
			$options_about->options();
			break;
		default:
			// placeholder to make it the only way to show pages
			// with the time
			$view = new Base_Page_Settings();
			$view->options();

			do_action( "w3tc_settings_page-{$this->_page}" );

			$view->render_footer();

			break;
		}
	}
}
