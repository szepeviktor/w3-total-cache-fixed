<?php
namespace W3TC;

class Extension_Amp_Plugin {
	private $is_amp_endpoint = null;



	public function run() {
		add_filter( 'w3tc_minify_js_enable',
			array( $this, 'w3tc_minify_jscss_enable' ) );
		add_filter( 'w3tc_minify_css_enable',
			array( $this, 'w3tc_minify_jscss_enable' ) );
		add_filter( 'w3tc_footer_comment',
			array( $this, 'w3tc_footer_comment' ) );
		add_filter( 'w3tc_newrelic_should_disable_auto_rum',
			array( $this, 'w3tc_newrelic_should_disable_auto_rum' ) );
		add_filter( 'pgcache_flush_post_queued_urls',
			array( $this, 'x_flush_post_queued_urls' ) );
		add_filter( 'varnish_flush_post_queued_urls',
			array( $this, 'x_flush_post_queued_urls' ) );

	}



	private function is_amp_endpoint() {
		// support for different plugins defining those own functions
		if ( is_null( $this->is_amp_endpoint ) ) {
			if ( function_exists('is_amp_endpoint') ) {
				$this->is_amp_endpoint = is_amp_endpoint();
			} elseif ( function_exists('ampforwp_is_amp_endpoint') ) {
				$this->is_amp_endpoint = ampforwp_is_amp_endpoint();
			}
		}

		return $this->is_amp_endpoint;
	}



	public function w3tc_minify_jscss_enable( $enabled ) {
		$is_amp_endpoint = $this->is_amp_endpoint();

		if ( !is_null( $is_amp_endpoint ) && $is_amp_endpoint ) {
			// amp has own rules for CSS and JS files, don't touch them by default
			return false;
		}

		return $enabled;
	}



	public function w3tc_newrelic_should_disable_auto_rum( $reject_reason ) {
		$is_amp_endpoint = $this->is_amp_endpoint();

		if ( !is_null( $is_amp_endpoint ) && $is_amp_endpoint ) {
			return 'AMP endpoint';
		}

		return $reject_reason;
	}



	public function x_flush_post_queued_urls( $queued_urls ) {
		$amp_urls = array();

		foreach ( $queued_urls as $url ) {
			$amp_urls[] = trailingslashit( $url ) . 'amp';
		}

		$queued_urls = array_merge( $queued_urls, $amp_urls );
		return $queued_urls;
	}



	public function w3tc_footer_comment( $strings ) {
		$is_amp_endpoint = $this->is_amp_endpoint();

		if ( !is_null( $is_amp_endpoint ) && $is_amp_endpoint ) {
			$strings[] = 'AMP page, minification is limited';
		}

		return $strings;
	}
}



$p = new Extension_Amp_Plugin();
$p->run();

if ( is_admin() ) {
	$p = new Extension_Amp_Plugin_Admin();
	$p->run();
}
