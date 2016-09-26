<?php
namespace W3TC;



class Extension_CloudFlare_Popup {
	static public function w3tc_ajax() {
		$o = new Extension_CloudFlare_Popup();

		add_action( 'w3tc_ajax_extension_cloudflare_intro',
			array( $o, 'w3tc_ajax_extension_cloudflare_intro' ) );
		add_action( 'w3tc_ajax_extension_cloudflare_intro_done',
			array( $o, 'w3tc_ajax_extension_cloudflare_intro_done' ) );
		add_action( 'w3tc_ajax_extension_cloudflare_zones_done',
			array( $o, 'w3tc_ajax_extension_cloudflare_zones_done' ) );
	}



	public function w3tc_ajax_extension_cloudflare_intro() {
		$c = Dispatcher::config();

		$details = array(
			'email' => $c->get_string( array( 'cloudflare', 'email' ) ),
			'key' => $c->get_string( array( 'cloudflare', 'key' ) )
		);

		include  W3TC_DIR . '/Extension_CloudFlare_Popup_View_Intro.php';
		exit();
	}



	public function w3tc_ajax_extension_cloudflare_intro_done() {
		$this->_render_extension_cloudflare_zones( array(
				'email' => $_REQUEST['email'],
				'key' => $_REQUEST['key'] ) );
	}



	private function _render_extension_cloudflare_zones( $details ) {
		$email = $details['email'];
		$key = $details['key'];

		$details = array(
			'email' => $email,
			'key' => $key
		);

		try {
			$api = new Extension_CloudFlare_Api( array(
					'email' => $email,
					'key' => $key ) );
			$zones = $api->zones();
		} catch ( \Exception $ex ) {
			$details['error_message'] = 'Can\'t authenticate: ' .
				$ex->getMessage();
			include  W3TC_DIR . '/Extension_CloudFlare_Popup_View_Intro.php';
			exit();
		}

		$details['zones'] = $zones;

		include  W3TC_DIR . '/Extension_CloudFlare_Popup_View_Zones.php';
		exit();
	}



	public function w3tc_ajax_extension_cloudflare_zones_done() {
		$email = $_REQUEST['email'];
		$key = $_REQUEST['key'];
		$zone_id = Util_Request::get( 'zone_id' );

		if ( empty( $zone_id ) ) {
			return $this->_render_extension_cloudflare_zones( array(
					'email' => $email,
					'key' => $key,
					'error_message' => 'Please select zone'
				) );
		}

		$zone_name = '';

		try {
			$api = new Extension_CloudFlare_Api( array(
					'email' => $email,
					'key' => $key ) );
			$zones = $api->zones();
			foreach ( $zones as $z ) {
				if ( $z['id'] == $zone_id )
					$zone_name = $z['name'];
			}
		} catch ( \Exception $ex ) {
			$details['error_message'] = 'Can\'t authenticate: ' .
				$ex->getMessage();
			include  W3TC_DIR . '/Extension_CloudFlare_Popup_View_Intro.php';
			exit();
		}

		$c = Dispatcher::config();

		$c->set( array( 'cloudflare', 'email' ), $email );
		$c->set( array( 'cloudflare', 'key' ), $key );
		$c->set( array( 'cloudflare', 'zone_id' ), $zone_id );
		$c->set( array( 'cloudflare', 'zone_name' ), $zone_name );
		$c->save();

		delete_transient( 'w3tc_cloudflare_stats' );

		$postfix = Util_Admin::custom_message_id( array(),
			array(
				'extension_cloudflare_configuration_saved' =>
				'CloudFlare credentials are saved successfully' ) );
		echo 'Location admin.php?page=w3tc_extensions&extension=cloudflare&' .
			'action=view&' . $postfix;
		exit();
	}
}
