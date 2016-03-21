<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 3/11/2016
 * Time: 10:39 AM
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if(!class_exists('Traveler_Config'))
{
	class Traveler_Config
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
				$real_file=Traveler()->get_dir('shinetheme/configs/'.$file.'.php');
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
				return $this->configs[$config];
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

	Traveler_Config::inst();

	if(!function_exists('TravelerConfig'))
	{
		function TravelerConfig()
		{
			return Traveler_Config::inst();
		}
	}

}