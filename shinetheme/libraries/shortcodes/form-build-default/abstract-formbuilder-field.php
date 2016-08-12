<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 4/27/2016
 * Time: 4:35 PM
 */
if(!class_exists('WPBooking_Abstract_Formbuilder_Field') )
{
	/**
	 * Base class for Form Builder Field
	 *
	 * @since 1.0
	 * @author dungdt
	 *
	 * Class WPBooking_Abstract_Formbuilder_Field
	 */
	abstract class  WPBooking_Abstract_Formbuilder_Field
	{
		protected $field_id=FALSE;
		protected $field_data=array();

		function __construct()
		{
			if(!$this->field_id) return;

			add_action('init',array($this,'_register_field'));
			add_filter('wpbooking_get_form_field_data_'.$this->field_id,array($this,'_get_form_data_value'),10,3);
		}

		/**
		 * Get Full Field ID, Example for Shortcode Name
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @return string
		 */
		function get_field_id()
		{
			$field_id= apply_filters('wpbooking_form_field_id','wpbooking_form_'.$this->field_id);
			$field_id= apply_filters('wpbooking_form_field_id_'.$this->field_id,$field_id);

			return $field_id;
		}

		/**
		 * Get array of  Field Config Data
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @return bool|mixed|void
		 */
		function get_field_data()
		{
			if(empty($this->field_data)) return FALSE;

			$this->field_data['name']=$this->get_field_id();

			return apply_filters('wpbooking_form_get_field_data',$this->field_data,$this);
		}

		/**
		 * Hook Callback for Register Field, Register Shortcode
		 *
		 * @since 1.0
		 * @author dungdt
		 */
		function _register_field()
		{
			if(!empty($this->field_data))
			{
				wpbooking_add_field_form_builder($this->get_field_data());
			}

			add_shortcode($this->get_field_id() , array($this,'shortcode') );
		}

		/**
		 * Add Field To Current Form Loop
		 * @author dungdt
		 * @since 1.0
		 *
		 * @param $name
		 * @param $options
		 */
		protected function add_field($name,$options)
		{
			$options['field_id']=$this->field_id;
			WPBooking_Admin_Form_Build::inst()->add_form_field($name,$options);
		}

		/**
		 * Shortcode HTML Render Function. Remember to call parent::add_field() inside this function
		 *
		 * @author dungdt
		 * @since 1.0
		 *
		 * @param array $attr
		 * @param bool|FALSE $content
		 * @return mixed
		 */
		abstract function shortcode($attr=array(),$content=FALSE);

		/**
		 *
		 * Parse Form Item data to Value
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param array $form_item_data
		 * @param $post_id
		 * @return string
		 */
		abstract function get_value($form_item_data,$post_id);

		/**
		 *
		 * Hook Callback for get Form Data Value
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param string $result Default Result String
		 * @param array $form_item_data
		 * @param $post_id
		 * @return string
		 */
		function _get_form_data_value($result,$form_item_data,$post_id)
		{
			return $this->get_value($form_item_data,$post_id);
		}

		/**
		 * Check if Field is required
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param array $attr
		 * @return bool
		 */
		function is_required($attr=array()){
			$attr=wp_parse_args($attr,array(
				'is_required'=>'',
				'hide_when_logged_in'=>FALSE
			));

			if($attr['is_required']=='on' and (empty($attr['hide_when_logged_in']) or !is_user_logged_in())){
				return true;
			}else{
				return FALSE;
			}
		}

		/**
		 * Check if field is hidden for logged in user
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param $attr
		 * @return bool
		 */
		function is_hidden($attr){
			$attr=wp_parse_args($attr,array(
				'hide_when_logged_in'=>FALSE
			));
			if(is_user_logged_in() and !empty($attr['hide_when_logged_in'])) return true;
			return FALSE;
		}
	}
}