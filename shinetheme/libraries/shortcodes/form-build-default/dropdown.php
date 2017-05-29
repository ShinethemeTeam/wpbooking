<?php
if(!class_exists('WPBooking_Form_Dropdown_Field')){
	class WPBooking_Form_Dropdown_Field extends WPBooking_Abstract_Formbuilder_Field{
		static $_inst;

		function __construct()
		{
			$this->field_id = 'dropdown';
			$this->field_data =array(
				"title"    => esc_html__( "Drop Down" , 'wpbooking' ) ,
				"name"     => 'wpbooking_form_field_dropdown' ,
				"category" => 'Standard Fields' ,
				"options"  => array(
					array(
						"type"             => "required" ,
						"title"            => esc_html__( "Set as <strong>required</strong>" , 'wpbooking' ) ,
						"desc"             => "" ,
						'edit_field_class' => 'wpbooking-col-md-6' ,
					) ,
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
						"type"             => "text" ,
						"title"            => esc_html__( "Title" , 'wpbooking' ) ,
						"name"             => "title" ,
						"desc"             => esc_html__( "Title" , 'wpbooking' ) ,
						'edit_field_class' => 'wpbooking-col-md-6' ,
						'value'            => ""
					) ,
					array(
						"type"             => "text" ,
						"title"            => esc_html__( "Name" , 'wpbooking' ) ,
						"name"             => "name" ,
						"desc"             => esc_html__( "Name" , 'wpbooking' ) ,
						'edit_field_class' => 'wpbooking-col-md-6' ,
						'value'            => ""
					) ,
					array(
						"type"             => "text" ,
						"title"            => esc_html__( "ID" , 'wpbooking' ) ,
						"name"             => "id" ,
						"desc"             => esc_html__( "ID" , 'wpbooking' ) ,
						'edit_field_class' => 'wpbooking-col-md-6' ,
						'value'            => ""
					) ,
					array(
						"type"             => "text" ,
						"title"            => esc_html__( "Class" , 'wpbooking' ) ,
						"name"             => "class" ,
						"desc"             => esc_html__( "Class" , 'wpbooking' ) ,
						'edit_field_class' => 'wpbooking-col-md-6' ,
						'value'            => ""
					) ,
					array(
						"type"             => "textarea" ,
						"title"            => esc_html__( "Options" , 'wpbooking' ) ,
						"name"             => "options" ,
						"desc"             => esc_html__( "Ex: Title 1:value_1|Title 2:value_2" , 'wpbooking' ) ,
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
			$list_item = "<option value=''>" . esc_html__( "-- Select --" , 'wpbooking' ) . '</option>';
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

            $label=false;

            if(!empty($data['title'])){

                $title=wpbooking_get_translated_string($data['title']);
                if($required) $title.=' <span class=required >*</span>';

                $label=sprintf('<p><label>%s</label></p>',$title);
            }

			return '<div class="wb-field dropdown">'.$label.'<select name="' . $name . '" id="' . $id . '" class="' . $class . ' '.$required.'">
                    ' . $list_item . '
                </select></div>';
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

	WPBooking_Form_Dropdown_Field::inst();
}