<?php
namespace W3TC;



class Cdn_MaxCdn_Popup {
	static public function w3tc_ajax() {
		$o = new Cdn_MaxCdn_Popup();

		add_action( 'w3tc_ajax_cdn_maxcdn_intro',
			array( $o, 'w3tc_ajax_cdn_maxcdn_intro' ) );
		add_action( 'w3tc_ajax_cdn_maxcdn_list_zones',
			array( $o, 'w3tc_ajax_cdn_maxcdn_list_zones' ) );
		add_action( 'w3tc_ajax_cdn_maxcdn_view_zone',
			array( $o, 'w3tc_ajax_cdn_maxcdn_view_zone' ) );
		add_action( 'w3tc_ajax_cdn_maxcdn_configure_zone',
			array( $o, 'w3tc_ajax_cdn_maxcdn_configure_zone' ) );
	}



	public function __construct() {
		if ( !class_exists( 'NetDNA' ) )
			require_once W3TC_LIB_NETDNA_DIR . '/NetDNA.php';
	}



	public function w3tc_ajax_cdn_maxcdn_intro() {
		$config = Dispatcher::config();

		$this->render_intro( array(
				'api_key' => $config->get_string( 'cdn.maxcdn.authorization_key' ) ) );
	}



	private function render_intro( $details ) {
		$config = Dispatcher::config();
		$url_obtain_key = Util_Ui::url( array(
				'page' => 'w3tc_dashboard',
				'w3tc_cdn_maxcdn_authorize' => 'y'
			) );

		include  W3TC_DIR . '/Cdn_MaxCdn_Popup_View_Intro.php';
		exit();
	}



	public function w3tc_ajax_cdn_maxcdn_list_zones() {
		$api_key = $_REQUEST['api_key'];

		$api = \NetDNA::create( $api_key );
		if ( !$api->is_valid() ) {
			$this->render_intro( array(
					'api_key' => $api_key,
					'error_message' => 'Can\'t authenticate: API key not valid'
				) );
			exit();
		}

		try {
			$zones = $api->get_pull_zones();
		} catch ( \Exception $ex ) {
			$error_message = 'Can\'t authenticate: ' . $ex->getMessage();

			if ( strpos( $error_message, 'not whitelisted' ) > 0 ) {
				$error_message .= '. You can whitelist IP ' .
					'<a target="_blank" href="https://cp.maxcdn.com/account/api/whitelist">here</a>';
			}
			$this->render_intro( array(
					'api_key' => $api_key,
					'error_message' => $error_message
				) );
			exit();
		}

		$details = array(
			'api_key' => $api_key,
			'zones' => $zones
		);

		include  W3TC_DIR . '/Cdn_MaxCdn_Popup_View_Zones.php';
		exit();
	}



	public function w3tc_ajax_cdn_maxcdn_view_zone() {
		$config = Dispatcher::config();
		$api_key = $_REQUEST['api_key'];
		$zone_id = Util_Request::get( 'zone_id', '' );

		$details = array(
			'api_key' => $api_key,
			'zone_id' => $zone_id,
			'name' => '',
			'url' => array(
				'new' => get_home_url() ),
			'compress' => array(
				'new' => 1 ),
			'ssl' => array(
				// off, dedicated, sni, shared
				'current' => null,
				'new' => null
			),
			'cors_headers' => array(
				'new' => ( $config->get_boolean( 'cdn.cors_header') ? 0 : 1 ) ),
			'domains' => array()
  		);

		if ( empty( $zone_id ) ) {
			// create new zone mode
			$details['name'] = Util_Request::get( 'zone_new_name' );
		} else {
			$api = \NetDNA::create( $api_key );
			try {
				$zone = $api->get_zone( $zone_id );
				$details['domains']['current'] = $api->get_custom_domains( $zone_id );
			} catch ( \Exception $ex ) {
				$this->render_intro( array(
						'api_key' => $api_key,
						'error_message' => 'Can\'t obtain zone: ' . $ex->getMessage()
					) );
				exit();
			}

			$details['name'] = $zone['name'];
			$details['compress']['current'] = $zone['compress'];
			$details['cors_headers']['current'] = $zone['cors_headers'];
			if ( $zone['ssl'] ) {
				$details['ssl']['current'] = 'dedicated';
			} elseif ( $zone['ssl_sni'] ) {
				$details['ssl']['current'] = 'sni';
			} elseif ( $zone['sslshared'] ) {
				$details['ssl']['current'] = 'shared';
			} else {
				$details['ssl']['current'] = 'off';
			}
			$details['url']['current'] = $zone['url'];
		}


		if ( Util_Environment::is_https() &&
			( is_null( $details['ssl']['current'] ) ||
				$details['ssl']['current'] == 'off' ) ) {
			$details['ssl']['new'] = 'shared';
		}

		include  W3TC_DIR . '/Cdn_MaxCdn_Popup_View_Zone.php';
		exit();
	}



