<?php
    if ( !defined('K_COUCH_DIR') ) die(); // cannot be loaded directly

    class KUID extends KUserDefinedField{

        var $shortcodes = array();

        function __construct( $row, &$page, &$siblings ){
            global $FUNCS, $DB;

            // call parent
            parent::__construct( $row, $page, $siblings );

            if( $FUNCS->is_error($page) ){ // happens when called from ajax - field definition being removed
                $rs = $DB->select( K_TBL_TEMPLATES, array('name'), "id='" . $DB->sanitize( $this->template_id ). "'" );
                if( count($rs) ){
                    $page->tpl_name = $rs[0]['name'];
                }
            }

            $this->counter_field_name = 'uid_'.$page->tpl_name.'$'.$this->name;
        }

        static function handle_params( $params ){
            global $FUNCS, $AUTH;
            if( $AUTH->user->access_level < K_ACCESS_LEVEL_SUPER_ADMIN ) return;

            $attr = $FUNCS->get_named_vars(
                array(
                    'begin_from'=>'1',
                    'prefix'=>'',
                    'suffix'=>'',
                    'min_length'=>'1',
                ),
                $params
            );

            $begin_from = trim( $attr['begin_from'] );
            $begin_from = ( $FUNCS->is_non_zero_natural($begin_from) ) ? intval( $begin_from ) : 1;
            $attr['begin_from'] = $begin_from;

            $attr['prefix'] = trim( $attr['prefix'] );
            $attr['suffix'] = trim( $attr['suffix'] );

            $min_length = trim( $attr['min_length'] );
            $min_length = ( $FUNCS->is_non_zero_natural($min_length) ) ? intval( $min_length ) : 1;
            $attr['min_length'] = $min_length;

            return $attr;
        }

        function store_posted_changes( $post_val ){
            if( $post_val ){ // handle direct input through cms:db_persist
                if( $this->deleted || $this->k_inactive ) return; // no need to store
                if( is_null($this->orig_data) ) $this->orig_data = $this->data;

                $this->data = intval( $post_val );
                $this->modified = ( $this->orig_data==$this->data ) ? false : true; // modified?
            }

            return;
        }

        function get_search_data(){
            global $FUNCS;

            // HOOK: uid_get_search_data
            $data = '';
            $skip = $FUNCS->dispatch_event( 'uid_get_search_data', array(&$data, $this) );
            if( !$skip ){
                $data = sprintf( "%04s", $this->data ); // make length at least 4 chars for MySQL fulltext not to ignore it
            }

            return $data;
        }

        function get_data( $for_ctx=0 ){
            global $FUNCS, $CTX;

            $data = parent::get_data( $for_ctx );

            if( $for_ctx ){
                $CTX->set( $this->name.'__unformatted', $data ); // make available the unformatted value too

                if( $data && $this->page->id != '-1' ){

                    // HOOK: uid_get_data
                    $skip = $FUNCS->dispatch_event( 'uid_get_data', array(&$data, $this) );
                    if( $skip ){ return $data; }

                    // padding
                    $data = sprintf( "%0".$this->min_length."s", $data );

                    // prefix and suffix
                    $prefix = trim( $this->prefix );
                    $suffix = trim( $this->suffix );
                    if( strpos($prefix, '[') !== false || strpos($suffix, '[') !== false ){
                        if( !count($this->shortcodes) ){
                            $arr = array( 'D', 'DD', 'M', 'MM', 'YY', 'YYYY', 'H', 'HH', 'N', 'S' );
                            $handler = array( $this, 'shortcode_handler' );
                            foreach( $arr as $a ){
                                $this->shortcodes[strtolower($a)] = $handler;
                            }
                        }

                        $orig_handlers = $FUNCS->shortcodes;
                        $FUNCS->shortcodes = $this->shortcodes;
                        if( strpos($prefix, '[') !== false ){
                            $parser = new KBBParser( $prefix );
                            $prefix = $parser->get_HTML();
                        }
                        if( strpos($suffix, '[') !== false ){
                            $parser = new KBBParser( $suffix );
                            $suffix = $parser->get_HTML();
                        }
                        $FUNCS->shortcodes = $orig_handlers;
                    }

                    return $prefix.$data.$suffix;

                }
            }
            else{
                return $data;
            }
        }

        function _render( $input_name, $input_id, $extra1='', $dynamic_insertion=0 ){
            global $FUNCS;

            $html = $FUNCS->render( 'field_'.$this->k_type, $this, $input_name, $input_id, $extra, $dynamic_insertion );

            return $html;
        }

        function validate(){
            global $FUNCS, $DB;
            if( $this->deleted || $this->k_inactive ) return true;

            // if a new page or an existing page with no id is being saved, generate the unique id for it ..
            if( ($this->page->id == '-1' && is_null($this->data)) || (!intval($this->data)) ){

                // HOOK: uid_validate
                $skip = $FUNCS->dispatch_event( 'uid_validate', array($this) );
                if( !$skip ){

                    $key = $this->counter_field_name;

                    $sql = "SELECT k_value FROM " . K_TBL_SETTINGS . " WHERE k_key='". $DB->sanitize($key) ."' FOR UPDATE";
                    $rs = $DB->raw_select( $sql );

                    if( count($rs) ){
                        $value = $rs[0]['k_value'];
                        $DB->update( K_TBL_SETTINGS, array('k_value'=>$value+1), "k_key='" . $DB->sanitize( $key ). "'" );
                    }
                    else{
                        // counter field does not exist .. create it
                        $value = intval( $this->begin_from );

                        $max = $this->_get_max_uid();
                        if( $max >= $value ){ $value =  $max+1; } // if any existing page has a higher value that the 'begin_from' param, use that instead.

                        $DB->insert( K_TBL_SETTINGS, array('k_key'=>$key, 'k_value'=>$value+1) );
                    }

                    $this->data = $value;
                    $this->modified = 1;
                }
            }

            return parent::validate();
        }

        function _get_max_uid(){
            global $DB;

            $table = ( $this->search_type=='text' ) ? K_TBL_DATA_TEXT : K_TBL_DATA_NUMERIC;
            $rs = $DB->raw_select( "SELECT MAX(value) as max FROM " . $table . " WHERE field_id='".$this->id."'" );

            return intval( $rs[0]['max'] );
        }

        function _create( $page_id, $first_time=0 ){
            global $FUNCS;

            if( $first_time ){
                $FUNCS->set_setting( $this->counter_field_name, $this->begin_from );
            }
        }

        function _update_schema( $orig_values ){
            global $FUNCS, $DB;

            if( array_key_exists('begin_from', $orig_values) ){
                $begin_from = $this->begin_from;
                $max = $this->_get_max_uid();
                if( $begin_from > $max+1 ){
                    $FUNCS->set_setting( $this->counter_field_name, $begin_from );
                }
            }

            return;
        }

        function _delete( $page_id ){
            global $FUNCS;

            if( $page_id==-1 ){
                $FUNCS->delete_setting( $this->counter_field_name );
            }
        }

        function _prep_cached(){
            unset( $this->shortcodes );
            $this->shortcodes = array();
        }

        function shortcode_handler( $params, $content, $name ){
            if( !is_object($this->page) ) return;

            $date = @strtotime( $this->page->creation_date );
            if( !$date ) return;

            switch( $name ){
                case 'd': /* Day of the month without leading zeros */
                    $format = 'j';
                    break;
                case 'dd': /* Day of the month, 2 digits with leading zeros */
                    $format = 'd';
                    break;
                case 'm': /* Numeric representation of a month, without leading zeros */
                    $format = 'n';
                    break;
                case 'mm': /* Numeric representation of a month, with leading zeros */
                    $format = 'm';
                    break;
                case 'yy': /* A two digit representation of a year */
                    $format = 'y';
                    break;
                case 'yyyy': /* A full numeric representation of a year, 4 digits */
                    $format = 'Y';
                    break;
                case 'h': /* 24-hour format of an hour without leading zeros */
                    $format = 'G';
                    break;
                case 'hh': /* 24-hour format of an hour with leading zeros */
                    $format = 'H';
                    break;
                case 'n': /* Minutes with leading zeros */
                    $format = 'i';
                    break;
                case 's': /* Seconds, with leading zeros */
                    $format = 's';
                    break;
            }

            $val = date( $format, $date );
            return $val;
        }

        // renderable theme functions
        static function register_renderables(){
            global $FUNCS;

            $FUNCS->register_render( 'field_uid', array('renderable'=>array('KUID', '_render_uid')) );
        }

        static function _render_uid( $f, $input_name, $input_id, $extra, $dynamic_insertion ){
            $html = $f->get_data( 1 );
            return $html;
        }

    }// end class

    // Register
    $FUNCS->register_udf( 'uid', 'KUID', 0/*repeatable*/ );
    $FUNCS->add_event_listener( 'register_renderables',  array('KUID', 'register_renderables') );
