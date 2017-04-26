<?php
namespace W3TC;

if (!defined('W3TC'))
    die();

if (empty($cnames)) {
} else if (count($cnames) == 1) {
    echo '<div class="w3tc_cdn_cnames_readonly">' . $cnames[0] . '</div>';
} else {
    echo '<ol class="w3tc_cdn_cnames_readonly">';

    foreach ($cnames as $index => $cname) {
        $label = '';

    	if ($index == 0)
            $label = __('(reserved for CSS)', 'w3-total-cache');
        else if ($index == 1)
            $label = __('(reserved for JS in <head>)', 'w3-total-cache');
        else if ($index == 2)
            $label = __('(reserved for JS after <body>)', 'w3-total-cache');
        else if ($index == 3)
            $label = __('(reserved for JS before </body>)', 'w3-total-cache');
        else
            $label = '';

        echo '<li>';
        echo $cname;
	    echo '<span class="w3tc_cdn_cname_comment">';
        echo htmlspecialchars($label);
        echo '</span></li>';
    }

    echo '</ol>';
}
