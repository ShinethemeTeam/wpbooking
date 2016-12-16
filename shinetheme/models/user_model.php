<?php
/**
 * Created by WpBooking Team.
 * User: NAZUMI
 * Date: 10/31/2016
 * Version: 1.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if(!class_exists('WPBooking_user_model')){
    class WPBooking_user_model extends WPBooking_Model{
        static $_inst = false;

        function __construct()
        {
            $this->table_name = "users";
            $this->ignore_create_table = true;
            parent::__construct();
        }

        public function update_reset_key($data,$where){
            if(empty($data)) return false;

            $res = $this->where($where)->update($data);

            return $res;
        }

        static function _inst(){
            if(!(self::$_inst)){
                self::$_inst = new self();
            }
            return self::$_inst;
        }
    }
    WPBooking_user_model::_inst();
}