	public function w3tc_ajax_cdn_maxcdn_configure_zone() {
		$api_key = $_REQUEST['api_key'];
		$zone_id = Util_Request::get( 'zone_id', '' );

		if ( empty( $zone_id ) ) {
			$zone = array(
				'name' => Util_Request::get( 'name' ),
				'label' => Util_Request::get( 'name' ),
				'url' => Util_Request::get( 'url' ),
				'use_stale' => 1,
				'queries' => 1,
				'compress' => 1,
				'backend_compress' => 1
			);
		} else {
			$zone = array();

			if ( isset( $_REQUEST['url_change'] ) ) {
				$zone['url'] = Util_Request::get( 'url' );
			}
			if ( isset( $_REQUEST['compress_change'] ) ) {
				$zone['compress'] = Util_Request::get( 'compress' );
			}
			if ( isset( $_REQUEST['cors_headers_change'] ) ) {
				$zone['cors_headers'] = Util_Request::get( 'cors_headers' );
			}
			if ( Util_Request::get( 'ssl' ) == 'shared' ) {
				$zone['sslshared'] = 1;
				$zone['http2'] = 1;
			}
		}

		$api = \NetDNA::create( $api_key );

		try {
			if ( empty( $zone_id ) ) {
				$response = $api->create_pull_zone( $zone );
				$zone_id = $response['id'];
			} else {
				if ( count( array_keys( $zone ) ) > 0 ) {
					$response = $api->update_pull_zone( $zone_id, $zone );
				}
			}

			$response = $api->get_zone( $zone_id );
		} catch ( \Exception $ex ) {
			$this->render_intro( array(
					'api_key' => $api_key,
					'error_message' => 'Failed to configure zone: ' .
						$ex->getMessage()
				) );
			exit();
		}

		$c = Dispatcher::config();
		$domains = $c->get( 'cdn.maxcdn.domain' );
		$domains['http_default'] = $response['cdn_url'];
		$domains['https_default'] = $response['ssl_url'];

		$c->set( 'cdn.maxcdn.authorization_key', $api_key );
		$c->set( 'cdn.maxcdn.zone_id', $zone_id );
		$c->set( 'cdn.maxcdn.domain', $domains );
		$c->save();

 		include  W3TC_DIR . '/Cdn_MaxCdn_Popup_View_Success.php';
		exit();
	}



	private function render_zone_textbox_change( $details, $field ) {
		Util_Ui::hidden( '', $field, $details[$field]['new'] );

		if ( !isset( $details[$field]['current'] ) ) {
			echo 'will be set to <strong>';
			echo htmlspecialchars( $details[$field]['new'] );
			echo '</strong>';
		} elseif ( $details[$field]['current'] == $details[$field]['new'] ) {
				echo '<strong>';
				echo htmlspecialchars( $details[$field]['new'] );
				echo '</strong>';
		} else {
			echo 'currently set to <strong>';
			echo htmlspecialchars( $details[$field]['current'] );
			echo '</strong><br />';
			echo '<input type="checkbox" name="' . $field . '_change" value="y"' .
				' checked="checked" /> ';
			echo 'change to <strong>';
			echo htmlspecialchars( $details[$field]['new'] );
			echo '</strong><br />';
		}
	}


	private function render_zone_boolean_change( $details, $field ) {
		Util_Ui::hidden( '', $field, $details[$field]['new'] );

		if ( !isset( $details[$field]['current'] ) ) {
			echo 'will be set to <strong>';
			$this->render_zone_boolean( $details[$field]['new'] );
			echo '</strong>';
		} elseif ( $details[$field]['current'] == $details[$field]['new'] ) {
				echo '<strong>';
				$this->render_zone_boolean( $details[$field]['new'] );
				echo '</strong>';
		} else {
			echo 'currently set to <strong>';
			$this->render_zone_boolean( $details[$field]['current'] );
			echo '</strong><br />';
			echo '<input type="checkbox" name="' . $field . '_change" value="y"' .
				' checked="checked" /> ';
			echo 'change to <strong>';
			$this->render_zone_boolean( $details[$field]['new'] );
			echo '</strong><br />';
		}
	}



	private function render_zone_boolean( $v ) {
		if ( $v == 0 )
			echo 'disabled';
		else
			echo 'enabled';
	}
}
