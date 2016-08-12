<?php
if(!class_exists('WPBooking_Form_Textarea_Field')){
	class WPBooking_Form_Textarea_Field extends WPBooking_Abstract_Formbuilder_Field{
		static $_inst;

		function __construct()
		{
			$this->field_id = 'textarea';
			$this->field_data =
				array(
					"title"    => __( "TextArea" , 'wpbooking' ) ,
					"name"     => 'wpbooking_form_field_textarea' ,
					"category" => esc_attr__('Standard Fields','wpbooking') ,
					"options"  => array(
						array(
							"type"             => "required" ,
							"title"            => __( "Set as <strong>required</strong>" , 'wpbooking' ) ,
							"desc"             => "" ,
							'edit_field_class' => 'wpbooking-col-md-6' ,
						) ,
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
							"type"             => "text" ,
							"title"            => __( "Title" , 'wpbooking' ) ,
							"name"             => "title" ,
							"desc"             => __( "Title" , 'wpbooking' ) ,
							'edit_field_class' => 'wpbooking-col-md-6' ,
							'value'            => ""
						) ,
						array(
							"type"             => "text" ,
							"title"            => __( "Name" , 'wpbooking' ) ,
							"name"             => "name" ,
							"desc"             => __( "Name" , 'wpbooking' ) ,
							'edit_field_class' => 'wpbooking-col-md-6' ,
							'value'            => ""
						) ,
						array(
							"type"             => "text" ,
							"title"            => __( "ID" , 'wpbooking' ) ,
							"name"             => "id" ,
							"desc"             => __( "ID" , 'wpbooking' ) ,
							'edit_field_class' => 'wpbooking-col-md-6' ,
							'value'            => ""
						) ,
						array(
							"type"             => "text" ,
							"title"            => __( "Class" , 'wpbooking' ) ,
							"name"             => "class" ,
							"desc"             => __( "Class" , 'wpbooking' ) ,
							'edit_field_class' => 'wpbooking-col-md-6' ,
							'value'            => ""
						) ,
						array(
							"type"             => "textarea" ,
							"title"            => __( "Value" , 'wpbooking' ) ,
							"name"             => "value" ,
							"desc"             => __( "Value" , 'wpbooking' ) ,
							'edit_field_class' => 'wpbooking-col-md-12' ,
							'value'            => ""
						) ,
						array(
							"type"             => "text" ,
							"title"            => __( "Rows" , 'wpbooking' ) ,
							"name"             => "rows" ,
							"desc"             => __( "Rows" , 'wpbooking' ) ,
							'edit_field_class' => 'wpbooking-col-md-6' ,
							'value'            => ""
						) ,
						array(
							"type"             => "text" ,
							"title"            => __( "Columns" , 'wpbooking' ) ,
							"name"             => "columns" ,
							"desc"             => __( "Columns" , 'wpbooking' ) ,
							'edit_field_class' => 'wpbooking-col-md-6' ,
							'value'            => ""
						) ,

					)
				);

			parent::__construct();
		}

		function shortcode($attr=array(),$content=FALSE){
			$data = wp_parse_args(
				$attr,
				array(
					'is_required' => 'off' ,
					'title'        => '' ,
					'name'        => '' ,
					'id'          => '' ,
					'class'       => '' ,
					'value'       => '' ,
					'rows'        => '' ,
					'columns'     => '' ,
				) );
			extract( $data );
			$required = "";
			$rule = "";
			if($this->is_required($attr)) {
				$required = "required";
				$rule .= "required";
			}
			$this->add_field($name,array('data'=>$data,'rule'=>$rule));
			if($this->is_hidden($attr)) return FALSE;
			return '<textarea name="' . $name . '" id="' . $id . '" class="' . $class . '" rows="' . $rows . '" cols="' . $columns . '" ' . $required . ' >' . $value . '</textarea>';
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

	WPBooking_Form_Textarea_Field::inst();
}