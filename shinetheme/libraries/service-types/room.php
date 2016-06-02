<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 3/24/2016
 * Time: 4:23 PM
 */
if (!class_exists('WPBooking_Room_Service_Type') and class_exists('WPBooking_Abstract_Service_Type')) {
	class WPBooking_Room_Service_Type extends WPBooking_Abstract_Service_Type
	{
		static $_inst = FALSE;

		protected $type_id = 'room';

		function __construct()
		{
			$this->type_info = array(
				'label' => __("Room", 'wpbooking')
			);
			$this->settings = array(

				array(
					'id'    => 'title',
					'label' => __('General Options', 'wpbooking'),
					'type'  => 'title',
				), array(
					'id'    => 'archive_page',
					'label' => __('Archive Page', 'wpbooking'),
					'type'  => 'page-select',
				),
				array(
					'id'    => 'review',
					'label' => __('Review', 'wpbooking'),
					'type'  => 'multi-checkbox',
					'value' => array(
						array(
							'id'    => $this->type_id . '_enable_review',
							'label' => __('Enable Review', 'wpbooking')
						),
						array(
							'id'    => $this->type_id . '_allow_guest_review',
							'label' => __('Allow Guest Review', 'wpbooking')
						),
						array(
							'id'    => $this->type_id . '_review_without_booking',
							'label' => __('Review Without Booking', 'wpbooking')
						),
						array(
							'id'    => $this->type_id . '_show_rate_review_button',
							'label' => __('Show Rate (Help-full) button in each review?', 'wpbooking')
						),
						array(
							'id'    => $this->type_id . '_required_partner_approved_review',
							'label' => __('Review require Partner Approved?', 'wpbooking')
						),
					)
				),

				array(
					'id'    => 'review_stats',
					'label' => __("Review Stats", 'wpbooking'),
					'type'  => 'list-item',
					'value' => array()
				),
				array(
					'id'    => 'maximum_review',
					'label' => __("Maximum review per user", 'wpbooking'),
					'type'  => 'number',
					'std'   => 1
				),
				array(
					'type' => 'hr'
				),
				array(
					'id'    => 'title',
					'label' => __('Booking Options', 'wpbooking'),
					'type'  => 'title',
				),
				array(
					'id'        => 'order_form',
					'label'     => __('Order Form', 'wpbooking'),
					'type'      => 'post-select',
					'post_type' => array('wpbooking_form')
				),
				array(
					'id'    => 'confirm-settings',
					'label' => __('Instant Booking?', 'wpbooking'),
					'type'  => 'multi-checkbox',
					'value' => array(
						array(
							'id'    => 'service_type_' . $this->type_id . '_customer_confirm',
							'label' => __("Require customer confirm the booking by send them an email", 'wpbooking')
						),
						array(
							'id'    => 'service_type_' . $this->type_id . '_partner_confirm',
							'label' => __("Require partner confirm the booking", 'wpbooking')
						),
					)
				),
				array(
					'type' => 'hr'
				),
				array(
					'id'    => 'title',
					'label' => __('Layout', 'wpbooking'),
					'type'  => 'title',
				),
				array(
					'id'    => 'posts_per_page',
					'label' => __("Item per page", 'wpbooking'),
					'type'  => 'number',
					'std'   => 10
				),
				array(
					'id'    => "thumb_size",
					'label' => __("Thumb Size", 'travel-booking'),
					'type'  => 'image-size'
				),
				array(
					'id'    => "gallery_size",
					'label' => __("Gallery Size", 'travel-booking'),
					'type'  => 'image-size'
				),
			);

			parent::__construct();
			// add metabox
			add_filter('wpbooking_metabox_after_st_post_metabox_field_gallery',array($this,'_add_metabox'));

			add_filter('wpbooking_model_table_wpbooking_service_columns',array($this,'_add_meta_table_column'));


			add_filter('wpbooking_add_to_cart_validate_' . $this->type_id, array($this, '_add_to_cart_validate'), 10, 3);

			add_filter('wpbooking_cart_item_price_' . $this->type_id, array($this, '_change_cart_item_price'), 10, 2);
			add_filter('wpbooking_cart_item_pay_amount_' . $this->type_id, array($this, '_change_cart_item_price'), 10, 2);

			add_filter('wpbooking_order_item_total_' . $this->type_id, array($this, '_change_order_item_price'), 10, 2);

			// Add more params to cart items
			add_filter('wpbooking_cart_item_params_' . $this->type_id, array($this, '_change_cart_item_params'), 10, 2);

			// Change Search Query
			add_action('wpbooking_before_service_query_'.$this->type_id,array($this,'_add_change_query'));
			add_action('wpbooking_after_service_query_'.$this->type_id,array($this,'_remove_change_query'));

		}

		/**
		 * Add some extra columns for room
		 *
		 * @param $columns
		 * @return array
		 * @author dungdt
		 * @since 1.0
		 */
		function _add_meta_table_column($columns)
		{
			$columns['bed']=array('type'=>'INT');
			$columns['bedroom']=array('type'=>'VARCHAR','length'=>'20');
			$columns['bathroom']=array('type'=>'VARCHAR','length'=>'20');
			$columns['require_customer_confirm']=array('type'=>'VARCHAR','length'=>'10');
			$columns['require_partner_confirm']=array('type'=>'VARCHAR','length'=>'10');
			return $columns;
		}
		/**
		 * @author dungdt
		 * @since 1.0
		 */
		function _add_metabox($fields)
		{
			$new_fields=array(
				array(
					'type' => 'hr'
				),
				array(
					'label' => __('Number of Bedrooms', 'wpbooking'),
					'id' => 'bedroom',
					'type' => 'number'
				),
				array(
					'label' => __('Number of Bathrooms', 'wpbooking'),
					'id' => 'bathroom',
					'type' => 'number'
				),
				array(
					'label' => __('Number of Beds', 'wpbooking'),
					'id' => 'bed',
					'type' => 'number'
				),
				array(
					'label' => __('Check-in Time', 'wpbooking'),
					'id' => 'check_in_time',
					'type' => 'text',
					'class' => 'time-picker'
				),
				array(
					'label' => __('Check-out Time', 'wpbooking'),
					'id' => 'check_out_time',
					'type' => 'text',
					'class' => 'time-picker'
				),
				array(
					'type' => 'hr'
				),
			);
			$fields=array_merge($fields,$new_fields);

			return $fields;
		}
		function _add_change_query()
		{
			add_action('posts_fields',array($this,'_add_select_fields'));
		}
		function _add_select_fields($fields)
		{

			return $fields;
		}
		function _remove_change_query()
		{
			remove_action('posts_fields',array($this,'_add_select_fields'));
		}

		/**
		 * Calendar Validate Before Add To Cart
		 *
		 * @author dungdt
		 * @since 1.0
		 *
		 * @param $is_validated
		 * @param $service_type
		 * @param $post_id
		 * @return mixed
		 */
		function _add_to_cart_validate($is_validated, $service_type, $post_id)
		{
			$calendar = WPBooking_Calendar_Model::inst();

			$check_in = WPBooking_Input::post('check_in');
			$check_out = WPBooking_Input::post('check_out');

			if ($check_in) {
				$check_in_timestamp = strtotime($check_in);

				if ($check_out) {
					$check_out_timestamp = strtotime($check_out);
				} else {
					$check_out_timestamp = $check_in_timestamp;
				}

				$calendar_prices = $calendar->get_prices($post_id, $check_in_timestamp, $check_out_timestamp);

				if (!empty($calendar_prices)) {
					$check_in_temp = $check_in_timestamp;
					$unavailable = array();
					while ($check_in_temp <= $check_out_timestamp) {
						$match=FALSE;
						foreach ($calendar_prices as $key => $value) {
							if ($value['start'] == $check_in_temp) {
								$match = TRUE;
								if ($value['status'] == 'not_available') $unavailable[] = $check_in_temp;
							}
						}
						if(!$match){
							$unavailable[]=$check_in_temp;
						}

						$check_in_temp = strtotime('+1 day', $check_in_temp);
					}

					// If there are some day not available, return the message

					if (!empty($unavailable)) {
						$message = __("Sorry, This item is not available in: %s", 'wpbooking');
						$not_avai_string = FALSE;
						foreach ($unavailable as $k => $v) {
							$not_avai_string .= date(get_option('date_format'), $v) . ', ';
						}
						$not_avai_string = substr($not_avai_string, 0, -2);

						wpbooking_set_message(sprintf($message, $not_avai_string), 'error');
						$is_validated = FALSE;
					}
				} else {
					wpbooking_set_message(__("Sorry, This item is not available at the moment", 'wpbooking'), 'error');
					$is_validated = FALSE;
				}
			}

			return $is_validated;
		}

		/**
		 * Add Specific params to cart item before adding to cart
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param $cart_item
		 * @param bool|FALSE $post_id
		 * @return array
		 */
		function _change_cart_item_params($cart_item, $post_id = FALSE)
		{
			$cart_item = wp_parse_args($cart_item, array(
				'check_in_timestamp'  => '',
				'check_out_timestamp' => '',
			));

			$calendar = WPBooking_Calendar_Model::inst();
			$cart_item['price_type'] = get_post_meta($post_id, 'price_type', TRUE);

			if ($cart_item['check_in_timestamp'] and $cart_item['check_out_timestamp']) {

				$calendar_prices = $calendar->get_prices($post_id, $cart_item['check_in_timestamp'], $cart_item['check_out_timestamp']);
				$cart_item['calendar_price'] = $calendar_prices;

				$price =0;

				// Calculate Sub Total
				if (empty($calendar_prices)) {

					$price= $cart_item['base_price'];

					$night = wpbooking_timestamp_diff_day($cart_item['check_in_timestamp'], $cart_item['check_out_timestamp']);
					if (!$night) $night = 1;

					$price *= $night;
				} else {
					$check_in_temp = $cart_item['check_in_timestamp'];
					while ($check_in_temp <= $cart_item['check_out_timestamp']) {

						foreach ($calendar_prices as $key => $value) {
							if ($value['start'] == $check_in_temp) {
								if ($value['status'] == 'available') {
									$price += $value['price'];
								}
							}
						}

						$check_in_temp = strtotime('+1 day', $check_in_temp);
					}
				}
				// Remember Subtotal is total of cart item without Extra Price
				$cart_item['sub_total'] = $price;
			}

			return $cart_item;
		}

		/**
		 * Change Cart Item Price Hook Callback
		 *
		 * @author dungdt
		 * @since 1.0
		 *
		 * @param $price
		 * @return float
		 */
		function _change_cart_item_price($price, $cart_item)
		{
			$cart_item = wp_parse_args($cart_item, array(
				'extra_prices' => '',
			));
			// Calculate Extra Prices

			return $price;
		}


		function _change_order_item_price($price, $order_item)
		{
			$order_item = wp_parse_args($order_item, array(
				'raw_data'            => '',
			));

			// We need raw data because table order_item can not save all value from the cart_item data. Example price_type for room
			$raw_data = unserialize($order_item['raw_data']);

			if (!empty($raw_data) and is_array($raw_data)) {
				$raw_data = wp_parse_args($raw_data,
					array(
						'extra_prices'     => FALSE,
					));

			}

			return $price;
		}

		function _add_page_archive_search($args)
		{
			$id_page = $this->get_option('archive_page');
			$args = array($id_page => $this->type_id);

			return $args;
		}

		function _service_query_args($args)
		{
			$meta_query=array();
//			$meta_query[]=array(
//				'key'   => 'service_type',
//				'value' => $this->type_id,
//			);

			if ($location_id = WPBooking_Input::request('location_id')) {
				$args['tax_query'][] = array(
					'taxonomy' => 'wpbooking_location',
					'field'    => 'term_id',
					'terms'    => array($location_id),
					'operator' => 'IN',
				);
			}
			$tax = WPBooking_Input::request('taxonomy');
			if (!empty($tax) and is_array($tax)) {
				$taxonomy_operator = WPBooking_Input::request('taxonomy_operator');
				$tax_query = array();
				foreach ($tax as $key => $value) {
					if ($value) {
						if (!empty($taxonomy_operator[$key])) {
							$operator = $taxonomy_operator[$key];
						} else {
							$operator = "OR";
						}
						$value = explode(',', $value);
						if (!empty($value) and is_array($value)) {
							foreach ($value as $k => $v) {
								if (!empty($v)) {
									$ids[] = $v;
								}
							}
						}
						if (!empty($ids)) {
							$tax_query[] = array(
								'taxonomy' => $key,
								'terms'    => $ids,
								'operator' => $operator,
							);
						}
						$ids = array();
					}
				}



				if (!empty($tax_query)) {
					$args['tax_query'][] = $tax_query;
				}
			}

			if($posts_per_page=$this->get_option('posts_per_page'))
			{
				$args['posts_per_page']=$posts_per_page;
			}

			$args['meta_query']=$meta_query;

			return $args;
		}

		function _get_where_query($where)
		{

			$is_meta_table_working=WPBooking_Service_Model::inst()->is_ready();

			global $wpdb;
			if ($review_rate = WPBooking_Input::request('review_rate') and is_array(explode(',', $review_rate))) {
				$and = "HAVING ";
				foreach (explode(',', $review_rate) as $k => $v) {
					if ($k > 0) {
						$and .= " OR ";
					}

					$and .= $wpdb->prepare("  ( avg_rate>= %d and avg_rate<%d )",$v,$v+1);
				}
				if (!empty($and)) {
					$where .= " AND $wpdb->posts.ID IN
						(
							SELECT post_id FROM (
									SELECT
										{$wpdb->prefix}comments.comment_post_ID as post_id,avg({$wpdb->commentmeta}.meta_value) as avg_rate
									FROM
										wp_comments
									JOIN {$wpdb->prefix}commentmeta ON {$wpdb->prefix}comments.comment_ID = {$wpdb->prefix}commentmeta.comment_id and {$wpdb->commentmeta}.meta_key='wpbooking_review'
									WHERE comment_approved=1

									GROUP BY {$wpdb->prefix}comments.comment_post_ID {$and}
							)as ID

						)";

				}

			}


			// Beds
			if($beds=WPBooking_Input::get('bed') and $is_meta_table_working){
				$where.=$wpdb->prepare(' AND bed>=%d',$beds);
			}
			// Bedrooms
			if($bedrooms=WPBooking_Input::get('bedroom') and $is_meta_table_working){
				$where.=' AND bedroom>='.$bedrooms;
				$where.=$wpdb->prepare(' AND bedroom>=%d',$bedrooms);
			}
			// Bathrooms
			if($bathrooms=WPBooking_Input::get('bathroom') and $is_meta_table_working){

				$where.=$wpdb->prepare(' AND bathroom>=%d',$bathrooms);
			}
			// Required Customer Confirm
			if($bathroom=WPBooking_Input::get('customer_confirm') and $is_meta_table_working){
				if($this->get_option('customer_confirm')){
					$where.=' AND (require_customer_confirm is null or LENGTH(require_customer_confirm)=0)';
				}else{
					$where.=' AND require_customer_confirm=1';
				}
			}
			// Required Partner Confirm
			if($bathroom=WPBooking_Input::get('partner_confirm') and $is_meta_table_working){
				if($this->get_option('partner_confirm')){
					$where.=' AND (require_partner_confirm is null or LENGTH(require_partner_confirm)=0)';
				}else{
					$where.=' AND require_partner_confirm=1';
				}
			}


			return parent::_get_where_query($where);
		}

		static function inst()
		{
			if (!self::$_inst) {
				self::$_inst = new self();
			}

			return self::$_inst;
		}
	}

	WPBooking_Room_Service_Type::inst();
}

