<?php
if(!defined( 'ABSPATH' )) {
    exit; // Exit if accessed directly
}

if(!class_exists( 'Traveler_Admin_Form_Build' )) {
    class Traveler_Admin_Form_Build extends Traveler_Controller
    {
        private static $_inst;

        public static $traveler_param = array();

        function __construct()
        {
            add_action( 'admin_menu' , array( $this , "register_traveler_booking_sub_menu_page" ) );

            add_action( 'init' , array( $this , '_add_post_type' ) , 5 );
            // add script and style
            add_action( 'admin_enqueue_scripts' , array( $this , "_add_scripts" ) );

            add_action( 'admin_init' , array( $this , "_save_layout" ) );
            add_action( 'admin_init' , array( $this , "_del_layout" ) );


            add_action( 'admin_init' , array( $this , "_test" ) , 1 );
        }

        function _add_scripts()
        {

        }

        function _get_list_type_layout()
        {
            return apply_filters( 'traveler_build_form_list_type_layout' , array(
                'Single Hotel' ,
                'Single Room' ,
                'Booking'
            ) );
        }

        function traveler_add_field( $option = array() )
        {
            self::$traveler_param[ ] = $option;
        }

        function traveler_get_all_field()
        {
            return self::$traveler_param;
        }

        function _test()
        {
            traveler_add_field( array(
                    "title"   => __( "Option Text" , 'traveler-booking' ) ,
                    "name"    => 'option_text' ,
                    "options" => array(
                        array(
                            "type"             => "text" ,
                            "title"            => __( "Title" , 'traveler-booking' ) ,
                            "name"             => "title" ,
                            "desc"             => " Điền Title" ,
                            'edit_field_class' => 'traveler-col-md-6' ,
                            'value' => "xxx"
                        ) ,
                        array(
                            "type"             => "text" ,
                            "title"            => __( "Name" , 'traveler-booking' ) ,
                            "name"             => "name" ,
                            "description"      => "" ,
                            'edit_field_class' => 'traveler-col-md-6' ,
                            'value' => "xxx"
                        ) ,
                        array(
                            "type"             => "textarea" ,
                            "title"            => __( "Text area" , 'traveler-booking' ) ,
                            "name"             => "text_area" ,
                            "desc"             => "" ,
                            'edit_field_class' => 'traveler-col-md-12' ,
                            'value' => "xxx"
                        ) ,
                        array(
                            "type"             => "dropdown" ,
                            "title"            => __( "Dropdown" , 'traveler-booking' ) ,
                            "name"             => "dropdown" ,
                            "desc"             => "" ,
                            'edit_field_class' => 'traveler-col-md-3' ,
                            'options'            => array(
                                __( 'No' , 'traveler-booking' )  => 'no' ,
                                __( 'Yes' , 'traveler-booking' ) => 'yes' ,
                            )
                        ),
                        array(
                            "type"             => "checkbox" ,
                            "title"            => __( "Check Box" , 'traveler-booking' ) ,
                            "name"             => "checkbox" ,
                            "desc"             => "" ,
                            'edit_field_class' => 'traveler-col-md-12' ,
                            'options'            => array(
                                __( 'Check 1' , 'traveler-booking' )  => 'check_1' ,
                                __( 'Check 2' , 'traveler-booking' ) => 'check_2' ,
                                __( 'Check 3' , 'traveler-booking' ) => 'check_3' ,
                            )
                        )
                    )
                )
            );
            traveler_add_field( array(
                    "title"   => __( "Option Text 2" , 'traveler-booking' ) ,
                    "name"    => 'option_text_2' ,
                    "options" => array(
                    )
                )
            );
        }

        function _get_list_layout()
        {
            $query = array(
                'post_type'      => 'traveler_form_builder' ,
                'posts_per_page' => -1 ,
            );
            query_posts( $query );
            $list_layout = array();
            while( have_posts() ) {
                the_post();
                $type_layout = get_post_meta( get_the_ID() , 'type_layout' , true );
                if(empty( $type_layout )) {
                    $type_layout = 'Other';
                }
                $list_layout[ $type_layout ][ ] = array(
                    'id'   => get_the_ID() ,
                    'name' => get_the_title()
                );
            }
            wp_reset_query();
            return $list_layout;
        }

        function _del_layout()
        {
            if(Traveler_Input::request( 'del_layout' )) {
                wp_delete_post( Traveler_Input::request( 'del_layout' ) , true );
            }
        }

        function _save_layout()
        {
            if(!empty( $_POST[ 'traveler_booking_btn_save_layout' ] ) and wp_verify_nonce( $_REQUEST[ 'traveler_booking_save_layout' ] , "traveler_booking_action" )) {
                $current_user = wp_get_current_user();
                $layout_id    = Traveler_Input::request( "traveler-layout-id" );
                $title        = Traveler_Input::request( "traveler-title" );
                $type         = 'update';
                if(empty( $layout_id )) {
                    $type = 'create';
                }
                if(!empty( $title )) {
                    if(empty( $layout_id )) {
                        $my_layout = array(
                            'post_title'   => Traveler_Input::request( "traveler-title" ) ,
                            'post_content' => stripslashes( Traveler_Input::request( "traveler-content-build" ) ) ,
                            'post_status'  => 'publish' ,
                            'post_author'  => $current_user->ID ,
                            'post_type'    => 'traveler_form_builder' ,
                            'post_excerpt' => ''
                        );
                        $layout_id = wp_insert_post( $my_layout );
                    } else {
                        $my_layout = array(
                            'ID'           => $layout_id ,
                            'post_title'   => Traveler_Input::request( "traveler-title" ) ,
                            'post_content' => stripslashes( Traveler_Input::request( "traveler-content-build" ) ) ,
                        );
                        wp_update_post( $my_layout );
                    }
                    if(!empty( $layout_id )) {
                        $type_layout = Traveler_Input::request( "traveler-layout-type" );
                        update_post_meta( $layout_id , 'type_layout' , $type_layout );
                        if($type == 'update') {
                            traveler_set_admin_message( 'Update layout successfully !' , 'success' );
                        } else {
                            traveler_set_admin_message( 'Create layout successfully !' , 'success' );
                        }

                    } else {
                        if($type == 'update') {
                            traveler_set_admin_message( 'Error : Update layout not successfully !' , 'error' );
                        } else {
                            traveler_set_admin_message( 'Error : Create layout not successfully !' , 'error' );
                        }
                    }
                } else {
                    if($type == 'update') {
                        traveler_set_admin_message( 'Error : Update layout not successfully !' , 'error' );
                    } else {
                        traveler_set_admin_message( 'Error : Create layout not successfully !' , 'error' );
                    }
                }


            }
        }

        function register_traveler_booking_sub_menu_page()
        {

            $menu_page = $this->get_menu_page();

            add_submenu_page(
                $menu_page[ 'parent_slug' ] ,
                $menu_page[ 'page_title' ] ,
                $menu_page[ 'menu_title' ] ,
                $menu_page[ 'capability' ] ,
                $menu_page[ 'menu_slug' ] ,
                $menu_page[ 'function' ]
            );
        }

        function get_menu_page()
        {

            $menu_page = Traveler()->get_menu_page();
            $page      = array(
                'parent_slug' => $menu_page[ 'menu_slug' ] ,
                'page_title'  => __( 'Form Builder' , 'traveler-booking' ) ,
                'menu_title'  => __( 'Form Builder' , 'traveler-booking' ) ,
                'capability'  => 'manage_options' ,
                'menu_slug'   => 'traveler_booking_page_form_builder' ,
                'function'    => array( $this , 'callback_traveler_booking_sub_menu_form_builder' )
            );

            return apply_filters( 'traveler_setting_menu_args' , $page );

        }

        function callback_traveler_booking_sub_menu_form_builder()
        {
            echo $this->admin_load_view( 'form-builder' );
        }

        function _get_all_shortcode_in_content( $content = false )
        {
            $shortcode = array();
            if(!empty( $content )) {
                $pattern = get_shortcode_regex();
                preg_match_all( '/' . $pattern . '/s' , $content , $matches2 );
                if(!empty( $matches2[ 0 ] )) {
                    $shortcode = $matches2[ 0 ];
                }
            }
            return $shortcode;
        }

        function _add_post_type()
        {
            $labels = array(
                'name'               => _x( 'Form Builder' , 'post type general name' , 'traveler-booking' ) ,
                'singular_name'      => _x( 'Form Builder' , 'post type singular name' , 'traveler-booking' ) ,
                'menu_name'          => _x( 'Form Builder' , 'admin menu' , 'traveler-booking' ) ,
                'name_admin_bar'     => _x( 'Form Builder' , 'add new on admin bar' , 'traveler-booking' ) ,
                'add_new'            => _x( 'Add New' , 'service' , 'traveler-booking' ) ,
                'add_new_item'       => __( 'Add New Form Builder' , 'traveler-booking' ) ,
                'new_item'           => __( 'New Form Builder' , 'your-plugin-textdomain' ) ,
                'edit_item'          => __( 'Edit Form Builder' , 'traveler-booking' ) ,
                'view_item'          => __( 'View Form Builder' , 'traveler-booking' ) ,
                'all_items'          => __( 'All Form Builders' , 'traveler-booking' ) ,
                'search_items'       => __( 'Search Form Builders' , 'traveler-booking' ) ,
                'parent_item_colon'  => __( 'Parent Form Builders:' , 'traveler-booking' ) ,
                'not_found'          => __( 'No form builder found.' , 'traveler-booking' ) ,
                'not_found_in_trash' => __( 'No form builder found in Trash.' , 'traveler-booking' )
            );

            $args = array(
                'labels'             => $labels ,
                'description'        => __( 'Description.' , 'traveler-booking' ) ,
                'public'             => true ,
                'publicly_queryable' => true ,
                'show_ui'            => true ,
                'show_in_menu'       => false ,
                'query_var'          => true ,
                'rewrite'            => array( 'slug' => 'form_builder' ) ,
                'capability_type'    => 'post' ,
                'has_archive'        => true ,
                'hierarchical'       => false ,
                //'menu_position'      => '59.9',
                'supports'           => array( 'title' , 'editor' )
            );

            register_post_type( 'traveler_form_builder' , $args );
        }

        static function inst()
        {
            if(!self::$_inst) {
                self::$_inst = new self();
            }
            return self::$_inst;
        }

    }

    Traveler_Admin_Form_Build::inst();
}