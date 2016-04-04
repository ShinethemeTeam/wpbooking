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
				'tour'=>array(
					'label'=>__("Tour",'traveler-booking')
				),

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