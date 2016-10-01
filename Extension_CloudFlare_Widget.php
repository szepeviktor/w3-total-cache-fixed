<?php
namespace W3TC;

/**
 * widget with stats
 */
class Extension_CloudFlare_Widget {
	function init() {
		add_action( 'admin_print_styles-toplevel_page_w3tc_dashboard',
			array( $this, 'admin_print_styles_w3tc_dashboard' ) );
		add_action( 'admin_print_scripts-toplevel_page_w3tc_dashboard',
			array( $this, 'admin_print_scripts_w3tc_dashboard' ) );

		add_action( 'w3tc_widget_setup', array(
				$this,
				'w3tc_widget_setup'
			) );
	}



	function w3tc_widget_setup() {
		Util_Widget::add( 'w3tc_cloudflare', 
			'<div class="w3tc_cloudflare_widget_logo"></div>',
			array( $this, 'widget_form' ),
			Util_Ui::admin_url( 'admin.php?page=w3tc_general#cloudflare' ),
			'normal' );
	}



	function widget_form() {
		$api = Extension_CloudFlare_SettingsForUi::api();
		$c = Dispatcher::config();
		$interval = $c->get_integer( array( 'cloudflare', 'widget_interval' ) );

		$v = get_transient( 'w3tc_cloudflare_stats' );

		try {
			$key = 'dashboard-' . $interval;
			if ( !isset( $v[$key] ) ) {
				if ( !is_array( $v ) )
					$v = array();

				$v[$key] = $api->analytics_dashboard( $interval );
				set_transient( 'w3tc_cloudflare_stats', $v,
					$this->_cache_mins * 60 );
			}

			$stats = $v[$key];
		} catch ( \Exception $e ) {
			$stats = null;
		}
		
		include  W3TC_DIR . '/Extension_CloudFlare_Widget_View.php';
	}



	public function admin_print_styles_w3tc_dashboard() {
		wp_enqueue_style( 'w3tc-widget' );
		wp_enqueue_style( 'w3tc-cloudflare-widget',
			plugins_url( 'Extension_CloudFlare_Widget_View.css', W3TC_FILE ),
			array(), W3TC_VERSION );
	}



	public function admin_print_scripts_w3tc_dashboard() {
		wp_enqueue_script( 'w3tc-metadata' );
		wp_enqueue_script( 'w3tc-widget' );
	}



	private function v( $stats, $key1, $key2 ) {
		echo '<td class="cloudflare_td_value">';
		echo number_format( $this->value( $stats, 'totals', $key1, $key2 ) );
		echo "</td>\n";
	}



	private function value( $a, $k1, $k2, $k3 = null ) {
		$v = $a;
		if ( !is_null( $k1 ) )
			$v = isset( $v[$k1] ) ? $v[$k1] : null;
		if ( !is_null( $k2 ) )
			$v = isset( $v[$k2] ) ? $v[$k2] : null;
		if ( !is_null( $k3 ) )
			$v = isset( $v[$k3] ) ? $v[$k3] : null;

		return $v;
	}



	private function time( $a, $k1 ) {
		if ( !isset( $a['totals'][$k1] ) )
			return;

		echo date( 'm/d/Y H:i:s', strtotime( $a['totals'][$k1] ) );
	}



	private function time_mins( $a, $k1 ) {
		if ( !isset( $a['totals'][$k1] ) )
			return;

		echo date( 'm/d/Y H:i', strtotime( $a['totals'][$k1] ) );
	}
}
