<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 7/28/2016
 * Time: 1:47 PM
 */

/**
 * Base Class for one Service
 *
 * @since 1.0
 * @author dungdt
 *
 */
if (!class_exists('WB_Service')) {
	class WB_Service
	{

		private $ID = FALSE;
		private $service_type = FALSE;
		private $thumb_size = FALSE;
		private $gallery_size = FALSE;
		private $service_data=array();

		function __construct($service_id = FALSE)
		{
			if (!$service_id) $service_id = get_the_ID();

			if (!$service_id) return;

			$this->ID = $service_id;

			$this->service_type = get_post_meta($service_id, 'service_type', TRUE);

			$this->thumb_size = apply_filters('wpbooking_archive_loop_image_size', FALSE, $this->service_type, $this->ID);
			$this->gallery_size = apply_filters('wpbooking_single_loop_image_size', 'full', $this->service_type, $this->ID);

			/**
			 * Data from extra Table
			 */
			$this->service_data=WPBooking_Service_Model::inst()->find_by('post_id',$service_id);
		}

		/**
		 * Check if Service is Enable
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @return bool
		 */
		function is_enable(){
			if($this->ID){
				if(!empty($this->service_data['enable_property']) and $this->service_data['enable_property']=='off') return FALSE;
				return true;
			}
		}

		/**
		 * Get Array of Gallery of the Service.
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @return mixed|void
		 */
		function get_gallery()
		{
			if ($this->ID) {
				$res = array();

				$gallery = get_post_meta($this->ID, 'gallery', TRUE);
				if ($gallery) {
					$gallery = explode(',', $gallery);
					if (!empty($gallery)) {
						foreach ($gallery as $media) {
							$thumb = wp_get_attachment_image_src($media, $this->thumb_size);
							$gallery = wp_get_attachment_image_src($media, $this->gallery_size);
							$res[] = array(
								'thumb'       => wp_get_attachment_image($media, $this->thumb_size),
								'thumb_url'   => !empty($thumb[0]) ? $thumb[0] : FALSE,
								'gallery'     => wp_get_attachment_image($media, $this->gallery_size),
								'gallery_url' => !empty($gallery[0]) ? $gallery[0] : FALSE,
							);

						}
					}
				}

				if (empty($res)) {
					// Default
					$res[] = array(
						'thumb'       => sprintf('<img src="%s" alt="%s"/>', wpbooking_assets_url('images/default.png'), get_the_title($this->ID)),
						'thumb_url'   => wpbooking_assets_url('images/default.png'),
						'gallery'     => sprintf('<img src="%s" alt="%s"/>', wpbooking_assets_url('images/default.png'), get_the_title($this->ID)),
						'gallery_url' => wpbooking_assets_url('images/default.png'),

					);
				}

				return apply_filters('wbooking_service_get_gallery', $res);
			}
		}

		/**
		 * Get Featured Image
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param $need bool|string
		 * @return array
		 */
		function get_featured_image($need=FALSE)
		{
			$res = array(
				'thumb'       => sprintf('<img src="%s" alt="%s"/>', wpbooking_assets_url('images/default.png'), get_the_title($this->ID)),
				'thumb_url'   => wpbooking_assets_url('images/default.png'),
				'gallery'     => sprintf('<img src="%s" alt="%s"/>', wpbooking_assets_url('images/default.png'), get_the_title($this->ID)),
				'gallery_url' => wpbooking_assets_url('images/default.png'),

			);
			if($this->ID){
				if(has_post_thumbnail($this->ID)){

					$thumb = wp_get_attachment_image_src(get_post_thumbnail_id($this->ID), $this->thumb_size);
					$gallery = wp_get_attachment_image_src(get_post_thumbnail_id($this->ID), $this->gallery_size);
					$res = array(
						'thumb'       => get_the_post_thumbnail($this->ID,$this->thumb_size),
						'thumb_url'   => !empty($thumb[0]) ? $thumb[0] : FALSE,
						'gallery'     => get_the_post_thumbnail($this->ID, $this->gallery_size),
						'gallery_url' => !empty($gallery[0]) ? $gallery[0] : FALSE,

					);
				}
			}
			if($need){
				return !empty($res[$need])?$res[$need]:FALSE;
			}
			return $res;
		}

		/**
		 * IF $need is specific, return the single value of author of the service. Otherwise, return the array
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param bool|FALSE $need
		 * @return array|bool|string
		 */
		function get_author($need = FALSE)
		{
			$need=strtolower($need);
			if ($this->ID) {
				$author_id = get_post_field('post_author', $this->ID);
				$udata = get_userdata($author_id);
				$contact_now_url = FALSE;
				if (is_user_logged_in()) {
					$url=WPBooking_User::inst()->account_page_url().'tab/inbox/';
					$contact_now_url=add_query_arg(array('user_id'=>$author_id),$url);
				}
				$author_info = array(
					'id'              => $author_id,
					'name'            => $udata->display_name,
					'avatar'          => get_avatar($author_id),
					'user_registered' => $udata->user_registered,
					'description'     => $udata->user_description,
					'address'         => get_user_meta($author_id, 'wb_address', TRUE),
					'profile_url'     => WPBooking_User::inst()->account_page_url() . 'profile/' . $author_id,
					'contact_now_url' => $contact_now_url,
					'email'           => $udata->user_email
				);
				if ($need) {
					switch ($need) {
						case "since":
							return sprintf(esc_html__('since %s', 'wpbooking'), date_i18n('Y M', strtotime($author_info['user_registered'])));
							break;
						default:
							return !empty($author_info[$need]) ? $author_info[$need] : FALSE;
							break;
					}

				}

				return $author_info;
			}
		}

		/**
		 * Get Location Address String of Current Service
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @return mixed
		 */
		function get_address()
		{
			if ($this->ID) {
				return get_post_meta($this->ID, 'address', TRUE);
			}
		}

		/**
		 * Get Rate in HTML format of current Service
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param bool|TRUE $echo
		 * @return string
		 */
		function get_rate_html($echo = TRUE)
		{
			if ($this->ID) {
				$rate = wpbooking_service_rate_to_html($this->ID);
				if ($echo) echo($rate);
				else return $rate;
			}
		}

		/**
		 * Get Price in HTML format of current Service
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param bool|TRUE $echo
		 * @return mixed|string|void
		 */
		function get_price_html($echo = TRUE)
		{
			if ($this->ID) {
				$rate = wpbooking_service_price_html($this->ID);
				if ($echo) echo($rate);
				else return $rate;
			}
		}

		/**
		 * Get Service Type ID of current Service
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @return bool|mixed
		 */
		function get_type()
		{
			return $this->service_type;
		}

		/**
		 * Get Service Type Name
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @return mixed
		 */
		function get_type_name()
		{
			if($this->ID){
				$type_object=WPBooking_Service_Controller::inst()->get_service_type($this->get_type());
				if(!empty($type_object['label'])) return $type_object['label'];

			}
		}

		/**
		 * Check if Current Service is in Favorites of Curent User
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param bool|FALSE $user_id
		 * @return bool
		 */
		function check_favorite($user_id = FALSE)
		{
			if ($this->ID) {
				if (!$user_id) $user_id = get_current_user_id();

				if (!$user_id) return FALSE;

				$model = WPBooking_User_Favorite_Model::inst();

				if ($model->where(array(
					'post_id' => $this->ID,
					'user_id' => $user_id
				))->get(1)->row()
				) {
					return TRUE;
				}
			}
		}

		/**
		 * Add Current Service to Current Logged In User
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @return bool
		 */
		function do_favorite()
		{
			if ($this->ID and $user_id = is_user_logged_in()) {
				if ($this->check_favorite($user_id)) {
					$model = WPBooking_User_Favorite_Model::inst();
					$model->where(array(
						'post_id' => $this->ID,
						'user_id' => $user_id
					))->delete();

					return FALSE;
				} else {
					$model = WPBooking_User_Favorite_Model::inst();
					$model->insert(array(
						'post_id'    => $this->ID,
						'user_id'    => $user_id,
						'created_at' => time()
					));

					return TRUE;
				}
			}
		}

		/**
		 * Get Object of Service Type of Current Service
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @return mixed
		 */
		function service_type_object()
		{
			if ($this->ID and $this->service_type) {
				$service_types = WPBooking_Service_Controller::inst()->get_service_types();

				if (!empty($service_types) and array_key_exists($this->service_type, $service_types)) {
					return $service_types[$this->service_type];
				}
			}
		}

		/**
		 * Get All Added Extra Services of current Service
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @return array
		 */
		function get_extra_services()
		{
			if ($this->ID) {
				$meta = get_post_meta($this->ID, 'extra_services', TRUE);
				if (!empty($meta) and $this->service_type and array_key_exists($this->service_type, $meta)) {
					$res = $meta[$this->service_type];

					if (!empty($res) and is_array($res)) {
						foreach ($res as $key => $value) {
							if(term_exists($key,'wpbooking_extra_service')){
								$res[$key]['title'] = $value['is_selected'];
								unset($res[$key]['is_selected']);
							}else{
								unset($res[$key]);
							}

						}
					}

					return $res;
				}
			}
		}

		/**
		 * Get Number of Minimum Stay
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @return mixed
		 */
		function get_minimum_stay()
		{
			if($this->ID){
				return get_post_meta($this->ID,'minimum_stay',TRUE);
			}
		}

		/**
		 * Get Max Guests of Service
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @return mixed
		 */
		function get_max_guests()
		{
			if($this->ID){
				return get_post_meta($this->ID,'max_guests',TRUE);
			}
		}

		/**
		 * Get Terms array of current Post by Taxonomy
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param bool|FALSE $tax
		 * @return array|bool|WP_Error
		 */
		function get_terms($tax = FALSE)
		{
			if ($this->ID and $tax) {
				$terms = wp_get_post_terms($this->ID, $tax);
				if (is_wp_error($terms)) return FALSE;
				if (empty($terms)) return FALSE;

				return $terms;
			}
		}

		/**
		 * Check if current post is allowed to vote for the review
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param $review_id int
		 * @return bool|mixed|void
		 */
		function enable_vote_for_review($review_id)
		{
			$enable = FALSE;
			if ($this->ID and is_user_logged_in()) {
				$enable = apply_filters('wpbooking_enable_vote_for_review', $enable, $this->ID, $this->service_type,$review_id);
				$enable = apply_filters('wpbooking_enable_vote_for_review_' . $this->service_type, $enable, $this->ID, $this->service_type,$review_id);
			}

			return $enable;
		}

		/**
		 * Get Query to return related service
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param $arg array
		 * @return object|mixed
		 */
		function get_related_query($arg = array())
		{
			if ($this->ID) {

				do_action('wpbooking_before_related_query', $this->ID, $this->service_type);
				do_action('wpbooking_before_related_query_' . $this->service_type, $this->ID, $this->service_type);

				$arg = wp_parse_args($arg, array(
					'post_type'      => 'wpbooking_service',
					'posts_per_page' => 4,
					'post__not_in'   => array($this->ID)
				));

				$query = wpbooking_query('related_service', $arg);

				return $query;

			}

		}

		/**
		 * Get Default State of Service, Available forever or specific periods
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @return mixed
		 */
		function is_available_for(){
			if($this->ID){
				return get_post_meta($this->ID,'property_available_for',true);
			}
		}

		/**
		 * Check Available state for date range
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param $start
		 * @param $end
		 * @return bool
		 */
		function check_availability($start,$end){

			$return=array(
				'status'=>0,
				'unavailable_dates'=>array()
			);

			if($this->ID){
				$calendar = WPBooking_Calendar_Model::inst();
				$calendar_prices = $calendar->calendar_months($this->ID, $start, $start);

				// Reformat the res
				if(!empty($calendar_prices)){
					foreach($calendar_prices as $key=>$value){
						$calendar_prices[$value['start']]=$value;
					}
				}

				switch($this->is_available_for()){
						case "specific_periods":
							if(!empty($calendar_prices)){
								$return['status']=1;
								$check_in_temp=$start;
								while ($check_in_temp <= $end) {

									if(!array_key_exists($check_in_temp,$calendar_prices) or $calendar_prices[$check_in_temp]['status']=='not_available'){
										$return['unavailable_dates'] = $check_in_temp;
										$return['status']=0;
									}

									$check_in_temp = strtotime('+1 day', $check_in_temp);
								}

							}
							break;

						case "forever":
						default:
							$return['status']=1;
							if(!empty($calendar_prices)){
								$check_in_temp=$start;
								while ($check_in_temp <= $end) {

									if(array_key_exists($check_in_temp,$calendar_prices) and $calendar_prices[$check_in_temp]['status']=='not_available'){
										$return['unavailable_dates'] = $check_in_temp;
										$return['status']=0;
									}

									$check_in_temp = strtotime('+1 day', $check_in_temp);
								}
							}
							break;
				}
				if(!empty($calendar_prices)){
					if(array_key_exists($start,$calendar_prices) and $calendar_prices[$start]['can_check_in']==FALSE){
						$return['status']=0;
						$return['can_not_check_in']=TRUE;
					}
					if(array_key_exists($end,$calendar_prices) and $calendar_prices[$end]['can_check_out']==FALSE){
						$return['status']=0;
						$return['can_not_check_out']=TRUE;
					}
				}

			}

			return apply_filters('wpbooking_service_check_availability',$return,$this,$start,$end);
		}

		/**
		 * Get Meta Value by Key
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param $key
		 * @return mixed
		 */
		function get_meta($key)
		{
			if($this->ID){
				return get_post_meta($this->ID,$key,TRUE);
			}
		}

		/**
		 * Update Meta Field and Value of Extra Table
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param $key
		 * @param $value
		 */
		function update_meta($key,$value)
		{
			if($this->ID){
				$model=WPBooking_Service_Model::inst();
				update_post_meta($this->ID,$key,$value);
				$columns=$model->get_columns();

				if(array_key_exists($key,$columns)){
					$model->where('post_id',$this->ID)->update(array(
						$key=>$value
					));
				}
			}
		}
	}
}