<?php
if (!class_exists('WPBooking_Country_Dropdown_Field')) {
	class WPBooking_Country_Dropdown_Field extends WPBooking_Abstract_Formbuilder_Field
	{
		static $_inst;

		function __construct()
		{
			$this->field_id = 'country_dropdown';
			$this->field_data = array(
				"title"    => esc_html__("Country Dropdown", 'wpbooking'),
				"category" => esc_html__("Advanced Field", 'wpbooking'),
				"options"  => array(
					array(
						"type"             => "required",
						"title"            => esc_html__("Set as <strong>required</strong>", 'wpbooking'),
						"desc"             => "",
						'edit_field_class' => 'wpbooking-col-md-6',
					),
					array(
						"type"             => "checkbox" ,
						'name'=>'hide_when_logged_in',
						'options'=>array(
							esc_html__( "Hide with <strong>Logged in user</strong>" , 'wpbooking' )=>1
						),
						'single_checkbox'=>1,
						'edit_field_class' => 'wpbooking-col-md-6' ,
					) ,
					array(
						"type"             => "text",
						"title"            => esc_html__("Title", 'wpbooking'),
						"name"             => "title",
						"desc"             => esc_html__("Title", 'wpbooking'),
						'edit_field_class' => 'wpbooking-col-md-6',
						'value'            => "",
						'required'         => TRUE
					),
					array(
						"type"             => "text",
						"title"            => esc_html__("Name", 'wpbooking'),
						"name"             => "name",
						"desc"             => esc_html__("Name", 'wpbooking'),
						'edit_field_class' => 'wpbooking-col-md-6',
						'value'            => "",
						'required'         => TRUE
					),
					array(
						"type"             => "text",
						"title"            => esc_html__("ID (optional)", 'wpbooking'),
						"name"             => "id",
						"desc"             => esc_html__("ID", 'wpbooking'),
						'edit_field_class' => 'wpbooking-col-md-6',
						'value'            => ""
					),
					array(
						"type"             => "text",
						"title"            => esc_html__("Class (optional)", 'wpbooking'),
						"name"             => "class",
						"desc"             => esc_html__("Class", 'wpbooking'),
						'edit_field_class' => 'wpbooking-col-md-6',
						'value'            => ""
					),
					array(
						"type"             => "text",
						"title"            => esc_html__("Value (optional)", 'wpbooking'),
						"name"             => "value",
						"desc"             => esc_html__("Value", 'wpbooking'),
						'edit_field_class' => 'wpbooking-col-md-6',
						'value'            => ""
					),
					array(
						"type"             => "text",
						"title"            => esc_html__("Placeholder (optional)", 'wpbooking'),
						"name"             => "placeholder",
						"desc"             => esc_html__("Placeholder", 'wpbooking'),
						'edit_field_class' => 'wpbooking-col-md-6',
						'value'            => ""
					),
				)
			);

			parent::__construct();
		}

		function shortcode($attr = array(), $content = FALSE)
		{
			$data = wp_parse_args($attr,
				array(
					'is_required' => 'off',
					'title'       => '',
					'name'        => 'user_first_name',
					'id'          => '',
					'class'       => '',
					'value'       => '',
					'placeholder' => '',
					'size'        => '',
					'maxlength'   => '',
				));
			extract($data);
			$array = array(
				'id'          => $id,
				'class'       => $class . ' ',
				'value'       => $value,
				'placeholder' => $placeholder,
				'size'        => $size,
				'maxlength'   => $maxlength,
				'name'        => $name
			);

			$required = "";
			$rule = array();
			if ($this->is_required($attr)) {
				$required = "required";
				$rule [] = "required";
				$array['class'] .= ' required';
			}
			if (!empty($maxlength)) {
				$rule [] = "max_length[" . $maxlength . "]";
			}

			parent::add_field($name, array('data' => $data, 'rule' => implode('|', $rule)));


			if($this->is_hidden($attr)) return FALSE;

			$a = FALSE;

			foreach ($array as $key => $val) {
				if ($val) {
					$a .= ' ' . $key . '="' . $val . '"';
				}
			}

			$country = $this->get_country_list();

			$html=array('<div class="wb-field">');
			if(!empty($data['title'])){

                $title=wpbooking_get_translated_string($data['title']);
                if($required) $title.=' <span class=required >*</span>';

				$html[]=sprintf('<p><label>%s</label></p>',$title);
			}
			$html[]= '<select ' . $a . ' >';
			if (!empty($country)) {
				foreach ($country as $k => $v) {
					$html[]= sprintf('<option value="%s">%s</option>', $k, $v);
				}
			}
			$html[] = '</select></div>';

			return implode("\r\n",$html);
		}


		function get_country_list()
		{
			$countryList = array(
				"AF" => "Afghanistan",
				"AL" => "Albania",
				"DZ" => "Algeria",
				"AS" => "American Samoa",
				"AD" => "Andorra",
				"AO" => "Angola",
				"AI" => "Anguilla",
				"AQ" => "Antarctica",
				"AG" => "Antigua and Barbuda",
				"AR" => "Argentina",
				"AM" => "Armenia",
				"AW" => "Aruba",
				"AU" => "Australia",
				"AT" => "Austria",
				"AZ" => "Azerbaijan",
				"BS" => "Bahamas",
				"BH" => "Bahrain",
				"BD" => "Bangladesh",
				"BB" => "Barbados",
				"BY" => "Belarus",
				"BE" => "Belgium",
				"BZ" => "Belize",
				"BJ" => "Benin",
				"BM" => "Bermuda",
				"BT" => "Bhutan",
				"BO" => "Bolivia",
				"BA" => "Bosnia and Herzegovina",
				"BW" => "Botswana",
				"BV" => "Bouvet Island",
				"BR" => "Brazil",
				"BQ" => "British Antarctic Territory",
				"IO" => "British Indian Ocean Territory",
				"VG" => "British Virgin Islands",
				"BN" => "Brunei",
				"BG" => "Bulgaria",
				"BF" => "Burkina Faso",
				"BI" => "Burundi",
				"KH" => "Cambodia",
				"CM" => "Cameroon",
				"CA" => "Canada",
				"CT" => "Canton and Enderbury Islands",
				"CV" => "Cape Verde",
				"KY" => "Cayman Islands",
				"CF" => "Central African Republic",
				"TD" => "Chad",
				"CL" => "Chile",
				"CN" => "China",
				"CX" => "Christmas Island",
				"CC" => "Cocos [Keeling] Islands",
				"CO" => "Colombia",
				"KM" => "Comoros",
				"CG" => "Congo - Brazzaville",
				"CD" => "Congo - Kinshasa",
				"CK" => "Cook Islands",
				"CR" => "Costa Rica",
				"HR" => "Croatia",
				"CU" => "Cuba",
				"CY" => "Cyprus",
				"CZ" => "Czech Republic",
				"CI" => "Côte d’Ivoire",
				"DK" => "Denmark",
				"DJ" => "Djibouti",
				"DM" => "Dominica",
				"DO" => "Dominican Republic",
				"NQ" => "Dronning Maud Land",
				"DD" => "East Germany",
				"EC" => "Ecuador",
				"EG" => "Egypt",
				"SV" => "El Salvador",
				"GQ" => "Equatorial Guinea",
				"ER" => "Eritrea",
				"EE" => "Estonia",
				"ET" => "Ethiopia",
				"FK" => "Falkland Islands",
				"FO" => "Faroe Islands",
				"FJ" => "Fiji",
				"FI" => "Finland",
				"FR" => "France",
				"GF" => "French Guiana",
				"PF" => "French Polynesia",
				"TF" => "French Southern Territories",
				"FQ" => "French Southern and Antarctic Territories",
				"GA" => "Gabon",
				"GM" => "Gambia",
				"GE" => "Georgia",
				"DE" => "Germany",
				"GH" => "Ghana",
				"GI" => "Gibraltar",
				"GR" => "Greece",
				"GL" => "Greenland",
				"GD" => "Grenada",
				"GP" => "Guadeloupe",
				"GU" => "Guam",
				"GT" => "Guatemala",
				"GG" => "Guernsey",
				"GN" => "Guinea",
				"GW" => "Guinea-Bissau",
				"GY" => "Guyana",
				"HT" => "Haiti",
				"HM" => "Heard Island and McDonald Islands",
				"HN" => "Honduras",
				"HK" => "Hong Kong SAR China",
				"HU" => "Hungary",
				"IS" => "Iceland",
				"IN" => "India",
				"ID" => "Indonesia",
				"IR" => "Iran",
				"IQ" => "Iraq",
				"IE" => "Ireland",
				"IM" => "Isle of Man",
				"IL" => "Israel",
				"IT" => "Italy",
				"JM" => "Jamaica",
				"JP" => "Japan",
				"JE" => "Jersey",
				"JT" => "Johnston Island",
				"JO" => "Jordan",
				"KZ" => "Kazakhstan",
				"KE" => "Kenya",
				"KI" => "Kiribati",
				"KW" => "Kuwait",
				"KG" => "Kyrgyzstan",
				"LA" => "Laos",
				"LV" => "Latvia",
				"LB" => "Lebanon",
				"LS" => "Lesotho",
				"LR" => "Liberia",
				"LY" => "Libya",
				"LI" => "Liechtenstein",
				"LT" => "Lithuania",
				"LU" => "Luxembourg",
				"MO" => "Macau SAR China",
				"MK" => "Macedonia",
				"MG" => "Madagascar",
				"MW" => "Malawi",
				"MY" => "Malaysia",
				"MV" => "Maldives",
				"ML" => "Mali",
				"MT" => "Malta",
				"MH" => "Marshall Islands",
				"MQ" => "Martinique",
				"MR" => "Mauritania",
				"MU" => "Mauritius",
				"YT" => "Mayotte",
				"FX" => "Metropolitan France",
				"MX" => "Mexico",
				"FM" => "Micronesia",
				"MI" => "Midway Islands",
				"MD" => "Moldova",
				"MC" => "Monaco",
				"MN" => "Mongolia",
				"ME" => "Montenegro",
				"MS" => "Montserrat",
				"MA" => "Morocco",
				"MZ" => "Mozambique",
				"MM" => "Myanmar [Burma]",
				"NA" => "Namibia",
				"NR" => "Nauru",
				"NP" => "Nepal",
				"NL" => "Netherlands",
				"AN" => "Netherlands Antilles",
				"NT" => "Neutral Zone",
				"NC" => "New Caledonia",
				"NZ" => "New Zealand",
				"NI" => "Nicaragua",
				"NE" => "Niger",
				"NG" => "Nigeria",
				"NU" => "Niue",
				"NF" => "Norfolk Island",
				"KP" => "North Korea",
				"VD" => "North Vietnam",
				"MP" => "Northern Mariana Islands",
				"NO" => "Norway",
				"OM" => "Oman",
				"PC" => "Pacific Islands Trust Territory",
				"PK" => "Pakistan",
				"PW" => "Palau",
				"PS" => "Palestinian Territories",
				"PA" => "Panama",
				"PZ" => "Panama Canal Zone",
				"PG" => "Papua New Guinea",
				"PY" => "Paraguay",
				"YD" => "People's Democratic Republic of Yemen",
				"PE" => "Peru",
				"PH" => "Philippines",
				"PN" => "Pitcairn Islands",
				"PL" => "Poland",
				"PT" => "Portugal",
				"PR" => "Puerto Rico",
				"QA" => "Qatar",
				"RO" => "Romania",
				"RU" => "Russia",
				"RW" => "Rwanda",
				"RE" => "Réunion",
				"BL" => "Saint Barthélemy",
				"SH" => "Saint Helena",
				"KN" => "Saint Kitts and Nevis",
				"LC" => "Saint Lucia",
				"MF" => "Saint Martin",
				"PM" => "Saint Pierre and Miquelon",
				"VC" => "Saint Vincent and the Grenadines",
				"WS" => "Samoa",
				"SM" => "San Marino",
				"SA" => "Saudi Arabia",
				"SN" => "Senegal",
				"RS" => "Serbia",
				"CS" => "Serbia and Montenegro",
				"SC" => "Seychelles",
				"SL" => "Sierra Leone",
				"SG" => "Singapore",
				"SK" => "Slovakia",
				"SI" => "Slovenia",
				"SB" => "Solomon Islands",
				"SO" => "Somalia",
				"ZA" => "South Africa",
				"GS" => "South Georgia and the South Sandwich Islands",
				"KR" => "South Korea",
				"ES" => "Spain",
				"LK" => "Sri Lanka",
				"SD" => "Sudan",
				"SR" => "Suriname",
				"SJ" => "Svalbard and Jan Mayen",
				"SZ" => "Swaziland",
				"SE" => "Sweden",
				"CH" => "Switzerland",
				"SY" => "Syria",
				"ST" => "São Tomé and Príncipe",
				"TW" => "Taiwan",
				"TJ" => "Tajikistan",
				"TZ" => "Tanzania",
				"TH" => "Thailand",
				"TL" => "Timor-Leste",
				"TG" => "Togo",
				"TK" => "Tokelau",
				"TO" => "Tonga",
				"TT" => "Trinidad and Tobago",
				"TN" => "Tunisia",
				"TR" => "Turkey",
				"TM" => "Turkmenistan",
				"TC" => "Turks and Caicos Islands",
				"TV" => "Tuvalu",
				"UM" => "U.S. Minor Outlying Islands",
				"PU" => "U.S. Miscellaneous Pacific Islands",
				"VI" => "U.S. Virgin Islands",
				"UG" => "Uganda",
				"UA" => "Ukraine",
				"SU" => "Union of Soviet Socialist Republics",
				"AE" => "United Arab Emirates",
				"GB" => "United Kingdom",
				"US" => "United States",
				"ZZ" => "Unknown or Invalid Region",
				"UY" => "Uruguay",
				"UZ" => "Uzbekistan",
				"VU" => "Vanuatu",
				"VA" => "Vatican City",
				"VE" => "Venezuela",
				"VN" => "Vietnam",
				"WK" => "Wake Island",
				"WF" => "Wallis and Futuna",
				"EH" => "Western Sahara",
				"YE" => "Yemen",
				"ZM" => "Zambia",
				"ZW" => "Zimbabwe",
				"AX" => "Åland Islands",
			);

			return apply_filters('wpbooking_country_lists', $countryList);
		}

		function get_value($form_item_data,$post_id)
		{
			$form_item_data=wp_parse_args($form_item_data,array(
				'value'=>FALSE
			));

			$form_item_data['value']=(string)$form_item_data['value'];
			$country = $this->get_country_list();

			return (isset($form_item_data['value']) and array_key_exists($form_item_data['value'], $country)) ? $country[$form_item_data['value']] : FALSE;
		}

		static function inst()
		{
			if (!self::$_inst) {
				self::$_inst = new self();
			}

			return self::$_inst;
		}
	}

	WPBooking_Country_Dropdown_Field::inst();

}

