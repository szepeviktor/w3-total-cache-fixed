<?php
namespace W3TC;



class Licensing_Core {
	/**
	 * Activates a license
	 */
	static public function activate_license( $license, $version ) {
		// data to send in our API request
		$api_params = array(
			'edd_action'=> 'activate_license',
			'license' => $license,   // legacy
			'license_key' => $license,
			'home_url' => network_home_url(),
			'item_name' => urlencode( EDD_W3EDGE_W3TC_NAME ), // the name of our product in EDD
			'r' => rand(),
			'version' => $version
		);

		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, EDD_W3EDGE_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );
		if ( is_wp_error( $response ) )
			return false;

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );
		return $license_data;
	}

	/**
	 * Deactivates a license
	 */
	static public function deactivate_license( $license ) {
		// data to send in our API request
		$api_params = array(
			'edd_action'=> 'deactivate_license',
			'license' => $license,   // legacy
			'license_key' => $license,
			'home_url' => network_home_url(),
			'item_name' => urlencode( EDD_W3EDGE_W3TC_NAME ), // the name of our product in EDD,
			'r' => rand()
		);

		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, EDD_W3EDGE_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) )
			return false;

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "deactivated" or "failed"
		return $license_data->license == 'deactivated';
	}

	/**
	 * Checks if a license key is still valid
	 */
	static public function check_license( $license, $version ) {
		global $wp_version;

		$api_params = array(
			'edd_action' => 'check_license',
			'license' => $license,   // legacy
			'license_key' => $license,
			'home_url' => network_home_url(),
			'item_name' => urlencode( EDD_W3EDGE_W3TC_NAME ),
			'r' => rand(),
			'version' => $version
		);

		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, EDD_W3EDGE_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

		if ( is_wp_error( $response ) )
			return false;
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );
		return $license_data;
	}

	static public function reset_rooturi( $license, $version ) {
		// data to send in our API request
		$api_params = array(
			'edd_action'=> 'reset_rooturi',
			'license_key' => $license,
			'home_url' => network_home_url(),
			'item_name' => urlencode( EDD_W3EDGE_W3TC_NAME ), // the name of our product in EDD
			'r' => rand(),
			'version' => $version
		);

		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, EDD_W3EDGE_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );
		if ( is_wp_error( $response ) )
			return false;

		// decode the license data
		$status = json_decode( wp_remote_retrieve_body( $response ) );
		return $status;
	}
}
