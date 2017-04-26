<?php
namespace W3TC;

if ( !defined( 'W3TC' ) )
	die();

?>
<div id="w3tc-help">
	<p><?php _e( 'Request professional <a href="admin.php?page=w3tc_support" style="color: red;"><strong>support</strong></a> or troubleshoot issues using the common questions below:', 'w3-total-cache' ); ?></p>

	<?php foreach ( $columns as $entries ): ?>
        <ul>
            <?php foreach ( $entries as $entry ): ?>
                <li>
                    <a href="admin.php?page=w3tc_faq#<?php echo $entry['tag']; ?>"><?php
	echo $entry['question'];
?></a>
            	</li>
            <?php endforeach; ?>
        </ul>
    <?php endforeach; ?>

    <div style="clear: left;"></div>
</div>
