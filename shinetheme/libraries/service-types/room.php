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
					'value' => array(
						array(
							'id'    => 'title',
							'label' => __("Stat Name", 'traveler-booking'),
							'type'	=>'text'
						),
						array(
							'id'    => 'title',
							'label' => __("Stat Name", 'traveler-booking'),
							'type'	=>'text'
						)
					),
					'type'  => 'list-item'
				)

			);

			parent::__construct();
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

