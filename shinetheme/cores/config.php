<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if(!class_exists('WPBooking_Config'))
{
	class WPBooking_Config
	{
		protected $configs=array();
		private static $_inst;

		/**
		 * Load config file, array or file name
		 *
		 * @param  $file
		 * @since 1.0
		 */
		function load($file)
		{
			if(is_array($file) and !empty($file)){
				foreach($file as $f){
					$this->load($f);
				}
			}

			if(is_string($file)){
				$real_file=WPBooking()->get_dir('shinetheme/configs/'.$file.'.php');
				if(file_exists($real_file))
				{
					include $real_file;

					if(isset($config)){
						$this->configs=array_merge($this->configs,$config);
					}
				}
			}
		}

		/**
		 * Get config item or get all configs
		 *
		 * @param bool|FALSE $config
		 * @return array|bool
		 * @since 1.0
		 */
		function item($config=FALSE){
			if(!$config) return $this->configs;
			elseif(isset($this->configs[$config]))
			{
				return apply_filters('wpbooking_config_'.$config,$this->configs[$config]);
			}
			return FALSE;

		}

		static function inst()
		{
			if(!self::$_inst){
				self::$_inst=new self();
			}
			return self::$_inst;
		}
	}

	WPBooking_Config::inst();

	if(!function_exists('WPBookingConfig'))
	{
		function WPBookingConfig()
		{
			return WPBooking_Config::inst();
		}
	}

}