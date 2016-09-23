<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 8/10/2016
 * Time: 3:47 PM
 */
if (!class_exists('WPBooking_Hotel_Service_Type') and class_exists('WPBooking_Abstract_Service_Type')) {
    class WPBooking_Hotel_Service_Type extends WPBooking_Abstract_Service_Type
    {
        static $_inst = FALSE;

        protected $type_id = 'hotel';

        function __construct()
        {
            $this->type_info = array(
                'label' => __("Hotel", 'wpbooking'),
                'desc'  => esc_html__('Chỗ nghỉ cho khách du lịch, thường có nhà hàng, phòng họp và các dịch vụ khác dành cho khách', 'wpbooking')
            );

            parent::__construct();
        }

        static function inst()
        {
            if (!self::$_inst) self::$_inst = new self();

            return self::$_inst;
        }
    }

    WPBooking_Hotel_Service_Type::inst();
}