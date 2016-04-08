<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 3/23/2016
 * Time: 2:35 PM
 */
if(!class_exists('Traveler_Abstract_Service_Type'))
{
	class Traveler_Abstract_Service_Type
	{
		protected $type_id = FALSE;
		protected $type_info = array();
		protected $settings = array();

		function __construct()
		{
			if (!$this->type_id) return FALSE;
			$this->type_info = wp_parse_args($this->type_info, array(
				'label'       => '',
				'description' => ''
			));

			add_filter('traveler_service_types', array($this, '_register_type'));
			add_filter('traveler_service_setting_sections', array($this, '_add_setting_section'));
			add_filter('traveler_review_stats', array($this, '_filter_get_review_stats'));
			add_filter('traveler_get_order_form_'.$this->type_id, array($this, '_get_order_form'));

            /*Change Search*/
			add_filter('traveler_add_page_archive_search', array($this, '_add_page_archive_search'));
			add_filter('traveler_service_query_args_'.$this->type_id, array($this, '_service_query_args'));
			add_action('traveler_before_service_query_'.$this->type_id, array($this, '_get_where_query'));


			add_filter('traveler_get_order_form_id_'.$this->type_id, array($this, 'get_order_form_id'));

		}

		/**
		 * Filter the Order Form HTML
		 */
		function _get_order_form()
		{
			$form_id= $this->get_option('order_form');
			$post=get_post($form_id);
			if($post){
				return apply_filters('the_content',$post->post_content);
			}

		}
		function get_order_form_id()
		{
			return $form_id= $this->get_option('order_form');
		}

		function _filter_get_review_stats($stats)
		{
			$post_id = get_the_ID();

			if (get_post_meta($post_id, 'service_type', TRUE) != $this->type_id) return $stats;

			$stats = $this->get_review_stats();
			if (!empty($stats)) return $stats;

			return $stats;
		}

		function get_review_stats()
		{
			return $this->get_option('review_stats', array());
		}

		function _add_setting_section($sections=array())
		{
			$settings=$this->get_settings_fields();
			if(!empty($settings)){
				foreach($settings as $key=>$value){
					if(!empty($value['id']))
					$settings[$key]['id']='service_type_'.$this->type_id.'_'.$value['id'];
				}
			}
			$sections['service_type_'.$this->type_id]=array(
				'id'     => 'service_type_'.$this->type_id,
				'label'  => $this->get_info('label'),
				'fields' => $settings
			);
			return $sections;
		}

		function get_settings_fields()
		{

			return apply_filters('traveler_service_type_'.$this->type_id.'_settings_fields',$this->settings);
		}

		function get_info($key=FALSE)
		{
			$info= apply_filters('traveler_service_type_info',$this->type_info);
			$info= apply_filters('traveler_service_type_'.$this->type_id.'_info',$info);

			if($key){

				$data= isset($info[$key])?$info[$key]:FALSE;

				$data= apply_filters('traveler_service_type_info_'.$key,$data);
				$data= apply_filters('traveler_service_type_'.$this->type_id.'_info_'.$key,$data);
				return $data;
			}

			return $info;
		}
		function get_option($key,$default=FALSE)
		{
			return traveler_get_option('service_type_'.$this->type_id.'_'.$key,$default);
		}

		function _register_type($service_types=array())
		{
			$service_types[$this->type_id]=array(
				'label'=>$this->get_info('label'),
				'object'=>$this
			);

			return $service_types;
		}
        function _add_page_archive_search($args)
        {
            return $args;
        }
        function _service_query_args($args){
            return $args;
        }
        function _get_where_query($where){
            return $where;
        }
	}
}