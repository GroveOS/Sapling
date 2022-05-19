<?php

if ( !defined('K_COUCH_DIR') ) die(); // cannot be loaded directly
require 'TinyHtmlMinifier.php';

class TinyMinify
{

    public static function compress( $params, $node ){
        global $FUNCS;
        foreach( $node->children as $child ){
            $html .= $child->get_HTML();
        }
        $minifier = new TinyHtmlMinifier($options=[]);
        return $minifier->minify($html);
    }
}
$FUNCS->register_tag( 'compress', array('TinyMinify', 'compress') );