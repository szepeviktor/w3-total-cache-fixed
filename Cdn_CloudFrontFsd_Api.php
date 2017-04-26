<?php
namespace W3TC;

class Cdn_CloudFrontFsd_Api {
	private $access_key; // AWS Access key
	private $secret_Key; // AWS Secret key
	private $api_host;  // AWS host where API is located


	public function __construct( $access_key = null, $secret_key = null,
		$api_host = 'cloudfront.amazonaws.com' ) {
		$this->access_key = $access_key;
		$this->secret_key = $secret_key;
		$this->api_host = $api_host;
	}



	/**
	 * Get a list of CloudFront distributions
	 */
	public function distributions_list() {
		$response = $this->request( 'GET', '/2014-11-06/distribution' );
		$data = $response['body_array'];

		$data = $this->fix_array( $data, array(
				'Items', 'DistributionSummary' ) );

		if ( isset( $data['Items']['DistributionSummary'] ) ) {
			foreach ( $data['Items']['DistributionSummary'] as $key => $value ) {
				$data['Items']['DistributionSummary'][$key] =
					$this->fix_distribution(
					$data['Items']['DistributionSummary'][$key] );
			}
		}

		return $data;
	}



	/**
	 * Get a list of CloudFront distributions
	 */
	public function distribution_get( $id ) {
		$response = $this->request( 'GET', '/2014-11-06/distribution/' . $id );
		$data = $response['body_array'];

		if ( isset( $data['DistributionConfig'] ) )
			$data['DistributionConfig'] = $this->fix_distribution(
				$data['DistributionConfig'] );

		return $data;
	}



	/**
	 * $distribution
	 *   origin
	 */
	public function distribution_create( $distribution ) {
		if ( !isset( $distribution['CallerReference'] ) )
			$distribution['CallerReference'] = rand();
		if ( !isset( $distribution['Enabled'] ) )
			$distribution['Enabled'] = 'true';

		$data =
			'<?xml version="1.0" encoding="UTF-8"?>' .
			'<DistributionConfig xmlns="http://cloudfront.amazonaws.com/doc/2014-11-06/">' .
			$this->array_to_xml( $distribution ) .
			'</DistributionConfig>';

		$response = $this->request( 'POST', '/2014-11-06/distribution', $data );
		$response_data = $response['body_array'];

		return $response_data;
	}



	/**
	 * $distribution
	 *   origin
	 */
	public function distribution_update( $distribution_id, $distribution ) {
		$get_response = $this->request( 'GET', '/2014-11-06/distribution/' .
			$distribution_id );
		$get_distribution = $get_response['body_array'];

		$c = $get_distribution['DistributionConfig'];

		// update values
		foreach ( $distribution as $key => $value ) {
			if ( $key == 'DefaultCacheBehavior' ) {
				// inherit inner values of that setting too
				foreach ( $distribution[$key] as $key2 => $value2 ) {
					$c[$key][$key2] = $value2;
				}
			} else {
				$c[$key] = $value;
			}
		}

		$data =
			'<?xml version="1.0" encoding="UTF-8"?>' .
			'<DistributionConfig xmlns="http://cloudfront.amazonaws.com/doc/2014-11-06/">' .
			$this->array_to_xml( $c ) .
			'</DistributionConfig>';

		$response = $this->request( 'PUT',
			'/2014-11-06/distribution/' . $distribution_id . '/config', $data,
			array( 'If-Match' => $get_response['headers']['etag'] ) );
		$data = $response['body_array'];

		if ( isset( $data['DistributionConfig'] ) )
			$data['DistributionConfig'] = $this->fix_distribution(
				$data['DistributionConfig'] );

		return $data;
	}



	/**
	 * Invalidate cache
	 */
	public function invalidation_create( $distribution_id, $uris ) {
		$data =
			'<?xml version="1.0" encoding="UTF-8"?>' .
			'<InvalidationBatch xmlns="http://cloudfront.amazonaws.com/doc/2014-11-06/">' .
			'<Paths>' .
			'<Quantity>' . count( $uris ) . '</Quantity>' .
			$this->array_to_xml( array(
				'Items' => array(
					'Path' => $uris
				)
			) ) .
			'</Paths>' .
			'<CallerReference>' . rand() . '</CallerReference>' .
			'</InvalidationBatch>';

		$response = $this->request( 'POST',
			'/2014-11-06/distribution/' . $distribution_id . '/invalidation', $data );
		$response_data = $response['body_array'];

		return $response_data;
	}

