<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 3/11/2016
 * Time: 10:38 AM
 */

/**
 * @see WPBooking_Loader::_autoload();
 */
$autoload['config']=array(
	'config',
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
	'helper',
    'input',
    'assets',
	'session',
	'currency',
	'validator',
	'metabox',
	'email/emogrifier',
	'query',
	'base/service',
	'base/order',
	'base/order-item',
	'base/coupon',
	'taxonomy-metabox',
	'query-inject'
);

$autoload['controller']=array(
	'user',
	'service',
	'admin/order',
	'admin/coupon',
	'admin/location',
	'admin/taxonomy',
	'admin/service',
	'admin/form-builder',
    'admin/settings',
	'admin/taxonomy',
	'admin/calendar.metabox',
	'gateways',
	'email',
	'order',
	'inbox'
);

$autoload['model']=array(
	'service_model',
	'order_model',
	'calendar_model',
	'payment_model',
	'comments_model',
	'inbox_model',
	'user_favorite_model',
	'review_helpful'
);

$autoload['widget']=array(
    'search-form',
    'currency-switcher',
	'cart-widget'
);

$autoload['encrypr_key'] = 'wpbooking';
