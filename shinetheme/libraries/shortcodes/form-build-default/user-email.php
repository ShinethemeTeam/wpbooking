<?php
if (!class_exists('WPBooking_User_Email_Field')) {
	class WPBooking_User_Email_Field extends WPBooking_Abstract_Formbuilder_Field
	{
		static $_inst;

		function __construct()
		{
			$this->field_id = 'user_email';
			$this->field_data = array(
				"title"    => __("User Email", 'wpbooking'),
				"category" => __("User Field", 'wpbooking'),
				"options"  => array(
					array(
						"type"             => "required",
						"title"            => __("Set as <strong>required</strong>", 'wpbooking'),
						"desc"             => "",
						'edit_field_class' => 'wpbooking-col-md-6',
					),
					array(
						"type"             => "checkbox" ,
						'name'=>'hide_when_logged_in',
						'options'=>array(
							__( "Hide with <strong>Logged in use</strong>" , 'wpbooking' )=>1
						),
						'single_checkbox'=>1,
						'edit_field_class' => 'wpbooking-col-md-6' ,
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
						"type"             => "text",
						"title"            => __("ID (optional)", 'wpbooking'),
						"name"             => "id",
						"desc"             => __("ID", 'wpbooking'),
						'edit_field_class' => 'wpbooking-col-md-6',
						'value'            => ""
					),
					array(
						"type"             => "text",
						"title"            => __("Class (optional)", 'wpbooking'),
						"name"             => "class",
						"desc"             => __("Class", 'wpbooking'),
						'edit_field_class' => 'wpbooking-col-md-6',
						'value'            => ""
					),
//					array(
//						"type"             => "text",
//						"title"            => __("Value (optional)", 'wpbooking'),
//						"name"             => "value",
//						"desc"             => __("Value", 'wpbooking'),
//						'edit_field_class' => 'wpbooking-col-md-6',
//						'value'            => ""
//					),
					array(
						"type"             => "text",
						"title"            => __("Placeholder (optional)", 'wpbooking'),
						"name"             => "placeholder",
						"desc"             => __("Placeholder", 'wpbooking'),
						'edit_field_class' => 'wpbooking-col-md-6',
						'value'            => ""
					),
				)
			);
			parent::__construct();
		}

		function shortcode($attr = array(), $content = FALSE)
		{
			$data = wp_parse_args($attr,
				array(
					'is_required' => 'off',
					'title'       => '',
					'name'        => 'user_email',
					'id'          => '',
					'class'       => '',
					'value'       => '',
					'placeholder' => '',
					'size'        => '',
					'maxlength'   => '',
				));
			extract($data);

			if(is_user_logged_in()){
				$current_user = wp_get_current_user();
				$value=$current_user->user_email;
			}

			$array = array(
				'id'          => $id,
				'class'       => $class.' ',
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
				$array['class'].=' required';
			}
			if (!empty($maxlength)) {
				$rule [] = "max_length[".$maxlength."]";
			}
			$rule[]='valid_email';

			parent::add_field($name, array('data' => $data, 'rule' => implode('|', $rule)));
			if($this->is_hidden($attr)) return FALSE;

			if(is_user_logged_in()){
				$array['readonly']='readonly';
			}

			$a = FALSE;

			foreach ($array as $key => $val) {
				if ($val) {
					$a .= ' ' . $key . '="' . $val . '"';
				}
			}
			$html=array();
			if(!empty($data['title'])){
				$html[]=sprintf('<p><label>%s</label></p>',wpbooking_get_translated_string($data['title']));
			}
			$html[]= '<div class="wb-field"><input type="text" '.$a.' /></div>';

			return implode("\r\n",$html);
		}

		function get_value($form_item_data,$post_id)
		{
			return isset($form_item_data['value']) ? $form_item_data['value'] : FALSE;
		}

		static function inst()
		{
			if (!self::$_inst) {
				self::$_inst = new self();
			}

			return self::$_inst;
		}
	}

	WPBooking_User_Email_Field::inst();

}

