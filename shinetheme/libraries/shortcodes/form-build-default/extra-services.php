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
							__("Hide with <strong>Logged in user</strong>", 'wpbooking') => 1
						),
						'single_checkbox'  => 1,
						'edit_field_class' => 'wpbooking-col-md-12',
					),
					array(
						"type"             => "text",
						"title"            => __("Title", 'wpbooking'),
						"name"             => "title",
						'edit_field_class' => 'wpbooking-col-md-6',
						'value'            => ""
					),
					array(
						"type"             => "label",
						"title"            => __("Name", 'wpbooking'),
						'edit_field_class' => 'wpbooking-col-md-6',
						'value'            => "extra_services",
						"desc"             => __("This is default attribute, you can not change it", 'wpbooking'),
					),
					array(
						"type"             => "text",
						"title"            => __("CSS ID (Optional)", 'wpbooking'),
						"name"             => "id",
						"desc"             => __("ID", 'wpbooking'),
						'edit_field_class' => 'wpbooking-col-md-6',
						'value'            => ""
					),
					array(
						"type"             => "text",
						"title"            => __("CSS Class (Optional)", 'wpbooking'),
						"name"             => "class",
						"desc"             => __("Class", 'wpbooking'),
						'edit_field_class' => 'wpbooking-col-md-6 clear',
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
                $list_item[]='<div class="wb-field wb-extra-fields">';
				if(!empty($extra_services) and is_array($extra_services)){
					if(!empty($data['title'])){


						$list_item[]=sprintf('<p><label>%s</label></p>',wpbooking_get_translated_string($data['title']));
					}
					$list_item[]='';
					$i=0;
					foreach($extra_services as $key=>$value){
						$i++;
						if(!$value['money']) continue;
						$title='#'.($i).' '.wpbooking_get_translated_string($value['title']);
						if($value['money']){
							$title.='<br><span class="extra-service-money">'.WPBooking_Currency::format_money($value['money']).'</span>';
						}
						$checked=FALSE;
						$class=FALSE;
						$start_from=1;
						if(!empty($value['require']) and $value['require']=='yes'){
							$checked='checked disable';
							$class='disable';
							$start_from=1;
						}
						$list_item[]='<div class="wb-extra-field">';
						$list_item[]=sprintf('<label class="field-title"><input  name="extra_services[%s][selected]" data-style="icheckbox_square-orange" class="wb-icheck %s" %s  type="checkbox" value="%s"> %s</label>',$key,$class,$checked,$value['title'],$title);
						$list_item[]='<label class="field-number">';
							$list_item[]=sprintf("<select name='extra_services[%s][number]'>",$key);
							for($k=$start_from;$k<=20;$k++){
								$list_item[]=sprintf('<option value="%d">%d</option>',$k,$k);
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

		function get_value($form_value,$post_id=FALSE)
		{
			$form_value=wp_parse_args($form_value,array(
				'value'=>array()
			));
			$service=new WB_Service($post_id);
			$default = $service->get_extra_services();
			$extra_services=$form_value['value'];
			$html=array();

			if(!empty($default)){
				foreach($default as $k=>$v){
					if(!array_key_exists($k,$default)) continue;
					if(!$v['money']) continue;

					$number=!empty($extra_services[$k]['number'])?$extra_services[$k]['number']:1;
					$money=WPBooking_Currency::format_money($v['money']*$number);

					if($v['require']=='yes' or !empty($extra_services[$k]['selected'])) {
						$html[]=sprintf('<div class="extra-item"><span class="extra-name">%s:</span> <span class="extra-number">(%s x %s) = </span><span class="extra-total">%s</span></div>',$v['title'],WPBooking_Currency::format_money($v['money']),$number,$money);
					}
				}
			}
			if(!empty($html)){
				return implode("\r\n",$html);
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
