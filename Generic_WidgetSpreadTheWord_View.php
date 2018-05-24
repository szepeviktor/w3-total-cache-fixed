<?php
namespace W3TC;

if ( !defined( 'W3TC' ) )
	die();

?>
<p><?php _e( "Enjoying W3TC? Please support us!", 'w3-total-cache' ) ?></p>
<ul>
    <li><label>Vote: </label><input type="button" class="button button-vote" value="Give us a 5 stars!" />
    <!-- <li><label>Share: </label><input type="button" class="button button-share" value="Share on Google+ Now" /></li>
    <li><label>Tweet: </label><input type="button" class="button button-tweet" value="Post to Twitter Now" /></li>
    <li><label>Like: </label><input type="button" class="button button-like" value="Like on Facebook Now" /></li>
    <li><label><?php _e( 'Rate:', 'w3-total-cache' )?> </label><input type="button" class="button button-rating" value="Vote &amp; Rate Now" /></li>
    <li><label><?php _e( 'Link:', 'w3-total-cache' ) ?></label>
        <select id="common_support" name="common__support" class="w3tc-ignore-change">
            <option value=""><?php esc_attr_e( 'select one', 'w3-total-cache' )?></option>
            <?php foreach ( $supports as $support_id => $support_name ): ?>
            <option value="<?php echo esc_attr( $support_id ); ?>" <?php selected( $support, $support_id ); ?>><?php echo esc_attr( $support_name ); ?></option>
            <?php endforeach; ?>
        </select>
    </li>-->
</ul>

<p>Or please share <a href="admin.php?page=w3tc_support&amp;request_type=new_feature">your feedback</a> so that we can improve!</p>
<!--<p><?php _e( 'Or manually place a link, here is the code:', 'w3-total-cache' ) ?></p>
<div class="w3tc-manual-link widefat"><p><?php echo sprintf( __( 'Optimization %s by W3 EDGE', 'w3-total-cache' ), "&lt;a href=&quot;https://www.w3-edge.com/products/&quot; rel=&quot;nofollow&quot;&gt;WordPress Plugins&lt;/a&gt;" )?></p></div>-->