	/**
	 * Constructor
	 *
	 * @param string  $verb   Verb
	 * @param string  $bucket Bucket name
	 * @param string  $uri    Object URI
	 * @return mixed
	 */
	private function request( $method, $uri = '', $data = '', $headers = array() ) {
		$url = 'https://' . $this->api_host . $uri;

		$headers['Host'] = $this->api_host;
		$headers['x-amz-date'] = gmdate( 'Ymd\THis\Z', time() );

		if ( $method == "POST" || $method == "PUT" ) {
			$headers['Content-Type'] = 'application/xml; charset=utf-8';
			$headers['Content-Length'] = strlen( $data );
		} else {
			$data = '';
		}

		$headers['Authorization'] = $this->calculateSignature( $method, $uri,
			$headers, $data );


		// do the request
		$request = array(
			'sslverify' => false,
			'headers' => $headers,
			'method' => $method,
			'body' => $data
		);

		$response = wp_remote_request( $url, $request );

		// handle response
		if ( substr( $response['body'], 0, 5 ) != '<?xml' )
			throw new \Exception( 'Unexpected non-xml response from service received' );

		$xml = simplexml_load_string( $response['body'] );
		$json = json_encode( $xml );
		$body_array = json_decode( $json, TRUE );
		$response['body_array'] = $body_array;

		if ( isset( $body_array['Error'] ) && isset( $body_array['Error']['Message'] ) )
			throw new \Exception( $body_array['Error']['Message'] );

		return $response;
	}



	public function calculateSignature( $method, $uri, $headers, $data ) {
		$short_date = substr( $headers['x-amz-date'], 0, 8 );

		// Parse the service and region or use one that is explicitly set
		$region = 'us-east-1';
		$service = 'cloudfront';

		$credential_scope = $short_date . '/' . $region . '/' . $service .
			'/aws4_request';


		// calc payload
		if ( empty( $data ) )
			$payload = 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855';
		else
			$payload = hash( 'sha256', $data );


		// create canonical request
		$canonical_headers = array(
			'host:' . $headers['Host'],
			'x-amz-date:' . $headers['x-amz-date']
		);

		$signed_headers = 'host;x-amz-date';
		$canonical_request = $method . "\n" .
			$this->canonicalize_uri( $uri ) . "\n" .
			'' /* query string */ . "\n" .
			implode( "\n", $canonical_headers ) . "\n\n" .
			$signed_headers . "\n" .
			$payload;

		$string_to_sign =
			"AWS4-HMAC-SHA256\n" .
			$headers['x-amz-date'] . "\n" .
			$credential_scope . "\n" .
			hash( 'sha256', $canonical_request );

		// Calculate the signing key using a series of derived keys
		$signingKey = $this->get_signing_key( $short_date, $region, $service,
			$this->secret_key );
		$signature = hash_hmac( 'sha256', $string_to_sign, $signingKey );

		return "AWS4-HMAC-SHA256 " .
			"Credential={$this->access_key}/{$credential_scope}, " .
			"SignedHeaders={$signed_headers}, Signature={$signature}";
	}




	private function canonicalize_uri( $uri ) {
		$doubleEncoded = rawurlencode( ltrim( $uri, '/' ) );
		return '/' . str_replace( '%2F', '/', $doubleEncoded );
	}



	private function get_signing_key( $short_date, $region, $service, $secretKey ) {
		$dateKey = hash_hmac( 'sha256', $short_date, 'AWS4' . $secretKey, true );
		$regionKey = hash_hmac( 'sha256', $region, $dateKey, true );
		$serviceKey = hash_hmac( 'sha256', $service, $regionKey, true );

		return hash_hmac( 'sha256', 'aws4_request', $serviceKey, true );
	}



	private function array_to_xml( $a ) {
		if ( !is_array( $a ) )
			return $a;

		$s = '';
		foreach ( $a as $key => $value ) {
			if ( is_array( $value ) && isset( $value[0] ) ) {
				// number-indexed array, serialized as list
				foreach ( $value as $array_item ) {
					$s .=
						'<' . $key . '>' .
						$this->array_to_xml( $array_item ) .
						'</' . $key . '>';
				}
			} else {
				$s .=
					'<' . $key . '>' .
					$this->array_to_xml( $value ) .
					'</' . $key . '>';
			}
		}

		return $s;
	}



	/**
	 * XML parsing suffers common problem of array recognition
	 *  <items><item>a</></> is accessible via $data['items']['item']
	 * but
	 *  <items><item>a</><item>b</></> is accessible
	 *  via $data['items'][$n]['item']
	 *
	 * by knowing tag we can try to guess that it contains only 1 element
	 * and turn it to array so that it will be always accessible via 2nd way
	 */
	private function fix_array( $response, $keys ) {
		$a = &$response;

		for ( $n = 0; $n < count( $keys ) - 1; $n++ ) {
			$key = $keys[$n];
			if ( !isset( $a[$key] ) )
				break;
			$a = &$a[$key];
		}

		$last_key = $keys[count( $keys ) - 1];
		if ( isset( $a[$last_key] ) ) {
			if ( !isset( $a[$last_key][0] ) || is_string( $a[$last_key] ) ) {
				$a[$last_key] = array(
					$a[$last_key]
				);
			}
		}

		return $response;
	}



	private function fix_distribution( $data ) {
		$data = $this->fix_array( $data, array( 'Aliases', 'Items', 'CNAME' ) );
		$data = $this->fix_array( $data, array( 'Origins', 'Items', 'Origin' ) );
		$data = $this->fix_array( $data, array( 'DefaultCacheBehavior',
				'ForwardedValues', 'Headers', 'Items', 'Name' ) );

		return $data;
	}
}
