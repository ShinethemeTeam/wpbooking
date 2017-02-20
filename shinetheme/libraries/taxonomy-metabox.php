<?php
if(!class_exists('WPBooking_Taxonomy_Metabox'))
{
	class WPBooking_Taxonomy_Metabox
	{
		static $_inst;

		protected $metabox_array=array();

		function __construct()
		{
			WPBooking_Loader::inst()->load_model('taxonomy_meta');
			add_action('init',array($this,'register_metabox_hook'),30);
			add_action('create_term',array($this,'_save_term_meta'),30,3);
			add_action('edit_term',array($this,'_save_term_meta'),30,3);
		}
		function register_metabox_hook(){
			if(!empty($this->metabox_array)){
				foreach($this->metabox_array as $taxonomy=>$metabox){
					add_action($taxonomy.'_add_form_fields',array($this,'_show_metabox'));
					add_action($taxonomy.'_edit_form',array($this,'_show_update_metabox'),10,2);
				}
			}

		}


		function _save_term_meta($term_id,$term_taxonomy_id=FALSE,$taxonomy=FALSE)
		{
			$fields=array();
			// Get all fields from metabox
			if(!empty($this->metabox_array) and !empty($this->metabox_array[$taxonomy])){
				foreach($this->metabox_array[$taxonomy] as $metabox){
					if(!empty($metabox['fields'])){
						foreach($metabox['fields'] as $field){
							$fields[]=$field;
						}
					}
				}
			}

			if(!empty($fields)){
				foreach($fields as $field){
					if(empty($field['id'])) continue;
					$field_id='wb-'.$field['id'];
					if(isset($_POST[$field_id]) and $value=WPBooking_Input::post($field_id)){
						switch($field['type']){
							case "service-type-checkbox":
                                delete_term_meta($term_id, $field['id']);
								if(is_array($value) and !empty($value)){
									foreach($value as $val){
                                        add_term_meta($term_id, $field['id'], $val);
									}
								}else{
                                    add_term_meta($term_id, $field['id'], $value);
								}
								break;
							default:
                                update_term_meta($term_id, $field['id'], $value);
								break;

						}
					}

				}
			}
		}


		function _show_metabox($taxonomy=FALSE)
		{

			if(!empty($this->metabox_array) and !empty($this->metabox_array[$taxonomy])){
				foreach($this->metabox_array[$taxonomy] as $metabox){
					$this->show_single_metabox($metabox,$taxonomy);
				}
			}
		}
		function _show_update_metabox($tag,$taxonomy)
		{
			if(!empty($this->metabox_array) and !empty($this->metabox_array[$taxonomy])){
				foreach($this->metabox_array[$taxonomy] as $metabox){
					$this->show_single_metabox($metabox,$tag,'edit_page');
				}
			}
		}

		/**
		 * Check if Taxonomy has meta field id
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param $field_id
		 * @param $taxonomy
		 * @return bool
		 */
		function check_field_exists($field_id,$taxonomy){

			$fields=array();
			if(!empty($this->metabox_array) and !empty($this->metabox_array[$taxonomy])){
				foreach($this->metabox_array[$taxonomy] as $metabox){
					if(!empty($metabox['fields'])){
						foreach($metabox['fields'] as $field){
							$fields[]=$field;
						}
					}
				}
			}
			if(!empty($fields)){
				foreach($fields as $field){
					if($field['id']==$field_id) return true;
				}
			}
			return FALSE;
		}
		/**
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param $metabox
		 * @param bool|FALSE $taxonomy
		 * @param string $layout_type |add_page|edit_page
		 */
		function show_single_metabox($metabox,$taxonomy=FALSE,$layout_type='add_page')
		{
			echo wpbooking_admin_load_view('taxonomy-metabox/metabox',array('metabox'=>$metabox,'taxonomy'=>$taxonomy,'layout_type'=>$layout_type));
		}

		function add_metabox($metabox){
			$metabox=wp_parse_args($metabox,array(
				'taxonomy'=>array(),
				'fields'=>array(),
				'id'=>FALSE
			));
			if(is_array($metabox['taxonomy']) and !empty($metabox['taxonomy'])){
				foreach($metabox['taxonomy'] as $taxonomy){
					if(taxonomy_exists($taxonomy)){
						$this->metabox_array[$taxonomy][]=$metabox;
					}
				}
			}
		}

		static function inst()
		{
			if(!self::$_inst){
				self::$_inst=new self();
			}
			return self::$_inst;
		}

	}

	WPBooking_Taxonomy_Metabox::inst();
}