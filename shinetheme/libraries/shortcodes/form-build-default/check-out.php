<?php
if (!class_exists('WPBooking_Form_Check_Out_Field')) {
	class WPBooking_Form_Check_Out_Field extends WPBooking_Abstract_Formbuilder_Field
	{
		static $_inst;

		function __construct()
		{
			$this->field_id = 'check_out';
			$this->field_data = array(
				"title"    => esc_html__("Check-Out", 'wpbooking'),
				"category" => esc_html__("Specific Fields", 'wpbooking'),
				"options"  => array(
					array(
						"type"             => "required",
						"title"            => esc_html__("Set as <strong>required</strong>", 'wpbooking'),
						"desc"             => "",
						'edit_field_class' => 'wpbooking-col-md-6',
					),
					array(
						"type"             => "checkbox" ,
						'name'=>'hide_when_logged_in',
						'options'=>array(
							esc_html__( "Hide with <strong>Logged in user</strong>" , 'wpbooking' )=>1
						),
						'single_checkbox'=>1,
						'edit_field_class' => 'wpbooking-col-md-6' ,
					) ,
					array(
						"type"             => "text",
						"title"            => esc_html__("Title", 'wpbooking'),
						"name"             => "title",
						"desc"             => esc_html__("Title", 'wpbooking'),
						'edit_field_class' => 'wpbooking-col-md-6',
						'value'            => ""
					),
					array(
						"type"             => "label",
						"title"            => esc_html__("Name", 'wpbooking'),
						'edit_field_class' => 'wpbooking-col-md-6',
						'value'            => "check_out",
						"desc"             => esc_html__("This is default attribute, you cannot change ", 'wpbooking'),
					),
					array(
						"type"             => "text",
						"title"            => esc_html__("CSS ID (optional)", 'wpbooking'),
						"name"             => "id",
						"desc"             => esc_html__("ID", 'wpbooking'),
						'edit_field_class' => 'wpbooking-col-md-6',
						'value'            => ""
					),
					array(
						"type"             => "text",
						"title"            => esc_html__("CSS Class (optional)", 'wpbooking'),
						"name"             => "class",
						"desc"             => esc_html__("Class", 'wpbooking'),
						'edit_field_class' => 'wpbooking-col-md-6',
						'value'            => ""
					),
					array(
						"type"             => "text",
						"title"            => esc_html__("Value (optional)", 'wpbooking'),
						"name"             => "value",
						"desc"             => esc_html__("Value", 'wpbooking'),
						'edit_field_class' => 'wpbooking-col-md-6',
						'value'            => ""
					),
					array(
						"type"             => "text",
						"title"            => esc_html__("Placeholder (optional)", 'wpbooking'),
						"name"             => "placeholder",
						"desc"             => esc_html__("Placeholder", 'wpbooking'),
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
				'class'       => $class.' wpbooking-field-date-end',
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

			if($check_out=WPBooking_Input::get('check_out')){
				$array['value']=$check_out;
			}
			$a = FALSE;

			foreach ($array as $key => $val) {
				if ($val) {
					$a .= ' ' . $key . '="' . $val . '"';
				}
			}
			$html=array('<div class="wb-field-datepicker wb-field">');
			if(!empty($data['title'])){

                $title=wpbooking_get_translated_string($data['title']);
                if($required) $title.=' <span class=required >*</span>';

				$html[]=sprintf('<p><label>%s</label></p>',$title);
			}
			$html[]= '<label><input readonly type="text" '.$a.' /><i class="fa fa-calendar"></i></label></div>';

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

	WPBooking_Form_Check_Out_Field::inst();

}

