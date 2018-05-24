<?php
namespace W3TC;



class Cdn_AdminActions {
	private $_config = null;

	function __construct() {
		$this->_config = Dispatcher::config();
	}

	/**
	 * CDN queue action
	 *
	 * @return void
	 */
	function w3tc_cdn_queue() {
		$w3_plugin_cdn = Dispatcher::component( 'Cdn_Core_Admin' );
		$cdn_queue_action = Util_Request::get_string( 'cdn_queue_action' );
		$cdn_queue_tab = Util_Request::get_string( 'cdn_queue_tab' );

		$notes = array();

		switch ( $cdn_queue_tab ) {
		case 'upload':
		case 'delete':
		case 'purge':
			break;

		default:
			$cdn_queue_tab = 'upload';
		}

		switch ( $cdn_queue_action ) {
		case 'delete':
			$cdn_queue_id = Util_Request::get_integer( 'cdn_queue_id' );
			if ( !empty( $cdn_queue_id ) ) {
				$w3_plugin_cdn->queue_delete( $cdn_queue_id );
				$notes[] = __( 'File successfully deleted from the queue.', 'w3-total-cache' );
			}
			break;

		case 'empty':
			$cdn_queue_type = Util_Request::get_integer( 'cdn_queue_type' );
			if ( !empty( $cdn_queue_type ) ) {
				$w3_plugin_cdn->queue_empty( $cdn_queue_type );
				$notes[] = __( 'Queue successfully emptied.', 'w3-total-cache' );
			}
			break;

		case 'process':
			$w3_plugin_cdn_normal = Dispatcher::component( 'Cdn_Plugin' );
			$n = $w3_plugin_cdn_normal->cron_queue_process();
			$notes[] = sprintf( __( 'Number of processed queue items: %d', 'w3-total-cache' ), $n );
			break;
		}

		$nonce = wp_create_nonce( 'w3tc' );
		$queue = $w3_plugin_cdn->queue_get();
		$title = __( 'Unsuccessful file transfer queue.', 'w3-total-cache' );

		include W3TC_INC_DIR . '/popup/cdn_queue.php';
	}

	/**
	 * CDN export library action
	 *
	 * @return void
	 */
	function w3tc_cdn_export_library() {
		$w3_plugin_cdn = Dispatcher::component( 'Cdn_Core_Admin' );

		$total = $w3_plugin_cdn->get_attachments_count();
		$title = __( 'Media Library export', 'w3-total-cache' );

		include W3TC_INC_DIR . '/popup/cdn_export_library.php';
	}

	function w3tc_cdn_flush() {
		$flush = Dispatcher::component( 'CacheFlush' );
		$flush->flush_all( array(
				'only' => 'cdn'
			) );

		$status = $flush->execute_delayed_operations();
		$errors = array();
		foreach ( $status as $i ) {
			if ( isset( $i['error'] ) )
				$errors[] = $i['error'];
		}

		if ( empty( $errors ) ) {
			Util_Admin::redirect( array(
					'w3tc_note' => 'flush_cdn'
				), true );
		} else {
			Util_Admin::redirect_with_custom_messages2( array(
					'errors' => array( 'Failed to purge CDN: ' .
						implode( ', ', $errors ) )
				), true );
		}
	}

	/**
	 * CDN export library process
	 *
	 * @return void
	 */
	function w3tc_cdn_export_library_process() {
		$w3_plugin_cdn = Dispatcher::component( 'Cdn_Core_Admin' );

		$limit = Util_Request::get_integer( 'limit' );
		$offset = Util_Request::get_integer( 'offset' );

		$count = null;
		$total = null;
		$results = array();

		$w3_plugin_cdn->export_library( $limit, $offset, $count, $total,
			$results, time() + 120 );

		$response = array(
			'limit' => $limit,
			'offset' => $offset,
			'count' => $count,
			'total' => $total,
			'results' => $results
		);

		echo json_encode( $response );
	}

