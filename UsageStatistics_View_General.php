<?php
namespace W3TC;

if ( !defined( 'W3TC' ) )
	die();

?>
<tr>
    <th colspan="2">
        <?php

$key = 'stats.enabled';
$c = Dispatcher::config();
$value = $c->get( $key );
$label = Util_Ui::config_label( $key );
$name = Util_Ui::config_key_to_http_name( $key );
Util_Ui::checkbox( $key, $name, $value, $c->is_sealed( 'common.' ),
	$label );

?>
    </th>
</tr>
