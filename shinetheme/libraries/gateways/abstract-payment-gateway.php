<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 3/23/2016
 * Time: 2:35 PM
 */
if(!class_exists('Traveler_Abstract_Payment_Gateway'))
{
	class Traveler_Abstract_Payment_Gateway
	{
		protected $gateway_id=FALSE;
		protected $gateway_info=array();
		protected $settings=array();

		function __construct()
		{
			if(!$this->gateway_id) return FALSE;
			$this->gateway_info=wp_parse_args($this->gateway_info,array(
				'label'=>'',
				'description'=>''
			));

			add_filter('traveler_payment_gateways',array($this,'_register_gateway'));
			add_filter('traveler_payment_settings_sections',array($this,'_add_setting_section'));
		}

		function _add_setting_section($sections=array())
		{
			$settings=$this->get_settings_fields();
			if(!empty($settings)){
				foreach($settings as $key=>$value){
					$settings[$key]['id']='gateway_'.$this->gateway_id.'_'.$value['id'];
				}
			}
			$sections['payment_'.$this->gateway_id]=array(
				'id'     => 'payment_'.$this->gateway_id,
				'label'  => $this->get_info('label'),
				'fields' =>$settings
			);
			return $sections;
		}

		function get_settings_fields()
		{
			return apply_filters('traveler_payment_'.$this->gateway_id.'_settings_fields',$this->settings);
		}
		function get_info($key=FALSE)
		{
			$info= apply_filters('traveler_gateway_info',$this->gateway_info);
			$info= apply_filters('traveler_gateway_'.$this->gateway_id.'_info',$info);

			if($key){

				$data= isset($info[$key])?$info[$key]:FALSE;

				$data= apply_filters('traveler_gateway_info_'.$key,$data);
				$data= apply_filters('traveler_gateway_'.$this->gateway_id.'_info_'.$key,$data);
				return $data;
			}

			return $info;
		}

		function get_option($key,$default)
		{
			return traveler_get_option('gateway_'.$this->gateway_id.'_'.$key,$default);
		}

		function _register_gateway($gateways=array())
		{
			$gateways[$this->gateway_id]=$this;

			return $gateways;
		}
	}
}