	/**
	 * CDN import library action
	 *
	 * @return void
	 */
	function w3tc_cdn_import_library() {
		$w3_plugin_cdn = Dispatcher::component( 'Cdn_Core_Admin' );
		$common = Dispatcher::component( 'Cdn_Core' );

		$cdn = $common->get_cdn();

		$total = $w3_plugin_cdn->get_import_posts_count();
		$cdn_host = $cdn->get_domain();

		$title = __( 'Media Library import', 'w3-total-cache' );

		include W3TC_INC_DIR . '/popup/cdn_import_library.php';
	}

	/**
	 * CDN import library process
	 *
	 * @return void
	 */
	function w3tc_cdn_import_library_process() {
		$w3_plugin_cdn = Dispatcher::component( 'Cdn_Core_Admin' );

		$limit = Util_Request::get_integer( 'limit' );
		$offset = Util_Request::get_integer( 'offset' );
		$import_external = Util_Request::get_boolean( 'cdn_import_external' );
		$config_state = Dispatcher::config_state();
		$config_state->set( 'cdn.import.external', $import_external );
		$config_state->save();

		$count = null;
		$total = null;
		$results = array();

		@$w3_plugin_cdn->import_library( $limit, $offset, $count, $total, $results );

		$response = array(
			'limit' => $limit,
			'offset' => $offset,
			'count' => $count,
			'total' => $total,
			'results' => $results,
		);

		echo json_encode( $response );
	}

	/**
	 * CDN rename domain action
	 *
	 * @return void
	 */
	function w3tc_cdn_rename_domain() {
		$w3_plugin_cdn = Dispatcher::component( 'Cdn_Core_Admin' );

		$total = $w3_plugin_cdn->get_rename_posts_count();

		$title = __( 'Modify attachment URLs', 'w3-total-cache' );

		include W3TC_INC_DIR . '/popup/cdn_rename_domain.php';
	}

	/**
	 * CDN rename domain process
	 *
	 * @return void
	 */
	function w3tc_cdn_rename_domain_process() {
		$w3_plugin_cdn = Dispatcher::component( 'Cdn_Core_Admin' );

		$limit = Util_Request::get_integer( 'limit' );
		$offset = Util_Request::get_integer( 'offset' );
		$names = Util_Request::get_array( 'names' );

		$count = null;
		$total = null;
		$results = array();

		@$w3_plugin_cdn->rename_domain( $names, $limit, $offset, $count, $total, $results );

		$response = array(
			'limit' => $limit,
			'offset' => $offset,
			'count' => $count,
			'total' => $total,
			'results' => $results
		);

		echo json_encode( $response );
	}

	/**
	 * CDN export action
	 *
	 * @return void
	 */
	function w3tc_cdn_export() {
		$w3_plugin_cdn = Dispatcher::component( 'Cdn_Plugin' );

		$cdn_export_type = Util_Request::get_string( 'cdn_export_type', 'custom' );

		switch ( $cdn_export_type ) {
		case 'includes':
			$title = __( 'Includes files export', 'w3-total-cache' );
			$files = $w3_plugin_cdn->get_files_includes();
			break;

		case 'theme':
			$title = __( 'Theme files export', 'w3-total-cache' );
			$files = $w3_plugin_cdn->get_files_theme();
			break;

		case 'minify':
			$title = __( 'Minify files export', 'w3-total-cache' );
			$files = $w3_plugin_cdn->get_files_minify();
			break;

		default:
		case 'custom':
			$title = __( 'Custom files export', 'w3-total-cache' );
			$files = $w3_plugin_cdn->get_files_custom();
			break;
		}

		include W3TC_INC_DIR . '/popup/cdn_export_file.php';
	}

	/**
	 * CDN export process
	 *
	 * @return void
	 */
	function w3tc_cdn_export_process() {
		$common = Dispatcher::component( 'Cdn_Core' );
		$files = Util_Request::get_array( 'files' );

		$upload = array();
		$results = array();

		foreach ( $files as $file ) {
			$local_path = $common->docroot_filename_to_absolute_path( $file );
			$remote_path = $common->uri_to_cdn_uri( $common->docroot_filename_to_uri( $file ) );
			$d = $common->build_file_descriptor( $local_path, $remote_path );
			$d['_original_id'] = $file;
			$upload[] = $d;
		}

		$common->upload( $upload, false, $results, time() + 5 );
		$output = array();

		foreach ( $results as $item ) {
			$file = '';
			if ( isset( $item['descriptor']['_original_id'] ) )
				$file = $item['descriptor']['_original_id'];

			$output[] = array(
				'result' => $item['result'],
				'error' => $item['error'],
				'file' => $file
			);
		}

		$response = array(
			'results' => $output
		);

		echo json_encode( $response );
	}

