<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if(!class_exists('WPBooking_Loader')){
	class WPBooking_Loader
	{
		private static $_inst;

		private $lib_loaded=array();


		function __construct()
		{
			$this->_autoload();
		}

		/**
		 * @return bool
		 */
		function _autoload(){
			$file=WPBooking()->get_dir('shinetheme/configs/autoload.php');

			if(file_exists($file)) include $file;

			if(!isset($autoload)) return FALSE;

			if(!empty($autoload['config']))
			{
				WPBooking_Config::inst()->load($autoload['config']);
			}

			// Composer Vendor
			if (!version_compare(phpversion(), '5.3', '<')) {
				$this->load_library('vendor/autoload');
			}

			if(!empty($autoload['helper']))
			{
				$this->load_helper($autoload['helper']);
			}
			if(!empty($autoload['library']))
			{
				$this->load_library($autoload['library']);
			}

			if(!empty($autoload['model']))
			{
				$this->load_model($autoload['model']);
			}

			if(!empty($autoload['controller']))
			{
				$this->load_controller($autoload['controller']);
			}

            //Load controller frontend
            if(!is_admin()) {
                if(!empty($autoload['frontend'])){
                    $this->load_helper($autoload['frontend']);
                }
            }

			if(!empty($autoload['widget']))
			{
				$this->load_widget($autoload['widget']);
			}

            $this->load_extension();

			return TRUE;
		}

		/**
		 * @param $file
		 */
		function load_controller($file){
			if(is_array($file) and !empty($file)){
				foreach($file as $f){
					$this->load_controller($f);
				}
			}

			if(is_string($file)){
				$real_file=WPBooking()->get_dir('shinetheme/controllers/'.$file.'.php');
				if(file_exists($real_file))
				{
					include_once $real_file;

				}
			}
		}

		/**
		 * @param $file
		 */
        function load_library( $file )
        {
            if ( is_array( $file ) and !empty( $file ) ) {
                foreach ( $file as $f ) {
                    $this->load_library( $f );
                }
            }

            if ( is_string( $file ) ) {
                $real_file = WPBooking()->get_dir( 'shinetheme/libraries/' . $file . '.php' );
                if ( file_exists( $real_file ) ) {
                    $custom_file = locate_template( ['shinetheme/libraries/' . $file . '.php'] );
                    if(file_exists($custom_file)){
                        include_once $custom_file;
                    }else{
                        include_once $real_file;
                    }

                }
            }
        }
		function load_helper($file){
			if(is_array($file) and !empty($file)){
				foreach($file as $f){
					$this->load_helper($f);
				}
			}

			if(is_string($file)){
				$real_file=WPBooking()->get_dir('shinetheme/helpers/'.$file.'.php');
				if(file_exists($real_file))
				{
					include_once $real_file;

				}
			}
		}

		function load_model($file)
		{
			if(is_array($file) and !empty($file)){
				foreach($file as $f){
					$this->load_model($f);
				}
			}

			if(is_string($file)){

				if(isset($this->lib_loaded['model'][$file])) return;// Ignore Loaded File

				$real_file=WPBooking()->get_dir('shinetheme/models/'.$file.'.php');
				if(file_exists($real_file))
				{
					$this->lib_loaded['model'][$file]=true;
					include_once $real_file;

				}
			}
		}

		function load_widget($file)
		{
			if(is_array($file) and !empty($file)){
				foreach($file as $f){
					$this->load_widget($f);
				}
			}

			if(is_string($file)){
				$real_file=WPBooking()->get_dir('shinetheme/widgets/'.$file.'.php');
				if(file_exists($real_file))
				{
					include_once $real_file;

				}
			}
		}

        function load_extension(){
            $dir = WPBooking()->get_dir('shinetheme/libraries/extensions/');

            if(is_dir($dir)) {
                $extensions = array_diff(scandir($dir), array('..', '.'));
            }

            // Auto load all extension file
            if(!empty($extensions)){
                foreach ($extensions as $extension)
                {
                    if(file_exists($dir.$extension.'/'.$extension.'.php')){
                        include_once $dir.$extension.'/'.$extension.'.php';
                    }
                }
            }
        }

		static function inst()
		{
			if(!self::$_inst){
				self::$_inst=new self();
			}
			return self::$_inst;
		}
	}

	WPBooking_Loader::inst();

}