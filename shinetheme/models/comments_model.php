<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 5/30/2016
 * Time: 6:01 PM
 */
if(!class_exists('WPBooking_Comment_Model'))
{
	class WPBooking_Comment_Model extends WPBooking_Model
	{
		static $_inst;

		function __construct()
		{
			$this->table_name='comments';
			$this->ignore_create_table=TRUE;

		}

		static function inst()
		{
			if(!self::$_inst) self::$_inst=new self();
			return self::$_inst;
		}
	}

	WPBooking_Comment_Model::inst();

}