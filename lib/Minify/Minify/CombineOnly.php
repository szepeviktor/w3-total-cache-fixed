<?php

/**
 * Combine only minifier
 */
class Minify_CombineOnly {
    /**
     * Minifies content
     * @param string $content
     * @param array $options
     * @return string
     */
    public static function minify($content, $options = array()) {
        $content = Minify_CSS_UriRewriter::rewrite($content, $options);

        return $content;
    }
}
