<?php

    if ( !defined('K_COUCH_DIR') ) die(); // cannot be loaded directly

    if( defined('K_ADMIN') ){

        class FilterAddon{
            var $config = array();

            function __construct(){

                // populate config
                $t = array();
                if( file_exists(K_ADDONS_DIR.'filter/config.php') ){
                    require_once( K_ADDONS_DIR.'filter/config.php' );
                }
                $this->config = array_map( "trim", $t );
                unset( $t );
            }

            function register_renderables(){
                global $FUNCS, $FA;

                $FUNCS->register_render( 'filter_fields',
                                            array(
                                                'template_path'=>K_ADDONS_DIR . 'filter/theme/',
                                                'template_ctx_setter'=>array($FA, 'render_filter_fields'),
                                            )
                                        );
            }

            function render_filter_fields( $name ){
                global $CTX, $PAGE, $FUNCS;

                $html=$selected='';
                $field = $PAGE->_fields[$name];

                if( $field->k_type=='dropdown' || $field->k_type=='radio' || $field->k_type=='checkbox' ){
                        $field->resolve_dynamic_params();

                        $separator = ( $field->k_separator ) ? $field->k_separator : '|';
                        $val_separator = ( $field->val_separator ) ? $field->val_separator : '=';
                        $post_val = html_entity_decode( trim($_GET[$field->name]), ENT_QUOTES, K_CHARSET );
                        $title = strlen( trim($field->label) ) ? $field->label : $field->name;

                        $html .= '<select id="flt-'.$field->id.'" name="'.$field->name.'">';
                        $html .= '<option value="-1" >-- '.$title.' --</option>';

                        if( strlen($field->opt_values) ){
                            $arr_values = array_map( "trim", preg_split( "/(?<!\\\)\\".$separator."/", $field->opt_values ) );
                            foreach( $arr_values as $val ){
                                if( $val!=='' ){
                                    $val = str_replace( '\\'.$separator, $separator, $val ); //unescape
                                    $arr_values_args = array_map( "trim", preg_split( "/(?<!\\\)\\".$val_separator."/", $val ) );
                                    $opt = str_replace( '\\'.$val_separator, $val_separator, $arr_values_args[0] ); //unescape
                                    if( isset($arr_values_args[1]) ){
                                        $opt_val = str_replace( '\\'.$val_separator, $val_separator, $arr_values_args[1] ); //unescape
                                    }
                                    else{
                                        $opt_val = $opt;
                                    }

                                    if( $field->k_type=='dropdown' && ($opt_val=='-' || $opt_val=='_') ){
                                        continue;
                                    }

                                    if( strlen($opt_val) ){
                                        $html .= '<option value="'.$opt_val.'"';
                                        if( $opt_val===$post_val ){
                                            $html .= '  selected="selected"';
                                            $selected=$opt_val;
                                        }
                                        $html .= '>'.$opt.'</option>';
                                    }
                                }
                            }
                        }

                        $html .= '</select>';

                        $CTX->set( 'k_filter_dropdown', $html );
                        $CTX->set( 'k_filter_id', 'flt-'.$field->id );
                        $CTX->set( 'k_filter_link', $FUNCS->get_qs_link($CTX->get('k_route_link'), array('pg',$field->name)) );
                        $CTX->set( 'k_filter_qs', $field->name );

                        // for cms:pages
                        if( strlen($selected) ){
                            $str_custom_field = $field->name.'='.$selected;
                            $CTX->set( 'k_selected_custom_field', $CTX->get('k_selected_custom_field').' | '.$str_custom_field, 'global' );
                        }
                }
                else if( $field->k_type=='relation' ){

                    $post_val = ( isset($_GET[$field->name]) && $FUNCS->is_non_zero_natural($_GET[$field->name]) ) ? (int)$_GET[$field->name] : null;
                    $title = strlen( trim($field->label) ) ? $field->label : $field->name;
                    $html .= '<select id="flt-'.$field->id.'" name="'.$field->name.'">';
                    $html .= '<option value="-1" >-- '.$title.' --</option>';

                    $orig_reverse = $field->reverse_has;
                    $field->reverse_has = 'many';
                    $rows = $field->_get_rows();
                    if( !$FUNCS->is_error($rows) ){
                        foreach( $rows as $key=>$value ){
                            $html .= '<option value="'.$key.'"';
                            if( $post_val && $key==$post_val ){
                                $html .= '  selected="selected"';
                                $selected=$key;
                            }
                            $html .= '>'.$value.'</option>';
                        }
                    }
                    $field->reverse_has = $orig_reverse;

                    $html .= '</select>';

                    $CTX->set( 'k_filter_dropdown', $html );
                    $CTX->set( 'k_filter_id', 'flt-'.$field->id );
                    $CTX->set( 'k_filter_link', $FUNCS->get_qs_link($CTX->get('k_route_link'), array('pg',$field->name)) );
                    $CTX->set( 'k_filter_qs', $field->name );

                    // for cms:pages
                    if( strlen($selected) ){
                        $str_custom_field = $field->name.'=id('.$selected.')';
                        $CTX->set( 'k_selected_custom_field', $CTX->get('k_selected_custom_field').' | '.$str_custom_field, 'global' );
                    }
                }
            }

            function add_filter_actions( &$actions ){
                global $FUNCS, $PAGE;

                $route = $FUNCS->current_route;
                if( is_object($route) && ($route->module=='pages' || $route->module=='relation')){
                    $masterpage = ( $route->module=='pages' ) ? $route->masterpage : $route->resolved_values['field']->masterpage;

                    if( array_key_exists($masterpage, $this->config) && !$PAGE->tpl_nested_pages ){
                        $fields = $this->config[$masterpage];
                        $fields = array_unique( array_filter(array_map("trim", explode('|', $fields))) );

                        for( $x=0; $x<count($fields); $x++ ){
                            $name = $fields[$x];

                            if( array_key_exists($name, $PAGE->_fields) ){
                                $field = $PAGE->_fields[$name];

                                if( in_array($field->k_type, array('radio', 'checkbox', 'dropdown', 'relation')) ){
                                    $actions['filter_fields_'.$x] =
                                        array(
                                            'render'=>'filter_fields',
                                            'args'=>$name,
                                            'weight'=>10 + $x,
                                        );
                                }
                            }
                        }
                    }
                }
            }

        } /* FilterAddon */


        $FA = new FilterAddon();
        $FUNCS->add_event_listener( 'register_renderables', array($FA, 'register_renderables') );
        $FUNCS->add_event_listener( 'alter_pages_list_filter_actions', array($FA, 'add_filter_actions') );

    } /* K_ADMIN */
