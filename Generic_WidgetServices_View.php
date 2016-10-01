<?php
namespace W3TC;

if ( !defined( 'W3TC' ) )
	die();

?>
<form action="admin.php" xmlns="http://www.w3.org/1999/html" method="get">
<input type="hidden" name="page" value="w3tc_support" />

<ul>
<?php
for ( $n = 0; $n < count( $items ); $n++ ): ?>
    <li>
    	<div class="w3tc_generic_widgetservice_radio_outer">
	    	<input id="service<?php echo $n ?>" 
	    		type="radio"
	    		class="w3tc_generic_widgetservice_radio w3tc-ignore-change"
	    		name="service_item"
	    		value="<?php echo $n ?>"
	    		/>
	    </div>
    	<label for="service<?php echo $n ?>" class="w3tc_generic_widgetservice_label">
    		<?php echo htmlspecialchars( $items[$n]['name'] ) ?>
   		</label>
    </li>
<?php endfor ?>
</ul>
<div id="buy-w3-service-area"></div>
<p>
    <input id="buy-w3-service" name="buy-w3-service" type="submit" 
    	class="button button-primary button-large" 
    	value="<?php _e( 'Buy now', 'w3-total-cache' ) ?>" />
</p>
</form>
