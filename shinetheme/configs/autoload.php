<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 3/11/2016
 * Time: 10:38 AM
 */

/**
 * @see Traveler_Loader::_autoload();
 */
$autoload['config']=array(
	'settings'
);

$autoload['helper']=array(
	'application',
	'assets'
);


$autoload['library']=array(

    'input'
);

$autoload['controller']=array(
	'service',
	'admin/service',
    'admin/settings'
);
