<?php
namespace W3TC;

class Util_RuleSnippet {
	/**
	 * Return canonical rules
	 *
	 * @param bool    $cdnftp
	 * @return string
	 */
	static public function canonical_without_location( $cdnftp = false,
		$add_header_rules, $cors_header ) {
		$rules = '';

		switch ( true ) {
		case Util_Environment::is_apache():
		case Util_Environment::is_litespeed():
			$host = ( $cdnftp ) ? Util_Environment::home_url_host() : '%{HTTP_HOST}';
			$rules .= "   <IfModule mod_rewrite.c>\n";
			$rules .= "      RewriteEngine On\n";
			$rules .= "      RewriteCond %{HTTPS} !=on\n";
			$rules .= "      RewriteRule .* - [E=CANONICAL:http://$host%{REQUEST_URI},NE]\n";
			$rules .= "      RewriteCond %{HTTPS} =on\n";
			$rules .= "      RewriteRule .* - [E=CANONICAL:https://$host%{REQUEST_URI},NE]\n";
			$rules .= "   </IfModule>\n";
			$rules .= "   <IfModule mod_headers.c>\n";
			$rules .= '      Header set Link "<%{CANONICAL}e>; rel=\"canonical\""' . "\n";
			$rules .= "   </IfModule>\n";
			break;

		case Util_Environment::is_nginx():
			$home = ( $cdnftp ) ? Util_Environment::home_url_host() : '$host';
			// nginx overrides all add_header directives when context inherited
			// so add_header rules has to be repeated
			$link_header = '    add_header Link "<$scheme://' .
				$home . '$uri>; rel=\"canonical\"";' . "\n";

			$rules .= $link_header;

			if ( $cors_header ) {
				$rules .=
				'    if ($request_uri ~ ^[^?]*\\.(ttf|ttc|otf|eot|woff|woff2|font.css)(\\?|$)) {' .
				"\n    " . $link_header .
				"    " .
				str_replace( "\n", "\n    ", $add_header_rules ) .
				"    add_header Access-Control-Allow-Origin \"*\";\n" .
				"    }\n";
			}

			break;
		}

		return $rules;
	}

	/**
	 * Returns canonical rules
	 *
	 * @param bool    $cdnftp
	 * @return string
	 */
	static public function canonical( $cdnftp = false, $cors_header ) {
		$rules = '';

		$mime_types = self::_get_other_types();
		$extensions = array_keys( $mime_types );

		switch ( true ) {
		case Util_Environment::is_apache():
		case Util_Environment::is_litespeed():
			$extensions_lowercase = array_map( 'strtolower', $extensions );
			$extensions_uppercase = array_map( 'strtoupper', $extensions );
			$rules .= "<FilesMatch \"\\.(" . implode( '|',
				array_merge( $extensions_lowercase, $extensions_uppercase ) ) . ")$\">\n";
			$rules .= self::canonical_without_location( $cdnftp, '', $cors_header );
			$rules .= "</FilesMatch>\n";
			break;

		case Util_Environment::is_nginx():
			$rules .= "location ~ \.(" . implode( '|', $extensions ) . ")$ {\n";
			$rules .= self::canonical_without_location( $cdnftp, '', $cors_header );
			$rules .= "}\n";
			break;
		}

		return $rules;
	}

	/**
	 * Returns other mime types
	 *
	 * @return array
	 */
	static private function _get_other_types() {
		$mime_types = include W3TC_INC_DIR . '/mime/other.php';
		return $mime_types;
	}

}
