<?php
namespace W3TC;

class Cdn_GoogleDrive_Page {
	// called from plugin-admin
	static public function admin_print_scripts_w3tc_cdn() {
		wp_enqueue_script( 'w3tc_cdn_google_drive',
			plugins_url( 'Cdn_GoogleDrive_Page_View.js', W3TC_FILE ),
			array( 'jquery' ), '1.0' );

		$path = 'admin.php?page=w3tc_cdn';
		$return_url = self_admin_url( $path );

		wp_localize_script( 'w3tc_cdn_google_drive',
			'w3tc_cdn_google_drive_url',
			GOOGLE_DRIVE_AUTHORIZE_URL . '?return_url=' . urlencode( $return_url ) );

		// it's return from google oauth
		if ( isset( $_GET['oa_client_id'] ) ) {
			$path = wp_nonce_url( 'admin.php', 'w3tc' ) .
				'&page=w3tc_cdn&w3tc_cdn_google_drive_auth_return';
			foreach ( $_GET as $key => $value ) {
				if ( substr( $key, 0, 3 ) == 'oa_' )
					$path .= '&' . urlencode( $key ) . '=' . urlencode( $value );
			}

			$popup_url = self_admin_url( $path );

			wp_localize_script( 'w3tc_cdn_google_drive',
				'w3tc_cdn_google_drive_popup_url', $popup_url );

		}
	}



	static public function w3tc_settings_cdn_boxarea_configuration() {
		$config = Dispatcher::config();
		include  W3TC_DIR . '/Cdn_GoogleDrive_Page_View.php';
	}
}
