<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 3/23/2016
 * Time: 2:35 PM
 */
if(!class_exists('WPBooking_Abstract_Service_Type'))
{
	abstract class WPBooking_Abstract_Service_Type
	{
		protected $type_id = FALSE;
		protected $type_info = array();
		protected $settings = array();

		function __construct()
		{
			if (!$this->type_id) return FALSE;
			$this->type_info = wp_parse_args($this->type_info, array(
				'label'       => '',
				'description' => ''
			));

			add_filter('wpbooking_service_types', array($this, '_register_type'));
			add_filter('wpbooking_service_setting_sections', array($this, '_add_setting_section'));
			add_filter('wpbooking_review_stats', array($this, '_filter_get_review_stats'));
			add_filter('wpbooking_get_order_form_'.$this->type_id, array($this, '_get_order_form'));

            /*Change Search*/
			add_filter('wpbooking_add_page_archive_search', array($this, '_add_page_archive_search'));
			add_filter('wpbooking_service_query_args_'.$this->type_id, array($this, '_service_query_args'));
			add_action('wpbooking_before_service_query_'.$this->type_id, array($this, '_before_service_query'));
			add_action('wpbooking_after_service_query_'.$this->type_id, array($this, '_after_service_query_'));


			add_filter('wpbooking_get_order_form_id_'.$this->type_id, array($this, 'get_order_form_id'));



			/**
			 * Add to cart add Need Customer Confirm
			 * @see WPBooking_Order::add_to_cart();
			 */
			add_filter('wpbooking_service_need_customer_confirm',array($this,'_get_customer_confirm'),10,3);
			add_filter('wpbooking_service_need_partner_confirm',array($this,'_get_partner_confirm'),10,3);

			add_action('wpbooking_cart_item_information_'.$this->type_id,array($this,'_show_cart_item_information'));
			add_action('wpbooking_review_order_item_information_'.$this->type_id,array($this,'_show_cart_item_information'));
			add_action('wpbooking_order_item_information_'.$this->type_id,array($this,'_show_order_item_information'));

		}

		/**
		 * Show Cart Item Information Based on Service Type ID
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param $cart_item
		 */
		function _show_cart_item_information($cart_item)
		{
			$cart_item=wp_parse_args($cart_item,array(
				'need_customer_confirm'=>'',
				'order_form'=>array(),
				'need_partner_confirm'=>FALSE
			));

			// Show Order Form Field
			$order_form=$cart_item['order_form'];
			if(!empty($order_form) and is_array($order_form))
			{
				echo "<ul class='cart-item-order-form-fields'>";
				foreach($order_form as $key=>$value){

					$value=wp_parse_args($value,array(
						'data'=>'',
						'field_type'=>''
					));

					$value_html= WPBooking_Admin_Form_Build::inst()->get_form_field_data($value);

					if($value_html){
						printf("<li class='field-item %s'>
								<span class='field-title'>%s:</span>
								<span class='field-value'>%s</span>
							</li>",$key,$value['title'],$value_html);
					}

					do_action('wpbooking_form_field_to_html',$value);
					do_action('wpbooking_form_field_to_html_'.$value['field_type'],$value);
				}
				echo "</ul>";
			}

			// Show Need Confirm Notification
			if($cart_item['need_customer_confirm'])
			{
				echo "<div class='label label-warning'>".__("Need Confirmation ",'wpbooking')."</div>";
			}
			if($cart_item['need_partner_confirm'])
			{
				echo "<div class='label label-warning'>".__("Need Approval",'wpbooking')."</div>";
			}
		}

		/**
		 * Show Order Item Information Based on Service Type ID
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param $order_item
		 */
		function _show_order_item_information($order_item)
		{
			$order_item=wp_parse_args($order_item,array(
				'need_customer_confirm'=>'',
				'order_form'=>'',
				'payment_status'=>'',
				'status'=>''
			));

			// Show Order Form Field
			$order_form_string=$order_item['order_form'];

			if($order_form_string and $order_form=unserialize($order_form_string) and  !empty($order_form) and is_array($order_form))
			{
				echo "<ul class='cart-item-order-form-fields'>";
				foreach($order_form as $key=>$value){

					$value=wp_parse_args($value,array(
						'data'=>'',
						'field_type'=>''
					));
					$value_html= WPBooking_Admin_Form_Build::inst()->get_form_field_data($value);

					if($value_html){
						printf("<li class='field-item %s'>
								<span class='field-title'>%s:</span>
								<span class='field-value'>%s</span>
							</li>",$key,$value['title'],$value_html);
					}

					do_action('wpbooking_form_field_to_html',$value);
					do_action('wpbooking_form_field_to_html_'.$value['field_type'],$value);
				}
				echo "</ul>";
			}

			// Show Need Confirm Notification
			if($order_item['need_customer_confirm'] or $order_item['need_customer_confirm'])
			{
				echo "<div class='label label-warning'>".__("Need Confirmation",'wpbooking')."</div>";
			}else{
				// Show Payment Status
				switch($order_item['status'])
				{
					case "completed":
						echo "<div class='label label-success'>".__("Completed",'wpbooking')."</div>";
						break;
					case "on-hold":
						echo "<div class='label label-warning'>".__("On-hold",'wpbooking')."</div>";
						break;

				}
			}

		}

		/**
		 * Filter the Order Form HTML
		 */
		function _get_order_form()
		{
			$form_id= $this->get_option('order_form');
			$post=get_post($form_id);
			if($post){
				return apply_filters('the_content',$post->post_content);
			}

		}

		/**
		 * Get the Order Form ID in the Settings
		 * @return bool|mixed|void
		 */
		function get_order_form_id()
		{
			return $form_id= $this->get_option('order_form');
		}

		/**
		 * Filter Function for Check Service Type is require Customer Confirm the Booking (Confirm by send the email)
		 * @param $need
		 * @param $post_id
		 * @param $service_type
		 * @return bool|mixed|void
		 */
		function _get_customer_confirm($need,$post_id,$service_type)
		{
			if($this->type_id==$service_type){
				$need=$this->get_option('customer_confirm');

				if($meta=get_post_meta($post_id,'require_customer_confirm',TRUE)) $need=$meta;
			}

			return $need;
		}

		/**
		 * Filter Function for Check Service Type is require Partner Confirm the Booking (Confirm by send the email)
		 * @param $need
		 * @param $post_id
		 * @param $service_type
		 * @return bool|mixed|void
		 */
		function _get_partner_confirm($need,$post_id,$service_type){
			if($this->type_id==$service_type){
				$need=$this->get_option('partner_confirm');

				if($meta=get_post_meta($post_id,'require_partner_confirm',TRUE)) $need=$meta;
			}

			return $need;
		}

		function _filter_get_review_stats($stats)
		{
			$post_id = get_the_ID();

			if (get_post_meta($post_id, 'service_type', TRUE) != $this->type_id) return $stats;

			$stats = $this->get_review_stats();
			if (!empty($stats)) return $stats;

			return $stats;
		}

		/**
		 * Get All Review Stats from the Settings
		 *
		 * @since 1.0
		 * @return bool|mixed|void
		 *
		 */
		function get_review_stats()
		{
			return $this->get_option('review_stats', array());
		}

		function _add_setting_section($sections=array())
		{
			$settings=$this->get_settings_fields();
			if(!empty($settings)){
				foreach($settings as $key=>$value){
					if(!empty($value['id']))
					$settings[$key]['id']='service_type_'.$this->type_id.'_'.$value['id'];
				}
			}
			$sections['service_type_'.$this->type_id]=array(
				'id'     => 'service_type_'.$this->type_id,
				'label'  => $this->get_info('label'),
				'fields' => $settings
			);
			return $sections;
		}

		function get_settings_fields()
		{

			return apply_filters('wpbooking_service_type_'.$this->type_id.'_settings_fields',$this->settings);
		}

		function get_info($key=FALSE)
		{
			$info= apply_filters('wpbooking_service_type_info',$this->type_info);
			$info= apply_filters('wpbooking_service_type_'.$this->type_id.'_info',$info);

			if($key){

				$data= isset($info[$key])?$info[$key]:FALSE;

				$data= apply_filters('wpbooking_service_type_info_'.$key,$data);
				$data= apply_filters('wpbooking_service_type_'.$this->type_id.'_info_'.$key,$data);
				return $data;
			}

			return $info;
		}
		function get_option($key,$default=FALSE)
		{
			return wpbooking_get_option('service_type_'.$this->type_id.'_'.$key,$default);
		}

		function _register_type($service_types=array())
		{
			$service_types[$this->type_id]=array(
				'label'=>$this->get_info('label'),
				'object'=>$this
			);

			return $service_types;
		}
        function _add_page_archive_search($args)
        {
            return $args;
        }
        function _service_query_args($args){
            return $args;
        }
        function _before_service_query($args){
            add_filter('posts_where', array($this, '_get_where_query'));
            add_filter('posts_join', array($this, '_get_join_query'));
            return $args;
        }
        function _after_service_query_($args){
            remove_filter('posts_where', array($this, '_get_where_query'));
			remove_filter('posts_join', array($this, '_get_join_query'));
            return $args;
        }
		function _get_join_query($join){
			global $wpdb;
			$table=WPBooking_Service_Model::inst()->get_table_name();
			$join.=' JOIN '.$table.' on '.$table.'.post_id='.$wpdb->posts.'.ID ';
			return $join;
		}

        function _get_where_query($where){
			global $wpdb;

			//Room Price Filter
			if($price=WPBooking_Input::get('price'))
			{
				$array=explode(',',$price);

				if(!empty($array[0]))
				{
					$where.=$wpdb->prepare('AND price>=%d',$array[0]);
				}
				if(!empty($array[1]))
				{
					$where.=$wpdb->prepare('AND price<=%d',$array[1]);
				}
			}


            return $where;
        }
	}
}