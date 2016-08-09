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
							'id'    => 'service_type_'.$this->type_id . '_enable_review',
							'label' => __('Enable Review', 'wpbooking')
						),
						array(
							'id'    => 'service_type_'.$this->type_id . '_allow_guest_review',
							'label' => __('Allow Guest Review', 'wpbooking')
						),
						array(
							'id'    => 'service_type_'.$this->type_id . '_review_without_booking',
							'label' => __('Review Without Booking', 'wpbooking')
						),
						array(
							'id'    => 'service_type_'.$this->type_id . '_show_rate_review_button',
							'label' => __('Show Rate (Help-full) button in each review?', 'wpbooking')
						),
						array(
							'id'    => 'service_type_'.$this->type_id . '_allowed_review_on_own_listing',
							'label' => __('User can write review on their own listing?', 'wpbooking')
						),
						array(
							'id'    => 'service_type_'.$this->type_id . '_allowed_vote_for_own_review',
							'label' => __('User can vote for their own review?', 'wpbooking')
						),
//						array(
//							'id'    => 'service_type_'.$this->type_id . '_required_partner_approved_review',
//							'label' => __('Review require Partner Approved?', 'wpbooking')
//						),
					)
				),
				array(
					'id'    => 'extra_services',
					'label' => esc_html__('Extra Services', 'wpbooking'),
					'type'  => 'list-item',
					'value' => array()
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

			add_action('init',array($this,'_register_taxonomy'));

			// add metabox
			//add_filter('wpbooking_metabox_after_st_post_metabox_field_end_address_accordion',array($this,'_add_metabox'));

			add_filter('wpbooking_model_table_wpbooking_service_columns', array($this, '_add_meta_table_column'));


			add_filter('wpbooking_add_to_cart_validate_' . $this->type_id, array($this, '_add_to_cart_validate'), 10, 3);

			add_filter('wpbooking_cart_item_price_' . $this->type_id, array($this, '_change_cart_item_price'), 10, 2);
			add_filter('wpbooking_cart_item_pay_amount_' . $this->type_id, array($this, '_change_cart_item_price'), 10, 2);

			add_filter('wpbooking_order_item_total_' . $this->type_id, array($this, '_change_order_item_price'), 10, 2);

			// Add more params to cart items
			add_filter('wpbooking_cart_item_params_' . $this->type_id, array($this, '_change_cart_item_params'), 10, 2);

			add_filter('comments_open',array($this,'_comments_open'),10,2);
			add_action('pre_comment_on_post',array($this,'_validate_comment'));
			add_filter('pre_comment_approved',array($this,'_pre_comment_approved'));

			// wpbooking_archive_posts_per_page

			add_filter('wpbooking_archive_posts_per_page',array($this,'_change_posts_per_page'),10,2);

			//wpbooking_archive_loop_image_size
			add_filter('wpbooking_archive_loop_image_size',array($this,'_apply_thumb_size'),10,3);
			add_filter('wpbooking_single_loop_image_size',array($this,'_apply_gallery_size'),10,3);

			// Archive Room Type
			add_action('wpbooking_after_service_address_rate',array($this,'_show_room_type'),10,3);

			//add_action('after_setup_theme',array($this,'_add_image_size'));

			/**
			 * Enable Vote For Review
			 */
			add_filter('wpbooking_enable_vote_for_review_'.$this->type_id,array($this,'_enable_vote_for_review'),10,3);


		}

		function _register_taxonomy()
		{
			$labels = array(
				'name'              => _x('Room Type', 'taxonomy general name', 'wpbooking'),
				'singular_name'     => _x('Room Type', 'taxonomy singular name', 'wpbooking'),
				'search_items'      => __('Search Room Type', 'wpbooking'),
				'all_items'         => __('All Room Type', 'wpbooking'),
				'parent_item'       => __('Parent Room Type', 'wpbooking'),
				'parent_item_colon' => __('Parent Room Type:', 'wpbooking'),
				'edit_item'         => __('Edit Room Type', 'wpbooking'),
				'update_item'       => __('Update Room Type', 'wpbooking'),
				'add_new_item'      => __('Add New Room Type', 'wpbooking'),
				'new_item_name'     => __('New Room Type Name', 'wpbooking'),
				'menu_name'         => __('Room Type', 'wpbooking'),
			);

			$args = array(
				'hierarchical'      => TRUE,
				'labels'            => $labels,
				'show_ui'           => TRUE,
				'show_admin_column' => TRUE,
				'query_var'         => TRUE,
				'rewrite'           => array('slug' => 'room-type'),
			);
			$args = apply_filters('wpbooking_register_room_type_taxonomy', $args);

			register_taxonomy('wpbooking_room_type', array('wpbooking_service'), $args);

		}
		function _show_room_type($post_id,$service_type,$service_object)
		{
			if($this->type_id==$service_type){
				$terms=wp_get_post_terms($post_id,'wpbooking_room_type');
				if(!empty($terms) and !is_wp_error($terms)){
					$output[]='<div class="wpbooking-room-type">';
					foreach($terms as $term){
						$output[]=sprintf('<a href="%s">%s</a>',get_term_link($term,'wpbooking_room_type'),$term->name);
					}
					$output[]='</div>';

					$output=apply_filters('wpbooking_room_show_room_type',$output);
					echo implode(' ',$output);
				}
			}
		}
		function _add_image_size()
		{
			$thumb=$this->thumb_size('150,150,off');
			$thumb=explode(',',$thumb);
			if(count($thumb)==3){
				if($thumb[2]=='off') $thumb[2]=FALSE;

				add_image_size('wpbooking_room_thumb_size',$thumb[0],$thumb[1],$thumb[2]=FALSE);
			}

			$thumb=$this->gallery_size('800,600,off');
			$thumb=explode(',',$thumb);
			if(count($thumb)==3){
				if($thumb[2]=='off') $thumb[2]=FALSE;
				add_image_size('wpbooking_room_gallery_size',$thumb[0],$thumb[1]);
			}

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
			$columns['bed'] = array('type' => 'INT');
			$columns['bedroom'] = array('type' => 'VARCHAR', 'length' => '20');
			$columns['bathroom'] = array('type' => 'VARCHAR', 'length' => '20');
//			$columns['require_customer_confirm'] = array('type' => 'VARCHAR', 'length' => '10');
//			$columns['require_partner_confirm'] = array('type' => 'VARCHAR', 'length' => '10');

			return $columns;
		}

		/**
		 * @author dungdt
		 * @since 1.0
		 */
		function _add_metabox($fields)
		{
			$new_fields = array(

				array(
					'label' => __('Space of Room', 'wpbooking'),
					'type'  => 'accordion-start'
				),
				array(
					'label' => __('Bedrooms', 'wpbooking'),
					'id'    => 'bedroom',
					'type'  => 'number',
					'width' => 'two'
				),
				array(
					'label' => __('Bathrooms', 'wpbooking'),
					'id'    => 'bathroom',
					'type'  => 'number',
					'width' => 'two'
				),
				array(
					'label' => __('Beds', 'wpbooking'),
					'id'    => 'bed',
					'type'  => 'number',
					'width' => 'two'
				),
				array(
					'label' => __('Check-in Time', 'wpbooking'),
					'id'    => 'check_in_time',
					'type'  => 'text',
					'class' => 'time-picker',
					'width' => 'two'
				),
				array(
					'label' => __('Check-out Time', 'wpbooking'),
					'id'    => 'check_out_time',
					'type'  => 'text',
					'class' => 'time-picker',
					'width' => 'two'
				),

				array(
					'label' => __('No. Adult', 'wpbooking'),
					'id'    => 'number_adult',
					'type'  => 'number',
					'width' => 'two'
				),
				array(
					'label' => __('No. Children', 'wpbooking'),
					'id'    => 'number_children',
					'type'  => 'number',
					'width' => 'two'
				),
				array(
					'type' => 'accordion-end'
				),
			);
			$fields = array_merge($fields, $new_fields);

			return $fields;
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

			$check_in = WPBooking_Input::post('check_in');
			$check_out = WPBooking_Input::post('check_out');

			if ($check_in) {
				$check_in_timestamp = strtotime($check_in);

				if ($check_out) {
					$check_out_timestamp = strtotime($check_out);
				} else {
					$check_out_timestamp = $check_in_timestamp;
				}
				$service=new WB_Service($post_id);
				$res=$service->check_availability($check_in_timestamp,$check_out_timestamp);
				if(!$res['status']){
					$is_validated=FALSE;

					// If there are some day not available, return the message
					if(!empty($res['can_not_check_in'])){
						wpbooking_set_message(sprintf("You can not check-in at: %s", 'wpbooking'),date(get_option('date_format'),$check_in_timestamp));
					}
					if(!empty($res['can_not_check_out'])){
						wpbooking_set_message(sprintf("You can not check-out at: %s", 'wpbooking'),date(get_option('date_format'),$check_out_timestamp));
					}
					if (!empty($res['unavailable_dates'])) {
						$message = esc_html__("Those dates are not available: %s", 'wpbooking');
						$not_avai_string = FALSE;
						foreach ($res['unavailable_dates'] as $k => $v) {
							$not_avai_string .= date(get_option('date_format'), $v) . ', ';
						}
						$not_avai_string = substr($not_avai_string, 0, -2);

						wpbooking_set_message(sprintf($message, $not_avai_string), 'error');
					}
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

				$price = 0;

				// Calculate Sub Total
				if (empty($calendar_prices)) {

					$price = $cart_item['base_price'];

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
				'raw_data' => '',
			));

			// We need raw data because table order_item can not save all value from the cart_item data. Example price_type for room
			$raw_data = unserialize($order_item['raw_data']);

			if (!empty($raw_data) and is_array($raw_data)) {
				$raw_data = wp_parse_args($raw_data,
					array(
						'extra_prices' => FALSE,
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

		function _add_default_query_hook()
		{
			$injection=WPBooking_Query_Inject::inst();
			$tax_query=$injection->get_arg('tax_query');

			if ($location_id = WPBooking_Input::request('location_id')) {
				$tax_query[] = array(
					'taxonomy' => 'wpbooking_location',
					'field'    => 'term_id',
					'terms'    => array($location_id),
					'operator' => 'IN',
				);
			}

			// Taxonomy
			$tax = WPBooking_Input::request('taxonomy');
			if (!empty($tax) and is_array($tax)) {
				$taxonomy_operator = WPBooking_Input::request('taxonomy_operator');
				$tax_query_child = array();
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

				if(!empty($tax_query_child))
				$tax_query[]=$tax_query_child;
			}

			// Posts Per page
			if ($posts_per_page = $this->get_option('posts_per_page')) {
				$injection->add_arg('posts_per_page',$posts_per_page);
			}

			// Order By
			if(WPBooking_Input::request('wb_sort_by')){
				switch(WPBooking_Input::request('wb_sort_by')){
					case "price_asc":
						$injection->add_arg('orderby','price');
						$injection->add_arg('order','asc');
						break;
					case "price_desc":
						$injection->add_arg('orderby','price');
						$injection->add_arg('order','desc');
						break;
					case "date_asc":
						$injection->add_arg('orderby','date');
						$injection->add_arg('order','asc');
						break;
					case "date_desc":
						$injection->add_arg('orderby','date');
						$injection->add_arg('order','desc');
						break;
				}
			}


			// Beds
			if ($beds = WPBooking_Input::get('bed')) {
				$injection->where('bed>=', $beds);
			}
			// Bedrooms
			if ($bedrooms = WPBooking_Input::get('bedroom')) {
				$injection->where('bedroom>=', $bedrooms);
			}
			// Bathrooms
			if ($bathrooms = WPBooking_Input::get('bathroom')) {
				$injection->where('bathroom>=', $bedrooms);
			}

			// Review
			global $wpdb;
			if ($review_rate = WPBooking_Input::request('review_rate') and is_array(explode(',', $review_rate))) {

				$injection->select('avg('.$wpdb->commentmeta.'.meta_value) as avg_rate')
					->join('comments',$wpdb->prefix.'comments.comment_post_ID='.$wpdb->posts.'.ID and  '.$wpdb->comments.'.comment_approved=1','LEFT')
					->join('commentmeta',$wpdb->prefix.'commentmeta.comment_id='.$wpdb->prefix.'comments.comment_ID and '.$wpdb->commentmeta.".meta_key='wpbooking_review'",'LEFT');

				foreach (explode(',', $review_rate) as $k => $v) {
					$injection->having("avg_rate>=".$v)
						->having("avg_rate<=".($v+1));
				}

			}
			if(!empty($tax_query))
			$injection->add_arg('tax_query',$tax_query);

			parent::_add_default_query_hook();
		}


		/**
		 * Validate Before Post Comment
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param $comment_post_ID
		 */
		function _validate_comment($comment_post_ID)
		{
			$service_type=get_post_meta($comment_post_ID,'service_type',true);

			if($service_type==$this->type_id){
				// Validate start
				$is_validated=true;

				if(!$this->get_option('enable_review'))
				{
					wpbooking_set_message(esc_html__('This service is not allowed to write review','wpbooking'));
					$is_validated=FALSE;
				}

				if(!$this->allow_guest_review() and !is_user_logged_in()){
					wpbooking_set_message(esc_html__('You need login to write review','wpbooking'));
					$is_validated=FALSE;
				}

				// room_maximum_review
				if($max=$this->room_maximum_review() and is_user_logged_in()){
					$comment=WPBooking_Comment_Model::inst();
					$count=$comment->select('count(comment_ID) as total')->where(array('comment_post_ID'=>$comment_post_ID,'user_id'=>get_current_user_id()))->get()->row();
					if(!empty($count['total']) and $count['total']>=$max){

						wpbooking_set_message(sprintf(esc_html__('Maximum number of review you can post is %d','wpbooking'),$max));
						$is_validated=FALSE;
					}
				}

				// review_without_booking
				if(!$this->review_without_booking() and is_user_logged_in()){
					$order_item=WPBooking_Order_Model::inst();
					$count=$order_item->select('count(id) as total')->where(array('post_id'=>$comment_post_ID,'customer_id'=>get_current_user_id()))->get()->row();
					if(empty($count['total']) or $count['total']<1){

						wpbooking_set_message(esc_html__('This Room required booking before writing review','wpbooking'));
						$is_validated=FALSE;
					}
				}

				$is_validated=apply_filters('wpbooking_validate_before_post_comment_service_type_room',$is_validated,$comment_post_ID);

				if(!$is_validated){
					wp_redirect(get_permalink($comment_post_ID));
					die;
				}
			}
		}

		/**
		 * Change Default of Comment Approved
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param $approved
		 * @param $commentdata
		 * @return bool
		 */
		function _pre_comment_approved($approved, $commentdata)
		{
			if(!empty($commentdata['comment_post_ID'])){
				$service_type=get_post_meta($commentdata['comment_post_ID'],'service_type',true);

				if($service_type==$this->type_id){
					if($this->required_partner_approved_review()){
						$approved=0;
					}
				}

			}
			return $approved;
		}

		/**
		 * Hook Filter To Show Review Form for Room
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param $open
		 * @param $post_id
		 * @return bool|mixed|string|void
		 */
		function _comments_open($open,$post_id)
		{
			$service_type=get_post_meta($post_id,'service_type',true);
			if($service_type==$this->type_id){
				$open=$this->get_option('enable_review');

				if(!$this->allow_guest_review() and !is_user_logged_in()){
					wpbooking_set_message(esc_html__('You need login to write review','wpbooking'));
					$open=FALSE;
				}

				// room_maximum_review
				if($max=$this->room_maximum_review() and is_user_logged_in()){
					$comment=WPBooking_Comment_Model::inst();
					$count=$comment->select('count(comment_ID) as total')->where(array('comment_post_ID'=>$post_id,'user_id'=>get_current_user_id()))->get()->row();
					if(!empty($count['total']) and $count['total']>=$max){

						wpbooking_set_message(sprintf(esc_html__('Maximum number of review you can post is %d','wpbooking'),$max));
						$open=FALSE;
					}
				}

				// review_without_booking
				if(!$this->review_without_booking() and is_user_logged_in()){
					$order_item=WPBooking_Order_Model::inst();
					$count=$order_item->select('count(id) as total')->where(array('post_id'=>$post_id,'customer_id'=>get_current_user_id()))->get()->row();
					if(empty($count['total']) or $count['total']<1){

						wpbooking_set_message(esc_html__('This Room required booking before writing review','wpbooking'));
						$open=FALSE;
					}
				}

				// Review in their own posts
				if(!$this->get_option('allowed_review_on_own_listing') and is_user_logged_in()){
					$author_id = get_post_field('post_author', $post_id);
					if($author_id==get_current_user_id()){
						$open=FALSE;
					}
				}

				if($open) $open='open';
			}


			return $open;
		}

		/**
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @return bool|mixed|void
		 */
		function allow_guest_review()
		{
			return $this->get_option('allow_guest_review',FALSE);
		}


		/**
		 * Enable vote for review
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param $enable bool
		 * @param $post_id int
		 * @param @service_type string
		 * @return bool
		 */
		function _enable_vote_for_review($enable,$post_id,$service_type)
		{
			//_allowed_vote_for_own_review
			$enable= $this->get_option('show_rate_review_button',FALSE);
			$author_id = get_post_field('post_author', $post_id);
			if(is_user_logged_in()){
				if(!$this->get_option('allowed_vote_for_own_review') and get_current_user_id()==$author_id){
					$enable=FALSE;
				}
			}

			return $enable;
		}

		function required_partner_approved_review(){
			return $this->get_option('required_partner_approved_review',FALSE);
		}

		function room_maximum_review()
		{
			return $this->get_option('maximum_review');
		}
		function review_without_booking()
		{
			return $this->get_option('review_without_booking');
		}

		function posts_per_page(){
			return $this->get_option('posts_per_page');
		}

		function _change_posts_per_page($posts_per_page,$template_id=FALSE)
		{
			if($template_id and $this->get_option('archive_page')==$template_id){
				$posts_per_page=$this->posts_per_page();
			}

			return $posts_per_page;
		}
		function thumb_size($default=FALSE)
		{
			return $this->get_option('thumb_size',$default);
		}

		function gallery_size($default=FALSE)
		{
			return $this->get_option('gallery_size',$default);
		}
		function _apply_thumb_size($size,$service_type,$post_id)
		{
			if($service_type==$this->type_id){
				$thumb=$this->thumb_size('150,150,off');
				$thumb=explode(',',$thumb);
				if(count($thumb)==3){
					if($thumb[2]=='off') $thumb[2]=FALSE;

					$size= array($thumb[0],$thumb[1]);
				}

			}
			return $size;
		}
		function _apply_gallery_size($size,$service_type,$post_id)
		{
			if($service_type==$this->type_id){

				$thumb=$this->gallery_size('800,600,off');
				$thumb=explode(',',$thumb);
				if(count($thumb)==3){
					if($thumb[2]=='off') $thumb[2]=FALSE;
					$size=array($thumb[0],$thumb[1]);
				}
			}
			return $size;
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

