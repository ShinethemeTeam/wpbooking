<?php
if (!class_exists('WPBooking_Helpful_Model')) {
	class WPBooking_Helpful_Model extends WPBooking_Model
	{
		static $inst = FALSE;

		function __construct()
		{
			$this->table_name = 'wpbooking_review_helpful';
			$this->table_version = '1.0.1';
			$this->columns = array(
				'id'         => array(
					'type'           => "int",
					'AUTO_INCREMENT' => TRUE
				),
				'review_id'  => array('type' => "bigint"),
				'user_id'    => array('type' => "bigint"),
				'created_at' => array('type' => "INT"),
				'ip_address' => array('type' => 'varchar', 'length' => 50)
			);

            parent::__construct();

		}

		function vote($review_id, $user_id)
		{
			$action='vote';

			$check = $this->where(array(
				'review_id' => $review_id,
				'user_id'   => $user_id
			))->get(1)->row();
			if($check){
				$action = 'un_vote';
			}
			switch ($action) {
				case "un_vote":
					$this->where(array(
						'review_id' => $review_id,
						'user_id'   => $user_id
					))->delete();
					return FALSE;
					break;
				case "vote":
				default:
					if (!$check) {
						$this->insert(array(
							'review_id'  => $review_id,
							'user_id'    => $user_id,
							'created_at' => time(),
							'ip_address' => WPBooking_Input::ip_address()
						));
					}
					return true;
					break;
			}
		}

		/**
		 * Count All Vote by Review ID
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param $review_id
		 * @param $user_id
		 * @return int
		 */
		function count($review_id,$user_id=FALSE){
			$this->select('count(id) as total')->where(array(
				'review_id'=>$review_id,

			));
			if($user_id){
				$this->where('user_id',$user_id);
			}

			$res=$this->get(1)->row();

			if(!empty($res['total'])) return $res['total'];
			return 0;
		}

		static function inst()
		{
			if (!self::$inst) {
				self::$inst = new self();
			}

			return self::$inst;
		}
	}

    WPBooking_Helpful_Model::inst();
}