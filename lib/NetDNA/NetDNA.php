<?php

if (!defined('ABSPATH')) {
	die();
}

require_once(W3TC_LIB_DIR . '/OAuth/W3tcOAuth.php');
require_once('W3tcWpHttpException.php');

/**
 * NetDNA REST Client Library
 *
 * @copyright 2012
 * @author Karlo Espiritu
 * @version 1.0 2012-09-21
*/
class NetDNA {
	public $alias;

	public $key;

	public $secret;

	public $netdnarws_url = 'https://rws.netdna.com';



	static public function create($authorization_key) {
		$keys = explode('+', $authorization_key);
		$alias = '';
		$consumerkey = '';
		$consumersecret = '';

		if (sizeof($keys) == 3)
			list($alias, $consumerkey, $consumersecret) = $keys;

		$api = new NetDNA($alias, $consumerkey, $consumersecret);
		return $api;
	}

	/**
	 * @param string $alias
	 * @param string $key
	 * @param string $secret
	 */
	public function __construct($alias, $key, $secret) {
		$this->alias  = $alias;
		$this->key    = $key;
		$this->secret = $secret;
	}

	public function get_zone_domain($name) {
		return $name . '.' . $this->alias . '.netdna-cdn.com';
	}

	public function is_valid() {
		return !empty($this->alias) && !empty($this->key) &&
			!empty($this->secret);
	}

	/**
	 * @param $selected_call
	 * @param $method_type
	 * @param $params
	 * @return string
	 * @throws W3tcWpHttpException
	 */
	private function execute($selected_call, $method_type, $params) {
		//increase the http request timeout
		add_filter('http_request_timeout', array($this, 'filter_timeout_time'));
		add_filter('https_ssl_verify', array($this, 'https_ssl_verify'));

		$consumer = new W3tcOAuthConsumer($this->key, $this->secret, NULL);

		// the endpoint for your request
		$endpoint = "$this->netdnarws_url/$this->alias$selected_call";

		//parse endpoint before creating OAuth request
		$parsed = parse_url($endpoint);
		if (array_key_exists("parsed", $parsed)) {
			parse_str($parsed['query'], $params);
		}

		//generate a request from your consumer
		$req_req = W3tcOAuthRequest::from_consumer_and_token($consumer, NULL, $method_type, $endpoint, $params);

		//sign your OAuth request using hmac_sha1
		$sig_method = new W3tcOAuthSignatureMethod_HMAC_SHA1();
		$req_req->sign_request($sig_method, $consumer, NULL);

		$request = array();
		$request['sslverify'] = false;
		$request['method'] = $method_type;

		if ($method_type == "POST" || $method_type == "PUT") {
			$request['body'] = $req_req->to_postdata();
			$request['headers']['Content-Type'] =
				'application/x-www-form-urlencoded; charset=' . get_option('blog_charset');

			$url = $req_req->get_normalized_http_url();
		} else {
			// notice GET, PUT and DELETE both needs to be passed in URL
			$url = $req_req->to_url();
		}

		$response = wp_remote_request($url, $request);

		$json_output = '';
		if (!is_wp_error($response)) {
		// make call
			$result =  wp_remote_retrieve_body($response);
			$headers =  wp_remote_retrieve_headers($response);
			$response_code = wp_remote_retrieve_response_code($response);
			// $json_output contains the output string
			$json_output = $result;
		} else {
			$response_code = $response->get_error_code();
		}

		remove_filter('https_ssl_verify', array($this, 'https_ssl_verify'));
		remove_filter('http_request_timeout', array($this, 'filter_timeout_time'));

		// catch errors
		if(is_wp_error($response)) {
			throw new W3tcWpHttpException("ERROR: {$response->get_error_message()}, Output: $json_output", $response_code, null, $headers);
		}

		return $json_output;
	}

	/**
	 * @param $selected_call
	 * @param array $params
	 * @return string
	 * @throws W3tcWpHttpException
	 */
	public function get($selected_call, $params = array()){
		return $this->execute($selected_call, 'GET', $params);
	}

	/**
	 * @param $selected_call
	 * @param array $params
	 * @return string
	 * @throws W3tcWpHttpException
	 */
	public function post($selected_call, $params = array()){
		return $this->execute($selected_call, 'POST', $params);
	}

	/**
	 * @param $selected_call
	 * @param array $params
	 * @return string
	 * @throws W3tcWpHttpException
	 */
	public function put($selected_call, $params = array()){
		return $this->execute($selected_call, 'PUT', $params);
	}

	/**
	 * @param $selected_call
	 * @param array $params
	 * @return string
	 * @throws W3tcWpHttpException
	 */
	public function delete($selected_call, $params = array()){
		return $this->execute($selected_call, 'DELETE', $params);
	}

