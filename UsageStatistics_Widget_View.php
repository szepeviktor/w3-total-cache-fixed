<?php
namespace W3TC;

if ( !defined( 'W3TC' ) )
	die();
?>
<div class="ustats_loading w3tc_loading">Loading...</div>
<div class="ustats_error w3tc_none">An error occurred</div>
<div class="ustats_nodata w3tc_none">No data collected</div>
<div class="ustats_content w3tc_hidden">
    <p class="ustats_p ustats_top">
        Period <span class="ustats_period_timestamp_start_mins"></span>
          -
          <span class="ustats_period_timestamp_end_mins"></span>
    </p>



    <?php if ( isset( $summary_promise['pagecache'] ) ): ?>
    <div class="ustats_header">Page Cache:</div>
    <?php if ( $summary_promise['pagecache']['size_visible'] ): ?>
        Cache size: <span class="ustats_pagecache_size_used"></span><br />
        Entries: <span class="ustats_pagecache_items"></span><br />
    <?php endif; ?>
    <?php if ( $summary_promise['pagecache']['requests_visible'] ): ?>
        Requests/period: <span class="ustats_pagecache_requests_total"></span><br />
        Requests/sec: <span class="ustats_pagecache_requests_per_second"></span><br />
        Avg processing time (ms): <span class="ustats_pagecache_request_time_ms"></span><br />
        Hit rate: <span class="ustats_pagecache_hit_rate"></span><br />
    <?php endif; ?>
    <br />
    <?php endif; ?>



    <?php if ( isset( $summary_promise['minify'] ) ): ?>
    <div class="ustats_header">Minify:</div>
    <?php if ( $summary_promise['minify']['size_visible'] ): ?>
        Used: <span class="ustats_minify_size_used"></span><br />
        Files: <span class="ustats_minify_size_items"></span><br />
        CSS compression in cache: <span class="ustats_minify_size_compression_css"></span><br />
        JS compression in cache: <span class="ustats_minify_size_compression_js"></span><br />
    <?php endif ?>
    <?php if ( $summary_promise['minify']['requests_visible'] ): ?>
        Requests/period: <span class="ustats_minify_requests_total"></span><br />
        Requests/sec: <span class="ustats_minify_requests_per_second"></span><br />
        Responded CSS compression: <span class="ustats_minify_compression_css"></span><br />
        Responded JS compression: <span class="ustats_minify_compression_js"></span><br />
    <?php endif; ?>
    <br />
    <?php endif; ?>



    <?php if ( isset( $summary_promise['objectcache'] ) ): ?>
    <div class="ustats_header">Object Cache:</div>
    Calls/period: <span class="ustats_objectcache_calls_total"></span><br />
    Calls/sec: <span class="ustats_objectcache_calls_per_second"></span><br />
    Hit rate: <span class="ustats_objectcache_hit_rate"></span><br />
    <br />
    <?php endif; ?>



    <?php if ( isset( $summary_promise['fragmentcache'] ) ): ?>
    <div class="ustats_header">Fragment Cache:</div>
    Calls/period: <span class="ustats_fragmentcache_calls_total"></span><br />
    Calls/sec: <span class="ustats_fragmentcache_calls_per_second"></span><br />
    Hit rate: <span class="ustats_fragmentcache_hit_rate"></span><br />
    <br />
    <?php endif; ?>



    <?php if ( isset( $summary_promise['dbcache'] ) ): ?>
    <?php $m = $summary_promise['dbcache']; ?>
    <div class="ustats_header">Database Cache:</div>
    Calls/period: <span class="ustats_dbcache_calls_total"></span><br />
    Calls/sec: <span class="ustats_dbcache_calls_per_second"></span><br />
    Hit rate: <span class="ustats_dbcache_hit_rate"></span><br />
    <br />
    <?php endif; ?>



    PHP Memory: <span class="ustats_php_memory"></span><br />
    WordPress requests/period: <span class="ustats_php_wp_requests_total"></span><br />
    WordPress requests/sec: <span class="ustats_php_wp_requests_per_second"></span><br />
    <br />


    <?php if ( !empty( $summary_promise['memcached'] ) ): ?>
    <?php foreach ( $summary_promise['memcached'] as $id => $m ): ?>
        <div class="ustats_header">Memcached <?php echo $m['name'] ?></div>
        Used by <?php echo implode( ',', $m['module_names'] ) ?><br />
        Evictions/sec: <span class="ustats_memcached_<?php echo $id ?>_evictions_per_second"></span><br />
        Used: <span class="ustats_memcached_<?php echo $id ?>_size_used"></span><br />
        Used (%): <span class="ustats_memcached_<?php echo $id ?>_size_percent"></span><br />
        Hit rate: <span class="ustats_memcached_<?php echo $id ?>_get_hit_rate"></span><br />
    <?php endforeach ?>
    <?php endif; ?>


    <?php if ( !empty( $summary_promise['redis'] ) ): ?>
    <?php foreach ( $summary_promise['redis'] as $id => $m ): ?>
        <div class="ustats_header">Redis <?php echo $m['name'] ?></div>
        Used by <?php echo implode( ',', $m['module_names'] ) ?><br />
        Evictions/sec: <span class="ustats_redis_<?php echo $id ?>_evictions_per_second"></span><br />
        Expirations/sec: <span class="ustats_redis_<?php echo $id ?>_expirations_per_second"></span><br />
        Used: <span class="ustats_redis_<?php echo $id ?>_size_used"></span><br />
        Hit rate: <span class="ustats_redis_<?php echo $id ?>_hit_rate"></span><br />
    <?php endforeach ?>
    <?php endif; ?>
</div>
<a href="#" class="ustats_reload">Refresh</a>
