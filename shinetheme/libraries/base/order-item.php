<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 8/9/2016
 * Time: 12:07 PM
 */
if(!class_exists('WB_Order_Item')){
	class WB_Order_Item{

		private $item_id=FALSE;
		private $item_data=array();
		private $service=FALSE;

		function __construct($item_id){
			if(!$item_id or !$item_data=WPBooking_Order_Model::inst()->find($item_id)) return;

			$this->item_id=$item_id;
			$this->item_data=$item_data;

			$this->service=new WB_Service($this->item_data['post_id']);
		}

		/**
		 * Get Service Type Name
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @return mixed
		 */
		function get_type_name()
		{
			if($this->service){
				return $this->service->get_type_name();
			}
		}

		/**
		 * Get Order Item Money
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param bool|FALSE $need_convert
		 * @return float|mixed|void
		 */
		function get_total($need_convert=FALSE)
		{
			if($this->item_id){
				$item_price = $this->item_data['sub_total'];
				$item_price = apply_filters('wpbooking_order_item_total', $item_price, $this->item_data, $this->item_data['service_type']);
				$item_price = apply_filters('wpbooking_order_item_total_' . $this->item_data['service_type'], $item_price, $this->item_data);

				// Convert to current currency
				if ($need_convert) {
					$item_price = WPBooking_Currency::convert_money($item_price, array(
						'currency' => $need_convert['currency']
					));
				}

				return $item_price;
			}

		}

		/**
		 * Get Order Item Money with HTML format
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @return string
		 */
		function get_total_html(){

			if($this->item_id){
				$item_price = $this->get_total(TRUE);

				return WPBooking_Currency::format_money($item_price);
			}

		}

	}
}