	/**
	 * Finds the zone id that matches the provided url.
	 * @param $url
	 * @return null|int
	 * @throws W3tcWpHttpException
	 */
	public function get_zone_id($url) {
		$zone_id = null;
		$pull_zones =  json_decode($this->get('/zones/pull.json'));

		if (preg_match("(200|201)", $pull_zones->code)) {
			foreach ($pull_zones->data->pullzones as $zone) {
				if (trim($zone->url, '/') != trim($url, '/'))
					continue;
				else {
					$zone_id = $zone->id;
					break;
				}
			}
		} else
			return null;
		return $zone_id;
	}

	/**
	 * Retrieves statistics for the zone id
	 * @param $zone_id
	 * @return null|array
	 * @throws W3tcWpHttpException
	 */
	public function get_stats_per_zone($zone_id) {
		$api_stats = json_decode($this->get("/reports/{$zone_id}/stats.json"), true);
		if (preg_match("(200|201)", $api_stats['code'])) {
			$summary = $api_stats['data']['summary'];
			return $summary;
		} else
			return null;
	}

	/**
	 * Returns list of files for the zone id
	 * @param $zone_id
	 * @return null|array
	 * @throws W3tcWpHttpException
	 */
	public function get_list_of_file_types_per_zone($zone_id) {
		$api_list = json_decode($this->get("/reports/pull/{$zone_id}/filetypes.json"), true);
		if (preg_match("(200|201)", $api_list['code'])) {
			$stats['total'] = $api_list['data']['total'];

			foreach($api_list['data']['filetypes'] as $filetyp) {
				$stats['filetypes'][] = $filetyp;
			}
			$stats['summary'] = $api_list['data']['summary'];
			return $stats;
		} else
			return null;
	}

	/**
	 * Retrieves a list of popular files for zone id
	 *
	 * @param $zone_id
	 * @return null|array
	 * @throws W3tcWpHttpException
	 */
	public function get_list_of_popularfiles_per_zone($zone_id) {
		$api_popularfiles = json_decode($this->get("/reports/{$zone_id}/popularfiles.json"), true);
		if (preg_match("(200|201)", $api_popularfiles['code'])) {
			$popularfiles = $api_popularfiles['data']['popularfiles'];
			return $popularfiles;
		} else
			return null;
	}

	/**
	 * Retrieves an account connected with the authorization key
	 *
	 * @throws Exception
	 * @return null|string
	 */
	public function get_account() {
		$api_account = json_decode($this->get("/account.json"), true);
		if (preg_match("(200|201)", $api_account['code'])) {
			$account = $api_account['data']['account'];
			return $account;
		} else
			throw new Exception($this->error_message($api_account));
	}

	/**
	 * Retrieves a pull zone
	 * @param $zone_id
	 * @throws Exception
	 * @return null|string
	 */
	public function get_pull_zone($zone_id) {
		$api_pull_zone = json_decode($this->get("/zones/pull.json/{$zone_id}"), true);
		if (preg_match("(200|201)", $api_pull_zone['code'])) {
			$pull_zone = $api_pull_zone['data']['pullzone'];
			return $pull_zone;
		} else
			throw new Exception($this->error_message($api_pull_zone));
	}

	/**
	 * Creates a pull zone
	 * @param $zone
	 * @return mixed
	 * @throws Exception
	 */
	public function create_pull_zone($zone) {
		$zone_data = json_decode($this->post('/zones/pull.json', $zone), true);
		if (preg_match("(200|201)", $zone_data['code'])) {
			return $zone_data['data']['pullzone'];
		} else
			throw new Exception($this->error_message($zone_data));
	}

	private function error_message($o) {
		$m = isset( $o['error']['message'] ) ? $o['error']['message'] : '';

		if ( isset( $o['data']['errors'] ) && is_array( $o['data']['errors'] ) ) {
			foreach ( $o['data']['errors'] as $k => $v ) {
				$m .= '. ' . $k . ': ' . $v;
			}
		}

		return $m;
	}

	/**
	 * Returns all zones connected to an url
	 * @param $url
	 * @throws Exception
	 * @return array|null
	 */
	public function get_zones_by_url($url) {
		$zone_id = null;
		$pull_zones =  json_decode($this->get('/zones/pull.json'), true);
		$zones = array();
		if (preg_match("(200|201)", $pull_zones['code'])) {
			foreach ($pull_zones ['data']['pullzones'] as $zone) {
				if (trim($zone['url'], '/') != trim($url, '/'))
					continue;
				else {
					$zones[] = $zone;
				}
			}
		} else
			throw new Exception($this->error_message($pull_zones));
		return $zones;
	}

	/**
	 * Retrieves pull zones
	 * @throws Exception
	 * @return array|null
	 */
	public function get_pull_zones() {
		$pull_zones =  json_decode($this->get('/zones/pull.json'), true);
		$zones = array();
		if (preg_match("(200|201)", $pull_zones['code'])) {
			foreach ($pull_zones ['data']['pullzones'] as $zone) {
				$zones[] = $zone;
			}
		} else {
			throw new Exception($this->error_message($zone_data));
		}
		return $zones;
	}

