<?php
if ( !defined('K_COUCH_DIR') ) die(); // cannot be loaded directly

//require_once( K_SNIPPETS_DIR.'/sapling/addons/curl/curl.php' );
//require_once( K_SNIPPETS_DIR.'/sapling/addons/minify-js-css/minify.php' );
//require_once( K_SNIPPETS_DIR.'/sapling/addons/parsedown/parsedown.php' );
//require_once( K_SNIPPETS_DIR.'/sapling/addons/phpmailer/phpmailer.php' );
//require_once( K_SNIPPETS_DIR.'/sapling/addons/redirector/redirector.php' );
//require_once( K_SNIPPETS_DIR.'/sapling/addons/simple-access-control/simple-access-control.php' );
require_once( K_SNIPPETS_DIR.'/sapling/addons/tiny-html-minifier/TinyMinify.php' );
//require_once( K_SNIPPETS_DIR.'/sapling/addons/too-many-urls/too_many_urls.php' );
//require_once( K_SNIPPETS_DIR.'/sapling/addons/uid/uid.php' );



// Save and back buttons
if( defined('K_ADMIN') ){ // if admin-panel being displayed ..

    // 1. Add a 'Save and back' button to form view
    $my_target_action = 'page'; // available targets on the form are - toolbar, filter, page and extended

    $FUNCS->add_event_listener( 'alter_pages_form_'.$my_target_action.'_actions', 'my_add_button' );
    function my_add_button( &$arr_actions, &$obj ){
        global $FUNCS, $PAGE;

        $route = $FUNCS->current_route;
        if( is_object($route) && $route->module=='pages' ){

            if( $PAGE->tpl_is_clonable ){ // if template is clonable, add the new button to form

                $arr_actions['btn_save_and_back'] =
                    array(
                        'title'=>'Save and go back',
                        'onclick'=>array(
                            "$('#btn_submit').trigger('my_submit');",
                            "var form = $('#".$obj->form_name."');",
                            "form.find('#k_custom_action').val('save_and_back');",
                            "form.submit();",
                            "return false;",
                        ),
                        'icon'=>'collapse-left',
                        'weight'=>15,
                    );
            }
        }
    }

    // 2. When the button above submits the form, take custom action (go back to list-view in this case)
    $FUNCS->add_event_listener( 'pages_form_custom_action', 'my_add_button_action' );
    function my_add_button_action( $custom_action, &$redirect_dest, &$pg, $_mode ){
        global $FUNCS, $PAGE;

        $route = $FUNCS->current_route;
        if( is_object($route) && $route->module=='pages' ){

            if( $custom_action === 'save_and_back' ){
                // set the new redirect destination (the list view with all querystring parameters) ..

                if( $PAGE->tpl_is_clonable ){
                    $link = $FUNCS->generate_route( $PAGE->tpl_name, 'list_view' );
                    $link = $FUNCS->get_qs_link( $link );

                    $redirect_dest = $link;
                }
            }
        }
    }
}





// add new sidebar menu items
if( defined('K_ADMIN') ){
    $FUNCS->add_event_listener( 'register_admin_menuitems', 'my_register_admin_menuitems' );
    $FUNCS->add_event_listener( 'alter_admin_menuitems', 'my_alter_admin_menuitems' );

    function my_register_admin_menuitems(){
        global $FUNCS;
        $FUNCS->register_admin_menuitem( array('name'=>'settings', 'title'=>'Settings', 'is_header'=>'1', 'weight'=>'90')  );
    }

    function my_alter_admin_menuitems( &$items ){
        global $FUNCS;
        $items['_templates_']['title']="General";
        $items['_modules_']['weight']=100; // move administration group further down
        $tpls = array( '' ); // set hidden templates
        foreach( $tpls as $tpl ){
            if( array_key_exists($tpl, $items) ){
                unset( $items[$tpl] );
            }
        }
    }
}