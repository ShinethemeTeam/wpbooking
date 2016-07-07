<?php
if(!class_exists('WPBooking_Form_Dropdown_Field')){
	class WPBooking_Form_Dropdown_Field extends WPBooking_Abstract_Formbuilder_Field{
		static $_inst;

		function __construct()
		{
			$this->field_id = 'dropdown';
			$this->field_data =array(
				"title"    => __( "Drop Down" , 'wpbooking' ) ,
				"name"     => 'wpbooking_form_field_dropdown' ,
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
						"type"             => "textarea" ,
						"title"            => __( "Options" , 'wpbooking' ) ,
						"name"             => "options" ,
						"desc"             => __( "Ex: Title 1:value_1|Title 2:value_2" , 'wpbooking' ) ,
						'edit_field_class' => 'wpbooking-col-md-12' ,
						'value'            => ''
					) ,
				)
			);

			parent::__construct();
		}

		function shortcode($attr=array(),$content=FALSE){
			$data = wp_parse_args($attr,
				array(
					'is_required' => 'off' ,
					'title'        => '' ,
					'name'        => '' ,
					'id'          => '' ,
					'class'       => '' ,
					'options'     => '' ,
				) );
			extract( $data );
			$required = "";
			$rule = "";
			if($this->is_required($attr)) {
				$required = "required";
				$rule .= "required";
			}
			$this->add_field($name,array('data'=>$data,'rule'=>$rule));
			$list_item = "<option value=''>" . __( "-- Select --" , 'wpbooking' ) . '</option>';
			if(!empty( $options )) {
				$tmp_list_item = explode( '|' , $options );
				if(!empty( $tmp_list_item )) {
					foreach( $tmp_list_item as $k => $v ) {
						$tmp = explode( ':' , $v );
						if(!empty( $tmp[ 0 ] ) and !empty( $tmp[ 1 ] )) {
							$list_item .= "<option value='" . $tmp[ 1 ] . "'>" . $tmp[ 0 ] . "</option>";
						}
					}
				}

			}
			if($this->is_hidden($attr)) return FALSE;
			return '<select name="' . $name . '" id="' . $id . '" class="' . $class . ' '.$required.'">
                    ' . $list_item . '
                </select>';
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

	WPBooking_Form_Dropdown_Field::inst();
}