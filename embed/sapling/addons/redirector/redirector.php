<?php
    if ( !defined('K_COUCH_DIR') ) die(); // cannot be loaded directly

    class KRedirector{
        static function action(){
            global $FUNCS;

            $rules = self::get_rules();
            if( count($rules) ){
                $request = self::get_request();

                foreach( $rules as $rule ){
                    $qs = '';
                    $uri = trim( $FUNCS->unhtmlentities($rule['uri'], K_CHARSET) );
                    $to = trim( $FUNCS->unhtmlentities($rule['to'], K_CHARSET) );

                    if( $uri!=='' && $to!=='' ){
                        if( $rule['match']=='regex' ){
                            $uriparts = explode( ' ', $uri, 2 );
                            $uri = trim( $uriparts[0] );
                            $qs = trim( $uriparts[1] );

                            $matches_qs = array();
                            $pattern = '@'.str_replace('@', '\\@', $uri).'@i';
                            $match = preg_match( $pattern, $request['uri'] );
                            if( $match && $qs!=='' ){
                                $pattern_qs = '@'.str_replace('@', '\\@', $qs).'@i';
                                $match = preg_match( $pattern_qs, $request['qs'], $matches_qs );
                            }
                        }
                        else{
                            $pattern = '@^'.str_replace('@', '\\@', $uri).'(/|$)(.*)@i';
                            $to .= '$1$2';
                            $match = preg_match( $pattern, $request['uri'] );
                        }

                        if( $match ){

                            // get the new url
                            $redirect_to = preg_replace( $pattern, $to, $request['uri'] );

                            // substitute wildcards from the qs portion (e.g. %1)
                            if( $rule['match']=='regex' && count($matches_qs) ){
                                $patterns = array();
                                for( $x=0; $x<count($matches_qs); $x++ ){
                                    $patterns[] = '@%'.$x.'@';
                                    //$patterns[] = '@(?<!\\\)%'.$x.'@';
                                }
                                $redirect_to = preg_replace( $patterns, $matches_qs, $redirect_to );
                            }

                            // copy querystring over to the new url (unless explicitly forbidden)
                            if( !$rule['skip_qs'] && strlen($request['qs']) ){
                                $sep = strpos( $redirect_to, '?' )? '&' : '?';
                                $redirect_to .= $sep . $request['qs'];
                            }

                            // if relative, append site's url
                            if( $redirect_to[0]=='/' ){
                                $redirect_to = K_SITE_URL . ltrim( $redirect_to, "/" );
                            }

                            // and redirect..
                            $redirect_to = $FUNCS->sanitize_url( $redirect_to );
                            if( $rule['redirect']=='permanent' ){
                                header( "Location: ".$redirect_to, TRUE, 301 );
                            }
                            else{
                                header( "Location: ".$redirect_to );
                            }
                            header( 'X-Redirector: true' );

                            exit;
                        }
                    }
                }
            }
        }

        static function get_rules(){
            global $FUNCS, $DB;

            $sql = "t.id=p.template_id and t.id=f.template_id and p.id=d.page_id and t.name='redirections.php' and f.name='redirections' and p.is_master = '1' and d.field_id=f.id";

            $rs = $DB->select( K_TBL_TEMPLATES.' t, '.K_TBL_FIELDS.' f, '. K_TBL_PAGES.' p, '. K_TBL_DATA_TEXT.' d', array('d.value'), $sql );
            if( count($rs) ){
                $data = $FUNCS->unserialize( $rs[0]['value'] );
            }
            if( !is_array($data) ) $data=array();

            return $data;
        }

        static function get_request(){
            global $FUNCS;

            $request_uri = $FUNCS->sanitize_url( stripslashes($_SERVER['REQUEST_URI']) );
            if( $request_uri[0]!=='/' ){ $request_uri='/'.$request_uri; }

            $urlparts = @parse_url( K_SITE_URL );
            if( $urlparts['path']!=='/' ){
                $request_uri = preg_replace( '@'.$urlparts['path'].'@i', '', $request_uri, 1 );
                if( $request_uri[0]!=='/' ){ $request_uri='/'.$request_uri; }
            }

            $urlparts = @parse_url( $request_uri );
            return array( 'uri'=>$urlparts['path'], 'qs'=>$urlparts['query'] );
        }

        // validator for simple match
        static function validate_match( $field, $args ){
            if( $field->siblings[0]->data=='simple' && trim($field->data)[0]!=='/' ){
                return KFuncs::raise_error( "URI should begin with '/' for simple match" );
            }
        }

    } // end class

    if( !defined('K_ADMIN') && K_PRETTY_URLS ){
        $FUNCS->add_event_listener( 'init',  array('KRedirector', 'action') );
    }
