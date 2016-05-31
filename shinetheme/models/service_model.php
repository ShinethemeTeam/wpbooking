<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 3/14/2016
 * Time: 2:08 PM
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
if (!class_exists('WPBooking_Service_Model')) {
	class WPBooking_Service_Model extends WPBooking_Model
	{
		static $_inst = FALSE;

		function __construct()
		{
			$this->table_version = '1.0.2.2';
			$this->table_name = 'wpbooking_service';
			$this->columns = array(
				'id'                => array(
					'type'           => "int",
					'AUTO_INCREMENT' => TRUE
				),
				'post_id'           => array('type' => "INT"),
				'price'             => array('type' => "FLOAT"),
				'children_price'    => array('type' => "FLOAT"),
				'infant_price'      => array('type' => "FLOAT"),
				'max_people'        => array('type' => "INT"),
				'next_days_blocked' => array('type' => "INT"),
				'avg_review_rate'   => array('type' => "INT"),
				'map_lat'           => array('type' => "FLOAT"),
				'map_lng'           => array('type' => "FLOAT"),
				'service_type'      => array('type' => "varchar",'length' => "50"),
			);
			parent::__construct();
		}

		function save_extra($post_id)
		{
			$columns = $this->get_columns();
			if (empty($columns)) return;

			foreach ($columns as $k => $v) {
				if (in_array($k, array('id', 'post_id'))) continue;
				$data[$k] = get_post_meta($post_id, $k, TRUE);
			}

			if (!$this->find_by('post_id', $post_id)) {
				$data['post_id'] = $post_id;
				$this->insert($data);
			} else {
				$this->where('post_id', $post_id)->update($data);
			}
		}

		/**
		 * Get Min and Max Price
		 * @since 1.0
		 *
		 * @param $args array Search Params
		 * @return mixed
		 */
		function get_min_max_price($args = array())
		{
			$args = wp_parse_args($args, array(
				'service_type' => FALSE
			));


			$this->select('min(price)as min,max(price) as max')
				->join('posts', 'posts.ID=' . $this->table_name . '.post_id');
			if ($args['service_type']) {
				$this->where('service_type', $args['service_type']);
			}

			$res = $this->get()->row();

			return $res;
		}

		static function inst()
		{
			if (!self::$_inst) {
				self::$_inst = new self();
			}

			return self::$_inst;
		}


	}

	WPBooking_Service_Model::inst();
}