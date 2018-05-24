<?php
namespace W3TC;

class Generic_Faq {
	static public function sections() {
		// name => column where to show
		return array(
			'General' => 'https://github.com/Auctollo/w3-total-cache/wiki/FAQ%3A-General',
			'Usage' => 'https://github.com/Auctollo/w3-total-cache/wiki/FAQ%3A-Usage',
			'Compatibility' => 'https://github.com/Auctollo/w3-total-cache/wiki/FAQ%3A-Compatibility',
			'Minification' => 'https://github.com/Auctollo/w3-total-cache/wiki/FAQ%3A-Minification',
			'CDN' => 'https://github.com/Auctollo/w3-total-cache/wiki/FAQ%3A-CDN',
			'Browser Cache' => 'https://github.com/Auctollo/w3-total-cache/wiki/FAQ%3A-Browser-Cache',
			'Errors / Debugging' => 'https://github.com/Auctollo/w3-total-cache/wiki/FAQ%3A-Debugging',
			'Requirements' => 'https://github.com/Auctollo/w3-total-cache/wiki/FAQ%3A-Requirements',
			'Developers' => 'https://github.com/Auctollo/w3-total-cache/wiki/FAQ%3A-Developers',
			'Extensions' => 'https://github.com/Auctollo/w3-total-cache/wiki/FAQ%3A-Extensions',
			'Installation' => 'https://github.com/Auctollo/w3-total-cache/wiki/FAQ%3A-Installation'
		);
	}



	/**
	 * Returns list of questions for section
	 */
	static public function parse( $section ) {
		$faq = array();

		$sections = self::sections();
		if ( !isset( $sections[ $section ] ) ) {
			return null;
		}

		$url = $sections[ $section ];


		$response = wp_remote_get( $url );
		if ( is_wp_error( $response ) ) {
			return null;
		}

		$html = $response['body'];
		$questions = array();

		$m = array();
		preg_match_all( '~<h1>\s*<a[^>]+href="(#[^"]+)[^>]+>.*?</a>([^<]+)</h1>~mi',
			$html, $m );
		if ( is_array( $m ) && count( $m ) > 1 ) {
			for ( $n = 0; $n < count( $m[1] ); $n++ ) {
				$questions[] = array('q' => $m[2][$n], 'a' => $url . $m[1][$n] );
			}
		}

		$m = array();
		preg_match_all( '~<li>\s*<a[^>]+href="([^"]+)[^>]+>(.*?)</a>\s*[.]s*</li>~mi',
			$html, $m );
		if ( is_array( $m ) && count( $m ) > 1 ) {
			for ( $n = 0; $n < count( $m[1] ); $n++ ) {
				$questions[] = array('q' => $m[2][$n], 'a' => $m[1][$n] );
			}
		}

		return $questions;
	}
}
