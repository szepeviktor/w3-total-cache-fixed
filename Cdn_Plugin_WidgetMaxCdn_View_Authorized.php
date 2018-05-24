<?php
namespace W3TC;

if ( !defined( 'W3TC' ) )
	die();

/**
 *
 *
 * @var int $zone_id
 * @var array $summary
 * @var array $popular_files
 * @var string $content_zone
 * @var string $account_status
 */

?>
<div class="w3tcmaxcdn_loading w3tc_loading w3tc_hidden">Loading...</div>
<div class="w3tcmaxcdn_error w3tc_none">
    An error occurred
    <div class="w3tcmaxcdn_error_details"></div>
</div>

<div id="maxcdn-widget" class="maxcdn-netdna-widget-base w3tcmaxcdn_content w3tc_hidden">
    <div class="w3tcmaxcdn_wrapper">
        <div class="w3tcmaxcdn_status">
            <p>
                <span>
                	<?php _e( 'Status', 'w3-total-cache' ) ?>
                	<span class="w3tcmaxcdn_account_status"></span>
            	</span>
                <span style="display:inline-block;float:right">
                	<?php _e( 'Content Zone:', 'w3-total-cache' ) ?>
                	<span class="w3tcmaxcdn_zone_name"></span>
                </span>
            </p>

        </div>
        <div class="w3tcmaxcdn_tools">
            <ul class="w3tcmaxcdn_ul">
                <li><a class="button w3tcmaxcdn_href_manage" href=""><?php _e( 'Manage', 'w3-total-cache' )?></a></li>
                <li><a class="button w3tcmaxcdn_href_reports" href=""><?php _e( 'Reports', 'w3-total-cache' )?></a></li>
                <li><a class="button" href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=w3tc_cdn&amp;w3tc_cdn_purge' ) )?>" onclick="w3tc_popupadmin_bar(this.href); return false"><?php _e( 'Purge', 'w3-total-cache' )?></a></li>
            </ul>
        </div>
        <div class="w3tcmaxcdn_summary">
            <h4 class="w3tcmaxcdn_summary_h4"><?php _e( 'Report - 30 days', 'w3-total-cache' ) ?></h4>
        </div>
        <ul class="w3tcmaxcdn_ul">
            <li>
            	<span class="w3tcmaxcdn_summary_col1"><?php _e('Transferred', 'w3-total-cache') ?>:</span>
            	<span class="w3tcmaxcdn_summary_col2 w3tcmaxcdn_summary_size"></span>
            </li>
            <li>
            	<span class="w3tcmaxcdn_summary_col1"><?php _e('Cache Hits', 'w3-total-cache' ) ?>:</span>
            	<span class="w3tcmaxcdn_summary_col2">
            		<span class="w3tcmaxcdn_summary_cache_hit"></span>
            		(<span class="w3tcmaxcdn_summary_cache_hit_percentage"></span>)
            	</span>
            </li>
            <li>
            	<span class="w3tcmaxcdn_summary_col1"><?php _e('Cache Misses', 'w3-total-cache') ?>:</span>
            	<span class="w3tcmaxcdn_summary_col2">
            		<span class="w3tcmaxcdn_summary_noncache_hit">
            		(<span class="w3tcmaxcdn_summary_noncache_hit_percentage"></span>)
            	</span>
            </li>
        </ul>
        <div class="w3tcmaxcdn_chart charts w3tcmaxcdn_area">
            <h4 class="w3tcmaxcdn_h4"><?php _e( 'Requests', 'w3-total-cache' ) ?></h4>
            <div id="chart_div" style="width: 320px; height: 220px;margin-left: auto ;  margin-right: auto ;"></div>
            <h4 class="w3tcmaxcdn_h4"><?php _e( 'Content Breakdown', 'w3-total-cache' ) ?></h4>
            <p>
                <span><?php _e( 'File', 'w3-total-cache' )?></span>
                <span style="display:inline-block;float:right"><?php _e( 'Hits', 'w3-total-cache' ) ?></span>
            </p>
            <ul class="w3tcmaxcdn_file_hits">
            	<li>A</li>
            	<li>A</li>
            	<li>A</li>
            	<li>A</li>
            	<li>A</li>
            </ul>
        </div>
    </div>
</div>
