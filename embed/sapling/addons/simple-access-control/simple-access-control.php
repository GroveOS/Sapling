<?php

    if ( !defined('K_COUCH_DIR') ) die(); // cannot be loaded directly

    if( defined('K_ADMIN') ){

        class SimpleAccessControl{
            var $config = array();

            function __construct(){
                global $FUNCS;

                // populate config
                $t = array();
                if( file_exists(K_ADDONS_DIR.'simple-access-control/config.php') ){
                    require_once( K_ADDONS_DIR.'simple-access-control/config.php' );
                }

                foreach( $t as $tpl=>$users ){
                    $tpl = trim( $tpl );
                    $users = array_unique( array_filter(array_map("trim", explode('|', $users))) );
                    $this->config[$tpl] = $users;
                }

                unset( $t );

                // if any restricted templates specified in config, hook the right events ..
                if( count($this->config) ){
                    $FUNCS->add_event_listener( 'alter_admin_menuitems', array($this, 'alter_admin_menuitems') );
                    $FUNCS->add_event_listener( 'alter_admin_routes', array($this, 'alter_admin_routes') );
                }
            }

            function alter_admin_menuitems( &$menu_items ){
                foreach( $this->config as $tpl=>$users ){
                    if( array_key_exists($tpl, $menu_items) ){
                        $menu_items[$tpl]['access_callback'] = array( $this, 'access_control' );
                        $menu_items[$tpl]['access_callback_params'] = $users;
                    }
                }
            }

            function alter_admin_routes( &$routes ){
                foreach( $this->config as $tpl=>$users ){
                    if( array_key_exists($tpl, $routes) ){
                        foreach( $routes[$tpl] as $view ){
                            $view->access_callback = array( $this, 'access_control' );
                            $view->access_callback_params = $users;
                        }
                    }
                }
            }

            function access_control( $item, $users ){
                global $AUTH;

                if( in_array($AUTH->user->name, $users) ){
                    // if current user is in the list of users allowed access to this item ..
                    return true;
                }

                return false; // deny access to everybody else
            }

        } /* SimpleAccessControl */


        $SAC = new SimpleAccessControl();

    } /* K_ADMIN */
