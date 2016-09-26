<?php
namespace W3TC;

class Generic_AdminNotes {
	/**
	 *
	 *
	 * @param Config  $config
	 * @return string
	 */
	function w3tc_notes( $notes ) {
		$c = Dispatcher::config();
		$state = Dispatcher::config_state();
		$state_master = Dispatcher::config_state_master();
		$state_note = Dispatcher::config_state_note();

		/**
		 * Check wp-content permissions
		 */
		if ( !W3TC_WIN && !$state_master->get_boolean( 'common.hide_note_wp_content_permissions' ) ) {
			$wp_content_mode = Util_File::get_file_permissions( WP_CONTENT_DIR );

			if ( $wp_content_mode > 0755 ) {
				$notes['generic_wp_content_writeable'] = sprintf(
					__( '<strong>%s</strong> is write-able. When finished installing the plugin, change the permissions back to the default: <strong>chmod 755 %s</strong>. Permissions are currently %s. %s',
						'w3-total-cache' ),
					WP_CONTENT_DIR,
					WP_CONTENT_DIR,
					base_convert( Util_File::get_file_permissions( WP_CONTENT_DIR ), 10, 8 ),
					Util_Ui::button_hide_note2( array(
							'w3tc_default_config_state_master' => 'y',
							'key' => 'common.hide_note_wp_content_permissions',
							'value' => 'true' ) ) );
			}
		}

		/**
		 * Check Zlib extension
		 */
		if ( !$state_master->get_boolean( 'common.hide_note_no_zlib' ) &&
			!function_exists( 'gzencode' ) ) {
			$notes['no_zlib'] = sprintf(
				__( 'Unfortunately the PHP installation is incomplete, the <strong>zlib module is missing</strong>. This is a core PHP module. Notify the server administrator. %s',
					'w3-total-cache' ),
				Util_Ui::button_hide_note2( array(
						'w3tc_default_config_state_master' => 'y',
						'key' => 'common.hide_note_no_zlib',
						'value' => 'true' ) ) );
		}

		/**
		 * Check if Zlib output compression is enabled
		 */
		if ( !$state_master->get_boolean( 'common.hide_note_zlib_output_compression' ) &&
			Util_Environment::is_zlib_enabled() ) {
			$notes['zlib_output_compression'] = sprintf(
				__( 'Either the PHP configuration, web server configuration or a script in the WordPress installation has <strong>zlib.output_compression</strong> enabled.<br />Please locate and disable this setting to ensure proper HTTP compression behavior. %s',
					'w3-total-cache' ),
				Util_Ui::button_hide_note2( array(
						'w3tc_default_config_state_master' => 'y',
						'key' => 'common.hide_note_zlib_output_compression',
						'value' => 'true' ) ) );
		}

		if ( $state_master->get_boolean( 'common.show_note.nginx_restart_required' ) ) {
			$cf = Dispatcher::component( 'CacheFlush' );
			$notes['nginx_restart_required'] = sprintf(
				__( 'nginx.conf rules have been updated. Please restart nginx server to provide a consistent user experience. %s',
					'w3-total-cache' ),
				Util_Ui::button_hide_note2( array(
						'w3tc_default_config_state_master' => 'y',
						'key' => 'common.show_note.nginx_restart_required',
						'value' => 'false' ) ) );
		}

		/**
		 * Preview mode
		 */
		if ( $c->is_preview() ) {
			$notes['preview_mode'] = sprintf(
				__( 'Preview mode is active: Changed settings will not take effect until preview mode is %s or %s.', 'w3-total-cache' ),
				Util_Ui::button_link( __( 'deploy', 'w3-total-cache' ),
					Util_Ui::url( array(
							'w3tc_config_preview_deploy' => 'y' ) ) ),
				Util_Ui::button_link( __( 'disable', 'w3-total-cache' ),
					Util_Ui::url( array(
							'w3tc_config_preview_disable' => 'y' ) ) ) ) .
				'<br /><span class="description">'.
				sprintf(
				__( 'To preview any changed settings (without deploying): %s',
					'w3-total-cache' ),
				Util_Ui::preview_link() ).
				'</span>';
		}

		/**
		 * Show notification after plugin activate/deactivate
		 */
		if ( $state_note->get( 'common.show_note.plugins_updated' ) &&
			!is_network_admin() /* flushing under network admin do nothing */ ) {
			$texts = array();

			if ( $c->get_boolean( 'pgcache.enabled' ) ) {
				$texts[] = Util_Ui::button_link(
					__( 'empty the page cache', 'w3-total-cache' ),
					Util_Ui::url( array( 'w3tc_flush_posts' => 'y' ) ) );
			}

			if ( $c->get_boolean( 'minify.enabled' ) ) {
				$texts[] = sprintf(
					__( 'check the %s to maintain the desired user experience',
						'w3-total-cache' ),
					Util_Ui::button_link(
						__( 'minify settings', 'w3-total-cache' ),
						Util_Ui::url( array(
								'w3tc_default_config_state_note' => 'y',
								'key' => 'common.show_note.plugins_updated',
								'value' => 'false' ) ) ) );
			}

			if ( count( $texts ) ) {
				$notes['some_plugins_activated'] = sprintf(
					__( 'One or more plugins have been activated or deactivated, please %s. %s',
						'w3-total-cache' ),
					implode( __( ' and ', 'w3-total-cache' ), $texts ),
					Util_Ui::button_hide_note2( array(
							'w3tc_default_config_state_note' => 'y',
							'key' => 'common.show_note.plugins_updated',
							'value' => 'false' ) ) );
			}
		}


		/**
		 * Show notification when flush_statics needed
		 */
		if ( $c->get_boolean( 'browsercache.enabled' ) &&
			$state_note->get( 'common.show_note.flush_statics_needed' ) &&
			!is_network_admin() /* flushing under network admin do nothing */ &&
			!$c->is_preview() ) {
			$notes['flush_statics_needed'] = sprintf(
				__( 'The setting change(s) made either invalidate the cached data or modify the behavior of the site. %s now to provide a consistent user experience. %s', 'w3-total-cache' ),
				Util_Ui::button_link( 'Empty the static files cache',
					Util_Ui::url( array( 'w3tc_flush_statics' => 'y' ) ) ),
				Util_Ui::button_hide_note2( array(
						'w3tc_default_config_state_note' => 'y',
						'key' => 'common.show_note.flush_statics_needed',
						'value' => 'false' ) ) );
		}

		/**
		 * Show notification when flush_posts needed
		 */
		if ( $state_note->get( 'common.show_note.flush_posts_needed' ) &&
			!is_network_admin() /* flushing under network admin do nothing */ &&
			!$c->is_preview() &&
			!isset( $notes['flush_statics_needed'] ) ) {
			$cf = Dispatcher::component( 'CacheFlush' );
			if ( $cf->flushable_posts() ) {
				$notes['flush_posts_needed'] = sprintf(
					__( 'The setting change(s) made either invalidate the cached data or modify the behavior of the site. %s now to provide a consistent user experience. %s', 'w3-total-cache' ),
					Util_Ui::button_link( 'Empty the page cache',
						Util_Ui::url( array( 'w3tc_flush_posts' => 'y' ) ) ),
					Util_Ui::button_hide_note2( array(
							'w3tc_default_config_state_note' => 'y',
							'key' => 'common.show_note.flush_posts_needed',
							'value' => 'false' ) ) );
			}
		}

		return $notes;
	}



