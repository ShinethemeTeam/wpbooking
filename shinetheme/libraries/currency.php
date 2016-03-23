<?php
/**
 * Created by PhpStorm.
 * User: me664
 * Date: 4/8/15
 * Time: 12:39 PM
 */

if(!class_exists('Traveler_Currency'))
{
	class Traveler_Currency
	{
		public static $all_currency;

		static function _init()
		{
			add_action( 'init',array(__CLASS__,'_location_session'),1 );
			add_action('init',array(__CLASS__,'_change_current_currency'));


			self::$all_currency=array (
				'ALL' => 'Albania Lek',
				'AFN' => 'Afghanistan Afghani',
				'ARS' => 'Argentina Peso',
				'AWG' => 'Aruba Guilder',
				'AUD' => 'Australia Dollar',
				'AZN' => 'Azerbaijan New Manat',
				'BSD' => 'Bahamas Dollar',
				'BBD' => 'Barbados Dollar',
				'BDT' => 'Bangladeshi taka',
				'BYR' => 'Belarus Ruble',
				'BZD' => 'Belize Dollar',
				'BMD' => 'Bermuda Dollar',
				'BOB' => 'Bolivia Boliviano',
				'BAM' => 'Bosnia and Herzegovina Convertible Marka',
				'BWP' => 'Botswana Pula',
				'BGN' => 'Bulgaria Lev',
				'BRL' => 'Brazil Real',
				'BND' => 'Brunei Darussalam Dollar',
				'KHR' => 'Cambodia Riel',
				'CAD' => 'Canada Dollar',
				'KYD' => 'Cayman Islands Dollar',
				'CLP' => 'Chile Peso',
				'CNY' => 'China Yuan Renminbi',
				'COP' => 'Colombia Peso',
				'CRC' => 'Costa Rica Colon',
				'HRK' => 'Croatia Kuna',
				'CUP' => 'Cuba Peso',
				'CZK' => 'Czech Republic Koruna',
				'DKK' => 'Denmark Krone',
				'DOP' => 'Dominican Republic Peso',
				'XCD' => 'East Caribbean Dollar',
				'EGP' => 'Egypt Pound',
				'SVC' => 'El Salvador Colon',
				'EEK' => 'Estonia Kroon',
				'EUR' => 'Euro Member Countries',
				'FKP' => 'Falkland Islands (Malvinas) Pound',
				'FJD' => 'Fiji Dollar',
				'GHC' => 'Ghana Cedis',
				'GIP' => 'Gibraltar Pound',
				'GTQ' => 'Guatemala Quetzal',
				'GGP' => 'Guernsey Pound',
				'GYD' => 'Guyana Dollar',
				'HNL' => 'Honduras Lempira',
				'HKD' => 'Hong Kong Dollar',
				'HUF' => 'Hungary Forint',
				'ISK' => 'Iceland Krona',
				'INR' => 'India Rupee',
				'IDR' => 'Indonesia Rupiah',
				'IRR' => 'Iran Rial',
				'IMP' => 'Isle of Man Pound',
				'ILS' => 'Israel Shekel',
				'JMD' => 'Jamaica Dollar',
				'JPY' => 'Japan Yen',
				'JEP' => 'Jersey Pound',
				'KZT' => 'Kazakhstan Tenge',
				'KPW' => 'Korea (North) Won',
				'KRW' => 'Korea (South) Won',
				'KGS' => 'Kyrgyzstan Som',
				'LAK' => 'Laos Kip',
				'LVL' => 'Latvia Lat',
				'LBP' => 'Lebanon Pound',
				'LRD' => 'Liberia Dollar',
				'LTL' => 'Lithuania Litas',
				'MKD' => 'Macedonia Denar',
				'MYR' => 'Malaysia Ringgit',
				'MUR' => 'Mauritius Rupee',
				'MXN' => 'Mexico Peso',
				'MNT' => 'Mongolia Tughrik',
				'MZN' => 'Mozambique Metical',
				'NAD' => 'Namibia Dollar',
				'NPR' => 'Nepal Rupee',
				'ANG' => 'Netherlands Antilles Guilder',
				'NZD' => 'New Zealand Dollar',
				'NIO' => 'Nicaragua Cordoba',
				'NGN' => 'Nigeria Naira',
				'NOK' => 'Norway Krone',
				'OMR' => 'Oman Rial',
				'PKR' => 'Pakistan Rupee',
				'PAB' => 'Panama Balboa',
				'PYG' => 'Paraguay Guarani',
				'PEN' => 'Peru Nuevo Sol',
				'PHP' => 'Philippines Peso',
				'PLN' => 'Poland Zloty',
				'QAR' => 'Qatar Riyal',
				'RON' => 'Romania New Leu',
				'RUB' => 'Russia Ruble',
				'SHP' => 'Saint Helena Pound',
				'SAR' => 'Saudi Arabia Riyal',
				'RSD' => 'Serbia Dinar',
				'SCR' => 'Seychelles Rupee',
				'SGD' => 'Singapore Dollar',
				'SBD' => 'Solomon Islands Dollar',
				'SOS' => 'Somalia Shilling',
				'ZAR' => 'South Africa Rand',
				'LKR' => 'Sri Lanka Rupee',
				'SEK' => 'Sweden Krona',
				'CHF' => 'Switzerland Franc',
				'SRD' => 'Suriname Dollar',
				'SYP' => 'Syria Pound',
				'TWD' => 'Taiwan New Dollar',
				'THB' => 'Thailand Baht',
				'TTD' => 'Trinidad and Tobago Dollar',
				'TRY' => 'Turkey Lira',
				'TRL' => 'Turkey Lira',
				'TVD' => 'Tuvalu Dollar',
				'UAH' => 'Ukraine Hryvna',
				'GBP' => 'United Kingdom Pound',
				'USD' => 'United States Dollar',
				'UYU' => 'Uruguay Peso',
				'UZS' => 'Uzbekistan Som',
				'VEF' => 'Venezuela Bolivar',
				'VND' => 'Viet Nam Dong',
				'YER' => 'Yemen Rial',
				'ZWD' => 'Zimbabwe Dollar'
			);

			/**
			 * Use in $config[settings]
			 */
			add_filter('traveler_get_all_currency',array(__CLASS__,'get_all_currency'));
		}

		static function _location_session()
		{

			if(Traveler_Session::get('traveler_currency')){
				Traveler_Session::set('traveler_currency',self::get_default_currency());
			}

		}


		static function _change_current_currency()
		{

			if(Traveler_Input::get('currency') and $new_currency=self::find_currency($_GET['currency']))
			{

				Traveler_Session::set('traveler_currency',$new_currency);
			}
		}
		static function _list_currency()
		{
			return self::get_currency(true);
		}

		static function get_all_currency(){
			return apply_filters('traveler_all_currency',self::$all_currency);
		}

		/**
		 * Return All Currencies
		 *
		 *
		 * */
		static function get_currency($theme_option=false)
		{
			$all= apply_filters('traveler_get_list_currency', traveler_get_option('currency_list',array()));

			//return array for theme options choise
			if($theme_option){
				$choice=array();

				if(!empty($all) and is_array($all))
				{


					foreach($all as $key=>$value)
					{
						$choice[]=array(

							'label'=>$value['title'],
							'value'=>$value['name']
						);
					}

				}
				return $choice;
			}else{

				return $all;

			}

		}

		/**
		 * return Default Currency
		 *
		 *
		 * */
		static function get_default_currency($need=false)
		{
			$primary=false;

			// Get all added curencies
			$add_currencies=self::get_added_currencies();
			if(!empty($add_currencies)){
				// first item is default currency
				$primary=$add_currencies[0];
			}

			// Check currency exists and available in the system
			if($primary and isset(self::$all_currency[$primary]))
			{
				// Default Currency Object Format
//            array(
//                'currency'=>'USD',
//                'exchange_rate'=>1,
//                'decimal'=>2,
//                'decimal_sep'=>'.',
//                'thousands_sep'=>',',
//                'position'=>'left'
//            ),

				if($need){
					return  isset($primary[$need])?$primary[$need]:false;
				}else{
					return $primary;
				}

			}

			return false;


		}

		/**
		 * Get a dropdown of all currencies
		 *
		 * @since 1.0
		 * @param $name
		 * @param bool $selected
		 * @param array $atts
		 * @return mixed|void
		 */

		static function get_currency_dropdown($name,$selected=false,$atts=array()){

			$atts['name']=$name;
			if(!isset($atts['id'])) $atts['id']=$name;

			$attributes = '';
			foreach ( $atts as $attr => $value ) {
				if ( ! empty( $value ) ) {
					$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
					$attributes .= ' ' . $attr . '="' . $value . '"';
				}
			}

			$all=self::get_all_currency();

			$html="<select {$attributes}>";
			foreach($all as $k=>$v){
				$html.="<option ".selected($selected,$k,false)." value='{$k}'>{$v} - {$k}</option>";
			}
			$html.="</select>";


			return apply_filters('get_currency_dropdown',$html,$name,$selected,$atts);
		}

		/**
		 * Get a dropdown of added currencies
		 *
		 * @since 1.0
		 * @param $name
		 * @param bool $selected
		 * @param array $atts
		 * @return mixed|void
		 */
		static function get_added_currency_dropdown($name,$selected=false,$atts=array()){

			$atts['name']=$name;
			if(!isset($atts['id'])) $atts['id']=$name;

			$attributes = '';
			foreach ( $atts as $attr => $value ) {
				if ( ! empty( $value ) ) {
					$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
					$attributes .= ' ' . $attr . '="' . $value . '"';
				}
			}
			$all_currency=self::get_all_currency();

			$all=self::get_added_currencies();

			$html="<select {$attributes}>";
			foreach($all as $k=>$v){

				if(isset($v['currency']) and isset($all_currency[$v['currency']])){

					$code=$v['currency'];
					$country=$all_currency[$v['currency']];
					$html.="<option ".selected($selected,$code,false)." value='{$code}'>{$country} - {$code}</option>";

				}else{
					continue;
				}
			}
			$html.="</select>";


			return apply_filters('get_currency_dropdown',$html,$name,$selected,$atts);
		}

		/**
		 *
		 * Get added currency for metabox
		 *
		 * @since 1.0
		 * @param string $type
		 * @return mixed|void
		 */

		static function get_added_currency_array($type='metabox'){

			$all=self::get_added_currencies();
			$all_currency=self::get_all_currency();

			$html=array();

			if(!empty($all)){
				foreach($all as $key=>$value){

					$item=$all_currency[$value['currency']];

					switch($type){
						case "metabox":
							$html[]=array(
								'label'=>$item.' - '.$value['currency'],
								'value'=>$value['currency']
							);
							break;
					}
				}
			}


			return apply_filters('get_added_currency_array',$html,$type);
		}

		/**
		 * @todo Find currency by name, return false if not found
		 *
		 *
		 * */
		static  function find_currency($currency_name,$compare_key='currency')
		{
			$currency_name=esc_attr($currency_name);

			$all_currency=self::get_added_currencies();

			if(!empty($all_currency)){
				foreach($all_currency as $key)
				{
					if($key[$compare_key]==$currency_name)
					{
						return $key;
					}
				}
			}
			return false;
		}

		/**
		 * return Current Currency
		 *
		 *
		 * */
		static function get_current_currency($need=false)
		{
			$current=Traveler_Session::get('traveler_currency');
			//Check session of user first
			if($need and isset($current))
			{
				return @$current[$need];
			}else

				return self::get_default_currency($need);
		}


		static function get_added_currencies()
		{

			return traveler_get_option('list_currencies',array());
		}

	}

	Traveler_Currency::_init();
}