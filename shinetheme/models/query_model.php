<?php
if(!class_exists('WPBooking_Query_Model'))
{
    class WPBooking_Query_Model extends WPBooking_Model {

        protected $ignore_create_table=true;

        static $_inst;

        static function inst()
        {
            if(!self::$_inst){
                self::$_inst=new self();
            }

            return self::$_inst;
        }
    }


    WPBooking_Query_Model::inst();
}