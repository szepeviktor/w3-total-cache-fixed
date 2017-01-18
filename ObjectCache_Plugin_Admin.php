<?php
namespace W3TC;

class ObjectCache_Plugin_Admin {
	function run() {
		$config_labels = new ObjectCache_ConfigLabels();
		add_filter( 'w3tc_config_labels', array( $config_labels, 'config_labels' ) );

		$c = Dispatcher::config();
		if ( $c->get_boolean( 'objectcache.enabled' ) ) {
			add_filter( 'w3tc_errors', array( $this, 'w3tc_errors' ) );
			add_filter( 'w3tc_notes', array( $this, 'w3tc_notes' ) );
			add_filter( 'w3tc_usage_statistics_summary_from_history', array(
					$this, 'w3tc_usage_statistics_summary_from_history' ), 10, 2 );
		}
	}



	public function w3tc_errors( $errors ) {
		$c = Dispatcher::config();

		if ( $c->get_string( 'objectcache.engine' ) == 'memcached' ) {
			$memcached_servers = $c->get_array(
				'objectcache.memcached.servers' );

			if ( !Util_Installed::is_memcache_available( $memcached_servers ) ) {
				if ( !isset( $errors['memcache_not_responding.details'] ) )
					$errors['memcache_not_responding.details'] = array();

				$errors['memcache_not_responding.details'][] = sprintf(
					__( 'Object Cache: %s.', 'w3-total-cache' ),
					implode( ', ', $memcached_servers ) );
			}
		}

		return $errors;
	}



	public function w3tc_notes( $notes ) {
		$c = Dispatcher::config();
		$state = Dispatcher::config_state();
		$state_note = Dispatcher::config_state_note();

		/**
		 * Show notification when object cache needs to be emptied
		 */
		if ( $state_note->get( 'objectcache.show_note.flush_needed' ) &&
			!is_network_admin() /* flushed dont work under network admin */ &&
			!$c->is_preview() ) {
			$notes['objectcache_flush_needed'] = sprintf(
				__( 'The setting change(s) made either invalidate the cached data or modify the behavior of the site. %s now to provide a consistent user experience.',
					'w3-total-cache' ),
				Util_Ui::button_link(
					__( 'Empty the object cache', 'w3-total-cache' ),
					Util_Ui::url( array( 'w3tc_flush_objectcache' => 'y' ) ) ) );
		}

		return $notes;
	}



	public function w3tc_usage_statistics_summary_from_history( $summary, $history ) {
		// memcached servers
		$c = Dispatcher::config();
		if ( $c->get_string( 'objectcache.engine' ) == 'memcached' ) {
			$summary['memcached_servers']['objectcache'] = array(
				'servers' => $c->get_array( 'objectcache.memcached.servers' ),
				'username' => $c->get_string( 'objectcache.memcached.username' ),
				'password' => $c->get_string( 'objectcache.memcached.password' ),
				'name' => __( 'Object Cache', 'w3-total-cache' )
			);
		} elseif ( $c->get_string( 'objectcache.engine' ) == 'redis' ) {
			$summary['redis_servers']['objectcache'] = array(
				'servers' => $c->get_array( 'objectcache.redis.servers' ),
				'username' => $c->get_boolean( 'objectcache.redis.username' ),
				'dbid' => $c->get_integer( 'objectcache.redis.dbid' ),
				'password' => $c->get_string( 'objectcache.redis.password' ),
				'name' => __( 'Object Cache', 'w3-total-cache' )
			);
		}

		// counters
		$objectcache_calls_total = Util_UsageStatistics::sum( $history,
			'objectcache_calls_total' );
		$objectcache_calls_hits = Util_UsageStatistics::sum( $history,
			'objectcache_calls_hits' );

		$summary['objectcache'] = array(
			'calls_total' => Util_UsageStatistics::integer(
				$objectcache_calls_total ),
			'calls_per_second' => Util_UsageStatistics::value_per_period_seconds(
				$objectcache_calls_total, $summary ),
			'hit_rate' => Util_UsageStatistics::percent(
				$objectcache_calls_total, $objectcache_calls_total )
		);

		return $summary;
	}
}
