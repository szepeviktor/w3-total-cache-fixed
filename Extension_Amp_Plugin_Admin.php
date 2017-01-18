<?php
namespace W3TC;

class Extension_Amp_Plugin_Admin {
	function run() {
	}

	static public function w3tc_extensions( $extensions, $config ) {
		$enabled = true;
		$disabled_message = '';

		$requirements = array();

		$extensions['amp'] = array(
			'name' => 'AMP',
			'author' => 'W3 EDGE',
			'description' => __( 'Adds compatibility for accelerated mobile pages (AMP) to minify.',
				'w3-total-cache' ),
			'author_uri' => 'https://www.w3-edge.com/',
			'extension_uri' => 'https://www.w3-edge.com/',
			'extension_id' => 'amp',
			'settings_exists' => false,
			'version' => '0.1',
			'enabled' => $enabled,
			'disabled_message' => $disabled_message,
			'requirements' => implode( ', ', $requirements ),
			'path' => 'w3-total-cache/Extension_Amp_Plugin.php'
		);

		return $extensions;
	}
}
