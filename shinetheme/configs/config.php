<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 3/16/2016
 * Time: 5:07 PM
 */

$config['order_status']=array(
	'on-hold'=>array(
		'label'=>esc_html__('On Hold','wpbooking'),
		'desc'=>esc_html__('Waiting for Payment','wpbooking'),
	),
	'payment-failed'=>array(
		'label'=>esc_html__('Payment Failed','wpbooking'),
		'desc'=>esc_html__('Payment Failed because of Gateway Problem or Wrong API data of Gateway','wpbooking'),
	),
	'completed'=>array(
		'label'=>esc_html__('Completed','wpbooking'),
	),
	'cancelled'=>array(
		'label'=>esc_html__('Cancelled','wpbooking'),
		'desc'=>esc_html__('Customer or Admin cancel the booking','wpbooking'),
	),
	'refunded'=>array(
		'label'=>esc_html__('Refunded','wpbooking'),
		'desc'=>esc_html__('Refunded by Admin','wpbooking'),
	),
	'trash'=>array(
		'label'=>esc_html__('Trash','wpbooking'),
		'desc'=>esc_html__('Moved to Trash by Admin','wpbooking'),
	),
);