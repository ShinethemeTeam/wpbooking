<?php
if(!class_exists('WPBooking_Form_Email_Field')){
	class WPBooking_Form_Email_Field extends WPBooking_Abstract_Formbuilder_Field{
		static $_inst;

		function __construct()
		{
			$this->field_id = 'email';
			$this->field_data =
				array(
					"title"    => __( "Email" , 'wpbooking' ) ,
					"name"     => 'wpbooking_form_field_email' ,
					"category" => __('Standard Fields','wpbooking') ,
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
					'placeholder' => '' ,
					'size'        => '' ,
					'maxlength'   => '' ,
				)   );
			extract( $data );
			$required = "";
			$rule = array();
			if($this->is_required($attr)) {
				$required = "required";
				$rule []= "required";
			}
			if(!empty($maxlength)){
				$rule []= "max_length[100]";
			}
			$rule[]='valid_email';

			$this->add_field( $name,array('data'=>$data,'rule'=>implode('|',$rule)));

			if($this->is_hidden($attr)) return FALSE;

            $label=false;

            if(!empty($data['title'])){

                $title=wpbooking_get_translated_string($data['title']);
                if($required) $title.=' <span class=required >*</span>';

                $label=sprintf('<p><label>%s</label></p>',$title);
            }

			return '<div class="wb-field">'.$label.'<input type="text" name="' . $name . '" id="' . $id . '" class="' . $class . '" value="' . $value . '" placeholder="' . $placeholder . '"  maxlength="' . $maxlength . '" size="' . $size . '"  ' . $required . ' /></div>';
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

	WPBooking_Form_Email_Field::inst();
}