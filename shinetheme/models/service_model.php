<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 3/14/2016
 * Time: 2:08 PM
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if(!class_exists('Traveler_Service_Model'))
{
	class Traveler_Service_Model extends Traveler_Model{

		function __construct()
		{
			parent::__construct();
		}
	}
}