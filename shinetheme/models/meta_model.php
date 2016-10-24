<?php
/**
 * Created by ShineTheme.
 * User: NAZUMI
 * Date: 10/24/2016
 * Version: 1.0
 */
if(!class_exists('WPBooking_Meta_Model')){
    class WPBooking_Meta_Model extends WPBooking_Model{
        static $_inst;

        function __construct()
        {
            $this->table_name = "postmeta";
            $this->ignore_create_table = true;
            parent::__construct();
        }

        /**
         * Get price for hotel
         * @since: 1.0
         */
        function get_price_hotel($post_id){
            global $wpdb;

            if(!empty($post_id)) return;

            $row = $this->select('MIN(meta_value) as min_price')
                ->join('posts','postmeta.post_id=posts.ID')
                ->where('meta_key','base_price')
                ->where($wpdb->posts.'.post_parent',$post_id)
                ->get()->row();

            return !empty($row['min_price'])?$row['min_price']:'';

        }

        static function _inst(){
            if(!self::$_inst){
                self::$_inst = new self();
            }
            return self::$_inst;
        }
    }

    WPBooking_Meta_Model::_inst();
}