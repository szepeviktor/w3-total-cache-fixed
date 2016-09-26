<?php
namespace W3TC;



class Extension_FeedBurner_Page {
	static public function w3tc_extension_page_feedburner() {
		$config = Dispatcher::config();
		include  W3TC_DIR . '/Extension_FeedBurner_Page_View.php';
	}
}
