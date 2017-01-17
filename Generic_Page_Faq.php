<?php
namespace W3TC;



class Generic_Page_Faq extends Base_Page_Settings {
	/**
	 * Current page
	 *
	 * @var string
	 */
	protected $_page = 'w3tc_faq';

	/**
	 * FAQ tab
	 *
	 * @return void
	 */
	function view() {
		$faq = Generic_Faq::parse();
		$sections = Generic_Faq::sections();

		$columns = array();
		foreach ( $sections as $section => $number ) {
			if ( !isset( $columns[$number] ) )
				$columns[$number] = array();
			$columns[$number][] = $section;
		}

		include W3TC_INC_DIR . '/options/faq.php';
	}
}
