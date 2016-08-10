<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 8/10/2016
 * Time: 2:46 PM
 */
if (!class_exists('WPBooking_Extra_Service_Model')) {
	class WPBooking_Extra_Service_Model extends WPBooking_Model
	{

		static $_inst = FALSE;

		function __construct()
		{
			$this->table_version = '1.0';
			$this->table_name = 'wpbooking_extra_service';
			$this->columns = array(
				'id'         => array(
					'type'           => "int",
					'AUTO_INCREMENT' => TRUE
				),
				'name'  => array('type' => "varchar",'length'=>255),
				'service_type'    => array('type' => "varchar",'length'=>50),
				'to_user'    => array('type' => "FLOAT"),
				'created_at' => array('type' => "INT"),
				'delete_at'  => array('type' => "FLOAT"),
				'content'    => array('type' => "text"),
				'ip_address' => array('type' => "VARCHAR", 'length' => 20)
			);
			parent::__construct();
		}

		/**
		 * Send Message of Current User to Other
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param $to_user
		 * @param $content
		 * @param $post_id bool|int
		 * @return bool
		 */
		function send($to_user, $content, $post_id = FALSE)
		{
			global $wpdb;

			if (is_user_logged_in()) {
				$insert = array(
					'from_user'  => get_current_user_id(),
					'to_user'    => $to_user,
					'content'    => wp_kses_post($content),
					'created_at' => time(),
					'ip_address' => WPBooking_Input::ip_address(),
					'post_id'    => $post_id
				);
				return $this->insert($insert);
			}

			return FALSE;
		}

		function get_last_message()
		{

		}

		static function inst()
		{
			if (!self::$_inst) {
				self::$_inst = new self();
			}

			return self::$_inst;
		}
	}

	WPBooking_Inbox_Model::inst();
}