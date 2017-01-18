<?php
namespace W3TC;

define( 'W3TC_MARKER_BEGIN_FEEDBURNER', '# BEGIN W3TC FeedBurner' );
define( 'W3TC_MARKER_END_FEEDBURNER', '# END W3TC FeedBurner' );

/**
 * adds rules when needed
 */
class Extension_FeedBurner_Environment {
	/**
	 * Fixes environment in each wp-admin request
	 *
	 * @param Config  $config
	 * @param bool    $force_all_checks
	 *
	 * @throws Util_Environment_Exceptions
	 */
	static public function fix_on_wpadmin_request( $config, $force_all_checks ) {
		$exs = new Util_Environment_Exceptions();

		if ( $config->get_boolean( 'config.check' ) || $force_all_checks ) {
			$need_rules =
				$config->is_extension_active( 'feedburner' ) &&
				$config->is_extension_active( 'cloudflare' );

			if ( $need_rules ) {
				self::rules_add( $config, $exs );
			} else {
				self::rules_remove( $exs );
			}
		}

		if ( count( $exs->exceptions() ) > 0 )
			throw $exs;
	}


	static public function deactivate_extension() {
		$exs = new Util_Environment_Exceptions();
		self::rules_remove( $exs );
	}


	/**
	 * Fixes environment after plugin deactivation
	 *
	 * @throws Util_Environment_Exceptions
	 */
	static public function fix_after_deactivation() {
		$exs = new Util_Environment_Exceptions();

		self::rules_remove( $exs );

		if ( count( $exs->exceptions() ) > 0 )
			throw $exs;
	}

	/**
	 * Returns required rules for module
	 *
	 * @param Config  $config
	 * @return array
	 */
	static public function get_required_rules( $rewrite_rules, $config ) {
		if ( !$config->get_boolean( 'pgcache.enabled' ) ||
			$config->get_string( 'pgcache.engine' ) != 'file_generic' )
			return $rewrite_rules;

		$pgcache_rules_core_path = Util_Rule::get_pgcache_rules_core_path();
		$rewrite_rules[] = array(
			'filename' => $pgcache_rules_core_path,
			'content' => self::rules_generate( $config ),
			'priority' => 1000
		);

		return $rewrite_rules;
	}



	/*
	 * rules modification
	 */

	/**
	 * Writes directives to WP .htaccess
	 *
	 * @param Config  $config
	 * @param Util_Environment_Exceptions $exs
	 * @throws Util_WpFile_FilesystemOperationException with S/FTP form if it can't get the required filesystem credentials
	 */
	static private function rules_add( $config, $exs ) {
		return Util_Rule::add_rules( $exs,
			Util_Rule::get_pgcache_rules_core_path(),
			self::rules_generate( $config ),
			W3TC_MARKER_BEGIN_FEEDBURNER,
			W3TC_MARKER_END_FEEDBURNER,
			array(
				W3TC_MARKER_BEGIN_WORDPRESS => 0,
				W3TC_MARKER_END_PGCACHE_CORE =>
				strlen( W3TC_MARKER_END_PGCACHE_CORE ) + 1
			)
		);
	}

	/**
	 * Removes Page Cache core directives
	 *
	 * @param Util_Environment_Exceptions $exs
	 * @throws Util_WpFile_FilesystemOperationException with S/FTP form if it can't get the required filesystem credentials
	 */
	static private function rules_remove( $exs ) {
		Util_Rule::remove_rules( $exs, Util_Rule::get_pgcache_rules_core_path(),
			W3TC_MARKER_BEGIN_FEEDBURNER,
			W3TC_MARKER_END_FEEDBURNER
		);
	}

	/**
	 * Generates rules for WP dir
	 *
	 * @param Config  $config
	 * @return string
	 */
	static private function rules_generate( $config ) {
		switch ( true ) {
		case Util_Environment::is_apache():
		case Util_Environment::is_litespeed():
			return self::rules_generate_apache( $config );

		case Util_Environment::is_nginx():
			return self::rules_generate_nginx( $config );
		}

		return '';
	}

	/**
	 * Generates rules for WP dir
	 *
	 * @param Config  $config
	 * @return string
	 */
	static private function rules_generate_apache( $config ) {
		$rewrite_base = Util_Environment::network_home_url_uri();

		$a = Util_Environment::wp_upload_dir();
		$parse_url = @parse_url( $a['baseurl'] );
		if ( empty( $parse_url['path'] ) )
			return '';

		$uploads_path = $parse_url['path'];

		// cut off rewrite base since its already specified by rules
		if ( substr( $uploads_path, 0, strlen( $rewrite_base ) ) == $rewrite_base )
			$uploads_path_nobase = substr( $uploads_path, strlen( $rewrite_base ) );
		else
			$uploads_path_nobase = $uploads_path;

		$rules = W3TC_MARKER_BEGIN_FEEDBURNER . "\n";

		$rules .= "<IfModule mod_rewrite.c>\n";
		$rules .= "    RewriteCond %{HTTP_USER_AGENT} FeedBurner\n";
		$rules .= "    RewriteRule ^$uploads_path_nobase/([0-9]+)/([0-9]+)/hotlink-ok/(.*)$ $uploads_path/$1/$2/$3 [L]\n";
		$rules .= "</IfModule>\n";

		$rules .= W3TC_MARKER_END_FEEDBURNER . "\n";

		return $rules;
	}

	/**
	 * Generates rules for WP dir
	 *
	 * @param Config  $config
	 * @return string
	 */
	static private function rules_generate_nginx( $config ) {
		$a = Util_Environment::wp_upload_dir();
		$parse_url = @parse_url( $a['baseurl'] );
		if ( empty( $parse_url['path'] ) )
			return '';

		$uploads_path = $parse_url['path'];

		$rules = W3TC_MARKER_BEGIN_FEEDBURNER . "\n";

		$rules .= "if (\$http_user_agent ~* \"(FeedBurner)\") {\n";
		$rules .= "    rewrite ^$uploads_path/([0-9]+)/([0-9]+)/hotlink-ok/(.*)$ $uploads_path/$1/$2/$3 last;\n";
		$rules .= "}\n";

		$rules .= W3TC_MARKER_END_FEEDBURNER . "\n";

		return $rules;
	}
}
