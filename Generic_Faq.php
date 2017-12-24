<?php
namespace W3TC;

class Generic_Faq {
	static public function sections() {
		// name => column where to show
		return array(
			'General' => 1,
			'Usage' => 1,
			'Compatibility' => 1,
			'Minification' => 2,
			'CDN' => 2,
			'Browser Cache' => 3,
			'Errors / Debugging' => 3,
			'Requirements' => 3,
			'Developers' => 3,
			'Extensions' => 3
		);
	}



	/**
	 * Parses FAQ XML file into array
	 *
	 * @return array
	 */
	static public function parse() {
		$config = Dispatcher::config();
		$faq = array();

		self::parse_file( $faq, 'faq', '', '' );

		if ( Util_Environment::is_w3tc_pro( $config ) )
			self::parse_file( $faq, 'faq-pro', 'pro', '<b>Pro:</b> ' );

		return $faq;
	}



	static private function parse_file( &$entries, $filename_base, $flag,
		$question_prefix ) {
		$filename = W3TC_LANGUAGES_DIR . '/' . $filename_base . '-' .
			get_locale() . '.xml';
		if ( !file_exists( $filename ) )
			$filename = W3TC_LANGUAGES_DIR . '/' . $filename_base . '-en_US.xml';

		$xml = @file_get_contents( $filename );
		if ( empty( $xml ) )
			return;

		if ( !function_exists( 'xml_parser_create' ) )
			return;

		$parser = @xml_parser_create( 'UTF-8' );

		xml_parser_set_option( $parser, XML_OPTION_TARGET_ENCODING, 'UTF-8' );
		xml_parser_set_option( $parser, XML_OPTION_CASE_FOLDING, 0 );
		xml_parser_set_option( $parser, XML_OPTION_SKIP_WHITE, 1 );

		$values = null;
		$result = xml_parse_into_struct( $parser, $xml, $values );
		xml_parser_free( $parser );

		if ( !$result )
			return;

		$section = 'General';
		$entry = null;

		foreach ( $values as $value ) {
			switch ( $value['type'] ) {
			case 'open':
				if ( $value['tag'] === 'section' ) {
					$section = $value['attributes']['name'];
					if ( !isset( $entries[$section] ) )
						$entries[$section] = array();
				} else if ( $value['tag'] === 'entry' ) {
						$entry = array(
							'flag' => $flag
						);
					}
				break;

			case 'complete':
				if ( $value['tag'] == 'question' )
					$entry['question'] = $question_prefix . $value['value'];
				else if ( $value['tag'] == 'answer' )
						$entry['answer'] = $value['value'];
					else if ( $value['tag'] == 'tag' )
							$entry['tag'] = $value['value'];
						break;

				case 'close':
					if ( $value['tag'] == 'entry' ) {
						if ( !isset( $entry['tag'] ) )
							$entry['tag'] = md5( $entry['answer'] );

						$entries[$section][] = $entry;
					}
				break;
			}
		}
	}
}
