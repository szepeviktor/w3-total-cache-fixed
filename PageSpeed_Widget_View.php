<?php
namespace W3TC;

if ( !defined( 'W3TC' ) )
	die();

?>
<div class="w3tcps_loading w3tc_loading w3tc_hidden">Loading...</div>
<div class="w3tcps_error w3tc_none">
    <p>Unable to fetch Page Speed results.</p>
    <p>
        <input class="button w3tc-widget-ps-refresh" type="button" value="Refresh Analysis" />
    </p>
</div>

<div class="w3tcps_content w3tc_hidden">
    <h4>Page Speed Score: <span class="w3tcps_score"></span></h4>

    <div class="w3tcps_list">
        <ul class="w3tc-widget-ps-rules">
            <li>
                <div class="w3tc-widget-ps-icon"><div></div></div>
                <p>.</p>
            </li>
            <li>
                <div class="w3tc-widget-ps-icon"><div></div></div>
                <p>.</p>
            </li>
            <li>
                <div class="w3tc-widget-ps-icon"><div></div></div>
                <p>.</p>
            </li>
            <li>
                <div class="w3tc-widget-ps-icon"><div></div></div>
                <p>.</p>
            </li>
            <li>
                <div class="w3tc-widget-ps-icon"><div></div></div>
                <p>.</p>
            </li>
        </ul>
    </div>

    <p>
        <input class="button w3tc-widget-ps-refresh" type="button" value="Refresh analysis" />
        <input class="button w3tc-widget-ps-view-all {nonce: '<?php echo wp_create_nonce( 'w3tc' ); ?>'}" type="button" value="View all results" />
    </p>
</div>
