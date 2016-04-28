<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 3/24/2016
 * Time: 4:23 PM
 */
if (!class_exists('Traveler_Room_Service_Type') and class_exists('Traveler_Abstract_Service_Type')) {
	class Traveler_Room_Service_Type extends Traveler_Abstract_Service_Type
	{
		static $_inst = FALSE;

		protected $type_id = 'room';

		function __construct()
		{
			$this->type_info = array(
				'label' => __("Room", 'traveler-booking')
			);
			$this->settings = array(

				array(
					'id'    => 'title',
					'label' => __('General Options', 'traveler-booking'),
					'type'  => 'title',
				), array(
					'id'    => 'archive_page',
					'label' => __('Archive Page', 'traveler-booking'),
					'type'  => 'page-select',
				),
				array(
					'id'    => 'review',
					'label' => __('Review', 'traveler-booking'),
					'type'  => 'multi-checkbox',
					'value' => array(
						array(
							'id'    => $this->type_id . '_enable_review',
							'label' => __('Enable Review', 'traveler-booking')
						),
						array(
							'id'    => $this->type_id . '_allow_guest_review',
							'label' => __('Allow Guest Review', 'traveler-booking')
						),
						array(
							'id'    => $this->type_id . '_review_without_booking',
							'label' => __('Review Without Booking', 'traveler-booking')
						),
						array(
							'id'    => $this->type_id . '_show_rate_review_button',
							'label' => __('Show Rate (Help-full) button in each review?', 'traveler-booking')
						),
						array(
							'id'    => $this->type_id . '_required_partner_approved_review',
							'label' => __('Review require Partner Approved?', 'traveler-booking')
						),
					)
				),

				array(
					'id'    => 'review_stats',
					'label' => __("Review Stats", 'traveler-booking'),
					'type'  => 'list-item',
					'value' => array()
				),
				array(
					'id'    => 'maximum_review',
					'label' => __("Maximum review per user", 'traveler-booking'),
					'type'  => 'number',
					'std'   => 1
				),
				array(
					'type' => 'hr'
				),
				array(
					'id'    => 'title',
					'label' => __('Booking Options', 'traveler-booking'),
					'type'  => 'title',
				),
				array(
					'id'        => 'order_form',
					'label'     => __('Order Form', 'traveler-booking'),
					'type'      => 'post-select',
					'post_type' => array('traveler_form')
				),
				array(
					'id'        => 'confirm-settings',
					'label'     => __('Instant Booking?', 'traveler-booking'),
					'type'      => 'multi-checkbox',
					'value'=>array(
						array(
							'id'=>'service_type_'.$this->type_id.'_customer_confirm',
							'label'=>__("Require customer confirm the booking by send them an email",'traveler-booking')
						),
						array(
							'id'=>'service_type_'.$this->type_id.'_partner_confirm',
							'label'=>__("Require partner confirm the booking",'traveler-booking')
						),
					)
				),
				array(
					'type' => 'hr'
				),
				array(
					'id'    => 'title',
					'label' => __('Layout', 'traveler-booking'),
					'type'  => 'title',
				),
				array(
					'id'    => 'posts_per_page',
					'label' => __("Item per page", 'traveler-booking'),
					'type'  => 'number',
					'std'   => 10
				),
				array(
					'id'=>"thumb_size",
					'label'=>__("Thumb Size",'travel-booking'),
					'type'=>'image-size'
				),
				array(
					'id'=>"gallery_size",
					'label'=>__("Gallery Size",'travel-booking'),
					'type'=>'image-size'
				),
			);

			parent::__construct();

			add_filter('traveler_cart_item_price_'.$this->type_id,array($this,'_change_cart_item_price'),10,2);
			add_filter('traveler_cart_item_pay_amount_'.$this->type_id,array($this,'_change_cart_item_price'),10,2);

			add_filter('traveler_order_item_total_'.$this->type_id,array($this,'_change_order_item_price'),10,2);



			// Add more params to cart items
			add_filter('traveler_cart_item_params_'.$this->type_id,array($this,'_change_cart_item_params'),10,2);

		}

		function _change_cart_item_params($cart_item,$post_id=FALSE)
		{
			$cart_item['price_type']=get_post_meta($post_id,'price_type',true);
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
		function _change_cart_item_price($price,$cart_item)
		{
			$cart_item=wp_parse_args($cart_item,array(
				'base_price'=>0,
				'check_in_timestamp'=>'',
				'check_out_timestamp'=>'',
				'price_type'=>''
			));
			// Calculate price by number night
			if($cart_item['price_type']=='per_night' and $cart_item['check_in_timestamp'] and $cart_item['check_out_timestamp']){

				$price=$cart_item['base_price'];
				$night=traveler_timestamp_diff_day($cart_item['check_in_timestamp'],$cart_item['check_out_timestamp']);
				if(!$night) $night=1;


				$price*=$night;
			}

			return $price;
		}


		function _change_order_item_price($price,$order_item)
		{
			$order_item=wp_parse_args($order_item,array(
				'base_price'=>0,
				'check_in_timestamp'=>'',
				'check_out_timestamp'=>'',
				'raw_data'=>''
			));

			// We need raw data because table order_item can not save all value from the cart_item data. Example price_type for room
			$raw_data=unserialize($order_item['raw_data']);

			if(!empty($raw_data) and is_array($raw_data)){
				$raw_data=wp_parse_args($raw_data,
					array(
						'price_type'=>FALSE
					));
				// Calculate price by number night
				if($raw_data['price_type']=='per_night' and $order_item['check_in_timestamp'] and $order_item['check_out_timestamp']){

					$price=$order_item['base_price'];
					$night=traveler_timestamp_diff_day($order_item['check_in_timestamp'],$order_item['check_out_timestamp']);
					if(!$night) $night=1;


					$price*=$night;
				}
			}


			return $price;
		}

        function _add_page_archive_search($args){
            $id_page = $this->get_option('archive_page');
            $args = array($id_page=>$this->type_id);
            return $args;
        }
        function _service_query_args($args){
            $args[ 'meta_query' ][ ] = array(
                'key'   => 'service_type' ,
                'value' => $this->type_id ,
            );
            if($location_id = Traveler_Input::request( 'location_id' )) {
                $args[ 'tax_query' ][ ] = array(
                    'taxonomy' => 'traveler_location' ,
                    'field'    => 'term_id' ,
                    'terms'    => array( $location_id ) ,
                    'operator' => 'IN' ,
                );
            }
            $tax = Traveler_Input::request( 'taxonomy' );
            if(!empty( $tax ) and is_array( $tax )) {
                $taxonomy_operator = Traveler_Input::request( 'taxonomy_operator' );
                $tax_query         = array();
                foreach( $tax as $key => $value ) {
                    if($value) {
                        if(!empty( $taxonomy_operator[ $key ] )) {
                            $operator = $taxonomy_operator[ $key ];
                        } else {
                            $operator = "OR";
                        }
                        $value = explode( ',' , $value );
                        if(!empty( $value ) and is_array( $value )) {
                            foreach( $value as $k => $v ) {
                                if(!empty( $v )) {
                                    $ids[ ] = $v;
                                }
                            }
                        }
                        if(!empty( $ids )) {
                            $tax_query[ ] = array(
                                'taxonomy' => $key ,
                                'terms'    => $ids ,
                                'operator' => $operator ,
                            );
                        }
                        $ids = array();
                    }
                }

                if(!empty( $tax_query )) {
                    $args[ 'tax_query' ][ ] = $tax_query;
                }
            }
            return $args;
        }
        function _get_where_query($where){
            global $wpdb;
            if($review_rate = Traveler_Input::request( 'review_rate' ) and is_array( explode( ',' , $review_rate ) )) {
                $and = "";
                foreach( explode( ',' , $review_rate ) as $k => $v ) {
                    if($k > 0) {
                        $and .= " OR ";
                    }
                    $and .= " ( {$wpdb->prefix}commentmeta.meta_key = 'traveler_review' AND {$wpdb->prefix}commentmeta.meta_value = {$v} ) ";
                }
                if(!empty( $and )) {
                    $where .= " AND $wpdb->posts.ID IN
                            (
                                SELECT * FROM (
                                        SELECT
                                            {$wpdb->prefix}comments.comment_post_ID as post_id
                                        FROM
                                            wp_comments
                                        JOIN {$wpdb->prefix}commentmeta ON {$wpdb->prefix}comments.comment_ID = {$wpdb->prefix}commentmeta.comment_id
                                        WHERE 1 = 1
                                        AND ( {$and} )
                                )as ID

                            )";
                }

            }
            return $where;
        }

		static function inst()
		{
			if (!self::$_inst) {
				self::$_inst = new self();
			}

			return self::$_inst;
		}
	}

	Traveler_Room_Service_Type::inst();
}

