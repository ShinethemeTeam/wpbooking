<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 3/14/2016
 * Time: 9:32 AM
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if(!class_exists('Traveler_Service'))
{
	class Traveler_Service{

		private static $_inst;

		function __construct()
		{
			// Load Abstract Service Type class and Default Service Types

			$loader=Traveler_Loader::inst();
			$loader->load_library(array(
				'service-types/abstract-service-type',
				'service-types/room',
			));

			add_filter('comment_form_field_comment',array($this,'add_review_field'));
			add_action('comment_post',array($this,'_save_review_stats'));
			add_filter('get_comment_text',array($this,'_show_review_stats'),100);

			add_filter('template_include',array($this,'_show_single_service'));

            add_filter( 'template_include', array( $this, 'template_loader' ) );
		}

        function query($args=array(),$service_type=false)
        {
            $args=wp_parse_args($args,array(
                'post_type'=>'traveler_service'
            ));

            $args=apply_filters('traveler_service_query_args',$args);
            $args=apply_filters('traveler_service_query_args_'.$service_type,$args);

            do_action('traveler_before_service_query',$args);
            do_action('traveler_before_service_query_'.$service_type,$args);

            $query=new WP_Query($args);

            do_action('traveler_after_service_query',$args);
            do_action('traveler_after_service_query_'.$service_type,$args);

            return $query;
        }

        /**
         * @param $template
         * @return string
         */
        public function template_loader( $template ) {
            $is_page = get_the_ID();
            $list_page_search = apply_filters("traveler_add_page_archive_search",array());
            if(!empty($list_page_search[$is_page]))
            {
                $template=traveler_view_path('archive-service');
            }
            //var_dump($list_page_search);
            //var_dump($template);
            return $template;
        }
        /**
         * @return array|mixed|void
         */
        function _get_list_field_search(){
            $taxonomy = Traveler_Admin_Taxonomy_Controller::inst()->get_taxonomies();
            $list_taxonomy = array();
            if(!empty($taxonomy)) {
                foreach( $taxonomy as $k => $v ) {
                    $list_taxonomy[$k]=$v['label'];
                }
            }
            $list_filed = array(
                'room' => array(
                    array(
                        'name'    => 'title' ,
                        'label' => __( 'Title Field' , "traveler-booking" ) ,
                        'type'  => "text" ,
                        'value' => ""
                    ) ,
                    array(
                        'name'    => 'placeholder' ,
                        'label' => __( 'Placeholder' , "traveler-booking" ) ,
                        'desc'  => __( 'Placeholder' , "traveler-booking" ) ,
                        'type'  => 'text' ,
                    ) ,
                    array(
                        'name'      => 'field_type' ,
                        'label'   => __( 'Field Type' , "traveler-booking" ) ,
                        'type'    => "dropdown" ,
                        'options' => array(
                            "location_id"  => __( "Location" , "traveler-booking" ) ,
                            "check_in"  => __( "Check In" , "traveler-booking" ) ,
                            "check_out" => __( "Check Out" , "traveler-booking" ) ,
                            "taxonomy" => __( "Taxonomy" , "traveler-booking" ) ,
                            "review_rate" => __( "Review Rate" , "traveler-booking" ) ,
                        )
                    ) ,
                    array(
                        'name'      => 'taxonomy' ,
                        'label'   => __( '- Taxonomy' , "traveler-booking" ) ,
                        'type'    => "dropdown" ,
                        'class'    => "hide" ,
                        'options' => $list_taxonomy
                    ) ,
                    array(
                        'name'      => 'taxonomy_show' ,
                        'label'   => __( '- Type Show' , "traveler-booking" ) ,
                        'type'    => "dropdown" ,
                        'class'    => "hide" ,
                        'options' => array(
                            "dropdown"  => __( "Dropdown" , "traveler-booking" ) ,
                            "check_box"  => __( "Check Box" , "traveler-booking" ) ,
                        )
                    ) ,
                    array(
                        'name'      => 'taxonomy_operator' ,
                        'label'   => __( '- Operator' , "traveler-booking" ) ,
                        'type'    => "dropdown" ,
                        'class'    => "hide" ,
                        'options' => array(
                            "AND"  => __( "And" , "traveler-booking" ) ,
                            "OR"  => __( "Or" , "traveler-booking" ) ,
                        )
                    ) ,
                    array(
                        'name'      => 'required' ,
                        'label'   => __( 'Required' , "traveler-booking" ) ,
                        'type'    => "dropdown" ,
                        'options' => array(
                            "no"  => __( "No" , "traveler-booking" ) ,
                            "yes"  => __( "Yes" , "traveler-booking" ) ,
                        )
                    ) ,
                ) ,
                'tour' => array(
                    array(
                        'name'    => 'title' ,
                        'label' => __( 'Title' , "traveler-booking" ) ,
                        'type'  => "text" ,
                        'value' => ""
                    ) ,

                )
            );
            $list_filed = apply_filters( "traveler_booking_list_field_form_search" , $list_filed );
            return $list_filed;
        }


		/**
		 *
		 */
		function _show_single_service($template)
		{

			if(get_post_type()=='traveler_service' and is_single())
			{
				$template=traveler_view_path('single-service');
			}
			return $template;
		}

		/**
		 * Add Review Stats in End of the Content Text
		 * @param $content
		 * @return string
		 */
		function _show_review_stats($content)
		{
			$comnent_id=get_comment_ID();
			$comemntObj = get_comment($comnent_id);
			$post_id = $comemntObj->comment_post_ID;
			if(get_post_type($post_id)!='traveler_service') return $content;

			$content=traveler_load_view('review-item-stats').$content;
			return $content;
		}
		/**
		 * Save Comment Stats Data
		 * @param $comment_id
		 * @return bool
		 */
		function _save_review_stats($comment_id)
		{
			$comemntObj = get_comment($comment_id);
			$post_id = $comemntObj->comment_post_ID;

			if(get_post_type($post_id)!='traveler_service') return FALSE;

			update_comment_meta($comment_id,'traveler_review',Traveler_Input::post('traveler_review'));
			update_comment_meta($comment_id,'traveler_review_detail',Traveler_Input::post('traveler_review_detail'));

			do_action('after_traveler_update_review_stats');
		}

		function add_review_field($fields)
		{
			if(get_post_type()!='traveler_service') return $fields;

			$field_review=apply_filters('traveler_review_field',traveler_load_view('review-field'));
			return $field_review.$fields;
		}
		function get_service_types()
		{
			$default= array(
			);

			return apply_filters('traveler_service_types',$default);
		}

		function comments_template($template)
		{
			if(get_post_type()!='traveler_service') return $template;

			$template=traveler_view_path('reviews');

			return $template;
		}

		static function inst()
		{
			if(!self::$_inst){
				self::$_inst=new self();
			}

			return self::$_inst;
		}


	}

	Traveler_Service::inst();
}