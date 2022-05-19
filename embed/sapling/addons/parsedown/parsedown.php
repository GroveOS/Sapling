<?php

if ( !defined('K_COUCH_DIR') ) die(); // cannot be loaded directly
require_once( K_SNIPPETS_DIR.'/sapling/addons/parsedown/lib/Parsedown.php' );

// Couch Parsedown Tag
class CouchParsedown{
    static function couch_parsedown( $params, $node ){
        global $FUNCS, $CTX;
        
        $Parsedown = new Parsedown();

        extract($FUNCS->get_named_vars(
            array(
                'markdown' => '',
                'file' => '',
                'safemode' => '',
                'into' => '',
                'scope' => ''
            ),
            $params)
        );

        $markdown = trim($markdown);
        $file = trim($file);
        $safemode = trim($safemode);
        $into = trim($into);
        $scope = trim($scope);

        // Determine the input
        $input = '';
        if (strlen($file)) { // file
            $input = @file_get_contents( K_SITE_DIR . $file );
        } elseif (strlen($markdown)) { // parameter
            $input = $markdown;
        } else { // wrapped content
            if(count($node->children)) {
                foreach($node->children as $child){
                    $input = $child->text;
                }
            }
        }

        // Determine the safety mdoe
        if (strlen($safemode)) {
            if ($safemode == 'strict') {
                // Leverage Parsedown's setSafeMode option to escape all HTML
                // and scripting vectors introduced in the markdown itself
                if($safemode) {$Parsedown->setSafeMode(true);}
            } elseif($safemode == 'lax') {
                // If value submitted is untrusted, parse
                // only a limited subset of HTML tags
                $allowed_tags = '<a><br><strong><b><em><i><u><blockquote><pre><code><ul><ol><li><del><strike><div><p><h1><h2><h3><h4><h5><h6>';
                $input = trim( $FUNCS->cleanXSS(strip_tags($input, $allowed_tags), 2) );
            }
        }

        // Parse the input
        $parsed_input = $Parsedown->text($input);

        if(strlen($into)) {
            // Put the response into the given Couch variable
            if(strlen($scope)) {
                $CTX->set($into, $parsed_input, $scope);
            } else {
                $CTX->set($into, $parsed_input);
            }
        } else {
            return $parsed_input;
        }
    }
}
$FUNCS->register_tag( 'parsedown', array('CouchParsedown', 'couch_parsedown') );