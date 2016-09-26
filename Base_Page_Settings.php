<?php
namespace W3TC;



class Base_Page_Settings {
	/**
	 * Config
	 *
	 * @var Config
	 */
	protected $_config = null;

	/**
	 * Notes
	 *
	 * @var array
	 */
	protected $_notes = array();

	/**
	 * Errors
	 *
	 * @var array
	 */
	protected $_errors = array();

	/**
	 * Used in PHPMailer init function
	 *
	 * @var string
	 */
	protected $_phpmailer_sender = '';

	/**
	 * Master configuration
	 *
	 * @var Config
	 */
	protected $_config_master;

	protected $_page;

	function __construct() {
		$this->_config = Dispatcher::config();
		$this->_config_master = Dispatcher::config_master();

		$this->_page = Util_Admin::get_current_page();
	}

	function options() {
		$this->view();
	}

	public function render_footer() {
		include W3TC_INC_OPTIONS_DIR . '/common/footer.php';
	}

	/**
	 * Returns true if config section is sealed
	 *
	 * @param string  $section
	 * @return boolean
	 */
	protected function is_sealed( $section ) {
		return true;
	}

	/**
	 * Returns true if we edit master config
	 *
	 * @return boolean
	 */
	protected function is_master() {
		return $this->_config->is_master();
	}

	/**
	 * Prints checkbox with config option value
	 *
	 * @param string  $option_id
	 * @param bool    $disabled
	 * @param string  $class_prefix
	 * @param bool    $label
	 */
	protected function checkbox( $option_id, $disabled = false,
		$class_prefix = '', $label = true, $force_value = null ) {
		$disabled = $disabled || $this->_config->is_sealed( $option_id );
		$name = Util_Ui::config_key_to_http_name( $option_id );

		if ( !$disabled )
			echo '<input type="hidden" name="' . $name . '" value="0" />';

		if ( $label )
			echo '<label>';
		echo '<input class="'.$class_prefix.'enabled" type="checkbox" id="' . $name .
			'" name="' . $name . '" value="1" ';
		if ( !is_null( $force_value ) )
			checked( $force_value, true );
		else
			checked( $this->_config->get_boolean( $option_id ), true );

		if ( $disabled )
			echo 'disabled="disabled" ';

		echo ' />';
	}

	/**
	 * Prints a radio button and if config value matches value
	 *
	 * @param string  $option_id    config id
	 * @param unknown $value
	 * @param bool    $disabled
	 * @param string  $class_prefix
	 */
	protected function radio( $option_id, $value, $disabled = false, $class_prefix = '' ) {
		if ( is_bool( $value ) )
			$rValue = $value?'1':'0';
		else
			$rValue = $value;
		$disabled = $disabled || $this->_config->is_sealed( $option_id );

		$name = Util_Ui::config_key_to_http_name( $option_id );

		echo '<label>';
		echo '<input class="'.$class_prefix.'enabled" type="radio" id="' . $name .
			'" name="' . $name . '" value="', $rValue, '" ';
		checked( $this->_config->get_boolean( $option_id ), $value );

		if ( $disabled )
			echo 'disabled="disabled" ';

		echo ' />';
	}

	/**
	 * Prints checkbox for debug option
	 *
	 * @param string  $option_id
	 */
	protected function checkbox_debug( $option_id ) {
		if ( is_array( $option_id ) ) {
			$section = $option_id[0];
			$section_enabled = $this->_config->is_extension_active_frontend( $section );
		} else {
			$section = substr( $option_id, 0, strrpos( $option_id, '.' ) );
			$section_enabled = $this->_config->get_boolean( $section . '.enabled' );
		}

		$disabled = $this->_config->is_sealed( $option_id ) || !$section_enabled;
		$name = Util_Ui::config_key_to_http_name( $option_id );

		if ( !$disabled )
			echo '<input type="hidden" name="' . $name . '" value="0" />';

		echo '<label>';
		echo '<input class="enabled" type="checkbox" name="' . $name .
			'" value="1" ';
		checked( $this->_config->get_boolean( $option_id ), true );

		if ( $disabled )
			echo 'disabled="disabled" ';

		echo ' />';
	}

	protected function value_with_disabled( $option_id, $disabled,
		$value_when_disabled ) {
		if ( $disabled ) {
			echo 'value="' . esc_attr( $value_when_disabled ) . '" ';
			echo 'disabled="disabled" ';
		} else {
			echo 'value="' .
				esc_attr( $this->_config->get_string( $option_id ) ) . '" ';
		}
	}

	protected function view() {
		include W3TC_INC_DIR . '/options/common/header.php';
	}
}
