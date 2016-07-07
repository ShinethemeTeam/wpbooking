<?php
if(!class_exists('WPBooking_Form_Text_Field')){
	class WPBooking_Form_Text_Field extends WPBooking_Abstract_Formbuilder_Field
	{
		static $_inst;

		function __construct()
		{
			$this->field_id='text';
			$this->field_data=array(
				"title"    => __( "Text" , 'wpbooking' ) ,
				"category" => 'Standard Fields' ,
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
						"type"             => "text" ,
						"title"            => __( "Value" , 'wpbooking' ) ,
						"name"             => "value" ,
						"desc"             => __( "Value" , 'wpbooking' ) ,
						'edit_field_class' => 'wpbooking-col-md-6' ,
						'value'            => ""
					) ,
					array(
						"type"             => "text" ,
						"title"            => __( "Placeholder" , 'wpbooking' ) ,
						"name"             => "placeholder" ,
						"desc"             => __( "Placeholder" , 'wpbooking' ) ,
						'edit_field_class' => 'wpbooking-col-md-6' ,
						'value'            => ""
					) ,
					array(
						"type"             => "text" ,
						"title"            => __( "Size" , 'wpbooking' ) ,
						"name"             => "size" ,
						"desc"             => __( "Size" , 'wpbooking' ) ,
						'edit_field_class' => 'wpbooking-col-md-6' ,
						'value'            => ""
					) ,
					array(
						"type"             => "text" ,
						"title"            => __( "Maxlength" , 'wpbooking' ) ,
						"name"             => "maxlength" ,
						"desc"             => __( "Maxlength" , 'wpbooking' ) ,
						'edit_field_class' => 'wpbooking-col-md-6' ,
						'value'            => ""
					)
				)
			);
			parent::__construct();
		}

		function shortcode($attr=array(),$content=FALSE)
		{
			$data = wp_parse_args($attr,
				array(
					'is_required' => 'off' ,
					'title'        => '' ,
					'name'        => '' ,
					'id'          => '' ,
					'class'       => '' ,
					'value'       => '' ,
					'placeholder' => '' ,
					'size'        => '' ,
					'maxlength'   => '' ,
				)  );
			extract( $data );
			$required = "";
			$rule = array();
			if($this->is_required($attr)) {
				$required = "required";
				$rule []= "required";
			}
			if(!empty($maxlength)){
				$rule []= "max_length[".$maxlength."]";
			}

			parent::add_field($name,array('data'=>$data,'rule'=>implode('|',$rule)));
			if($this->is_hidden($attr)) return FALSE;

			return '<input type="text" name="' . $name . '" id="' . $id . '" class="' . $class . '" value="' . $value . '" placeholder="' . $placeholder . '"  maxlength="' . $maxlength . '" size="' . $size . '"  ' . $required . ' />';
		}

		function get_value($form_item_data){
			return isset($form_item_data['value'])?$form_item_data['value']:FALSE;
		}

		static  function inst()
		{
			if(!self::$_inst){
				self::$_inst=new self();
			}

			return self::$_inst;
		}
	}
	WPBooking_Form_Text_Field::inst();

}

