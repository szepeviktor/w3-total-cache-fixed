<?php
namespace W3TC;

class Cdn_Plugin_Admin {
	function run() {
		$config_labels = new Cdn_ConfigLabels();
		add_filter( 'w3tc_config_labels', array( $config_labels, 'config_labels' ) );

		$c = Dispatcher::config();
		$cdn_engine = $c->get_string( 'cdn.engine' );

		if ( $c->get_boolean( 'cdn.enabled' ) &&
			!Cdn_Util::is_engine_fsd( $cdn_engine ) ) {
			$admin_notes = new Cdn_AdminNotes();
			add_filter( 'w3tc_notes', array( $admin_notes, 'w3tc_notes' ) );
			add_filter( 'w3tc_errors', array( $admin_notes, 'w3tc_errors' ) );
		}


		// attach to actions without firing class loading at all without need
		if ( $cdn_engine == 'cloudfront_fsd' ) {
			add_action( 'admin_print_scripts-performance_page_w3tc_cdn', array(
					'\W3TC\Cdn_CloudFrontFsd_Page',
					'admin_print_scripts_w3tc_cdn' ) );
			add_action( 'w3tc_ajax', array(
					'\W3TC\Cdn_CloudFrontFsd_Popup',
					'w3tc_ajax' ) );
			add_action( 'w3tc_settings_cdn', array(
					'\W3TC\Cdn_CloudFrontFsd_Page',
					'w3tc_settings_cdn' ) );
		} elseif ( $cdn_engine == 'google_drive' ) {
			add_action( 'admin_print_scripts-performance_page_w3tc_cdn', array(
					'\W3TC\Cdn_GoogleDrive_Page',
					'admin_print_scripts_w3tc_cdn' ) );
			add_action( 'w3tc_settings_cdn_boxarea_configuration', array(
					'\W3TC\Cdn_GoogleDrive_Page',
					'w3tc_settings_cdn_boxarea_configuration'
				) );
		} elseif ( $cdn_engine == 'highwinds' ) {
			add_action( 'admin_print_scripts-performance_page_w3tc_cdn', array(
					'\W3TC\Cdn_Highwinds_Page',
					'admin_print_scripts_w3tc_cdn' ) );
			add_action( 'w3tc_ajax', array(
					'\W3TC\Cdn_Highwinds_Popup',
					'w3tc_ajax' ) );
			add_action( 'admin_init_w3tc_dashboard', array(
					'\W3TC\Cdn_Highwinds_Widget',
					'admin_init_w3tc_dashboard' ) );
			add_action( 'w3tc_ajax_cdn_highwinds_widgetdata', array(
					'\W3TC\Cdn_Highwinds_Widget',
					'w3tc_ajax_cdn_highwinds_widgetdata' ) );
			add_action( 'w3tc_settings_cdn_boxarea_configuration', array(
					'\W3TC\Cdn_Highwinds_Page',
					'w3tc_settings_cdn_boxarea_configuration' ) );
		} elseif ( $cdn_engine == 'maxcdn_fsd' ) {
			add_action( 'admin_print_scripts-performance_page_w3tc_cdn', array(
					'\W3TC\Cdn_MaxCdnFsd_Page',
					'admin_print_scripts_w3tc_cdn' ) );
			add_action( 'w3tc_ajax', array(
					'\W3TC\Cdn_MaxCdnFsd_Popup',
					'w3tc_ajax' ) );
			add_action( 'w3tc_settings_cdn', array(
					'\W3TC\Cdn_MaxCdnFsd_Page',
					'w3tc_settings_cdn' ) );
		} elseif ( $cdn_engine == 'rackspace_cdn' ) {
			add_filter( 'w3tc_admin_actions', array(
					'\W3TC\Cdn_RackSpaceCdn_Page',
					'w3tc_admin_actions' ) );
			add_action( 'admin_print_scripts-performance_page_w3tc_cdn', array(
					'\W3TC\Cdn_RackSpaceCdn_Page',
					'admin_print_scripts_w3tc_cdn' ) );
			add_action( 'w3tc_ajax', array(
					'\W3TC\Cdn_RackSpaceCdn_Popup',
					'w3tc_ajax' ) );
			add_action( 'w3tc_settings_cdn_boxarea_configuration', array(
					'\W3TC\Cdn_RackSpaceCdn_Page',
					'w3tc_settings_cdn_boxarea_configuration' ) );
		} elseif ( $cdn_engine == 'rscf' ) {
			add_action( 'admin_print_scripts-performance_page_w3tc_cdn', array(
					'\W3TC\Cdn_RackSpaceCloudFiles_Page',
					'admin_print_scripts_w3tc_cdn' ) );
			add_action( 'w3tc_ajax', array(
					'\W3TC\Cdn_RackSpaceCloudFiles_Popup',
					'w3tc_ajax' ) );
			add_action( 'w3tc_settings_cdn_boxarea_configuration', array(
					'\W3TC\Cdn_RackSpaceCloudFiles_Page',
					'w3tc_settings_cdn_boxarea_configuration' ) );
		}

		add_action( 'w3tc_settings_general_boxarea_cdn', array(
				$this,
				'w3tc_settings_general_boxarea_cdn'
			) );
	}



