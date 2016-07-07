<?php
if (!class_exists('WPBooking_Current_Datetime_Field')) {
	class WPBooking_Current_Datetime_Field extends WPBooking_Abstract_Formbuilder_Field
	{
		static $_inst;

		function __construct()
		{
			$this->field_id = 'current_datetime';
			$this->field_data = array(
				"title"    => __("Current Datetime (Server Time)", 'wpbooking'),
				"category" => __("Hidden Fields", 'wpbooking'),
				"options"  => array(
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
					'name'        => 'current_datetime',
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

			parent::add_field($name, array('data' => $data, 'rule' => implode('|', $rule)));

			if($this->is_hidden($attr)) return FALSE;

			$a = FALSE;

			foreach ($array as $key => $val) {
				if ($val) {
					$a .= ' ' . $key . '="' . $val . '"';
				}
			}

			return '<input type="hidden" '.$a.' />';
		}

		function get_value($form_item_data)
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

	WPBooking_Current_Datetime_Field::inst();

}

