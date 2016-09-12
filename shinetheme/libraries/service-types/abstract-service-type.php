<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 3/23/2016
 * Time: 2:35 PM
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('WPBooking_Abstract_Service_Type')) {
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

			add_filter('init', array($this, '_register_type'));
			add_filter('wpbooking_service_setting_sections', array($this, '_add_setting_section'));
			add_filter('wpbooking_review_stats', array($this, '_filter_get_review_stats'));
			add_filter('wpbooking_get_order_form_' . $this->type_id, array($this, '_get_order_form'));

			/*Change Search*/
			add_filter('wpbooking_add_page_archive_search', array($this, '_add_page_archive_search'));


			add_filter('wpbooking_get_order_form_id_' . $this->type_id, array($this, 'get_order_form_id'));

			/**
			 * Add to cart add Need Customer Confirm
			 * @see WPBooking_Order::add_to_cart();
			 */
			add_filter('wpbooking_service_need_customer_confirm', array($this, '_get_customer_confirm'), 10, 3);
			add_filter('wpbooking_service_need_partner_confirm', array($this, '_get_partner_confirm'), 10, 3);

			add_action('wpbooking_cart_item_information_' . $this->type_id, array($this, '_show_cart_item_information'),10,2);
			add_action('wpbooking_order_item_information_' . $this->type_id, array($this, '_show_order_item_information'),10,2);


			/**
			 * Change Related Query Search
			 * @since 1.0
			 * @author dungdt
			 */
			add_action('wpbooking_before_related_query_' . $this->type_id, array($this, '_add_related_query_hook'), 10, 2);


			/**
			 * Change Default Query Search
			 *
			 * @since 1.0
			 * @author dungdt
			 */
			// Check current service type
			if($service_type=WPBooking_Input::get('service_type') and  $service_type==$this->type_id){
				add_action('init', array($this, '_add_default_query_hook'));
			}


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
			$cart_item = wp_parse_args($cart_item, array(
				'need_customer_confirm' => '',
				'order_form'            => array(),
				'post_id'               => FALSE
			));

			$terms = wp_get_post_terms($cart_item['post_id'], 'wpbooking_room_type');
			if (!empty($terms) and !is_wp_error($terms)) {
				$output[] = '<div class="wpbooking-room-type">';
				$key = 0;
				foreach ($terms as $term) {
					$html = sprintf('<a href="%s">%s</a>', get_term_link($term, 'wpbooking_room_type'), $term->name);
					if ($key < count($term)-1) {
						$html .= ',';
					}
					$output[] = $html;
					$key++;
				}

				$output[] = '</div>';

				$output = apply_filters('wpbooking_room_show_room_type', $output);
				echo implode(' ', $output);
			}

			// Extra price and taxs
			$extra_price_html=array();

			// Show Order Form Field
			$order_form = $cart_item['order_form'];
			if ((!empty($order_form) and is_array($order_form)) or !empty($extra_price_html)) {
				echo '<div class="cart-item-order-form-fields-wrap">';
				echo '<span class="booking-detail-label">' . esc_html__('Booking Details:', 'wpbooking') . '</span>';
				echo "<ul class='cart-item-order-form-fields'>";
				foreach ($order_form as $key => $value) {

					$value = wp_parse_args($value, array(
						'data'       => '',
						'field_type' => ''
					));

					$value_html = WPBooking_Admin_Form_Build::inst()->get_form_field_data($value);

					if ($value_html) {
						printf("<li class='field-item %s'>
								<span class='field-title'>%s:</span>
								<span class='field-value'>%s</span>
							</li>", $key, $value['title'], $value_html);
					}

					do_action('wpbooking_form_field_to_html', $value);
					do_action('wpbooking_form_field_to_html_' . $value['field_type'], $value);
				}

				if($extra_price_html){
					echo implode("\r\n",$extra_price_html);
				}
				echo "</ul>";
				echo '<span class="show-more-less"><span class="more">' . esc_html__('More', 'wpbooking') . ' <i class="fa fa-angle-double-down"></i></span><span class="less">' . esc_html__('Less', 'wpbooking') . ' <i class="fa fa-angle-double-up"></i></span></span>';
				echo "</div>";
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
			$order_item = wp_parse_args($order_item, array(
				'need_customer_confirm' => '',
				'order_form'            => '',
				'payment_status'        => '',
				'status'                => ''
			));

			// Show Order Form Field
			$order_form_string = $order_item['order_form'];

			if ($order_form_string and $order_form = unserialize($order_form_string) and !empty($order_form) and is_array($order_form)) {

				echo '<div class="order-item-form-fields-wrap">';
				echo '<span class="booking-detail-label">' . esc_html__('Booking Details:', 'wpbooking') . '</span>';
				echo "<ul class='order-item-form-fields'>";
				foreach ($order_form as $key => $value) {

					$value = wp_parse_args($value, array(
						'data'       => '',
						'field_type' => ''
					));

					$value_html = WPBooking_Admin_Form_Build::inst()->get_form_field_data($value);

					if ($value_html) {
						printf("<li class='field-item %s'>
								<span class='field-title'>%s:</span>
								<span class='field-value'>%s</span>
							</li>", $key, $value['title'], $value_html);
					}

					do_action('wpbooking_form_field_to_html', $value);
					do_action('wpbooking_form_field_to_html_' . $value['field_type'], $value);
				}
				echo "</ul>";
				echo '<span class="show-more-less"><span class="more">' . esc_html__('More', 'wpbooking') . ' <i class="fa fa-angle-double-down"></i></span><span class="less">' . esc_html__('Less', 'wpbooking') . ' <i class="fa fa-angle-double-up"></i></span></span>';
				echo "</div>";
			}


		}

		/**
		 * Filter the Order Form HTML
		 */
		function _get_order_form()
		{
			$form_id = $this->get_option('order_form');
			$post = get_post($form_id);
			if ($post) {
				return apply_filters('the_content', $post->post_content);
			}

		}

		/**
		 * Get the Order Form ID in the Settings
		 * @return bool|mixed|void
		 */
		function get_order_form_id()
		{
			return $form_id = $this->get_option('order_form');
		}

		/**
		 * Filter Function for Check Service Type is require Customer Confirm the Booking (Confirm by send the email)
		 * @param $need
		 * @param $post_id
		 * @param $service_type
		 * @return bool|mixed|void
		 */
		function _get_customer_confirm($need, $post_id, $service_type)
		{
			if ($this->type_id == $service_type) {
				$need = $this->get_option('customer_confirm');

				if ($meta = get_post_meta($post_id, 'require_customer_confirm', TRUE)) $need = $meta;
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
		function _get_partner_confirm($need, $post_id, $service_type)
		{
			if ($this->type_id == $service_type) {
				$need = $this->get_option('partner_confirm');

				if ($meta = get_post_meta($post_id, 'require_partner_confirm', TRUE)) $need = $meta;
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

		function _add_setting_section($sections = array())
		{
			$settings = $this->get_settings_fields();
			if (!empty($settings)) {
				foreach ($settings as $key => $value) {
					if (!empty($value['id']))
						$settings[$key]['id'] = 'service_type_' . $this->type_id . '_' . $value['id'];
				}
			}
			$sections['service_type_' . $this->type_id] = array(
				'id'     => 'service_type_' . $this->type_id,
				'label'  => $this->get_info('label'),
				'fields' => $settings
			);

			return $sections;
		}

		function get_settings_fields()
		{

			return apply_filters('wpbooking_service_type_' . $this->type_id . '_settings_fields', $this->settings);
		}

		function get_info($key = FALSE)
		{
			$info = apply_filters('wpbooking_service_type_info', $this->type_info);
			$info = apply_filters('wpbooking_service_type_' . $this->type_id . '_info', $info);

			if ($key) {

				$data = isset($info[$key]) ? $info[$key] : FALSE;

				$data = apply_filters('wpbooking_service_type_info_' . $key, $data);
				$data = apply_filters('wpbooking_service_type_' . $this->type_id . '_info_' . $key, $data);

				return $data;
			}

			return $info;
		}

		function get_option($key, $default = FALSE)
		{
			return wpbooking_get_option('service_type_' . $this->type_id . '_' . $key, $default);
		}

		/**
		 * Get All Extra Services From Settings Page
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @return mixed|void
		 */
		function get_extra_services()
		{
			$terms = WPBooking_Taxonomy_Meta_Model::inst()->select('distinct term_id')->where(array(
				'meta_key'   => 'service_type',
				'meta_value' => $this->type_id
			))->get()->result();

			$term_ids=array();
			if(!empty($terms) and is_array($terms)){
				foreach($terms as $term){
					$term_ids[]=$term['term_id'];
				}
			}
			if(empty($term_ids)) return array();

			$terms=get_terms('wpbooking_extra_service',array('hide_empty'=>FALSE,'include'=>$term_ids));
			$extra_services=array();
			if(!empty($terms) and !is_wp_error($terms)){
				foreach($terms as $key=>$value){
					$extra_services[$value->term_id]=array(
						'title'=>$value->name
					);
				}
			}

			return $extra_services;
		}

        /**
         * Hook Callback Init to register Type
         *
         * @since 1.0
         * @author dungdt
         */
		function _register_type()
		{
		    WPBooking_Service_Controller::inst()->register_type($this->type_id,$this);

		}

		function _add_page_archive_search($args)
		{
			return $args;
		}

		function _service_query_args($args)
		{
			return $args;
		}

		/**
		 * Add Hook for Related Query
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param $post_id
		 * @param $service_type
		 */
		function _add_related_query_hook($post_id, $service_type)
		{
			global $wpdb;

			$rate = WPBooking_Comment_Model::inst()->get_avg_review($post_id);
			$table = WPBooking_Service_Model::inst()->get_table_name(FALSE);
			$table_prefix = WPBooking_Service_Model::inst()->get_table_name();

			$injection = WPBooking_Query_Inject::inst();

			$injection->join($table, $table_prefix . '.post_id=' . $wpdb->posts . '.ID');
			$injection->groupby($wpdb->posts . '.ID');
			$injection->where($table_prefix . '.enable_property', 'on');

			if ($rate) {
				if (is_float($rate)) {
					// Check if Avg is Decimal: example 4.3
					$rate = (float)$rate;
					$min = floor($rate);
					$max = ceil($rate);
				} else {
					// If Avg is Integerl: Example 4 -> we will get 4->5 star
					$min = $rate;
					$max = $rate + 1;
				}
				global $wpdb;

				$injection = WPBooking_Query_Inject::inst();
				$injection->select('avg(' . $wpdb->commentmeta . '.meta_value) as avg_rate')
					->groupby($wpdb->posts . '.ID')
					->join('comments', $wpdb->prefix . 'comments.comment_post_ID=' . $wpdb->posts . '.ID')
					->join('commentmeta', $wpdb->prefix . 'commentmeta.comment_id=' . $wpdb->prefix . 'comments.comment_ID and ' . $wpdb->commentmeta . ".meta_key='wpbooking_review'")
					->where('comment_approved', 1)
					->having("avg_rate>=" . $min)
					->having("avg_rate<=" . $max);

			}

			// Locations
			if ($location_id = get_post_meta($post_id, 'location_id', TRUE)) {

				$childs=get_term_children($location_id,'wpbooking_location');

				$ids=array($location_id);

				if(!empty($childs) and !is_wp_error($childs)){
					$ids=array_merge($ids,$childs);
				}
				if(!empty($ids)){
					$injection->where_in($table_prefix . '.location_id', $ids);
				}

			}


			// Price
			if ($price = get_post_meta($post_id, 'price', TRUE)) {
				$injection->where($table_prefix . '.price', $price);
			}


		}

		/**
		 * Add Query Hook in Archive Page
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 */
		function _add_default_query_hook()
		{

			global $wpdb;

			$table = WPBooking_Service_Model::inst()->get_table_name(FALSE);
			$table_prefix = WPBooking_Service_Model::inst()->get_table_name();

			$injection = WPBooking_Query_Inject::inst();

			$injection->join($table, $table_prefix . '.post_id=' . $wpdb->posts . '.ID');
			$injection->groupby($wpdb->posts . '.ID');

			// Service Type
			if($service_type=WPBooking_Input::get('service_type')){
				$injection->where($table_prefix.'.service_type',$service_type);
			}

			// Price
			if ($price = WPBooking_Input::get('price')) {
				$array = explode(';', $price);

				if (!empty($array[0])) {
					$injection->where('price>=', $array[0]);
				}
				if (!empty($array[1])) {
					$injection->where('price<=', $array[1]);
				}
			}

			// Enable
			$injection->where($table_prefix . '.enable_property', 'on');

			// Location
			if ($location_id = WPBooking_Input::get('location_id')){
				$childs=get_term_children($location_id,'wpbooking_location');

				$ids=array($location_id);

				if(!empty($childs) and !is_wp_error($childs)){
					foreach($childs as $key=>$value){
						$ids[]=$value;
					}
				}
				if(!empty($ids)){
					$injection->where_in($table_prefix . '.location_id', $ids);
				}

			}


		}

	}
}