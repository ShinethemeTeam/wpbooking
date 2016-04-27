<?php
if(!class_exists('Traveler_Form_Text_Field')){
	class Traveler_Form_Text_Field extends Traveler_Abstract_Formbuilder_Field
	{
		static $_inst;

		function __construct()
		{
			$this->field_id='text';
			$this->field_data=array(
				"title"    => __( "Text" , 'traveler-booking' ) ,
				"category" => 'Standard Fields' ,
				"options"  => array(
					array(
						"type"             => "required" ,
						"title"            => __( "Set as <strong>required</strong>" , 'traveler-booking' ) ,
						"desc"             => "" ,
						'edit_field_class' => 'traveler-col-md-12' ,
					) ,
					array(
						"type"             => "text" ,
						"title"            => __( "Title" , 'traveler-booking' ) ,
						"name"             => "title" ,
						"desc"             => __( "Title" , 'traveler-booking' ) ,
						'edit_field_class' => 'traveler-col-md-6' ,
						'value'            => ""
					) ,
					array(
						"type"             => "text" ,
						"title"            => __( "Name" , 'traveler-booking' ) ,
						"name"             => "name" ,
						"desc"             => __( "Name" , 'traveler-booking' ) ,
						'edit_field_class' => 'traveler-col-md-6' ,
						'value'            => ""
					) ,
					array(
						"type"             => "text" ,
						"title"            => __( "ID" , 'traveler-booking' ) ,
						"name"             => "id" ,
						"desc"             => __( "ID" , 'traveler-booking' ) ,
						'edit_field_class' => 'traveler-col-md-6' ,
						'value'            => ""
					) ,
					array(
						"type"             => "text" ,
						"title"            => __( "Class" , 'traveler-booking' ) ,
						"name"             => "class" ,
						"desc"             => __( "Class" , 'traveler-booking' ) ,
						'edit_field_class' => 'traveler-col-md-6' ,
						'value'            => ""
					) ,
					array(
						"type"             => "text" ,
						"title"            => __( "Value" , 'traveler-booking' ) ,
						"name"             => "value" ,
						"desc"             => __( "Value" , 'traveler-booking' ) ,
						'edit_field_class' => 'traveler-col-md-6' ,
						'value'            => ""
					) ,
					array(
						"type"             => "text" ,
						"title"            => __( "Placeholder" , 'traveler-booking' ) ,
						"name"             => "placeholder" ,
						"desc"             => __( "Placeholder" , 'traveler-booking' ) ,
						'edit_field_class' => 'traveler-col-md-6' ,
						'value'            => ""
					) ,
					array(
						"type"             => "text" ,
						"title"            => __( "Size" , 'traveler-booking' ) ,
						"name"             => "size" ,
						"desc"             => __( "Size" , 'traveler-booking' ) ,
						'edit_field_class' => 'traveler-col-md-6' ,
						'value'            => ""
					) ,
					array(
						"type"             => "text" ,
						"title"            => __( "Maxlength" , 'traveler-booking' ) ,
						"name"             => "maxlength" ,
						"desc"             => __( "Maxlength" , 'traveler-booking' ) ,
						'edit_field_class' => 'traveler-col-md-6' ,
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
			if($is_required == "on") {
				$required = "required";
				$rule []= "required";
			}
			if(!empty($maxlength)){
				$rule []= "max_length[100]";
			}

			parent::add_field($name,array('data'=>$data,'rule'=>implode('|',$rule)));

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
	Traveler_Form_Text_Field::inst();

}

