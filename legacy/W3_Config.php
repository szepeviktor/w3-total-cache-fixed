<?php

// Legacy Support
if(!class_exists('W3_Config')) :
    class W3_Config extends \W3TC\Config {
        
        public function __construct($blog_id = null) {
            return parent::__construct($blog_id);
        }
        
        public function get_cache_option( $key, $default = null) {
            return parent::get( $key, $default );
        }
    }
    
endif;
