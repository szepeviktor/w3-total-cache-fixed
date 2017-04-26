<?php
namespace W3TC;

/**
 * Amazon CloudFront (Custom origin) CDN engine
 */

class CdnEngine_S3_Cf_Custom extends CdnEngine_S3_Cf {
	var $type = W3TC_CDN_CF_TYPE_CUSTOM;

	/**
	 * How and if headers should be set
	 *
	 * @return string W3TC_CDN_HEADER_NONE, W3TC_CDN_HEADER_UPLOADABLE, W3TC_CDN_HEADER_MIRRORING
	 */
	function headers_support() {
		return W3TC_CDN_HEADER_MIRRORING;
	}
}
