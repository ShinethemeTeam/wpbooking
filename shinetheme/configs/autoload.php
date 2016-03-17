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
	'assets',
    'settings'
);


$autoload['library']=array(
    'input',
    'assets',
	'validator'
);

$autoload['controller']=array(
	'service',
	'admin/location',
	'admin/taxonomy',
	'admin/service',
    'admin/settings',
);
