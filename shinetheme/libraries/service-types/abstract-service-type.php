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
		protected $type_id=FALSE;
		protected $type_info=array();
		protected $settings=array();

		function __construct()
		{
			if(!$this->type_id) return FALSE;
			$this->type_info=wp_parse_args($this->type_info,array(
				'label'=>'',
				'description'=>''
			));

			add_filter('traveler_service_type',array($this,'_register_type'));
			add_filter('traveler_service_type_settings_sections',array($this,'_add_setting_section'));
		}

		function _add_setting_section($sections=array())
		{
			$sections['payment_'.$this->type_id]=array(
				'id'     => 'service_type_'.$this->type_id,
				'label'  => $this->get_info('label'),
				'fields' => $this->get_settings_fields()
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

		function _register_type($service_types=array())
		{
			$service_types[$this->type_id]=$this;

			return $service_types;
		}
	}
}