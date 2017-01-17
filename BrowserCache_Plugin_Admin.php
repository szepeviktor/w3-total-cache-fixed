<?php
namespace W3TC;

class BrowserCache_Plugin_Admin {
	function run() {
		$config_labels = new BrowserCache_ConfigLabels();
		add_filter( 'w3tc_config_labels', array(
				$config_labels, 'config_labels' ) );
	}
}
