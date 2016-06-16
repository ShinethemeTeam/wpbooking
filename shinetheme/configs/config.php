<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 3/16/2016
 * Time: 5:07 PM
 */
$config['payment_status']=array(
	'processing'=>array(
		'label'=>esc_html__('Processing','wpbooking'),
		'desc'=>esc_html__('Payment is on Processing','wpbooking'),
	),
	'completed'=>array(
		'label'=>esc_html__('Completed','wpbooking'),
	),
	'failed'=>array(
		'label'=>esc_html__('Failed','wpbooking'),
		'desc'=>esc_html__('Error on payment or can not parse the response from selected Payment Gateway','wpbooking'),
	),
);

$config['order_item_status']=array(
	'on-hold'=>array(
		'label'=>esc_html__('On Hold','wpbooking'),
		'desc'=>esc_html__('Waiting for Payment','wpbooking'),
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