	/**
	 * Increase http request timeout to 60 seconds
	 * @param int $time
	 * @return int
	 */
	public function filter_timeout_time($time) {
		return 600;
	}

	/**
	 * Don't check certificate, some users have limited CA list
	 */
	public function https_ssl_verify($v) {
		return false;
	}

	/**
	 * Update a pull zone
	 * @param $zone_id
	 * @param $zone
	 * @throws Exception
	 * @return
	 */
	public function update_pull_zone($zone_id, $zone) {
		$zone_data = json_decode($this->put("/zones/pull.json/$zone_id", $zone), true);
		if (preg_match("(200|201)", $zone_data['code'])) {
			return $zone_data['data']['pullzone'];
		} else {
			throw new Exception($this->error_message($zone_data));
		}
	}

	/**
	 * Creates a new pull zone with default settings
	 * @param string $url the sites url 4-100 chars; only valid URLs accepted
	 * @param null|string $name 3-32 chars; only letters, digits, and dash (-)accepted
	 * @param null|string $label length: 1-255 chars
	 * @param array $zone_settings custom settings
	 * @return string
	 */
	public function create_default_pull_zone($url, $name = null, $label = null, $zone_settings=array()) {
		$zone_defaults = array();
		if (is_null($name)) {
			$name = md5($url);
			$len = strlen($name)>24 ? 24 : strlen($name);
			$name = substr($name, 0, $len);
		}
		if (is_null($label))
			$label = sprintf(__('Zone for %s was created by W3 Total Cache', 'w3-total-cache'), $url);
		$zone_defaults['name'] = $name;
		$zone_defaults['label'] = $label;
		$zone_defaults['url'] = $url;
		$zone_defaults['use_stale'] = 0;
		$zone_defaults['queries'] = 1;
		$zone_defaults['compress'] = 1;
		$zone_defaults['backend_compress'] = 1;
		$zone_defaults['disallow_robots'] = 1;
		$zone_defaults = array_merge( $zone_defaults, $zone_settings);
		$response = $this->create_pull_zone($zone_defaults);
		return $response;
	}

	/**
	 * Returns number of zones
	 * @throws Exception
	 * @return array
	 */
	public function get_zone_count() {
		$pull_zones =  json_decode($this->get('/zones.json/count'), true);
		if (preg_match("(200|201)", $pull_zones['code'])) {
			return intval($pull_zones ['data']['count']);
		} else
			throw new Exception($this->error_message($pull_zones));
	}

	/**
	 * Creates custom domains
	 * @param $zone_id
	 * @throws Exception
	 * @return array|null
	 */
	public function create_custom_domain($zone_id, $custom_domain) {
		$custom_domain =  json_decode($this->post("/zones/pull/$zone_id/customdomains.json", array(
			'custom_domain' => $custom_domain)), true);
		if (preg_match("(200|201)", $custom_domain['code'])) {
			return $custom_domain;
		} else
			throw $this->to_exception($custom_domain);
	}

	private function to_exception($response) {
		$message = $response['error']['message'];
		if ( isset( $response['data'] ) && isset( $response['data']['errors'] ) ) {
			foreach ( $response['data']['errors'] as $field => $error ) {
				if ( isset( $error['error'] ) )
					$message .= '. ' . $field . ': ' . $error['error'];
				else
					$message .= '. ' . $field . ': ' . $error;
			}
		}

		return new Exception($message);
	}

	/**
	 * Returns custom domains
	 * @param $zone_id
	 * @throws Exception
	 * @return array|null
	 */
	public function get_custom_domains($zone_id) {
		$custom_domains =  json_decode($this->get("/zones/pull/$zone_id/customdomains.json"), true);
		$domains = array();
		if (preg_match("(200|201)", $custom_domains['code'])) {
			foreach ($custom_domains['data']['customdomains'] as $domain) {
				$domains[] = $domain['custom_domain'];
			}
		} else
			throw new Exception($this->error_message($custom_domains));
		return $domains;
	}

	/**
	 * Returns the zone data for the provided zone id
	 *
	 * @param int $zone_id
	 * @throws Exception
	 * @return array
	 */
	public function get_zone($zone_id) {
		$zone_data = json_decode($this->get("/zones/pull.json/$zone_id"), true);
		if (preg_match("(200|201)", $zone_data['code'])) {
			return $zone_data['data']['pullzone'];
		} else
			throw new Exception($this->error_message($zone_data));
	}

	/**
	 * Deletes files from cache
	 * @param $zone_id
	 * @param $files array of relative paths to files to delete
	 *        Deletes whole zone if empty list passed
	 **/
	public function cache_delete($zone_id, $files = array()) {
		if (empty($files))
			$params = array();
		else
			$params = array('files' => $files);

		$response = json_decode($this->delete(
			'/zones/pull.json/' . $zone_id . '/cache',
			$params), true);

		if (preg_match("(200|201)", $response['code'])) {
			return true;
		} else
			throw $this->to_exception($response);
	}

}
