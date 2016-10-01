<?php
namespace W3TC;

class Support_Page {
	/**
	 * called from Generic_Plugin_Admin on action
	 */
	static public function admin_print_scripts_w3tc_support() {
		$url = get_home_url();
		if ( substr( $url, 0, 7 ) == 'http://' )
			$url = substr( $url, 7 );
		elseif ( substr( $url, 0, 8 ) == 'https://' )
			$url = substr( $url, 8 );
		// aw3tc-options script is already queued so attach to it
		// just to make vars printed (while it's not related by semantics)
		wp_localize_script( 'w3tc-options',
			'w3tc_support_postprocess', urlencode( urlencode(
					Util_Ui::admin_url(
						wp_nonce_url( 'admin.php', 'w3tc' ) . '&page=w3tc_support&done'
					) ) ) );
		wp_localize_script( 'w3tc-options',
			'w3tc_support_home_url', $url );
		wp_localize_script( 'w3tc-options',
			'w3tc_support_email', get_bloginfo( 'admin_email' ) );
		$u = wp_get_current_user();
		wp_localize_script( 'w3tc-options',
			'w3tc_support_first_name', $u->first_name );
		wp_localize_script( 'w3tc-options',
			'w3tc_support_last_name', $u->last_name );

		// values from widget
		$w3tc_support_form_hash = 'm5pom8z0qy59rm';
		$w3tc_support_field_name = '';
		$w3tc_support_field_value = '';

		if ( isset( $_GET['service_item'] ) ) {
			$pos = (int) $_GET['service_item'];

			$v = get_site_option( 'w3tc_generic_widgetservices' );
			try {
				$v = json_decode( $v, true );
				if ( isset( $v['items'] ) && isset( $v['items'][$pos] )) {
					$i = $v['items'][$pos];
					$w3tc_support_form_hash = $i['form_hash'];
					$w3tc_support_field_name = $i['parameter_name'];
					$w3tc_support_field_value = $i['parameter_value'];
				}
			} catch ( \Exception $e ) {
			}
		}

		wp_localize_script( 'w3tc-options', 'w3tc_support_form_hash',
			$w3tc_support_form_hash );
		wp_localize_script( 'w3tc-options', 'w3tc_support_field_name',
			$w3tc_support_field_name );
		wp_localize_script( 'w3tc-options', 'w3tc_support_field_value',
			$w3tc_support_field_value );
	}
	/**
	 * Support tab
	 *
	 * @return void
	 */
	function options() {
		if ( isset( $_GET['done'] ) ) {
			$postprocess_url =
				'admin.php?page=w3tc_support&w3tc_support_send_details' .
				'&_wpnonce=' . urlencode( $_GET['_wpnonce'] );
			foreach ( $_GET as $p => $v ) {
				if ( $p != 'page' && $p != '_wpnonce' && $p != 'done' )
					$postprocess_url .= '&' . urlencode( $p ) . '=' . urlencode( $v );
			}
			include  W3TC_DIR . '/Support_Page_View_DoneContent.php';
		} else
			include  W3TC_DIR . '/Support_Page_View_PageContent.php';
	}
}
