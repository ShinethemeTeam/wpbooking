<?php
if(!class_exists('WPBooking_Form_Guest_Field')){
	class WPBooking_Form_Guest_Field extends WPBooking_Abstract_Formbuilder_Field{
		static $_inst;

		function __construct()
		{
			$this->field_id = 'guest';
			$this->field_data =array(
				"title"    => __( "Guest" , 'wpbooking' ) ,
				"category" => 'Specific Fields' ,
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
							__( "Hide with <strong>Logged in user</strong>" , 'wpbooking' )=>1
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
				)
			);

			parent::__construct();
		}

		function shortcode($attr=array(),$content=FALSE){
			$data = wp_parse_args($attr,
				array(
					'is_required' => 'off' ,
					'title'        => '' ,
					'name'        => 'guest' ,
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
				$class.=" wb-required";
			}
			$this->add_field($name,array('data'=>$data,'rule'=>$rule));
			$list_item = "<option value=''>" . __( "-- Select --" , 'wpbooking' ) . '</option>';

			$max_guest=20;
			if(is_singular()){
				$service=new WB_Service(get_the_ID());
				if($service->get_max_guests()){
					$max_guest=$service->get_max_guests();
				}
			}


			for($i=1;$i<=$max_guest;$i++){
				$list_item.=sprintf('<option value="%s" %s>%s</option>',$i,selected($i,WPBooking_Input::get('guest'),FALSE),$i);
			}

			if($this->is_hidden($attr)) return FALSE;

			$html='<div class="wb-field">';
			if(!empty($data['title'])){


                $title=wpbooking_get_translated_string($data['title']);
                if($required) $title.=' <span class=required >*</span>';

				$html.=sprintf('<p><label>%s</label></p>',$title);
			}

			$html.= '<select name="' . $name . '" id="' . $id . '" class="' . $class . ' '.$required.'">
                    ' . $list_item . '
                </select></div>';

			return $html;
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

	WPBooking_Form_Guest_Field::inst();
}