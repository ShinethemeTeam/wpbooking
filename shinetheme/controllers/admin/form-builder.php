<?php
if(!defined( 'ABSPATH' )) {
    exit; // Exit if accessed directly
}

if(!class_exists( 'WPBooking_Admin_Form_Build' )) {
    class WPBooking_Admin_Form_Build extends WPBooking_Controller
    {
        private static $_inst;

        public static $wpbooking_param = array();

        protected  $wpbooking_list_field_form_build = array();

        function __construct()
        {
            add_action( 'admin_menu' , array( $this , "register_wpbooking_sub_menu_page" ) );

            add_action( 'init' , array( $this , '_add_post_type' ) , 5 );
            add_action( 'after_setup_theme' , array( $this , '_load_default_shortcodes' ) );

            // add script and style
            add_action( 'admin_enqueue_scripts' , array( $this , "_add_scripts" ) );

            add_action( 'admin_init' , array( $this , "_save_layout" ) );
            add_action( 'admin_init' , array( $this , "_del_layout" ) );

        }

        function add_form_field($name , $data){
            if(!empty($name)){
				if(!empty($data['data']['title'])) $data['title']=$data['data']['title'];
				else $data['title']=$name;
                $this->wpbooking_list_field_form_build[$name] = $data;
            }
        }
        function get_form_fields($form_id){

			$this->_clear_fields();
			$post=get_post($form_id);

			if($post)
			{
				do_shortcode($post->post_content);
				return $this->wpbooking_list_field_form_build;
			}

        }
		function _clear_fields()
		{
			$this->wpbooking_list_field_form_build=array();
		}

        function _load_default_shortcodes()
        {
			WPBooking_Loader::inst()->load_library(array(
				'shortcodes/form-build-default/abstract-formbuilder-field',
                'shortcodes/form-build-default/text',
				'shortcodes/form-build-default/email',
               	'shortcodes/form-build-default/textarea',
              	'shortcodes/form-build-default/dropdown',
              	'shortcodes/form-build-default/checkbox',
              	'shortcodes/form-build-default/radio',
              	'shortcodes/form-build-default/submit-button',
              	'shortcodes/form-build-default/check-in',
              	'shortcodes/form-build-default/check-out',
              	'shortcodes/form-build-default/guest',
              	'shortcodes/form-build-default/first-name',
              	'shortcodes/form-build-default/last-name',
              	'shortcodes/form-build-default/user-email',
              	'shortcodes/form-build-default/captcha',
              	'shortcodes/form-build-default/post-dropdown',
              	'shortcodes/form-build-default/post-checkbox',
              	'shortcodes/form-build-default/term-dropdown',
              	'shortcodes/form-build-default/term-checkbox',
              	'shortcodes/form-build-default/country-dropdown',
              	'shortcodes/form-build-default/current-datetime',
              	'shortcodes/form-build-default/ip-address',
              	'shortcodes/form-build-default/extra-services',
            ));
        }
        function _add_scripts()
        {

        }

        function _get_list_type_layout()
        {
            return apply_filters( 'wpbooking_build_form_list_type_layout' , array(
                __('Order Form','wpbooking') ,
                __('Checkout','wpbooking') ,
            ) );
        }

        function add_field_form_builder( $option = array() )
        {
            self::$wpbooking_param[ ] = $option;
        }

        function wpbooking_get_all_field()
        {

            $list_field  =array();
            if(!empty(self::$wpbooking_param)){
                foreach(self::$wpbooking_param as $k=>$v){
                    $list_field [$v['category']][] = $v;
                }
            }
            return $list_field;
        }

        function _get_list_layout()
        {
            $query = array(
                'post_type'      => 'wpbooking_form' ,
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
            if(WPBooking_Input::request( 'del_layout' )) {
                wp_delete_post( WPBooking_Input::request( 'del_layout' ) , true );
            }
        }

        function _save_layout()
        {
            $current_user = wp_get_current_user();
            $form_id      = WPBooking_Input::request( "form_builder_id" );
            $title        = WPBooking_Input::request( "wpbooking-title" );
            if(!empty( $_POST[ 'wpbooking_btn_save_layout' ] ) and wp_verify_nonce( $_REQUEST[ 'wpbooking_save_layout' ] , "wpbooking_action" )) {
                if(!empty( $title )) {
                    if(!empty( $form_id )) {
                        $my_layout = array(
                            'ID'           => $form_id ,
                            'post_title'   => WPBooking_Input::request( "wpbooking-title" ) ,
                            'post_content' => stripslashes( WPBooking_Input::request( "wpbooking-content-build" ) ) ,
                        );
                        wp_update_post( $my_layout );
                        wpbooking_set_admin_message( __( "Update layout successfully !" , "wpbooking" ) , 'success' );
                        $type_layout = WPBooking_Input::request( "wpbooking-layout-type" );
                        update_post_meta( $form_id , 'type_layout' , $type_layout );

                    } else {
						wpbooking_set_admin_message( __( "Error : Update layout not successfully !" , "wpbooking" ) , 'error' );
                    }
                } else {
					wpbooking_set_admin_message( __( 'Error : Update layout not successfully !' , 'wpbooking' ) , 'error' );
                }
            }
            if(!empty( $_POST[ 'wpbooking_btn_add_layout' ] ) and wp_verify_nonce( $_REQUEST[ 'wpbooking_add_layout' ] , "wpbooking_action" )) {
                if(!empty( $title )) {
                    $my_layout = array(
                        'post_title'   => WPBooking_Input::request( "wpbooking-title" ) ,
                        'post_content' => stripslashes( WPBooking_Input::request( "wpbooking-content-build" ) ) ,
                        'post_status'  => 'publish' ,
                        'post_author'  => $current_user->ID ,
                        'post_type'    => 'wpbooking_form' ,
                        'post_excerpt' => ''
                    );
                    $form_id   = wp_insert_post( $my_layout );
                    if(!empty( $form_id )) {
                        $type_layout = WPBooking_Input::request( "wpbooking-layout-type" );
                        update_post_meta( $form_id , 'type_layout' , $type_layout );
						wpbooking_set_admin_message( __( "Create layout successfully !" , "wpbooking" ) , 'success' );
                        wp_redirect( add_query_arg( array( 'page' => WPBooking_Input::request( 'page' ) , 'form_builder_id' => $form_id ) , admin_url( 'admin.php' ) ) );
                        exit();
                    } else {
						wpbooking_set_admin_message( __( 'Error : Create layout not successfully !' , "wpbooking" ) , 'error' );
                    }
                } else {
					wpbooking_set_admin_message( __( 'Error : Create layout not successfully !' , "wpbooking" ) , 'error' );
                }
            }
        }

        function register_wpbooking_sub_menu_page()
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

            $menu_page = WPBooking()->get_menu_page();
            $page      = array(
                'parent_slug' => $menu_page[ 'menu_slug' ] ,
                'page_title'  => __( 'Form Builder' , 'wpbooking' ) ,
                'menu_title'  => __( 'Form Builder' , 'wpbooking' ) ,
                'capability'  => 'manage_options' ,
                'menu_slug'   => 'wpbooking_page_form_builder' ,
                'function'    => array( $this , 'callback_wpbooking_sub_menu_form_builder' )
            );

            return apply_filters( 'wpbooking_setting_menu_args' , $page );

        }

        function callback_wpbooking_sub_menu_form_builder()
        {
            echo ($this->admin_load_view( 'form-builder' ));
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
                'name'               => _x( 'Form Builder' , 'post type general name' , 'wpbooking' ) ,
                'singular_name'      => _x( 'Form Builder' , 'post type singular name' , 'wpbooking' ) ,
                'menu_name'          => _x( 'Form Builder' , 'admin menu' , 'wpbooking' ) ,
                'name_admin_bar'     => _x( 'Form Builder' , 'add new on admin bar' , 'wpbooking' ) ,
                'add_new'            => _x( 'Add New' , 'service' , 'wpbooking' ) ,
                'add_new_item'       => __( 'Add New Form Builder' , 'wpbooking' ) ,
                'new_item'           => __( 'New Form Builder' , 'wpbooking' ) ,
                'edit_item'          => __( 'Edit Form Builder' , 'wpbooking' ) ,
                'view_item'          => __( 'View Form Builder' , 'wpbooking' ) ,
                'all_items'          => __( 'All Form Builders' , 'wpbooking' ) ,
                'search_items'       => __( 'Search Form Builders' , 'wpbooking' ) ,
                'parent_item_colon'  => __( 'Parent Form Builders:' , 'wpbooking' ) ,
                'not_found'          => __( 'No form builder found.' , 'wpbooking' ) ,
                'not_found_in_trash' => __( 'No form builder found in Trash.' , 'wpbooking' )
            );

            $args = array(
                'labels'             => $labels ,
                'description'        => __( 'Description.' , 'wpbooking' ) ,
                'public'             => true ,
                'publicly_queryable' => true ,
                'show_ui'            => true ,
                'show_in_menu'       => false ,
                'query_var'          => FALSE ,
                'rewrite'            => array( 'slug' => 'form_builder' ) ,
                'capability_type'    => 'post' ,
                'has_archive'        => FALSE ,
                'hierarchical'       => false ,
                //'menu_position'      => '59.9',
                'supports'           => array( 'title' , 'editor' )
            );

            register_post_type( 'wpbooking_form' , $args );
        }

		function get_form_field_data($form_item_data,$post_id=FALSE){

			if(!empty($form_item_data['field_id']))
			{
				$return= apply_filters('wpbooking_get_form_field_data',FALSE,$form_item_data);
				return apply_filters('wpbooking_get_form_field_data_'.$form_item_data['field_id'],$return,$form_item_data,$post_id);
			}
		}

        static function inst()
        {
            if(!self::$_inst) {
                self::$_inst = new self();
            }
            return self::$_inst;
        }

    }

	WPBooking_Admin_Form_Build::inst();
}