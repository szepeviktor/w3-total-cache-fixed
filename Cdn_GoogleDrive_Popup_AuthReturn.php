<?php
namespace W3TC;



class Cdn_GoogleDrive_Popup_AuthReturn {
	function render() {
		$client_id = $_GET['oa_client_id'];
		$refresh_token = $_GET['oa_refresh_token'];

		$token_array = array(
			'access_token' => $_GET['oa_access_token'],
			'token_type' => $_GET['oa_token_type'],
			'expires_in' => $_GET['oa_expires_in'],
			'created' => $_GET['oa_created']
		);
		$access_token = json_encode( $token_array );

		$client = new \W3TCG_Google_Client();
		$client->setClientId( $client_id );
		$client->setAccessToken( $access_token );


		$service = new \W3TCG_Google_Service_Drive( $client );

		$items = $service->files->listFiles( array(
				'q' => "mimeType = 'application/vnd.google-apps.folder'"
			) );

		$folders = array();
		foreach ( $items as $item ) {
			if ( count( $item->parents ) > 0 && $item->parents[0]->isRoot )
				$folders[] = $item;
		}

		include  W3TC_DIR . '/Cdn_GoogleDrive_Popup_AuthReturn_View.php';
	}
}
