<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 9/5/2016
 * Time: 2:42 PM
 */
if (!class_exists('WB_Coupon')) {
    class WB_Coupon
    {
        private $item_id = false;


        function __construct($item_id = false)
        {
            if (!$item_id) $item_id = get_the_ID();

            $this->item_id = $item_id;

        }

        /**
         * Get Coupon Code
         *
         * @since 1.0
         * @author dungdt
         *
         */
        function get_code()
        {
            if ($this->item_id) {
                return get_the_title($this->item_id);
            }
        }

        /**
         * Get Raw Coupon Amount
         *
         * @since 1.0
         * @author dungdt
         *
         * @return mixed
         */
        function get_value()
        {
            if ($this->item_id) {
                return $this->get_meta('coupon_value');
            }
        }

        /**
         * Get Discount Value Type
         *
         * @since 1.0
         * @author dungdt
         *
         * @return mixed
         */
        function get_value_type()
        {

            if ($this->item_id) {
                return $this->get_meta('coupon_value_type');
            }

        }

        /**
         * Get Coupon Type (All or Specific Service)
         *
         * @since 1.0
         * @author dungdt
         *
         * @return mixed
         */
        function get_type()
        {
            if ($this->item_id) {
                return $this->get_meta('coupon_type');
            }
        }

        /**
         * Get Services
         * @since 1.0
         * @author dungdt
         *
         * @return array
         */
        function get_services()
        {
            $res = array();

            if ($this->item_id) {
                $ids = $this->get_meta('services_ids');
                $ids = explode(',', $ids);
                if (!empty($ids)) {
                    $res = $ids;
                }

            }

            return $res;
        }

        /**
         * Get Edit URL (For Dashboard Only)
         *
         * @since 1.0
         * @author dungdt
         *
         * @return string
         */
        function get_edit_url()
        {
            if ($this->item_id) {
                return add_query_arg(array(
                    'page'    => 'wpbooking_page_coupon',
                    'item_id' => $this->item_id
                ), admin_url('admin.php'));
            }
        }


        /**
         * Get Delete Coupon URL (For Dashboard Only)
         *
         * @since 1.0
         * @author dungdt
         *
         * @return string
         */
        function get_delete_url()
        {
            if ($this->item_id) {
                return add_query_arg(array(
                    'page'    => 'wpbooking_page_coupon',
                    'item_id' => $this->item_id,
                    'wb_action'  => 'delete_coupon'
                ), admin_url('admin.php'));
            }
        }

        /**
         * Get Coupon Meta By Key
         *
         * @since 1.0
         * @author dungdt
         *
         * @param $key
         * @return mixed
         */
        function get_meta($key)
        {
            if ($this->item_id) {
                return get_post_meta($this->item_id, $key, true);
            }
        }

    }
}