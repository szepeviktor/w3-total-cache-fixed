<?php
namespace W3TC;

if ( !defined( 'W3TC' ) )
	die();

include W3TC_INC_DIR . '/options/common/header.php';
?>

<h4 id="toc"><?php _e( 'Table of Contents', 'w3-total-cache' ); ?></h4>

<?php foreach ( $columns as $number => $sections ): ?>
    <div style="float: left; width: 29%; margin-left: 30px">
        <?php foreach ( $sections as $section ): ?>
            <?php if ( isset( $faq[$section] ) ): ?>
                <div style="margin-bottom: 20px">
                    <h5><?php echo strtoupper( $section ); ?>:</h5>
                    <ul>
                	    <?php foreach ( $faq[$section] as $entry ): ?>
                            <li><a href="#<?php echo $entry['tag']; ?>"><?php echo $entry['question']; ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
<?php endforeach; ?>

<div id="qa">
	<hr />
    <?php foreach ( $faq as $section => $entries ): ?>
        <?php foreach ( $entries as $entry ): ?>
            <p id="<?php echo $entry['tag']; ?>"><strong><?php echo $entry['question']; ?></strong></p>
            <?php echo $entry['answer']; ?>
    	    <p align="right"><a href="#toc">back to top</a></p>
        <?php endforeach; ?>
    <?php endforeach; ?>
</div>

<?php include W3TC_INC_DIR . '/options/common/footer.php'; ?>
