<?php
namespace W3TC;



class Cdn_Page extends Base_Page_Settings {
	/**
	 * Current page
	 *
	 * @var string
	 */
	protected $_page = 'w3tc_cdn';

	/**
	 * CDN tab
	 *
	 * @return void
	 */
	function view() {
		$config = Dispatcher::config();
		$cdn_engine = $config->get_string( 'cdn.engine' );

		if ( Cdn_Util::is_engine_fsd( $cdn_engine ) ) {
			do_action( 'w3tc_settings_cdn' );
			return;
		}

		$cdn_enabled = $config->get_boolean( 'cdn.enabled' );
		$cdn_mirror = Cdn_Util::is_engine_mirror( $cdn_engine );
		$cdn_mirror_purge_all = Cdn_Util::can_purge_all( $cdn_engine );
		$cdn_common = Dispatcher::component( 'Cdn_Core' );

		$cdn = $cdn_common->get_cdn();
		$cdn_supports_header = $cdn->headers_support() == W3TC_CDN_HEADER_MIRRORING;
		$minify_enabled = (
			$config->get_boolean( 'minify.enabled' ) &&
			Util_Rule::can_check_rules() &&
			$config->get_boolean( 'minify.rewrite' ) &&
			( !$config->get_boolean( 'minify.auto' ) ||
				Cdn_Util::is_engine_mirror( $config->get_string( 'cdn.engine' ) ) ) );

		$cookie_domain = $this->get_cookie_domain();
		$set_cookie_domain = $this->is_cookie_domain_enabled();

		// Required for Update Media Query String button
		$browsercache_enabled = $config->get_boolean( 'browsercache.enabled' );
		$browsercache_update_media_qs = ( $config->get_boolean( 'browsercache.cssjs.replace' ) || $config->get_boolean( 'browsercache.other.replace' ) );
		if ( in_array( $cdn_engine, array( 'netdna', 'maxcdn' ) ) ) {
			$pull_zones = array();
			$authorization_key = $config->get_string( "cdn.$cdn_engine.authorization_key" );
			$zone_id = $config->get_integer( "cdn.$cdn_engine.zone_id" );
			$alias = $consumerkey = $consumersecret = '';

			if ( $authorization_key ) {
				$keys = explode( '+', $authorization_key );
				if ( sizeof( $keys ) == 3 ) {
					list( $alias, $consumerkey, $consumersecret ) =  $keys;
				}
			}

			$authorized = $authorization_key != '' && $alias && $consumerkey && $consumersecret;
			$have_zone = $zone_id != 0;
			if ( $authorized ) {
				require_once W3TC_LIB_NETDNA_DIR . '/NetDNA.php';
				try {
					$api = new \NetDNA( $alias, $consumerkey, $consumersecret );
					$pull_zones = $api->get_zones_by_url( get_home_url() );
				} catch ( \Exception $ex ) {


					Util_Ui::error_box( '<p>There is an error with your CDN settings: ' . $ex->getMessage() . '</p>' );
				}
			}
		}
		include W3TC_INC_DIR . '/options/cdn.php';
	}

	/**
	 * Returns cookie domain
	 *
	 * @return string
	 */
	function get_cookie_domain() {
		$site_url = get_option( 'siteurl' );
		$parse_url = @parse_url( $site_url );

		if ( $parse_url && !empty( $parse_url['host'] ) ) {
			return $parse_url['host'];
		}

		return $_SERVER['HTTP_HOST'];
	}

	/**
	 * Checks if COOKIE_DOMAIN is enabled
	 *
	 * @return bool
	 */
	function is_cookie_domain_enabled() {
		$cookie_domain = $this->get_cookie_domain();

		return defined( 'COOKIE_DOMAIN' ) && COOKIE_DOMAIN == $cookie_domain;
	}
}