	/**
	 * CDN purge action
	 *
	 * @return void
	 */
	function w3tc_cdn_purge() {
		$title = __( 'Content Delivery Network (CDN): Purge Tool', 'w3-total-cache' );
		$results = array();

		$path = ltrim( str_replace( get_home_url(), '', get_stylesheet_directory_uri() ), '/' );
		include W3TC_INC_DIR . '/popup/cdn_purge.php';
	}

	/**
	 * CDN purge post action
	 *
	 * @return void
	 */
	function w3tc_cdn_purge_files() {
		$title = __( 'Content Delivery Network (CDN): Purge Tool', 'w3-total-cache' );
		$results = array();

		$files = Util_Request::get_array( 'files' );

		$purge = array();

		$common = Dispatcher::component( 'Cdn_Core' );

		foreach ( $files as $file ) {
			$local_path = $common->docroot_filename_to_absolute_path( $file );
			$remote_path = $common->uri_to_cdn_uri( $common->docroot_filename_to_uri( $file ) );

			$purge[] = $common->build_file_descriptor( $local_path, $remote_path );
		}

		if ( count( $purge ) ) {
			$common->purge( $purge, $results );
		} else {
			$errors[] = __( 'Empty files list.', 'w3-total-cache' );
		}

		$path = str_replace( get_home_url(), '', get_stylesheet_directory_uri() );
		include W3TC_INC_DIR . '/popup/cdn_purge.php';
	}

	/**
	 * CDN Purge Post
	 *
	 * @return void
	 */
	function w3tc_cdn_purge_attachment() {
		$results = array();
		$attachment_id = Util_Request::get_integer( 'attachment_id' );

		$w3_plugin_cdn = Dispatcher::component( 'Cdn_Core_Admin' );

		if ( $w3_plugin_cdn->purge_attachment( $attachment_id, $results ) ) {
			Util_Admin::redirect( array(
					'w3tc_note' => 'cdn_purge_attachment'
				), true );
		} else {
			Util_Admin::redirect( array(
					'w3tc_error' => 'cdn_purge_attachment'
				), true );
		}
	}

	/**
	 * CDN Test action
	 *
	 * @return void
	 */
	function w3tc_cdn_test() {
		$engine = Util_Request::get_string( 'engine' );
		$config = Util_Request::get_array( 'config' );

		//TODO: Workaround to support test case cdn/a04
		if ( $engine == 'ftp' && !isset( $config['host'] ) ) {
			$config = Util_Request::get_string( 'config' );
			$config = json_decode( $config, true );
		}

		$config = array_merge( $config, array(
				'debug' => false
			) );

		if ( isset( $config['domain'] ) && !is_array( $config['domain'] ) ) {
			$config['domain'] = explode( ',', $config['domain'] );
		}

		if ( Cdn_Util::is_engine( $engine ) ) {
			$result = true;
			$error = null;
		} else {
			$result = false;
			$error = __( 'Incorrect engine ' . $engine, 'w3-total-cache' );
		}
		if ( !isset( $config['docroot'] ) )
			$config['docroot'] = Util_Environment::document_root();

		if ( $result ) {
			if ( $engine == 'google_drive' || $engine == 'highwinds' ||
				$engine == 'limelight' ||
				$engine == 'maxcdn' || $engine == 'stackpath' ||
				$engine == 'rackspace_cdn' ||
				$engine == 'rscf' || $engine == 's3_compatible' ) {
				// those use already stored w3tc config
				$w3_cdn = Dispatcher::component( 'Cdn_Core' )->get_cdn();
			} else {
				// those use dynamic config from the page
				$w3_cdn = CdnEngine::instance( $engine, $config );
			}

			@set_time_limit( $this->_config->get_integer( 'timelimit.cdn_test' ) );

			if ( $w3_cdn->test( $error ) ) {
				$result = true;
				$error = __( 'Test passed', 'w3-total-cache' );
			} else {
				$result = false;
				$error = sprintf( __( 'Error: %s', 'w3-total-cache' ), $error );
			}
		}

		$response = array(
			'result' => $result,
			'error' => $error
		);

		echo json_encode( $response );
	}


