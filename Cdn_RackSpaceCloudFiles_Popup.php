<?php
namespace W3TC;



class Cdn_RackSpaceCloudFiles_Popup {
	static public function w3tc_ajax() {
		$o = new Cdn_RackSpaceCloudFiles_Popup();

		add_action( 'w3tc_ajax_cdn_rackspace_authenticate',
			array( $o, 'w3tc_ajax_cdn_rackspace_authenticate' ) );
		add_action( 'w3tc_ajax_cdn_rackspace_intro_done',
			array( $o, 'w3tc_ajax_cdn_rackspace_intro_done' ) );
		add_action( 'w3tc_ajax_cdn_rackspace_regions_done',
			array( $o, 'w3tc_ajax_cdn_rackspace_regions_done' ) );
		add_action( 'w3tc_ajax_cdn_rackspace_containers_done',
			array( $o, 'w3tc_ajax_cdn_rackspace_containers_done' ) );
	}



	public function w3tc_ajax_cdn_rackspace_authenticate() {
		$c = Dispatcher::config();

		$details = array(
			'user_name' => $c->get_string( 'cdn.rscf.user' ),
			'api_key' => $c->get_string( 'cdn.rscf.key' )
		);

		include  W3TC_DIR . '/Cdn_RackSpaceCloudFiles_Popup_View_Intro.php';
		exit();
	}



	public function w3tc_ajax_cdn_rackspace_intro_done() {
		$user_name = $_REQUEST['user_name'];
		$api_key = $_REQUEST['api_key'];

		try {
			$r = Cdn_RackSpace_Api_Tokens::authenticate( $user_name,
				$api_key );
		} catch ( \Exception $ex ) {
			$details = array(
				'user_name' => $user_name,
				'api_key' => $api_key,
				'error_message' => 'Can\'t authenticate: ' . $ex->getMessage()
			);
			include  W3TC_DIR . '/Cdn_RackSpaceCloudFiles_Popup_View_Intro.php';
			exit();
		}

		$r['regions'] = Cdn_RackSpace_Api_Tokens::cloudfiles_services_by_region(
			$r['services'] );

		$details = array(
			'user_name' => $user_name,
			'api_key' => $api_key,
			'access_token' => $r['access_token'],
			'region_descriptors' => $r['regions'],
			// avoid fights with quotes, magic_quotes may break randomly
			'region_descriptors_serialized' =>
			strtr( json_encode( $r['regions'] ), '"\\', '!^' )
		);

		include  W3TC_DIR . '/Cdn_RackSpaceCloudFiles_Popup_View_Regions.php';
		exit();
	}



	public function w3tc_ajax_cdn_rackspace_regions_done() {
		$user_name = $_REQUEST['user_name'];
		$api_key = $_REQUEST['api_key'];
		$access_token = $_REQUEST['access_token'];
		$region = Util_Request::get( 'region' );
		$region_descriptors = json_decode(
			strtr( $_REQUEST['region_descriptors'], '!^', '"\\' ), true );

		if ( !isset( $region_descriptors[$region] ) ) {
			$details = array(
				'user_name' => $user_name,
				'api_key' => $api_key,
				'error_message' => 'Please select region ' . $region
			);
			include  W3TC_DIR . '/Cdn_RackSpaceCloudFiles_Popup_View_Intro.php';
			exit();
		}

		$api = new Cdn_RackSpace_Api_CloudFilesCdn( array(
				'access_token' => $access_token,
				'access_region_descriptor' => $region_descriptors[$region],
				'new_access_required' => ''
			) );

		try {
			$containers = $api->containers();
		} catch ( \Exception $ex ) {
			$details = array(
				'user_name' => $user_name,
				'api_key' => $api_key,
				'error_message' => $ex->getMessage()
			);
			include  W3TC_DIR . '/Cdn_RackSpaceCloudFiles_Popup_View_Intro.php';
			exit();
		}

		$details = array(
			'user_name' => $user_name,
			'api_key' => $api_key,
			'access_token' => $access_token,
			'access_region_descriptor_serialized' =>
			strtr( json_encode( $region_descriptors[$region] ), '"\\', '!^' ),
			'region' => $region,
			// avoid fights with quotes, magic_quotes may break randomly
			'containers' => $containers
		);

		include  W3TC_DIR . '/Cdn_RackSpaceCloudFiles_Popup_View_Containers.php';
		exit();
	}



	public function w3tc_ajax_cdn_rackspace_containers_done() {
		$user_name = $_REQUEST['user_name'];
		$api_key = $_REQUEST['api_key'];
		$access_token = $_REQUEST['access_token'];
		$access_region_descriptor = json_decode(
			strtr( $_REQUEST['access_region_descriptor'], '!^', '"\\' ), true );
		$region = $_REQUEST['region'];
		$container = Util_Request::get( 'container' );

		$api_files = new Cdn_RackSpace_Api_CloudFiles( array(
				'access_token' => $access_token,
				'access_region_descriptor' => $access_region_descriptor,
				'new_access_required' => ''
			) );
		$api_cdn = new Cdn_RackSpace_Api_CloudFilesCdn( array(
				'access_token' => $access_token,
				'access_region_descriptor' => $access_region_descriptor,
				'new_access_required' => ''
			) );

		try {
			if ( empty( $container ) ) {
				$container_new = $_REQUEST['container_new'];
				if ( empty( $container_new ) )
					throw new \Exception( 'Please select container' );

				$api_files->container_create( $container_new );
				$api_cdn->container_cdn_enable( $container_new );
				$container = $container_new;
			}
		} catch ( \Exception $ex ) {
			$containers = $api_cdn->containers();
			$details = array(
				'user_name' => $user_name,
				'api_key' => $api_key,
				'access_token' => $access_token,
				// avoid fights with quotes, magic_quotes may break randomly
				'access_region_descriptor_serialized' =>
				strtr( json_encode( $access_region_descriptor ), '"\\', '!^' ),
				'region' => $region,
				'containers' => $containers
			);
			$details['error_message'] = $ex->getMessage();
			include  W3TC_DIR . '/Cdn_RackSpaceCloudFiles_Popup_View_Containers.php';
			exit();
		}

		$c = Dispatcher::config();

		$c->set( 'cdn.rscf.user', $user_name );
		$c->set( 'cdn.rscf.key', $api_key );
		$c->set( 'cdn.rscf.location', $region );
		$c->set( 'cdn.rscf.container', $container );
		$c->save();

		// reset calculated state
		$state = Dispatcher::config_state();
		$state->set( 'cdn.rackspace_cf.access_state', '' );
		$state->save();

		$postfix = Util_Admin::custom_message_id( array(),
			array(
				'cdn_configuration_saved' =>
				'CDN credentials are saved successfully' ) );
		echo 'Location admin.php?page=w3tc_cdn&' . $postfix;
		exit();
	}
}