	public function w3tc_errors( $errors ) {
		$state = Dispatcher::config_state();
		$c = Dispatcher::config();

		/**
		 * Check permalinks
		 */
		if ( !$state->get_boolean( 'common.hide_note_no_permalink_rules' ) &&
			( ( $c->get_boolean( 'pgcache.enabled' ) &&
					$c->get_string( 'pgcache.engine' ) == 'file_generic' ) ||
				( $c->get_boolean( 'browsercache.enabled' ) &&
					$c->get_boolean( 'browsercache.no404wp' ) ) ) &&
			!Util_Rule::is_permalink_rules() ) {
			$errors['generic_no_permalinks'] = sprintf(
				__( 'The required directives for fancy permalinks could not be detected, please confirm they are available: <a href="http://codex.wordpress.org/Using_Permalinks#Creating_and_editing_.28.htaccess.29">Creating and editing</a> %s',
					'w3-total-cache' ),
				Util_Ui::button_hide_note2( array(
						'w3tc_default_config_state_master' => 'y',
						'key' => 'common.hide_note_no_permalink_rules',
						'value' => 'true' ) ) );
		}

		/**
		 * Check memcached
		 */
		if ( isset( $errors['memcache_not_responding.details'] ) ) {
			$memcache_error =
				__( 'The following memcached servers are not responding or not running:</p><ul>',
				'w3-total-cache' );

			foreach ( $errors['memcache_not_responding.details'] as $memcaches_error ) {
				$memcache_error .= '<li>' . $memcaches_error . '</li>';
			}

			$memcache_error .= __( '</ul><p>This message will automatically disappear once the issue is resolved.', 'w3-total-cache' );

			$errors['memcache_not_responding'] = $memcache_error;
			unset( $errors['memcache_not_responding.details'] );
		}

		return $errors;
	}
}