	/**
	 * Create container action
	 *
	 * @return void
	 */
	function w3tc_cdn_create_container() {
		$engine = Util_Request::get_string( 'engine' );
		$config = Util_Request::get_array( 'config' );

		$config = array_merge( $config, array(
				'debug' => false
			) );

		$result = false;
		$error = __( 'Incorrect type.', 'w3-total-cache' );
		$container_id = '';

		switch ( $engine ) {
		case 's3':
		case 'cf':
		case 'cf2':
		case 'azure':
			$result = true;
			break;
		}

		if ( $result ) {
			$w3_cdn = CdnEngine::instance( $engine, $config );

			@set_time_limit( $this->_config->get_integer( 'timelimit.cdn_container_create' ) );

			if ( $w3_cdn->create_container( $container_id, $error ) ) {
				$result = true;
				$error = __( 'Created successfully.', 'w3-total-cache' );
			} else {
				$result = false;
				$error = sprintf( __( 'Error: %s', 'w3-total-cache' ), $error );
			}
		}

		$response = array(
			'result' => $result,
			'error' => $error,
			'container_id' => $container_id
		);

		echo json_encode( $response );
	}

	/**
	 * S3 bucket location lightbox
	 *
	 * @return void
	 */
	function w3tc_cdn_s3_bucket_location() {
		$type = Util_Request::get_string( 'type', 's3' );

		$locations = array(
			'us-east-1' 	=> __( 'US East (N. Virginia)', 'w3-total-cache' ),
			'us-east-2' 	=> __( 'US East (Ohio)', 'w3-total-cache' ),
			'us-west-1' 	=> __( 'US-West (N. California)', 'w3-total-cache' ),
			'us-west-2' 	=> __( 'US-West (Oregon)', 'w3-total-cache' ),
			'ca-central-1'	=> __( 'Canada (Montreal)', 'w3-total-cache' ),
			'ap-south-1' 	=> __( 'Asia Pacific (Mumbai)', 'w3-total-cache' ),
			'ap-northeast-2'=> __( 'Asia Pacific (Seoul)', 'w3-total-cache' ),
			'ap-southeast-1'=> __( 'Asia Pacific (Singapore)', 'w3-total-cache' ),
			'ap-southeast-2'=> __( 'Asia Pacific (Sydney)', 'w3-total-cache' ),
			'ap-northeast-1'=> __( 'Asia Pacific (Tokyo)', 'w3-total-cache' ),
			'eu-central-1' 	=> __( 'EU (Frankfurt)', 'w3-total-cache' ),
			'eu-west-1' 	=> __( 'EU (Ireland)', 'w3-total-cache' ),
			'eu-west-2' 	=> __( 'EU (London)', 'w3-total-cache' ),
			'sa-east-1' 	=> __( 'South America (S&atilde;o Paulo)', 'w3-total-cache' ),
		);

		include W3TC_INC_DIR . '/lightbox/cdn_s3_bucket_location.php';
	}



	private function test_cdn_url( $url ) {
		$response = wp_remote_get( $url );
		if ( is_wp_error( $response ) )
			return false;
		else {
			$code = wp_remote_retrieve_response_code( $response );
			return 200 == $code;
		}
	}



	function w3tc_cdn_maxcdn_authorize() {
		try {
			$state = Dispatcher::config_state();
			if ( $state->get_integer( 'track.maxcdn_authorize', 0 ) == 0 ) {
				$state->set( 'track.maxcdn_authorize', time() );
				$state->save();
			}
		} catch ( \Exception $ex ) {}
		Util_Environment::redirect( MAXCDN_AUTHORIZE_URL );
	}

	function w3tc_cdn_maxcdn_signup() {
		try {
			$state = Dispatcher::config_state();
			$state->set( 'track.maxcdn_signup', time() );
			$state->save();
		} catch ( \Exception $ex ) {}
		Util_Environment::redirect( MAXCDN_SIGNUP_URL );
	}
}
