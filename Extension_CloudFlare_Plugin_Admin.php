<?php
namespace W3TC;

class Extension_CloudFlare_Plugin_Admin {
	private $_config;
	private $api;



	static public function w3tc_extensions( $extensions, $config ) {
		$current_user = wp_get_current_user();

		$message = array();
		$message[] = 'CloudFlare';
		$cloudflare_signup_email = '';
		$cloudflare_signup_user = '';

		if ( is_a( $current_user, 'WP_User' ) ) {
			if ( $current_user->user_email ) {
				$cloudflare_signup_email = $current_user->user_email;
			}

			if ( $current_user->user_login && $current_user->user_login != 'admin' ) {
				$cloudflare_signup_user = $current_user->user_login;
			}
		}
		$extensions['cloudflare'] = array (
			'name' => 'CloudFlare',
			'author' => 'W3 EDGE',
			'description' =>  sprintf( __( 'CloudFlare protects and accelerates websites. <a href="%s" target="_blank">Sign up now for free</a> to get started,
            or if you have an account simply log in to obtain your <abbr title="Application Programming Interface">API</abbr> key from the <a target="_blank" href="https://www.cloudflare.com/my-account">account page</a> to enter it on the General Settings box that appears after plugin activation.
            Contact the CloudFlare <a href="http://www.cloudflare.com/help.html" target="_blank">support team</a> with any questions.', 'w3-total-cache' ), 'https://www.cloudflare.com/sign-up.html?affiliate=w3edge&amp;seed_domain=' . Util_Environment::host() . '&amp;email=' . htmlspecialchars( $cloudflare_signup_email ) . '&amp;username=' . htmlspecialchars( $cloudflare_signup_user ) ),
			'author_uri' => 'https://www.w3-edge.com/',
			'extension_uri' => 'https://www.w3-edge.com/',
			'extension_id' => 'cloudflare',
			'settings_exists' => true,
			'version' => '0.3',
			'enabled' => true,
			'requirements' => implode( ', ', $message ),
			'path' => 'w3-total-cache/Extension_CloudFlare_Plugin.php'
		);

		return $extensions;
	}



	function run() {
		$c = Dispatcher::config();
		$this->api = new Extension_CloudFlare_Api( array(
				'email' => $c->get_string( array( 'cloudflare', 'email' ) ),
				'key' => $c->get_string( array( 'cloudflare', 'key' ) ),
				'zone_id' => $c->get_string( array( 'cloudflare', 'zone_id' ) ),
				'timelimit_api_request' => $c->get_integer(
					array( 'cloudflare', 'timelimit.api_request' ) )
			)
		);
		$this->_config = $c;

		add_filter( 'w3tc_dashboard_actions',
			array( $this, 'w3tc_dashboard_actions' ) );

		$widget = new Extension_CloudFlare_Widget();
		$widget->init();

		// modify settings page
		add_filter( 'w3tc_ui_config_item_cdnfsd.enabled',
			array( $this, 'w3tc_ui_config_item_cdnfsd_enabled' ) );
		add_filter( 'w3tc_ui_config_item_cdnfsd.engine',
			array( $this, 'w3tc_ui_config_item_cdnfsd_engine' ) );

		add_filter( 'w3tc_settings_general_anchors',
			array( $this, 'w3tc_settings_general_anchors' ) );
		add_action( 'w3tc_settings_general_boxarea_cloudflare',
			array( $this, 'w3tc_settings_general_boxarea_cloudflare' ) );

		add_action( 'wp_ajax_w3tc_cloudflare_api_request',
			array( $this, 'action_cloudflare_api_request' ) );

		// modify main menu
		add_filter( 'w3tc_admin_bar_menu', array( $this, 'w3tc_admin_bar_menu' ) );

		// dashboard
		add_action( 'admin_print_scripts-toplevel_page_w3tc_dashboard',
			array( $this, 'admin_print_scripts_w3tc_dashboard' ) );
		add_filter( 'w3tc_admin_actions', array( $this, 'w3tc_admin_actions' ) );

		// own settings page
		add_action( 'w3tc_extension_page_cloudflare', array(
				'\W3TC\Extension_CloudFlare_Page',
				'w3tc_extension_page_cloudflare'
			) );
		add_action( 'admin_print_scripts-performance_page_w3tc_extensions',
			array( '\W3TC\Extension_CloudFlare_Page',
				'admin_print_scripts_w3tc_extensions'
			) );

		add_action( 'w3tc_ajax',
			array( '\W3TC\Extension_CloudFlare_Popup', 'w3tc_ajax' ) );

		$cdnfsd_engine = $c->get_string( 'cdnfsd.engine' );

		if ( empty( $cdnfsd_engine ) || $cdnfsd_engine == 'cloudflare' ) {
			add_action( 'w3tc_settings_box_cdnfsd', array(
					'\W3TC\Extension_CloudFlare_Page',
					'w3tc_settings_box_cdnfsd'
				) );
			add_action( 'admin_print_scripts-performance_page_w3tc_cdn',
				array( '\W3TC\Extension_CloudFlare_Page',
					'admin_print_scripts_w3tc_extensions'
				) );
		}

		// add check to comments page
		add_filter( 'comment_row_actions', array( $this, 'comment_row_actions' ),
			10, 2 );
		add_action( 'admin_print_styles-edit-comments.php',
			array( $this, 'admin_print_styles_edit_comments' ) );
		add_action( 'admin_print_scripts-edit-comments.php',
			array( $this, 'admin_print_scripts_edit_comments' ) );
		add_action( 'wp_ajax_w3tc_cloudflare_ip_check',
			array( $this, 'w3tc_cloudflare_ip_check' ) );

		// add notices about api health
		if ( Util_Admin::is_w3tc_admin_page() ) {
			add_action( 'admin_notices', array(
					$this,
					'admin_notices'
				) );
			add_action( 'network_admin_notices', array(
					$this,
					'admin_notices'
				) );
		}

		$this->check_ip_versions();
	}



	public function admin_notices() {
		$plugins = get_plugins();
		if ( array_key_exists( 'cloudflare/cloudflare.php', $plugins ) && $this->_config->get_boolean( 'notes.cloudflare_plugin' ) ) {

			echo sprintf( '<div class="error"><p>%s %s</p></div>', __( 'CloudFlare plugin detected. We recommend removing the
            plugin as it offers no additional capabilities when W3 Total Cache is installed. This message will disappear
            when CloudFlare is removed.', 'w3-total-cache' ),
				Util_Ui::button_hide_note( 'Hide this message', 'cloudflare_plugin' )
			);
		}
	}



	public function w3tc_admin_bar_menu( $menu_items ) {
		$menu_items['20810.cloudflare'] = array(
			'id' => 'w3tc_flush_cloudflare',
			'parent' => 'w3tc_flush',
			'title' => __( 'CloudFlare: All', 'w3-total-cache' ),
			'href' => wp_nonce_url( network_admin_url(
					'admin.php?page=w3tc_dashboard&amp;w3tc_cloudflare_flush' ), 'w3tc' )
		);

		return $menu_items;
	}



	/**
	 * Check if last check has expired. If so update CloudFlare ips
	 */
	function check_ip_versions() {
		$state = Dispatcher::config_state_master();

		if ( $state->get_integer( 'extension.cloudflare.next_ips_check' ) < time() ) {
			// update asap to avoid multiple processes entering the check
			$state->set( 'extension.cloudflare.next_ips_check',
				time() + 7 * 24 * 60 * 60 );

			$data = array();
			try {
				$data = $this->api->get_ip_ranges();
			} catch ( \Exception $ex ) {
			}

			if ( isset( $data['ip4'] ) ) {
				$state->set( 'extension.cloudflare.ips.ip4', $data['ip4'] );
			}
			if ( isset( $data['ip6'] ) ) {
				$state->set( 'extension.cloudflare.ips.ip6', $data['ip6'] );
			}

			$state->save();
		}
	}



	/**
	 * Send CloudFlare API request
	 *
	 * @return void
	 */
	function action_cloudflare_api_request() {
		$result = false;
		$response = null;

		$actions = array(
			'dev_mode',
			'sec_lvl',
			'fpurge_ts'
		);

		$email = Util_Request::get_string( 'email' );
		$key = Util_Request::get_string( 'key' );
		$zone = Util_Request::get_string( 'zone' );
		$action = Util_Request::get_string( 'command' );
		$value = Util_Request::get_string( 'value' );
		$nonce = Util_Request::get_string( '_wpnonce' );

		if ( !wp_verify_nonce( $nonce, 'w3tc' ) ) {
			$error ='Access denied.';
		} elseif ( !$email ) {
			$error = 'Empty email.';
		} elseif ( !filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
			$error = 'Invalid email.';
		} elseif ( !$key ) {
			$error = 'Empty key.';
		} elseif ( !$zone ) {
			$error = 'Empty zone.';
		} elseif ( strpos( $zone, '.' ) === false ) {
			$error = 'Invalid domain.';
		} elseif ( !in_array( $action, $actions ) ) {
			$error = 'Invalid action.';
		} else {
			$config = array(
				'email' => $email,
				'key' => $key,
				'zone' => $zone
			);

			@$this->api = new Extension_CloudFlare_Api( $config );

			@set_time_limit( $this->_config->get_integer( array( 'cloudflare', 'timelimit.api_request' ) ) );
			$response = $this->api->api_request( $action, $value );

			if ( $response ) {
				if ( $response->result == 'success' ) {
					$result = true;
					$error = 'OK';
				} else {
					$error = $response->msg;
				}
			} else {
				$error = 'Unable to make CloudFlare API request.';
			}
		}

		$return = array(
			'result' => $result,
			'error' => $error,
			'response' => $response
		);

		echo json_encode( $return );
		exit();
	}



	/**
	 * W3TC Dashboard page modifications
	 */
	public function w3tc_admin_actions( $handlers ) {
		$handlers['cloudflare'] = 'Extension_CloudFlare_AdminActions';
		return $handlers;
	}



	public function admin_print_scripts_w3tc_dashboard() {
		wp_enqueue_script( 'w3tc_extension_cloudflare_dashboard',
			plugins_url( 'Extension_CloudFlare_View_Dashboard.js', W3TC_FILE ),
			array( 'jquery' ), '1.0' );
	}



	public function w3tc_dashboard_actions( $actions ) {
		$email = $this->_config->get_string( array( 'cloudflare', 'email' ) );
		$key = $this->_config->get_string( array( 'cloudflare', 'key' ) );

		if ( empty( $email ) || empty( $key ) )
			return $actions;

		$modules = Dispatcher::component( 'ModuleStatus' );
		$can_empty_memcache = $modules->can_empty_memcache();
		$can_empty_opcode = $modules->can_empty_opcode();
		$can_empty_file = $modules->can_empty_file();
		$can_empty_varnish = $modules->can_empty_varnish();

		$actions[] = ' or <input id="flush_all_except_cf" class="button" type="submit" name="w3tc_cloudflare_flush_all_except_cf" value="'.
			__( 'empty all caches except CloudFlare', 'w3-total-cache' ).'"'.
			( ( ! $can_empty_memcache && ! $can_empty_opcode && ! $can_empty_file && ! $can_empty_varnish ) ?
			'disabled="disabled"':'' ) . '> ' . __( 'at once', 'w3-total-cache' );
		return $actions;
	}



	public function w3tc_ui_config_item_cdnfsd_enabled( $a ) {
		$c = Dispatcher::config();
		$cdnfsd_engine = $c->get_string( 'cdnfsd.engine' );

		// overwrite behavior if controlled by extension
		if ( empty( $cdnfsd_engine ) || $cdnfsd_engine == 'cloudflare' ) {
			$a['value'] = true;
		}

		return $a;
	}



	public function w3tc_ui_config_item_cdnfsd_engine( $a ) {
		$c = Dispatcher::config();
		$cdnfsd_engine = $c->get_string( 'cdnfsd.engine' );

		// overwrite behavior if controlled by extension
		if ( empty( $cdnfsd_engine ) || $cdnfsd_engine == 'cloudflare' ) {
			$a['value'] = 'cloudflare';
		}

		if ( isset( $a['selectbox_values']['cloudflare'] ) ) {
			$a['selectbox_values']['cloudflare']['label'] = 'CloudFlare';
			$a['selectbox_values']['cloudflare']['disabled'] = null;
		}

		return $a;
	}



	public function w3tc_settings_general_anchors( $anchors ) {
		$anchors[] = array( 'id' => 'cloudflare', 'text' => 'CloudFlare' );
		return $anchors;
	}



	public function w3tc_settings_general_boxarea_cloudflare() {
		$config = $this->_config;
		include  W3TC_DIR . '/Extension_CloudFlare_GeneralPage_View.php';
	}



	/**
	 * Comments page modification
	 */
	public function comment_row_actions( $actions, $comment_id ) {
		$ip = get_comment_author_IP( $comment_id );
		if ( !empty( $ip ) )
			$actions[] = '<a href="#ip=' . urlencode( $ip ) .
				'" class="w3tc_cloudflare_ip_check">CloudFlare IP score</a>';
		return $actions;
	}



	public function admin_print_styles_edit_comments() {
		wp_enqueue_style( 'w3tc_extension_cloudflare_general',
			plugins_url( 'Extension_CloudFlare_View_Comments.css', W3TC_FILE ) );
	}



	public function admin_print_scripts_edit_comments() {
		wp_enqueue_script( 'w3tc_extension_cloudflare_general',
			plugins_url( 'Extension_CloudFlare_View_Comments.js', W3TC_FILE ),
			array( 'jquery' ), '1.0' );
	}



	public function w3tc_cloudflare_ip_check() {
		$api = new Extension_CloudFlare_Api();

		$ip = $_REQUEST['ip'];
		$response = $this->api->ip_lkup( $ip );

		$error = true;

		//var_dump($response);
		if ( !isset( $response->result ) )
			$message = 'API failed';
		else if ( $response->result != 'success' )
				$message = 'API error: ' . $response->msg;
			else if ( !isset( $response->response ) || !isset( $response->response->$ip ) )
					$message = 'no information';
				else if ( !$response->response->$ip ) {
						$message = 'valid IP';
						$error = false;
					} else
					$message = 'invalid IP';

				echo json_encode( array(
						'message' => $message,
						'error' => $error
					) );

			exit();
	}

}
