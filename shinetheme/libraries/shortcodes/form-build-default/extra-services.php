<?php
if (!class_exists('WPBooking_Form_Extra_Service_Field')) {
	class WPBooking_Form_Extra_Service_Field extends WPBooking_Abstract_Formbuilder_Field
	{
		static $_inst;

		function __construct()
		{
			$this->field_id = 'extra_services';
			$this->field_data = array(
				"title"    => __("Extra Services", 'wpbooking'),
				"category" => 'Specific Fields',
				"options"  => array(
					array(
						"type"             => "checkbox",
						'name'             => 'hide_when_logged_in',
						'options'          => array(
							__("Hide with <strong>Logged in use</strong>", 'wpbooking') => 1
						),
						'single_checkbox'  => 1,
						'edit_field_class' => 'wpbooking-col-md-12',
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
				)
			);
			parent::__construct();
		}

		function shortcode($attr = array(), $content = FALSE)
		{
			$data = wp_parse_args(
				$attr,
				array(
					'title'   => '',
					'name'    => 'extra_services',
					'id'      => '',
					'class'   => '',
					'options' => '',
				));
			extract($data);
			$list_item = array();
			parent::add_field($name, array('data' => $data, 'rule' => ''));

			if ($this->is_hidden($attr)) return FALSE;

			if(is_singular()){
				$service=new WB_Service();
				$extra_services=$service->get_extra_services();

				if(!empty($extra_services) and is_array($extra_services)){
					$list_item[]='<div class="wb-field wb-extra-fields">';
					foreach($extra_services as $key=>$value){
						$title='#'.($key+1).' '.wpbooking_get_translated_string($value['title']);
						if($value['money']){
							$title.='<br><span class="extra-service-money">'.WPBooking_Currency::format_money($value['money']).'</span>';
						}
						$checked=FALSE;
						$class=FALSE;
						$start_from=0;
						if(!empty($value['require']) and $value['require']=='yes'){
							$checked='checked disable';
							$class='disable';
							$start_from=1;
						}
						$list_item[]='<div class="wb-extra-field">';
						$list_item[]=sprintf('<label class="field-title"><input  name="extra_services[%s][selected]" data-style="icheckbox_square-orange" class="wb-icheck %s" %s  type="checkbox" value="%s"> %s</label>',$key,$class,$checked,$value['title'],$title);
						$list_item[]='<label class="field-number">';
							$list_item[]=sprintf("<select name='extra_services[%s][number]'>",$key);
							for($i=$start_from;$i<=20;$i++){
								$list_item[]=sprintf('<option value="%d">%d</option>',$i,$i);
							}
							$list_item[]='</select>';
						$list_item[]='</label>';
						$list_item[]='</div>';
					}

					$list_item[]='</div>';
				}


				return implode("\r\n",$list_item);
			}


		}

		function get_value($value)
		{

			if (is_array($value['value']) and !empty($value['data']['options']) and !empty($value['value'])) {
				$options_array = explode('|', $value['data']['options']);
				$options = array();
				if (!empty($options_array)) {
					foreach ($options_array as $k => $v) {
						$ex = explode(':', $v);
						if (!empty($ex)) {
							$options[$ex[1]] = $ex[0];
						}
					}
				}

				$value_string = array();

				if (!empty($options)) {
					foreach ($value['value'] as $v2) {
						if (array_key_exists($v2, $options)) {
							$value_string[] = $options[$v2];
						}
					}
				}

				if (!empty($value_string))
					return implode(', ', $value_string);


			}
		}

		static function inst()
		{
			if (!self::$_inst) {
				self::$_inst = new self();
			}

			return self::$_inst;
		}
	}

	WPBooking_Form_Extra_Service_Field::inst();

}
