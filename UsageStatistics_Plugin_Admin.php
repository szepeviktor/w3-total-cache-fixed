<?php
namespace W3TC;



class UsageStatistics_Plugin_Admin {
	function run() {
		add_action( 'w3tc_settings_general_boxarea_miscellaneous_content',
			array( $this, 'w3tc_settings_general_boxarea_miscellaneous_content' ) );

		$widget = new UsageStatistics_Widget();
		$widget->init();

		add_filter( 'w3tc_usage_statistics_summary_from_history', array(
				$this, 'w3tc_usage_statistics_summary_from_history' ), 10, 2 );
	}



	public function w3tc_settings_general_boxarea_miscellaneous_content() {
		include  W3TC_DIR . '/UsageStatistics_View_General.php';
	}



	public function w3tc_usage_statistics_summary_from_history( $summary, $history ) {
		$php_memory_100kb = Util_UsageStatistics::sum( $history, 'php_memory_100kb' );
		$php_requests = Util_UsageStatistics::sum( $history, 'php_requests' );

		if ( $php_requests > 0 ) {
			$summary['php'] = array(
				'memory' => Util_UsageStatistics::bytes_to_size(
					$php_memory_100kb / $php_requests * 1024 * 10.24 )
			);

			$summary['php']['wp_requests_total'] =
				Util_UsageStatistics::integer( $php_requests );
			$summary['php']['wp_requests_per_second'] =
				Util_UsageStatistics::value_per_period_seconds( $php_requests,
				$summary );
		}

		return $summary;
	}
}