	public function w3tc_settings_general_boxarea_cdn() {
		$config = Dispatcher::config();

		$engine_optgroups = array();
		$engine_values = array();

		$is_fsd = Util_Environment::is_w3tc_pro( $config );

		if ( $is_fsd ) {
			$engine_optgroups[] = __( 'Full Site Delivery:', 'w3-total-cache' );
			$engine_values['cloudfront_fsd'] = array(
				'label' => __( 'Amazon CloudFront', 'w3-total-cache' ),
				'optgroup' => 0
			);
			$engine_values['maxcdn_fsd'] = array(
				'label' => __( 'MaxCDN (recommended)', 'w3-total-cache' ),
				'optgroup' => 0
			);

			$optgroup_pull = count( $engine_optgroups );
			$engine_optgroups[] = __( 'Origin Pull / Mirror:', 'w3-total-cache' );
		} else {
			$optgroup_pull = count( $engine_optgroups );
			$engine_optgroups[] = __( 'Origin Pull / Mirror:', 'w3-total-cache' );
		}

		$optgroup_push = count( $engine_optgroups );
		$engine_optgroups[] = __( 'Origin Push:', 'w3-total-cache' );


		$engine_values['akamai'] = array(
			'label' => __( 'Akamai', 'w3-total-cache' ),
			'optgroup' => $optgroup_pull
		);
		$engine_values['cf2'] = array(
			'label' => __( 'Amazon CloudFront', 'w3-total-cache' ),
			'disabled' => ( !Util_Installed::curl() ? true : null ),
			'optgroup' => $optgroup_pull
		);
		$engine_values['att'] = array(
			'label' => __( 'AT&amp;T', 'w3-total-cache' ),
			'optgroup' => $optgroup_pull
		);
		$engine_values['cotendo'] = array(
			'label' => __( 'Cotendo (Akamai)', 'w3-total-cache' ),
			'optgroup' => $optgroup_pull
		);
		$engine_values['mirror'] = array(
			'label' => __( 'Generic Mirror', 'w3-total-cache' ),
			'optgroup' => $optgroup_pull
		);
		$engine_values['highwinds'] = array(
			'label' => __( 'Highwinds', 'w3-total-cache' ),
			'optgroup' => $optgroup_pull
		);
		$engine_values['maxcdn'] = array(
			'label' => __( 'MaxCDN', 'w3-total-cache' ),
			'optgroup' => $optgroup_pull
		);
		$engine_values['netdna'] = array(
			'label' => __( 'MaxCDN Enterprise (NetDNA)', 'w3-total-cache' ),
			'optgroup' => $optgroup_pull
		);
		$engine_values['rackspace_cdn'] = array(
			'label' => __( 'RackSpace CDN', 'w3-total-cache' ),
			'optgroup' => $optgroup_pull
		);
		$engine_values['edgecast'] = array(
			'label' => __( 'Verizon Digital Media Services (EdgeCast) / Media Temple ProCDN', 'w3-total-cache' ),
			'optgroup' => $optgroup_pull
		);
		$engine_values['cf'] = array(
			'disabled' => ( !Util_Installed::curl() ? true : null ),
			'label' => __( 'Amazon CloudFront', 'w3-total-cache' ),
			'optgroup' => $optgroup_push
		);
		$engine_values['s3'] = array(
			'disabled' => ( !Util_Installed::curl() ? true : null ),
			'label' => __( 'Amazon Simple Storage Service (S3)', 'w3-total-cache' ),
			'optgroup' => $optgroup_push
		);
		$engine_values['s3_compatible'] = array(
			'disabled' => ( !Util_Installed::curl() ? true : null ),
			'label' => __( 'Amazon Simple Storage Service (S3) Compatible', 'w3-total-cache' ),
			'optgroup' => $optgroup_push
		);
		$engine_values['google_drive'] = array(
			'label' => __( 'Google Drive', 'w3-total-cache' ),
			'optgroup' => $optgroup_push
		);
		$engine_values['azure'] = array(
			'label' => __( 'Microsoft Azure Storage', 'w3-total-cache' ),
			'optgroup' => $optgroup_push
		);
		$engine_values['rscf'] = array(
			'disabled' => ( !Util_Installed::curl() ? true : null ),
			'label' => __( 'Rackspace Cloud Files', 'w3-total-cache' ),
			'optgroup' => $optgroup_push
		);
		$engine_values['ftp'] = array(
			'disabled' => ( !Util_Installed::ftp() ? true : null ),
			'label' => __( 'Self-hosted / File Transfer Protocol Upload', 'w3-total-cache' ),
			'optgroup' => $optgroup_push
		);

		$cdn_enabled = $config->get_boolean( 'cdn.enabled' );

		$cdn_engine = $config->get_string( 'cdn.engine' );

		$tag = '';
		if ( $cdn_engine == 'cloudfront_fsd' )
			$tag = '#cdn-fsd-cloudfront';
		elseif ( $cdn_engine == 'maxcdn_fsd' )
			$tag = '#cdn-fsd-maxcdn';

		if ( empty( $tag ) )
			$cdn_engine_extra_description = '';
		else
			$cdn_engine_extra_description =
				' See <a href="admin.php?page=w3tc_faq' . $tag .
				'">setup instructions</a>';

		include  W3TC_DIR . '/Cdn_GeneralPage_View.php';
	}
}
