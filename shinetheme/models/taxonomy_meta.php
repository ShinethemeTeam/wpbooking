<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 8/3/2016
 * Time: 4:35 PM
 */
if (!class_exists('WPBooking_Taxonomy_Meta_Model')) {
	class WPBooking_Taxonomy_Meta_Model extends WPBooking_Model
	{

		static $_inst;

		function __construct()
		{
			$this->table_name = 'wpbooking_term_meta';
			$this->table_version = '1.0';
			$this->columns = array(
				'meta_id'    => array(
					'type'           => "bigint",
					'AUTO_INCREMENT' => TRUE
				),
				'term_id'    => array('type' => "bigint"),
				'meta_key'   => array('type' => "VARCHAR", 'length' => 255),
				'meta_value' => array('type' => "longtext"),
			);
			parent::__construct();
		}

		function update_meta($term_id, $meta_key, $meta_value)
		{
			$check = $this->where(array(
				'term_id'  => $term_id,
				'meta_key' => $meta_key
			))->get(1)->row();

			if (is_object($meta_value) or is_array($meta_value)) $meta_value = serialize($meta_value);

			if ($check) {
				// Exits
				$this->where(array(
					'term_id'  => $term_id,
					'meta_key' => $meta_key
				))->update(array(
					'meta_value' => $meta_value
				));

			} else {
				$this->insert(array(
					'term_id'    => $term_id,
					'meta_key'   => $meta_key,
					'meta_value' => $meta_value
				));
			}
		}

		function get_meta($term_id, $meta_key)
		{

			$res = $this->where(array(
				'term_id'  => $term_id,
				'meta_key' => $meta_key
			))->get(1)->row();

			if ($res and !empty($res['meta_value'])) {
				// Try Unserialize
				if (is_serialized($res['meta_value']) and $data = @unserialize($res['meta_value'])) return $data;
				else return $res['meta_value'];
			}
		}

		static function inst()
		{
			if (!self::$_inst) {
				self::$_inst = new self();
			}

			return self::$_inst;
		}
	}

	WPBooking_Taxonomy_Meta_Model::inst();
}