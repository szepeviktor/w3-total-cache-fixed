<?php

/*
 * Legacy support for W3-total-cache 0.9.4 only
 */

if (@is_dir(W3TC_DIR) && file_exists(W3TC_DIR . '/w3-total-cache-api.php')) {
    require_once W3TC_DIR . '/w3-total-cache-api.php';
}

define('W3TC_LIB_W3_DIR', W3TC_DIR);

function w3_instance($class) {
    $modified_class = null;

    if ($class == 'W3_Redirect')
        $modified_class = 'Mobile_Redirect';
    else if ($class == 'W3_Config')
        $modified_class = 'Config';
    else if ($class == 'W3_PgCache')
        $modified_class = 'PgCache_ContentGrabber';
    else if ($class == 'W3_PgCache')
        $modified_class = 'PgCache_ContentGrabber';
    else if ($class == 'W3_ObjectCacheBridge')
        $modified_class = 'ObjectCache_WpObjectCache';

    return \W3TC\Dispatcher::component($modified_class);
}

function w3_require_once($file) {
}

class W3_Db {
    static public function instance() {
        return \W3TC\DbCache_Wpdb::instance();
    }
}