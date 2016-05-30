<?php
if(!class_exists('WPBooking_Form_Checkbox_Field')){
	class WPBooking_Form_Checkbox_Field extends WPBooking_Abstract_Formbuilder_Field
	{
		static $_inst;

		function __construct()
		{
			$this->field_id='checkbox';
			$this->field_data=array(
				"title"   => __( "Check Box" , 'wpbooking' ) ,
				"category" => 'Standard Fields',
				"options" => array(
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
						'value' => ""
					) ,
					array(
						"type"             => "text" ,
						"title"            => __( "Class" , 'wpbooking' ) ,
						"name"             => "class" ,
						"desc"             => __( "Class" , 'wpbooking' ) ,
						'edit_field_class' => 'wpbooking-col-md-6' ,
						'value' => ""
					) ,
					array(
						"type"             => "textarea" ,
						"title"            => __( "Options" , 'wpbooking' ) ,
						"name"             => "options" ,
						"desc"             => __("Ex: Title 1:value_1|Title 2:value_2",'wpbooking'),
						'edit_field_class' => 'wpbooking-col-md-12' ,
						'value' => ""
					) ,
				)
			);
			parent::__construct();
		}

		function shortcode($attr=array(),$content=FALSE)
		{
			$data = wp_parse_args(
				$attr,
				array(
					'title'          => '' ,
					'name'          => '' ,
					'id'          => '' ,
					'class'       => '' ,
					'options'       => '' ,
				)   );
			extract( $data );
			$list_item = "";
			if(!empty($options)){
				$tmp_list_item = explode('|',$options);
				if(!empty($tmp_list_item)){
					foreach($tmp_list_item as $k=>$v){
						$tmp = explode(':',$v);
						if(!empty($tmp[0]) and !empty($tmp[1])){
							$list_item .= '
                                        <label>
                                            <input type="checkbox" name="'.$name.'[]" id="'.$id.'" class="'.$class.'" value="'.$tmp[1].'"> '.$tmp[0].'
                                        </label>';
						}
					}
				}
			}
			parent::add_field($name,array('data'=>$data,'rule'=>''));

			return $list_item;
		}

		function get_value($value){

			if(is_array($value['value']) and !empty($value['data']['options']) and !empty($value['value'])){
				$options_array=explode('|',$value['data']['options']);
				$options=array();
				if(!empty($options_array))
				{
					foreach($options_array as $k=>$v){
						$ex=explode(':',$v);
						if(!empty($ex)){
							$options[$ex[1]]=$ex[0];
						}
					}
				}

				$value_string=array();

				if(!empty($options) )
				{
					foreach($value['value'] as $v2){
						if(array_key_exists($v2,$options)){
							$value_string[]=$options[$v2];
						}
					}
				}

				if(!empty($value_string))
					return implode(', ',$value_string);


			}
		}

		static  function inst()
		{
			if(!self::$_inst){
				self::$_inst=new self();
			}

			return self::$_inst;
		}
	}
	WPBooking_Form_Checkbox_Field::inst();

}
