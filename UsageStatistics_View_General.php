<?php
namespace W3TC;

if ( !defined( 'W3TC' ) )
	die();

?>
<tr>
    <th colspan="2">
        <?php

$c = Dispatcher::config();
$is_pro = Util_Environment::is_w3tc_pro( $c );

$key = 'stats.enabled';
$value = $c->get( $key );
if ( !$is_pro )
	$value = false;

$name = Util_Ui::config_key_to_http_name( $key );
Util_Ui::checkbox( $key, 
	$name, 
	$value, 
	$c->is_sealed( 'common.' ) || !$is_pro,
	__( 'Enable caching statistics (on dashboard)', 'w3-total-cache' )
);

if ( !$is_pro )
	echo ' (Available after <a href="#" class="button-buy-plugin">upgrade</a>)';
?>
    </th>
</tr>
