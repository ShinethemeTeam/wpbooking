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
	//'settings'
	'lang'
);

$autoload['helper']=array(
	'application',
	'assets',
    'settings',
    'form-build',
    'service',
	'email'
);

$autoload['library']=array(
    'input',
    'assets',
	'session',
	'currency',
	'validator',
	'metabox',
);

$autoload['controller']=array(
	'service',
	'admin/order',
	'admin/location',
	'admin/taxonomy',
	'admin/service',
	'admin/form-builder',
    'admin/settings',
	'admin/taxonomy',
	'admin/calendar.metabox',
	'gateways',
	'email',
	'booking'
);

$autoload['model']=array(
	'service_model',
	'order_model',
	'calendar_model',
	'payment_model'
);

$autoload['widget']=array(
    'search-form',
    'currency-switcher',
	'cart-widget'
);

$autoload['encrypr_key'] = 'traveler';
