<?php

/**
 * @package    WordPress
 * @subpackage Formidable, gfirem_adv_search
 * @author     GFireM
 * @copyright  2017
 * @link       http://www.gfirem.com
 * @license    http://www.apache.org/licenses/
 */
if ( !defined( 'WPINC' ) ) {
    die;
}
class gfirem_adv_search_meta_box
{
    private  $version = '1.0.0' ;
    private  $add_scroll_script = false ;
    private  $display_id ;
    public function __construct()
    {
        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
        add_action( 'save_post_frm_display', array( $this, 'save_meta_boxes_data' ) );
        add_action( 'admin_footer', array( $this, 'add_script' ) );
        add_action( 'wp_footer', array( $this, 'add_script' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_style' ) );
        add_filter(
            'frm_where_filter',
            array( $this, 'search_filter_query' ),
            10,
            2
        );
    }
    
    /**
     * Include styles in admin
     *
     * @param $hook
     */
    public function enqueue_style( $hook )
    {
        global  $current_screen ;
        if ( $current_screen->id == 'frm_display' ) {
            wp_enqueue_style(
                'gfirem_adv_search',
                FSE_CSS_PATH . 'gfirem_adv_search.css',
                array(),
                $this->version
            );
        }
    }
    
    /**
     * Add meta box
     *
     * @param WP_Post $post The post object
     *
     */
    public function add_meta_boxes( $post )
    {
        add_meta_box(
            'gfirem_adv_search_meta_box',
            __( 'Advance Search Filter & Sort', 'gfirem_adv_search-locale' ),
            array( $this, 'gfirem_adv_search_meta_box_callback' ),
            'frm_display'
        );
    }
    
    /**
     * Build the meta box view
     *
     * @param $post WP_Post
     */
    public function gfirem_adv_search_meta_box_callback( $post )
    {
        $enabled_adv_filtering = get_post_meta( $post->ID, '_enabled_adv_filtering', true );
        $show_adv_view = '';
        
        if ( empty($enabled_adv_filtering) ) {
            $show_adv_view = 'style="display:none;"';
            $enabled_adv_filtering = '0';
        } else {
            $enabled_adv_filtering = '1';
        }
        
        $display = FrmProDisplay::getOne( $post->ID, false, true );
        $data_encoded = get_post_meta( $post->ID, '_gfirem_adv_search_collect_setting', true );
        $filters = array();
        if ( !empty($data_encoded) ) {
            $filters = $data_encoded;
        }
        $orders = array();
        $frm_enabled_scroll_to = '';
        $frm_enabled_scroll_padding = '';
        $frm_enabled_scroll_if_query = '';
        include FSE_VIEW_PATH . 'meta_box.php';
    }
    
    /**
     * Store custom field meta box data
     *
     * @param int $post_id The post ID.
     */
    public function save_meta_boxes_data( $post_id )
    {
        if ( !empty($_POST['gfirem_adv_search_metabox_nonce']) && !wp_verify_nonce( $_POST['gfirem_adv_search_metabox_nonce'], 'gfirem_adv_search_metabox_collect_settings' ) ) {
            return;
        }
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        if ( !current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
        
        if ( !isset( $_POST['frm_search_enabled'] ) ) {
            delete_post_meta( $post_id, '_enabled_adv_filtering' );
            delete_post_meta( $post_id, '_gfirem_adv_search_collect_setting' );
        } else {
            update_post_meta( $post_id, '_enabled_adv_filtering', sanitize_text_field( $_POST['frm_search_enabled'] ) );
            
            if ( !empty($_POST['frm_search_field']) && is_array( $_POST['frm_search_field'] ) ) {
                $filters = array();
                foreach ( $_POST['frm_search_field'] as $field => $filter ) {
                    if ( empty($field) || empty($filter) ) {
                        continue;
                    }
                    $filters[sanitize_text_field( strval( $field ) )] = array(
                        'filter' => sanitize_text_field( $filter ),
                        'where'  => $this->get_where_val( $field ),
                    );
                }
                if ( !empty($filters) ) {
                    update_post_meta( $post_id, '_gfirem_adv_search_collect_setting', $filters );
                }
            }
        
        }
    
    }
    
    /**
     * Add script needed
     */
    public function add_script()
    {
        global  $current_screen ;
        
        if ( $current_screen->id == 'frm_display' || $this->add_scroll_script ) {
            wp_enqueue_script(
                'gfirem_adv_search',
                FSE_JS_PATH . 'gfirem_adv_search.js',
                array( 'jquery' ),
                $this->version,
                true
            );
            $params = array();
            wp_localize_script( 'gfirem_adv_search', 'gfirem_adv_search', $params );
        }
    
    }
    
    public function search_filter_query( $where, $args )
    {
        $enabled_adv_filtering = get_post_meta( $args['display']->ID, '_enabled_adv_filtering', true );
        
        if ( !empty($enabled_adv_filtering) ) {
            $data_encoded = get_post_meta( $args['display']->ID, '_gfirem_adv_search_collect_setting', true );
            if ( !empty($data_encoded) && is_array( $data_encoded ) ) {
                
                if ( array_key_exists( $args['where_opt'], $data_encoded ) ) {
                    $where_array = array();
                    $single_line = array(
                        'text',
                        'email',
                        'textarea',
                        'url',
                        'number'
                    );
                    foreach ( $data_encoded as $field_key => $field_term ) {
                        
                        if ( !empty($_GET[$field_term['where']]) ) {
                            $field_search_value = esc_attr( $_GET[$field_term['where']] );
                            $type = FrmField::get_type( $field_key );
                            
                            if ( in_array( $type, $single_line ) ) {
                                $where_array[$field_key] = ' (it.meta_value like \'%' . $field_search_value . '%\' and it.field_id = ' . $field_key . ') ';
                            } else {
                                $options = $this->get_array_of_options( $field_search_value );
                                $b = 1;
                                $where_str = '';
                                foreach ( $options as $option ) {
                                    if ( $b > 1 ) {
                                        $where_str .= ' OR ';
                                    }
                                    $where_str .= ' (it.meta_value like \'%' . trim( $option ) . '%\' and it.field_id = ' . $field_key . ') ';
                                    $b++;
                                }
                                $where_array[$field_key] = ( $b > 2 ? ' ( ' . $where_str . ' ) ' : $where_str );
                            }
                        
                        }
                    
                    }
                    
                    if ( !empty($where_array) ) {
                        $i = 1;
                        global  $wpdb ;
                        $where_str = '';
                        foreach ( $where_array as $field_key => $field_term ) {
                            $where_str .= '  ( it.item_id IN(SELECT DISTINCT it.item_id FROM ' . $wpdb->get_blog_prefix() . 'frm_item_metas it WHERE ' . $field_term . ' ' . FrmAppHelper::prepend_and_or_where( ' AND ', array(
                                'item_id' => $args['entry_ids'],
                            ) ) . ' ) ) ';
                            if ( $i != count( $where_array ) ) {
                                $where_str .= $data_encoded[$field_key]['filter'];
                            }
                            $i++;
                        }
                        return $where_str;
                    }
                
                }
            
            }
        }
        
        return $where;
    }
    
    private function get_array_of_options( $str )
    {
        return explode( ',', $str );
    }
    
    private function clean_the_where_val( $where_val )
    {
        $shortCodes = FrmFieldsHelper::get_shortcodes( $where_val, $_POST['form_id'] );
        foreach ( $shortCodes[3] as $tag ) {
            preg_match_all( '/param=(.*?)$/', $tag, $match );
            if ( !empty($match[1][0]) ) {
                return $match[1][0];
            }
        }
        return $where_val;
    }
    
    private function get_where_val( $field )
    {
        if ( !empty($field) ) {
            if ( !empty($_POST['options']) && !empty($_POST['options']['where']) && !empty($_POST['options']['where_val']) ) {
                foreach ( $_POST['options']['where'] as $where_key => $where_field ) {
                    if ( $where_field == strval( $field ) ) {
                        return $this->clean_the_where_val( $_POST['options']['where_val'][$where_key] );
                    }
                }
            }
        }
        return '';
    }
    
    public static function get_extra_option( $option )
    {
        $result = array(
            'id'         => __( 'Entry ID', 'formidable' ),
            'created_at' => __( 'Entry creation date', 'formidable' ),
            'updated_at' => __( 'Entry update date', 'formidable' ),
            'rand'       => __( 'Random', 'formidable' ),
        );
        
        if ( empty($option) ) {
            return $result;
        } else {
            return $result[$option];
        }
    
    }

}