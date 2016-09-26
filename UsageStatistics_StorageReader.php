<?php
namespace W3TC;

/**
 * Manages data statistics.
 * Metrics:
 *
 */
class UsageStatistics_StorageReader {
	public function get_history_summary_promise() {
		$summary_promise = array(
			'memcached_servers' => array(),
		);

		$summary_promise = apply_filters( 'w3tc_usage_statistics_summary_from_history',
			$summary_promise, array() );
		if ( isset( $summary_promise['memcached_servers'] ) &&
			count( $summary_promise['memcached_servers'] ) > 0 )
			$summary_promise['memcached'] = $this->get_memcached_summary(
				$summary_promise['memcached_servers'], true );
		if ( isset( $summary_promise['redis_servers'] ) &&
			count( $summary_promise['redis_servers'] ) > 0 )
			$summary_promise['redis'] = $this->get_redis_summary(
				$summary_promise['redis_servers'], true );

		return $summary_promise;
	}


	public function get_history_summary() {
		$w = new UsageStatistics_StorageWriter();
		$w->maybe_flush_hotspot_data();

		$history_encoded = get_site_option( 'w3tc_stats_history' );
		$history = null;
		if ( !empty( $history_encoded ) )
			$history = json_decode( $history_encoded, true );
		if ( !is_array( $history ) )
			$history = array();

		$summary = array(
			'memcached_servers' => array(),
			'redis_servers' => array()
		);

		if ( count( $history ) <= 0 ) {
			$summary = array( 'period' => array() );
		} else {
			$timestamp_start = $history[0]['timestamp_start'];
			$timestamp_end = $history[count( $history ) - 1]['timestamp_end'];

			$period = array(
				'timestamp_start' => $timestamp_start,
				'timestamp_start_mins' =>
				Util_UsageStatistics::time_mins( $timestamp_start ),
				'timestamp_end' => $timestamp_end,
				'timestamp_end_mins' =>
				Util_UsageStatistics::time_mins( $timestamp_end ),
			);

			$period['seconds'] = $timestamp_end - $timestamp_start;
			$summary['period'] = $period;
			$summary['timeout_time'] = time() + 15;

			$summary = apply_filters( 'w3tc_usage_statistics_summary_from_history',
				$summary, $history );

			if ( count( $summary['memcached_servers'] ) > 0 )
				$summary['memcached'] = $this->get_memcached_summary(
					$summary['memcached_servers'] );
			if ( count( $summary['redis_servers'] ) > 0 )
				$summary['redis'] = $this->get_redis_summary(
					$summary['redis_servers'] );
		}

		$summary['period']['to_update_secs'] = (int)$w->get_hotspot_end() - time() + 1;

		unset( $summary['memcached_servers'] );
		unset( $summary['redis_servers'] );

		return $summary;
	}



	private function get_memcached_summary( $server_descriptors, $promise_only = false ) {
		$servers = array();

		foreach ( $server_descriptors as $i ) {
			foreach ( $i['servers'] as $host_port ) {
				if ( !isset( $servers[$host_port] ) )
					$servers[$host_port] = array(
						'username' => $i['username'],
						'password' => $i['password'],
						'module_names' => array( $i['name'] )
					);
				else
					$servers[$host_port]['module_names'][] = $i['name'];
			}
		}

		$summary = array();

		foreach ( $servers as $host_port => $i ) {
			$cache = Cache::instance( 'memcached',
				array(
					'servers' => array( $host_port ),
					'username' => $i['username'],
					'password' => $i['password']
				) );

			if ( $promise_only )
				$stats = array();
			else
				$stats = $cache->get_statistics();

			$id = md5( $host_port );
			$summary[$id] = array(
				'name' => $host_port,
				'module_names' => $i['module_names'],
				'size_used' =>
				Util_UsageStatistics::bytes_to_size2( $stats, 'bytes' ),
				'size_percent' =>
				Util_UsageStatistics::percent2( $stats, 'bytes', 'limit_maxbytes' ),
				'get_hit_rate' =>
				Util_UsageStatistics::percent2( $stats, 'get_hits', 'cmd_get' ),
				'evictions_per_second' => Util_UsageStatistics::value_per_second(
					$stats, 'evictions', 'uptime' )
			);
		}

		return $summary;
	}



	private function get_redis_summary( $server_descriptors, $promise_only = false ) {
		$servers = array();

		foreach ( $server_descriptors as $i ) {
			foreach ( $i['servers'] as $host_port ) {
				if ( !isset( $servers[$host_port] ) )
					$servers[$host_port] = array(
						'password' => $i['password'],
						'dbid' => $i['dbid'],
						'module_names' => array( $i['name'] )
					);
				else
					$servers[$host_port]['module_names'][] = $i['name'];
			}
		}

		$summary = array();

		foreach ( $servers as $host_port => $i ) {
			$cache = Cache::instance( 'redis',
				array(
					'servers' => array( $host_port ),
					'password' => $i['password'],
					'dbid' => $i['dbid']
				) );

			if ( $promise_only )
				$stats = array();
			else
				$stats = $cache->get_statistics();

			if ( isset( $stats['keyspace_hits'] ) && $stats['keyspace_misses'] )
				$stats['_keyspace_total'] =
					(int)$stats['keyspace_hits'] + (int)$stats['keyspace_misses'];

			$id = md5( $host_port );
			$summary[$id] = array(
				'name' => $host_port,
				'module_names' => $i['module_names'],
				'size_used' =>
				Util_UsageStatistics::bytes_to_size2( $stats, 'used_memory' ),
				'hit_rate' =>
				Util_UsageStatistics::percent2( $stats, 'keyspace_hits', '_keyspace_total' ),
				'expirations_per_second' => Util_UsageStatistics::value_per_second(
					$stats, 'expired_keys', 'uptime_in_seconds' ),
				'evictions_per_second' => Util_UsageStatistics::value_per_second(
					$stats, 'evicted_keys', 'uptime_in_seconds' )
			);
		}

		return $summary;
	}
}
