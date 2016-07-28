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

		function get_author($need = FALSE)
		{
			if ($this->ID) {
				$author_id = get_post_field('post_author', $this->ID);
				$author_info = array(
					'id'     => $author_id,
					'avatar' => get_avatar($author_id)
				);
				if ($need) {
					return !empty($author_info[$need]) ? $author_info[$need] : FALSE;
				}

				return $author_info;
			}
		}

		function get_address()
		{
			if ($this->ID) {
				return get_post_meta($this->ID, 'address', TRUE);
			}
		}

		function get_rate_html($echo = TRUE)
		{
			if ($this->ID) {
				$rate = wpbooking_service_rate_to_html($this->ID);
				if ($echo) echo($rate);
				else return $rate;
			}
		}

		function get_price_html($echo = TRUE)
		{
			if ($this->ID) {
				$rate = wpbooking_service_price_html($this->ID);
				if ($echo) echo($rate);
				else return $rate;
			}
		}

		function get_type()
		{
			return $this->service_type;
		}

		function check_favorite($user_id=FALSE){
			if($this->ID){
				if(!$user_id) $user_id=get_current_user_id();

				if(!$user_id) return FALSE;

				$model=WPBooking_User_Favorite_Model::inst();

				if($model->where(array(
					'post_id'=>$this->ID,
					'user_id'=>$user_id
				))->get(1)->row()){
					return true;
				}
			}
		}
	}
}