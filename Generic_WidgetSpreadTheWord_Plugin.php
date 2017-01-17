<?php
namespace W3TC;

/**
 * spread the word widget's plugin
 */
class Generic_WidgetSpreadTheWord_Plugin {
	private $_config = null;



	function __construct() {
		$this->_config = Dispatcher::config();
	}



	/**
	 * Runs plugin
	 */
	function run() {
		if ( Util_Admin::get_current_wp_page() == 'w3tc_dashboard' )
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );

		add_action( 'w3tc_widget_setup', array(
				$this,
				'wp_dashboard_setup'
			) );
		add_action( 'w3tc_network_dashboard_setup', array(
				$this,
				'wp_dashboard_setup'
			) );

		if ( is_admin() ) {
			add_action( 'wp_ajax_w3tc_link_support', array( $this, 'action_widget_link_support' ) );
		}
	}

	/**
	 * Dashboard setup action
	 *
	 * @return void
	 */
	function wp_dashboard_setup() {
		Util_Widget::add( 'w3tc_spreadtheword',
			'<div class="w3tc-widget-w3tc-logo"></div>' .
			'<div class="w3tc-widget-text">' .
			__( 'Spread the Word', 'w3-total-cache' ) .
			'</div>',
			array( $this, 'widget_form' ),
			null,
			'normal' );
	}

	function widget_form() {
		$support = $this->_config->get_string( 'common.support' );
		$supports = $this->get_supports();

		include W3TC_DIR . '/Generic_WidgetSpreadTheWord_View.php';
	}

	/**
	 * Returns list of support types
	 *
	 * @return array
	 */
	function get_supports() {
		$supports = array(
			'footer' => 'page footer'
		);

		$link_categories = get_terms( 'link_category', array(
				'hide_empty' => 0
			) );

		foreach ( $link_categories as $link_category ) {
			$supports['link_category_' . $link_category->term_id] = strtolower( $link_category->name );
		}

		return $supports;
	}

	function action_widget_link_support() {
		$value = Util_Request::get_string( 'w3tc_common_support_us' );
		$this->_config->set( 'common.support', $value );
		$this->_config->save();

		Generic_AdminLinks::link_update( $this->_config );

		if ( $value ) {
			_e( 'Thank you for linking to us!', 'w3-total-cache' );
		} else {
			_e( 'You are no longer linking to us. Please support us in other ways instead!', 'w3-total-cache' );
		}
		die();
	}

	public function enqueue() {
		wp_enqueue_style( 'w3tc-widget' );
		wp_enqueue_script( 'w3tc-metadata' );
		wp_enqueue_script( 'w3tc-widget' );

		wp_enqueue_script( 'w3tc_spread_the_word',
			plugins_url( 'Generic_WidgetSpreadTheWord.js', W3TC_FILE ),
			array( 'jquery' ), '1.0' );

		wp_localize_script( 'w3tc_spread_the_word',
			'w3tc_spread_the_word_product_url', W3TC_SUPPORT_US_PRODUCT_URL );
		wp_localize_script( 'w3tc_spread_the_word',
			'w3tc_spread_the_word_tweet', W3TC_SUPPORT_US_TWEET );
		wp_localize_script( 'w3tc_spread_the_word',
			'w3tc_spread_the_word_rate_url', W3TC_SUPPORT_US_RATE_URL );
	}
}
