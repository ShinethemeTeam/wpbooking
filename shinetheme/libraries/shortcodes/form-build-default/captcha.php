<?php
if (!class_exists('WPBooking_Captcha_Field')) {
	class WPBooking_Captcha_Field extends WPBooking_Abstract_Formbuilder_Field
	{
		static $_inst;

		protected $_api_key=array(
			'recaptcha'=>array(
				'key'=>'6LfOAAETAAAAAGMpKXOEfaXJAjmQxD8oG7_zrO1w',
				'secret_key'=>'6LfOAAETAAAAAOoVLsmhL8a2xR4NUvWRKR2nNGn3'
			)
		);

		function __construct()
		{
			$this->field_id = 'captcha';
			$this->field_data = array(
				"title"    => __("Captcha", 'wpbooking'),
				"category" => __("Advance Field", 'wpbooking'),
				"options"  => array(
					array(
						"type"             => "checkbox" ,
						'name'=>'hide_when_logged_in',
						'options'=>array(
							__( "Hide with <strong>Logged in use</strong>" , 'wpbooking' )=>1
						),
						'single_checkbox'=>1,
						'edit_field_class' => 'wpbooking-col-md-12' ,
					) ,
					array(
						"type"             => "text",
						"title"            => __("Title", 'wpbooking'),
						"name"             => "title",
						"desc"             => __("Title", 'wpbooking'),
						'edit_field_class' => 'wpbooking-col-md-6',
						'value'            => ""
					),
					array(
						"type"             => "dropdown",
						"title"            => __("Type", 'wpbooking'),
						"name"             => "type",
						'edit_field_class' => 'wpbooking-col-md-6',
						'options'            => array(
							'recaptcha'=>esc_html__('Google reCAPTCHA','wpbooking')
						)
					),
				)
			);

			parent::__construct();

			add_action('wp_enqueue_scripts',array($this,'_add_scripts'));

			// Validate Captcha on Add To Cart
			add_filter('wpbooking_add_to_cart_validate',array($this,'_validate_add_to_cart_captcha'),10,3);

			// Validate Captcha on Do Check Out
			add_filter('wpbooking_do_checkout_validate',array($this,'_validate_do_checkout_captcha'),10,2);
		}

		function shortcode($attr = array(), $content = FALSE)
		{
			$data = wp_parse_args($attr,
				array(
					'is_required' => 'off',
					'title'       => '',
					'name'        => 'captcha',
					'id'          => '',
					'class'       => '',
					'value'       => '',
					'placeholder' => '',
					'size'        => '',
					'maxlength'   => '',
					'type'        => 'recaptcha'
				));
			extract($data);
			$array = array(
				'id'          => $id,
				'class'       => $class . ' ',
				'value'       => $value,
				'placeholder' => $placeholder,
				'size'        => $size,
				'maxlength'   => $maxlength,
				'name'        => $name
			);

			$required = "";
			$rule = array();
			if ($this->is_required($attr)) {
				$required = "required";
				$rule [] = "required";
				$array['class'] .= ' required';
			}
			if (!empty($maxlength)) {
				$rule [] = "max_length[" . $maxlength . "]";
			}

			parent::add_field($name, array('data' => $data, 'rule' => implode('|', $rule)));

			$a = FALSE;

			foreach ($array as $key => $val) {
				if ($val) {
					$a .= ' ' . $key . '="' . $val . '"';
				}
			}
			if($this->is_hidden($attr)) return FALSE;


			switch ($data['type']) {
				case "recaptcha":
				default:
					$html = $this->get_recaptcha();
					break;
			}
			$html .= '<div class="wb-field"><input type="hidden" name="wpbooking_captcha_type" value="' . $data['type'] . '"></div>';

			return $html;
		}

		/**
		 * Add Some Scripts for captcha libraries
		 *
		 * @since 1.0
		 * @author dungdt
		 */
		function _add_scripts()
		{
			wp_register_script('recaptcha','https://www.google.com/recaptcha/api.js',array(),null,true);
//			wp_localize_script('recaptcha','wpbooking_recaptcha',array(
//				'sitekey'=>$this->_api_key['recaptcha']['key']
//			));
		}

		/**
		 * Get Google reCAPTCHA HTML output
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @return string
		 */
		function get_recaptcha()
		{
			wp_enqueue_script('recaptcha');
			return '<div id="wpbooking_recaptcha_field" class="g-recaptcha" data-sitekey="'.$this->_api_key['recaptcha']['key'].'"></div>';
		}

		/**
		 * Hook callback for validate captcha on add-to-cart step
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param $is_validate
		 * @param $service_type
		 * @param $post_id
		 * @return bool
		 */
		function _validate_add_to_cart_captcha($is_validate, $service_type, $post_id)
		{
			$order_form_id = WPBooking_Order::inst()->get_order_form_id($service_type);
			$fields = wpbooking_get_form_fields($order_form_id);

			if(!empty($fields))
			{
				// check if this form is contain a captcha field and captcha type is not empty
				if(in_array('captcha',array_keys($fields)) and !empty($fields['captcha']['data']['type'])){
					$is_validate=$this->validate($fields['captcha']['data']['type']);
				}
			}

			return $is_validate;
		}

		/**
		 * Hook callback for validate captcha on checkout step
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param $is_validate
		 * @param array $cart
		 * @return bool
		 */
		function _validate_do_checkout_captcha($is_validate,$cart=array())
		{
			$form_id = WPBooking_Order::inst()->get_checkout_form_id();
			$fields = wpbooking_get_form_fields($form_id);

			if(!empty($fields))
			{
				// check if this form is contain a captcha field and captcha type is not empty
				if(in_array('captcha',array_keys($fields)) and !empty($fields['captcha']['data']['type'])){
					$is_validate=$this->validate($fields['captcha']['data']['type']);
				}
			}

			return $is_validate;
		}

		/**
		 * Validate Captcha by Captcha Type
		 *
		 * @since 1.0
		 * @autor dungdt
		 *
		 * @param bool|FALSE $captcha_type
		 * @return bool
		 */
		function validate($captcha_type=FALSE){
			switch($captcha_type){
				case "recaptcha":
				default:
					return $this->validate_recaptcha();
					break;
			}
		}


		/**
		 * Validate Google reCAPTCHA
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @return bool
		 */
		function validate_recaptcha()
		{
			$url='https://www.google.com/recaptcha/api/siteverify';

			$url=add_query_arg(array(
				'secret'=>$this->_api_key['recaptcha']['secret_key'],
				'response'=>WPBooking_Input::post('g-recaptcha-response'),
				'remoteip'=>WPBooking_Input::ip_address()
			),$url);

			$data=wp_remote_get($url);

			if(!is_wp_error($data)){
				$body=wp_remote_retrieve_body($data);

				$body_obj=json_decode($body);

				if(isset($body_obj->success) and $body_obj->success){
					return true;
				}else{
					wpbooking_set_message(esc_html__('Your captcha is not correct','wpbooking'),'danger');
					return FALSE;
				}
			}else{
				wpbooking_set_message(esc_html__('Can not verify captcha','wpbooking'),'danger');
				return FALSE;
			}
		}


		function get_value($form_item_data)
		{
			return FALSE;
		}

		static function inst()
		{
			if (!self::$_inst) {
				self::$_inst = new self();
			}

			return self::$_inst;
		}
	}

	WPBooking_Captcha_Field::inst();

}

