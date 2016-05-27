<?php
if (!class_exists('WPBooking_Form_Check_Out_Field')) {
	class WPBooking_Form_Check_Out_Field extends WPBooking_Abstract_Formbuilder_Field
	{
		static $_inst;

		function __construct()
		{
			$this->field_id = 'check_out';
			$this->field_data = array(
				"title"    => __("Check-Out", 'wpbooking'),
				"category" => __("Specific Fields", 'wpbooking'),
				"options"  => array(
					array(
						"type"             => "required",
						"title"            => __("Set as <strong>required</strong>", 'wpbooking'),
						"desc"             => "",
						'edit_field_class' => 'wpbooking-col-md-12',
					),
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
						"title"            => __("ID", 'wpbooking'),
						"name"             => "id",
						"desc"             => __("ID", 'wpbooking'),
						'edit_field_class' => 'wpbooking-col-md-6',
						'value'            => ""
					),
					array(
						"type"             => "text",
						"title"            => __("Class", 'wpbooking'),
						"name"             => "class",
						"desc"             => __("Class", 'wpbooking'),
						'edit_field_class' => 'wpbooking-col-md-6',
						'value'            => ""
					),
					array(
						"type"             => "text",
						"title"            => __("Value", 'wpbooking'),
						"name"             => "value",
						"desc"             => __("Value", 'wpbooking'),
						'edit_field_class' => 'wpbooking-col-md-6',
						'value'            => ""
					),
					array(
						"type"             => "text",
						"title"            => __("Placeholder", 'wpbooking'),
						"name"             => "placeholder",
						"desc"             => __("Placeholder", 'wpbooking'),
						'edit_field_class' => 'wpbooking-col-md-6',
						'value'            => ""
					),
					array(
						"type"             => "text",
						"title"            => __("Size", 'wpbooking'),
						"name"             => "size",
						"desc"             => __("Size", 'wpbooking'),
						'edit_field_class' => 'wpbooking-col-md-6',
						'value'            => ""
					),
					array(
						"type"             => "text",
						"title"            => __("Maxlength", 'wpbooking'),
						"name"             => "maxlength",
						"desc"             => __("Maxlength", 'wpbooking'),
						'edit_field_class' => 'wpbooking-col-md-6',
						'value'            => ""
					)
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
					'name'        => 'check_out',
					'id'          => '',
					'class'       => '',
					'value'       => '',
					'placeholder' => '',
					'size'        => '',
					'maxlength'   => '',
				));
			extract($data);
			$array = array(
				'id'          => $id,
				'class'       => $class.' wpbooking-date-end',
				'value'       => $value,
				'placeholder' => $placeholder,
				'size'        => $size,
				'maxlength'   => $maxlength,
				'name'        => $name
			);

			$required = "";
			$rule = array();
			if ($is_required == "on") {
				$required = "required";
				$rule [] = "required";
				$array['class'].=' required';
			}
			if (!empty($maxlength)) {
				$rule [] = "max_length[".$maxlength."]";
			}

			parent::add_field($name, array('data' => $data, 'rule' => implode('|', $rule)));

			$a = FALSE;

			foreach ($array as $key => $val) {
				if ($val) {
					$a .= ' ' . $key . '="' . $val . '"';
				}
			}

			return '<input type="text" '.$a.' />';
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

	WPBooking_Form_Check_Out_Field::inst();

}

