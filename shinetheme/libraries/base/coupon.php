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
                $res=$ids;

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
                    'wb_action'  => 'delete_coupon',
                    'wb_nonce'=>wp_create_nonce( 'delete_coupon_' .$this->item_id )
                ), admin_url('admin.php'));
            }
        }

        /**
         * Check Coupon Date is Valid
         *
         * @since 1.0
         * @author dungdt
         *
         * @return bool
         */
        function is_date_available(){
            if($this->item_id){
                $today=time();
                $start_time=$this->get_meta('start_date_timestamp');
                $end_time=$this->get_meta('end_date_timestamp');

                if($start_time){
                    if($end_time and  $today>=$start_time and $today<$end_time) return true;

                    if(!$end_time and $today>=$start_time) return true;

                }elseif($end_time){
                    if($today<=$end_time) return true;
                }else{
                    // IF there is no Start and End Date
                    return true;
                }

                if($today>=$start_time and $today<$end_time) return true;
            }

            return false;
        }


        /**
         * Check Coupon Minimum Spend
         *
         * @since 1.0
         * @author dungdt
         *
         * @return bool
         */
        function check_minimum_spend(){
            if($this->item_id){
                $cart_total=WPBooking_Order::inst()->get_cart_total();
                $minimum_spend=$this->get_meta('minimum_spend');
                if(!$minimum_spend or $cart_total>=$minimum_spend) return true;
            }
            return false;
        }

        /**
         * Return Bool if reach the limit, or return the remain number
         *
         * @since 1.0
         * @author dungdt
         *
         * @return bool|mixed
         */
        function check_usage_limit()
        {
            if($this->item_id){
                $limit=$this->get_meta('usage_limit');
                if($limit){
                    $query=WPBooking_Query_Model::inst();
                    $used=$query->select('count(meta_id) as total')->where('meta_key','coupon_code')->where('meta_value',$this->item_id)->table('postmeta')->get(1,0,OBJECT)->row()->total;

                    if($used<$limit) return $limit-$used;// Return the Remain

                }else{
                    // If there is no limit
                    return true;
                }
            }
            return false;
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

        /**
         * Get Full Data of Coupon for Saved in Order
         *
         * @since 1.0
         * @author dungdt
         *
         * @return array
         */
        function get_full_data()
        {
            if($this->item_id){

                $metas=array('coupon_type','services_ids','coupon_value','coupon_value_type','start_date_timestamp','end_date_timestamp','minimum_spend','usage_limit');

                $res=array();

                foreach ($metas as $k){
                    $res[$k]=$this->get_meta($k);
                }

                return $res;
            }

            return array();
        }

    }
}