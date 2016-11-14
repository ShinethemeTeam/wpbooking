<?php
/**
 * Created by ShineTheme.
 * User: NAZUMI
 * Date: 11/14/2016
 * Version: 1.0
 */
if(!defined('ABSPATH')){
    exit;
}

if(!class_exists('WPBooking_Chart')){
    class WPBooking_Chart
    {
        function __construct()
        {

        }

        /**
         * Get time range
         *
         * @author tienhd
         *
         * @param string $range
         * @param bool $start_date
         * @param bool $end_date
         * @return array
         */
        public function get_time_range($range = '',$start_date = false, $end_date = false){

            $data_range = array();
            switch($range){
                case 'this_year':
                    $current_date = strtotime('now');
                    $this_year = date('Y');
                    $start = strtotime($this_year.'-01-01');
                    while($start < $current_date){
                        $data_range['label'][] = date('F', $start);
                        $data_range['range'][] = $start;
                        $start = strtotime('+1 month',$start);
                    }
                    $data_range['range'][]= $current_date;
                    break;
                case 'last_year':
                    $last_year = date('Y',strtotime('-1 year'));
                    $start = strtotime($last_year.'-01-01');
                    $end = strtotime($last_year.'-12-31');
                    while($start < $end){
                        $data_range['label'][] = date('F', $start);
                        $data_range['range'][] = $start;
                        $start = strtotime('+1 month',$start);
                    }
                    $data_range['range'][]= $end;
                    break;
                case 'to_day':
                    break;
                case 'yesterday':
                    break;
                case 'this_week':
                    $current_date = strtotime('now');
                    $start = strtotime('monday this week',$current_date);
                    while($start <= $current_date){
                        $data_range['label'][] = date('Y/m/d', $start);
                        $data_range['range'][] = $start;
                        $start = strtotime('+1 day',$start);
                    }
                    $data_range['range'][]= $current_date;
                    break;
                case 'last_week':
                    $current_date = strtotime('now');
                    $monday_this_week = strtotime('monday this week',$current_date);
                    $start = strtotime('-1 week', $monday_this_week);
                    $end = strtotime('+6 days', $start);
                    while($start <= $end){
                        $data_range['label'][] = date('Y/m/d', $start);
                        $data_range['range'][] = $start;
                        $start = strtotime('+1 day',$start);
                    }
                    $data_range['range'][]= $end;
                    break;
                case 'last_7days':
                    $current_date = strtotime('now');
                    $start = strtotime('-1 week',$current_date);
                    while($start <= $current_date){
                        $data_range['label'][] = date('Y/m/d', $start);
                        $data_range['range'][] = $start;
                        $start = strtotime('+1 day',$start);
                    }
                    $data_range['range'][]= $current_date;
                    break;
                case 'last_30days':
                    $current_date = strtotime('now');
                    $start = strtotime('-30 days',$current_date);
                    while($start <= $current_date){
                        $data_range['label'][] = date('Y/m/d', $start);
                        $data_range['range'][] = $start;
                        $start = strtotime('+1 day',$start);
                    }
                    $data_range['range'][]= $current_date;
                    break;
                case 'last_60days':
                    $current_date = strtotime('now');
                    $start = strtotime('-60 days',$current_date);
                    while($start <= $current_date){
                        $data_range['label'][] = date('Y/m/d', $start);
                        $data_range['range'][] = $start;
                        $start = strtotime('+1 day',$start);
                    }
                    $data_range['range'][]= $current_date;
                    break;
                case 'last_90days':
                    $current_date = strtotime('now');
                    $start = strtotime('-90 days',$current_date);
                    while($start <= $current_date){
                        $data_range['label'][] = date('Y/m/d', $start);
                        $data_range['range'][] = $start;
                        $start = strtotime('+1 day',$start);
                    }
                    $data_range['range'][]= $current_date;
                    break;
                default:
                    break;
            }
            if(!empty($start_date) && !empty($end_date)){
                $start = strtotime($start_date);
                $end = strtotime($end_date);
                while($start <= $end){
                    $data_range['label'][] = date('Y/m/d', $start);
                    $data_range['range'][] = $start;
                    $start = strtotime('+1 day',$start);
                }
                $data_range['range'][]= $end;
            }
            return $data_range;
        }

        /**
         * Get total data in time range
         *
         * @author tienhd
         * @since 1.0
         *
         * @param $service_type
         * @param $select
         * @param $range
         * @param bool $start_date
         * @param bool $end_date
         * @return string
         */
        public function total_in_time_range($service_type, $select ,$range, $start_date = false, $end_date = false)
        {
            $time_range = $this->get_time_range($range, $start_date, $end_date);
            $res = '0';

            if (!empty($time_range['range'])) {
                switch($select){
                    case 'total_sale':
                        $res = WPBooking_Order_Model::inst()->get_rp_total_sale($service_type,$time_range['range'][0],$time_range['range'][count($time_range['range'])-1]);
                        break;
                    case 'net_profit':
                        $res = WPBooking_Order_Model::inst()->get_rp_total_net_profit($service_type,$time_range['range'][0],$time_range['range'][count($time_range['range'])-1]);
                        break;
                    case 'items':
                        $res = WPBooking_Order_Model::inst()->get_rp_total_items($service_type,$time_range['range'][0],$time_range['range'][count($time_range['range'])-1]);
                        break;
                    case 'total_bookings':
                        $res = WPBooking_Order_Model::inst()->get_rp_total_bookings($service_type,$time_range['range'][0],$time_range['range'][count($time_range['range'])-1]);
                        break;
                    case 'completed':
                    case 'on_hold':
                    case 'cancelled':
                    case 'refunded':
                    $res = WPBooking_Order_Model::inst()->get_rp_items_by_status($service_type,$time_range['range'][0],$time_range['range'][count($time_range['range'])-1],$select);
                        break;
                }
            }

            return $res;
        }

        /**
         * @param $service_type
         * @param $range
         * @param bool $start_date
         * @param bool $end_date
         * @return array
         */
        public function get_total_sale_in_time_range($service_type ,$range, $start_date = false, $end_date = false){
            $time_range = $this->get_time_range($range, $start_date, $end_date);
            $res = array();
            if(!empty($time_range['range'])){
                $res['label'] = $time_range['label'];
                foreach ($time_range['label'] as $item) {

                }
            }
            return $res;
        }

    }

}