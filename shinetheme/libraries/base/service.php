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

		function __construct($service_id = FALSE)
		{
			if (!$service_id) $service_id = get_the_ID();

			if (!$service_id) return;

			$this->ID = $service_id;

			$this->service_type = get_post_meta($service_id, 'service_type', TRUE);

			$this->thumb_size = apply_filters('wpbooking_archive_loop_image_size', FALSE, $this->service_type, $this->ID);
			$this->gallery_size = apply_filters('wpbooking_single_loop_image_size', 'full', $this->service_type, $this->ID);
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
			if ($this->ID) {
				$author_id = get_post_field('post_author', $this->ID);
				$udata = get_userdata($author_id);
				$contact_now_url=FALSE;
				if(is_user_logged_in()){
					$contact_now_url=WPBooking_User::inst()->account_page_url().'start-chat/'.$author_id;
				}
				$author_info = array(
					'id'              => $author_id,
					'name'            => $udata->display_name,
					'avatar'          => get_avatar($author_id),
					'user_registered' => $udata->user_registered,
					'description'     => $udata->user_description,
					'address'         => get_user_meta($author_id, 'wb_address', TRUE),
					'profile_url'     =>WPBooking_User::inst()->account_page_url().'profile/'.$author_id,
					'contact_now_url'=>$contact_now_url
				);
				if ($need) {
					switch ($need) {
						case "since":
							return sprintf(esc_html__('since %s','wpbooking'),date_i18n('Y M', strtotime($author_info['user_registered'])));
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
							$res[$key]['title'] = $value['is_selected'];
							unset($res[$key]['is_selected']);
						}
					}

					return $res;
				}
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
		 * @return bool|mixed|void
		 */
		function enable_vote_for_review(){
			$enable=FALSE;
			if($this->ID and is_user_logged_in()){
				$enable=apply_filters('wpbooking_enable_vote_for_review',$enable,$this->ID,$this->service_type);
				$enable=apply_filters('wpbooking_enable_vote_for_review_'.$this->service_type,$enable,$this->ID,$this->service_type);
			}

			return $enable;
		}
	}
}