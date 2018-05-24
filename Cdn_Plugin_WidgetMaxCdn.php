<?php
namespace W3TC;

/**
 * MaxCDN Widget
 */
class Cdn_Plugin_WidgetMaxCdn {
	private $authorized;
	private $have_zone;
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
			), 100 );
		add_action( 'w3tc_network_dashboard_setup', array(
				$this,
				'wp_dashboard_setup'
			), 100 );

		// Configure authorize and have_zone
		$this->authorized = $this->_config->get_string( 'cdn.maxcdn.authorization_key' ) != '' &&
			$this->_config->get_string( 'cdn.engine' ) == 'maxcdn';
		$keys = explode( '+', $this->_config->get_string( 'cdn.maxcdn.authorization_key' ) );
		$this->authorized = $this->authorized  && sizeof( $keys ) == 3;
		$this->have_zone = $this->_config->get_string( 'cdn.maxcdn.zone_id' ) != 0;

		add_action( 'w3tc_ajax_cdn_maxcdn_widgetdata', array(
			$this, 'w3tc_ajax_cdn_maxcdn_widgetdata' ) );

		if ( $this->have_zone && $this->authorized && isset( $_GET['page'] ) &&
				strpos( $_GET['page'], 'w3tc_dashboard' ) !== false ) {

		}
	}

	function w3tc_ajax_cdn_maxcdn_widgetdata() {
		require_once W3TC_LIB_NETDNA_DIR . '/NetDNA.php';
		require_once W3TC_LIB_NETDNA_DIR . '/NetDNAPresentation.php';
		$api = \NetDNA::create( $this->_config->get_string( 'cdn.maxcdn.authorization_key' ) );

		$zone_id = $this->_config->get_string( 'cdn.maxcdn.zone_id' );
		$response = array();

		try {
			$zone_info = $api->get_pull_zone( $zone_id );
			if ( !$zone_info )
				throw new \Exception("Zone not found");
			$filetypes = $api->get_list_of_file_types_per_zone( $zone_id );

			if ( !isset( $filetypes['filetypes'] ) )
				$filetypes['filetypes'] = array();

			$group_hits = \NetDNAPresentation::group_hits_per_filetype_group(
				$filetypes['filetypes'] );

			$graph = array( array('Filetype', 'Hits' ) );
			$colors = array();
			foreach ( $group_hits as $group => $hits ) {
				$graph[] = array( $group, $hits );
				$colors[] = \NetDNAPresentation::get_file_group_color( $group );
			}

			$response['graph'] = $graph;
			$response['colors'] = $colors;

			$summary = $api->get_stats_per_zone( $zone_id );

			$response['zone_name'] = $zone_info['name'];
			$response['summary'] = $summary;
			$response['summary_size'] = Util_Ui::format_bytes( $summary['size'] );
			$response['summary_cache_hit'] = $summary['cache_hit'];
			$response['summary_cache_hit_percentage'] = $summary['hit'] ?
				( $summary['cache_hit'] / $summary['hit'] ) * 100 :
				$summary['hit'];
        	$response['summary_noncache_hit'] = $summary['noncache_hit'];
        	$response['summary_noncache_hit_percentage'] = $summary['hit'] ?
        		( $summary['noncache_hit'] / $summary['hit'] ) * 100 :
        		$summary['hit'];

			$response['filetypes'] = $filetypes;
			$popular_files = $api->get_list_of_popularfiles_per_zone( $zone_id );
			$popular_files = \NetDNAPresentation::format_popular( $popular_files );
			$response['popular_files'] = array_slice( $popular_files, 0 , 5 );
			for ($n = 0; $n < count( $response['popular_files'] ); $n++) {
				$response['popular_files'][$n]['color'] =
					\NetDNAPresentation::get_file_group_color(
						$response['popular_files'][$n]['group'] );
			}

			$account = $api->get_account();
			$response['account_status'] = \NetDNAPresentation::get_account_status( $account['status'] );
			$response['url_manage'] = 'https://cp.maxcdn.com/zones/pull/' . $zone_id;
			$response['url_reports'] = 'https://cp.maxcdn.com/reporting/' . $zone_id;
		} catch ( \Exception $ex ) {
			$response['error'] = $ex->getMessage();
		}

		echo json_encode( $response );
	}

	/**
	 * Dashboard setup action
	 *
	 * @return void
	 */
	function wp_dashboard_setup() {
		Util_Widget::add( 'w3tc_maxcdn',
			'<div class="w3tc-widget-maxcdn-logo"></div>',
			array( $this, 'widget_maxcdn' ),
			Util_Ui::admin_url( 'admin.php?page=w3tc_cdn' ),
			'normal' );
	}

	/**
	 * Loads and configures NetDNA widget to be used in WP Dashboards.
	 *
	 * @param unknown $widget_id
	 * @param array   $form_inputs
	 */
	function widget_maxcdn( $widget_id, $form_inputs = array() ) {
		if ( $this->authorized && $this->have_zone &&
				$this->_config->get_integer( 'cdn.maxcdn.zone_id' ) ) {
			include dirname( __FILE__ ) . DIRECTORY_SEPARATOR .
				'Cdn_Plugin_WidgetMaxCdn_View_Authorized.php';
		} else {
			include dirname( __FILE__ ) . DIRECTORY_SEPARATOR .
				'Cdn_Plugin_WidgetMaxCdn_View_Unauthorized.php';
		}
	}



	public function enqueue() {
		wp_enqueue_style( 'w3tc-widget' );
		wp_enqueue_style( 'w3tc_maxcdn_widget',
			plugins_url( 'Cdn_Plugin_WidgetMaxCdn_View.css', W3TC_FILE ) );
		wp_enqueue_script( 'w3tc-metadata' );
		wp_enqueue_script( 'w3tc-widget' );
		wp_enqueue_script( 'google-jsapi', 'https://www.google.com/jsapi');
		wp_enqueue_script( 'google-jsapi', 'https://www.google.com/jsapi');
		wp_enqueue_script( 'w3tc_maxcdn_widget',
			plugins_url( 'Cdn_Plugin_WidgetMaxCdn_View.js', W3TC_FILE ),
			array( 'jquery' ), '1.0' );
	}
}
