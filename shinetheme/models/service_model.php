<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
if (!class_exists('WPBooking_Service_Model')) {
	class WPBooking_Service_Model extends WPBooking_Model
	{
		static $_inst = FALSE;

		function __construct()
		{
			$this->table_version = '1.0.2.11';
			$this->table_name = 'wpbooking_service';
			$this->columns = array(
				'id'                     => array(
					'type'           => "int",
					'AUTO_INCREMENT' => TRUE
				),
				'post_id'                => array('type' => "INT"),
				'enable_property'        => array('type' => "varchar", 'length' => 10),
				'price'                  => array('type' => "FLOAT"),
				'number'                 => array('type' => "INT"),
				'children_price'         => array('type' => "FLOAT"),
				'infant_price'           => array('type' => "FLOAT"),
				'map_lat'                => array('type' => "FLOAT"),
				'map_long'               => array('type' => "FLOAT"),
				'service_type'           => array('type' => "varchar", 'length' => "50"),
				'property_available_for' => array('type' => 'varchar', 'length' => 50),
				'max_guests'             => array('type' => "INT"),
				'location_id'            => array('type' => "INT"),
                'pricing_type'=>array('type' => "varchar", 'length' => "100")
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

				// Set Default Value
				switch ($k) {
					case "enable_property":
						if (!$data[$k]) $data[$k] = 'on';
						break;
					case "property_available_for":
						if (!$data[$k]) $data[$k] = 'forever';
						break;
					case "service_type":
						if (!$data[$k]) {
							// Set the first Type
							$all = WPBooking_Service_Controller::inst()->get_service_types();
							if (!empty($all)) {
								reset($all);
								$data[$k] = key($all);
							}
						}
						break;
					case "number":
						if (!$data[$k]) {
							$data[$k] = 1;
						}
						break;
				}
			}
			if (!$check_exists = $this->find_by('post_id', $post_id)) {
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

            $res = apply_filters('wpbooking_min_max_price_'.$args['service_type'],array(
                'min'=>0,
                'max'=>500
            ));

			return $res;
		}

		/**
		 * Get Array of Price for Chart
		 * @since 1.0
		 *
		 * @param $args array Search Params
		 * @return mixed
		 */
		function get_price_chart($args = array())
		{
			$min_max = $this->get_min_max_price($args);
			if ($min_max) {
				$res = array();
				$columns = 20;
				$step = ($min_max['max'] - $min_max['min']) / $columns;

				for ($i = 1; $i <= $columns; $i++) {
					$row = $this->select('count(post_id) as total')
						->where('price>=', $step * $i + $min_max['min'])
						->where('price<', $step * ($i + 1) + $min_max['min'])
						->get()->row();

					if ($row) {
						$res[] = (float)$row['total'];
					} else {
						$res[] = 0;
					}
				}

				return $res;
			}

			return array();
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