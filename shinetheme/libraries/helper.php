<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 6/20/2016
 * Time: 3:50 PM
 */
if(!function_exists('WB_Helpers'))
{
	class WB_Helpers{

		/**
		 * Check if current is ajax request
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @return bool
		 */
		static function is_ajax()
		{
			if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
				return true;
			}
			return false;
		}

        /**
         * Get List of Phone Country Code and Flag
         *
         * @since 1.0
         * @author dungdt
         *
         * @return mixed|void
         */
		static function get_phone_country_code(){
            return apply_filters('wpbooking_get_phone_country_code',array(
                array (
                    'code' => '+7 840',
                    'name' => esc_html__('Abkhazia','wpbooking'),
                    'flag'=>'AF'
                ),
                array (
                    'code' => '+93',
                    'name' => 'Afghanistan',
                ),
                array (
                    'code' => '+355',
                    'name' => 'Albania',
                ),
                array (
                    'code' => '+213',
                    'name' => 'Algeria',
                ),
                array (
                    'code' => '+1 684',
                    'name' => 'American Samoa',
                ),
                array (
                    'code' => '+376',
                    'name' => 'Andorra',
                ),
                array (
                    'code' => '+244',
                    'name' => 'Angola',
                ),
                array (
                    'code' => '+1 264',
                    'name' => 'Anguilla',
                ),
                array (
                    'code' => '+1 268',
                    'name' => 'Antigua and Barbuda',
                ),
                array (
                    'code' => '+54',
                    'name' => 'Argentina',
                ),
                array (
                    'code' => '+374',
                    'name' => 'Armenia',
                ),
                array (
                    'code' => '+297',
                    'name' => 'Aruba',
                ),
                array (
                    'code' => '+247',
                    'name' => 'Ascension',
                ),
                array (
                    'code' => '+61',
                    'name' => 'Australia',
                ),
                array (
                    'code' => '+672',
                    'name' => 'Australian External Territories',
                ),
                array (
                    'code' => '+43',
                    'name' => 'Austria',
                ),
                array (
                    'code' => '+994',
                    'name' => 'Azerbaijan',
                ),
                array (
                    'code' => '+1 242',
                    'name' => 'Bahamas',
                ),
                array (
                    'code' => '+973',
                    'name' => 'Bahrain',
                ),
                array (
                    'code' => '+880',
                    'name' => 'Bangladesh',
                ),
                array (
                    'code' => '+1 246',
                    'name' => 'Barbados',
                ),
                array (
                    'code' => '+1 268',
                    'name' => 'Barbuda',
                ),
                array (
                    'code' => '+375',
                    'name' => 'Belarus',
                ),
                array (
                    'code' => '+32',
                    'name' => 'Belgium',
                ),
                array (
                    'code' => '+501',
                    'name' => 'Belize',
                ),
                array (
                    'code' => '+229',
                    'name' => 'Benin',
                ),
                array (
                    'code' => '+1 441',
                    'name' => 'Bermuda',
                ),
                array (
                    'code' => '+975',
                    'name' => 'Bhutan',
                ),
                array (
                    'code' => '+591',
                    'name' => 'Bolivia',
                ),
                array (
                    'code' => '+387',
                    'name' => 'Bosnia and Herzegovina',
                ),
                array (
                    'code' => '+267',
                    'name' => 'Botswana',
                ),
                array (
                    'code' => '+55',
                    'name' => 'Brazil',
                ),
                array (
                    'code' => '+246',
                    'name' => 'British Indian Ocean Territory',
                ),
                array (
                    'code' => '+1 284',
                    'name' => 'British Virgin Islands',
                ),
                array (
                    'code' => '+673',
                    'name' => 'Brunei',
                ),
                array (
                    'code' => '+359',
                    'name' => 'Bulgaria',
                ),
                array (
                    'code' => '+226',
                    'name' => 'Burkina Faso',
                ),
                array (
                    'code' => '+257',
                    'name' => 'Burundi',
                ),
                array (
                    'code' => '+855',
                    'name' => 'Cambodia',
                ),
                array (
                    'code' => '+237',
                    'name' => 'Cameroon',
                ),
                array (
                    'code' => '+1',
                    'name' => 'Canada',
                ),
                array (
                    'code' => '+238',
                    'name' => 'Cape Verde',
                ),
                array (
                    'code' => '+ 345',
                    'name' => 'Cayman Islands',
                ),
                array (
                    'code' => '+236',
                    'name' => 'Central African Republic',
                ),
                array (
                    'code' => '+235',
                    'name' => 'Chad',
                ),
                array (
                    'code' => '+56',
                    'name' => 'Chile',
                ),
                array (
                    'code' => '+86',
                    'name' => 'China',
                ),
                array (
                    'code' => '+61',
                    'name' => 'Christmas Island',
                ),
                array (
                    'code' => '+61',
                    'name' => 'Cocos-Keeling Islands',
                ),
                array (
                    'code' => '+57',
                    'name' => 'Colombia',
                ),
                array (
                    'code' => '+269',
                    'name' => 'Comoros',
                ),
                array (
                    'code' => '+242',
                    'name' => 'Congo',
                ),
                array (
                    'code' => '+243',
                    'name' => 'Congo, Dem. Rep. of (Zaire)',
                ),
                array (
                    'code' => '+682',
                    'name' => 'Cook Islands',
                ),
                array (
                    'code' => '+506',
                    'name' => 'Costa Rica',
                ),
                array (
                    'code' => '+385',
                    'name' => 'Croatia',
                ),
                array (
                    'code' => '+53',
                    'name' => 'Cuba',
                ),
                array (
                    'code' => '+599',
                    'name' => 'Curacao',
                ),
                array (
                    'code' => '+537',
                    'name' => 'Cyprus',
                ),
                array (
                    'code' => '+420',
                    'name' => 'Czech Republic',
                ),
                array (
                    'code' => '+45',
                    'name' => 'Denmark',
                ),
                array (
                    'code' => '+246',
                    'name' => 'Diego Garcia',
                ),
                array (
                    'code' => '+253',
                    'name' => 'Djibouti',
                ),
                array (
                    'code' => '+1 767',
                    'name' => 'Dominica',
                ),
                array (
                    'code' => '+1 809',
                    'name' => 'Dominican Republic',
                ),
                array (
                    'code' => '+670',
                    'name' => 'East Timor',
                ),
                array (
                    'code' => '+56',
                    'name' => 'Easter Island',
                ),
                array (
                    'code' => '+593',
                    'name' => 'Ecuador',
                ),
                array (
                    'code' => '+20',
                    'name' => 'Egypt',
                ),
                array (
                    'code' => '+503',
                    'name' => 'El Salvador',
                ),
                array (
                    'code' => '+240',
                    'name' => 'Equatorial Guinea',
                ),
                array (
                    'code' => '+291',
                    'name' => 'Eritrea',
                ),
                array (
                    'code' => '+372',
                    'name' => 'Estonia',
                ),
                array (
                    'code' => '+251',
                    'name' => 'Ethiopia',
                ),
                array (
                    'code' => '+500',
                    'name' => 'Falkland Islands',
                ),
                array (
                    'code' => '+298',
                    'name' => 'Faroe Islands',
                ),
                array (
                    'code' => '+679',
                    'name' => 'Fiji',
                ),
                array (
                    'code' => '+358',
                    'name' => 'Finland',
                ),
                array (
                    'code' => '+33',
                    'name' => 'France',
                ),
                array (
                    'code' => '+596',
                    'name' => 'French Antilles',
                ),
                array (
                    'code' => '+594',
                    'name' => 'French Guiana',
                ),
                array (
                    'code' => '+689',
                    'name' => 'French Polynesia',
                ),
                array (
                    'code' => '+241',
                    'name' => 'Gabon',
                ),
                array (
                    'code' => '+220',
                    'name' => 'Gambia',
                ),
                array (
                    'code' => '+995',
                    'name' => 'Georgia',
                ),
                array (
                    'code' => '+49',
                    'name' => 'Germany',
                ),
                array (
                    'code' => '+233',
                    'name' => 'Ghana',
                ),
                array (
                    'code' => '+350',
                    'name' => 'Gibraltar',
                ),
                array (
                    'code' => '+30',
                    'name' => 'Greece',
                ),
                array (
                    'code' => '+299',
                    'name' => 'Greenland',
                ),
                array (
                    'code' => '+1 473',
                    'name' => 'Grenada',
                ),
                array (
                    'code' => '+590',
                    'name' => 'Guadeloupe',
                ),
                array (
                    'code' => '+1 671',
                    'name' => 'Guam',
                ),
                array (
                    'code' => '+502',
                    'name' => 'Guatemala',
                ),
                array (
                    'code' => '+224',
                    'name' => 'Guinea',
                ),
                array (
                    'code' => '+245',
                    'name' => 'Guinea-Bissau',
                ),
                array (
                    'code' => '+595',
                    'name' => 'Guyana',
                ),
                array (
                    'code' => '+509',
                    'name' => 'Haiti',
                ),
                array (
                    'code' => '+504',
                    'name' => 'Honduras',
                ),
                array (
                    'code' => '+852',
                    'name' => 'Hong Kong SAR China',
                ),
                array (
                    'code' => '+36',
                    'name' => 'Hungary',
                ),
                array (
                    'code' => '+354',
                    'name' => 'Iceland',
                ),
                array (
                    'code' => '+91',
                    'name' => 'India',
                ),
                array (
                    'code' => '+62',
                    'name' => 'Indonesia',
                ),
                array (
                    'code' => '+98',
                    'name' => 'Iran',
                ),
                array (
                    'code' => '+964',
                    'name' => 'Iraq',
                ),
                array (
                    'code' => '+353',
                    'name' => 'Ireland',
                ),
                array (
                    'code' => '+972',
                    'name' => 'Israel',
                ),
                array (
                    'code' => '+39',
                    'name' => 'Italy',
                ),
                array (
                    'code' => '+225',
                    'name' => 'Ivory Coast',
                ),
                array (
                    'code' => '+1 876',
                    'name' => 'Jamaica',
                ),
                array (
                    'code' => '+81',
                    'name' => 'Japan',
                ),
                array (
                    'code' => '+962',
                    'name' => 'Jordan',
                ),
                array (
                    'code' => '+7 7',
                    'name' => 'Kazakhstan',
                ),
                array (
                    'code' => '+254',
                    'name' => 'Kenya',
                ),
                array (
                    'code' => '+686',
                    'name' => 'Kiribati',
                ),
                array (
                    'code' => '+965',
                    'name' => 'Kuwait',
                ),
                array (
                    'code' => '+996',
                    'name' => 'Kyrgyzstan',
                ),
                array (
                    'code' => '+856',
                    'name' => 'Laos',
                ),
                array (
                    'code' => '+371',
                    'name' => 'Latvia',
                ),
                array (
                    'code' => '+961',
                    'name' => 'Lebanon',
                ),
                array (
                    'code' => '+266',
                    'name' => 'Lesotho',
                ),
                array (
                    'code' => '+231',
                    'name' => 'Liberia',
                ),
                array (
                    'code' => '+218',
                    'name' => 'Libya',
                ),
                array (
                    'code' => '+423',
                    'name' => 'Liechtenstein',
                ),
                array (
                    'code' => '+370',
                    'name' => 'Lithuania',
                ),
                array (
                    'code' => '+352',
                    'name' => 'Luxembourg',
                ),
                array (
                    'code' => '+853',
                    'name' => 'Macau SAR China',
                ),
                array (
                    'code' => '+389',
                    'name' => 'Macedonia',
                ),
                array (
                    'code' => '+261',
                    'name' => 'Madagascar',
                ),
                array (
                    'code' => '+265',
                    'name' => 'Malawi',
                ),
                array (
                    'code' => '+60',
                    'name' => 'Malaysia',
                ),
                array (
                    'code' => '+960',
                    'name' => 'Maldives',
                ),
                array (
                    'code' => '+223',
                    'name' => 'Mali',
                ),
                array (
                    'code' => '+356',
                    'name' => 'Malta',
                ),
                array (
                    'code' => '+692',
                    'name' => 'Marshall Islands',
                ),
                array (
                    'code' => '+596',
                    'name' => 'Martinique',
                ),
                array (
                    'code' => '+222',
                    'name' => 'Mauritania',
                ),
                array (
                    'code' => '+230',
                    'name' => 'Mauritius',
                ),
                array (
                    'code' => '+262',
                    'name' => 'Mayotte',
                ),
                array (
                    'code' => '+52',
                    'name' => 'Mexico',
                ),
                array (
                    'code' => '+691',
                    'name' => 'Micronesia',
                ),
                array (
                    'code' => '+1 808',
                    'name' => 'Midway Island',
                ),
                array (
                    'code' => '+373',
                    'name' => 'Moldova',
                ),
                array (
                    'code' => '+377',
                    'name' => 'Monaco',
                ),
                array (
                    'code' => '+976',
                    'name' => 'Mongolia',
                ),
                array (
                    'code' => '+382',
                    'name' => 'Montenegro',
                ),
                array (
                    'code' => '+1664',
                    'name' => 'Montserrat',
                ),
                array (
                    'code' => '+212',
                    'name' => 'Morocco',
                ),
                array (
                    'code' => '+95',
                    'name' => 'Myanmar',
                ),
                array (
                    'code' => '+264',
                    'name' => 'Namibia',
                ),
                array (
                    'code' => '+674',
                    'name' => 'Nauru',
                ),
                array (
                    'code' => '+977',
                    'name' => 'Nepal',
                ),
                array (
                    'code' => '+31',
                    'name' => 'Netherlands',
                ),
                array (
                    'code' => '+599',
                    'name' => 'Netherlands Antilles',
                ),
                array (
                    'code' => '+1 869',
                    'name' => 'Nevis',
                ),
                array (
                    'code' => '+687',
                    'name' => 'New Caledonia',
                ),
                array (
                    'code' => '+64',
                    'name' => 'New Zealand',
                ),
                array (
                    'code' => '+505',
                    'name' => 'Nicaragua',
                ),
                array (
                    'code' => '+227',
                    'name' => 'Niger',
                ),
                array (
                    'code' => '+234',
                    'name' => 'Nigeria',
                ),
                array (
                    'code' => '+683',
                    'name' => 'Niue',
                ),
                array (
                    'code' => '+672',
                    'name' => 'Norfolk Island',
                ),
                array (
                    'code' => '+850',
                    'name' => 'North Korea',
                ),
                array (
                    'code' => '+1 670',
                    'name' => 'Northern Mariana Islands',
                ),
                array (
                    'code' => '+47',
                    'name' => 'Norway',
                ),
                array (
                    'code' => '+968',
                    'name' => 'Oman',
                ),
                array (
                    'code' => '+92',
                    'name' => 'Pakistan',
                ),
                array (
                    'code' => '+680',
                    'name' => 'Palau',
                ),
                array (
                    'code' => '+970',
                    'name' => 'Palestinian Territory',
                ),
                array (
                    'code' => '+507',
                    'name' => 'Panama',
                ),
                array (
                    'code' => '+675',
                    'name' => 'Papua New Guinea',
                ),
                array (
                    'code' => '+595',
                    'name' => 'Paraguay',
                ),
                array (
                    'code' => '+51',
                    'name' => 'Peru',
                ),
                array (
                    'code' => '+63',
                    'name' => 'Philippines',
                ),
                array (
                    'code' => '+48',
                    'name' => 'Poland',
                ),
                array (
                    'code' => '+351',
                    'name' => 'Portugal',
                ),
                array (
                    'code' => '+1 787',
                    'name' => 'Puerto Rico',
                ),
                array (
                    'code' => '+974',
                    'name' => 'Qatar',
                ),
                array (
                    'code' => '+262',
                    'name' => 'Reunion',
                ),
                array (
                    'code' => '+40',
                    'name' => 'Romania',
                ),
                array (
                    'code' => '+7',
                    'name' => 'Russia',
                ),
                array (
                    'code' => '+250',
                    'name' => 'Rwanda',
                ),
                array (
                    'code' => '+685',
                    'name' => 'Samoa',
                ),
                array (
                    'code' => '+378',
                    'name' => 'San Marino',
                ),
                array (
                    'code' => '+966',
                    'name' => 'Saudi Arabia',
                ),
                array (
                    'code' => '+221',
                    'name' => 'Senegal',
                ),
                array (
                    'code' => '+381',
                    'name' => 'Serbia',
                ),
                array (
                    'code' => '+248',
                    'name' => 'Seychelles',
                ),
                array (
                    'code' => '+232',
                    'name' => 'Sierra Leone',
                ),
                array (
                    'code' => '+65',
                    'name' => 'Singapore',
                ),
                array (
                    'code' => '+421',
                    'name' => 'Slovakia',
                ),
                array (
                    'code' => '+386',
                    'name' => 'Slovenia',
                ),
                array (
                    'code' => '+677',
                    'name' => 'Solomon Islands',
                ),
                array (
                    'code' => '+27',
                    'name' => 'South Africa',
                ),
                array (
                    'code' => '+500',
                    'name' => 'South Georgia and the South Sandwich Islands',
                ),
                array (
                    'code' => '+82',
                    'name' => 'South Korea',
                ),
                array (
                    'code' => '+34',
                    'name' => 'Spain',
                ),
                array (
                    'code' => '+94',
                    'name' => 'Sri Lanka',
                ),
                array (
                    'code' => '+249',
                    'name' => 'Sudan',
                ),
                array (
                    'code' => '+597',
                    'name' => 'Suriname',
                ),
                array (
                    'code' => '+268',
                    'name' => 'Swaziland',
                ),
                array (
                    'code' => '+46',
                    'name' => 'Sweden',
                ),
                array (
                    'code' => '+41',
                    'name' => 'Switzerland',
                ),
                array (
                    'code' => '+963',
                    'name' => 'Syria',
                ),
                array (
                    'code' => '+886',
                    'name' => 'Taiwan',
                ),
                array (
                    'code' => '+992',
                    'name' => 'Tajikistan',
                ),
                array (
                    'code' => '+255',
                    'name' => 'Tanzania',
                ),
                array (
                    'code' => '+66',
                    'name' => 'Thailand',
                ),
                array (
                    'code' => '+670',
                    'name' => 'Timor Leste',
                ),
                array (
                    'code' => '+228',
                    'name' => 'Togo',
                ),
                array (
                    'code' => '+690',
                    'name' => 'Tokelau',
                ),
                array (
                    'code' => '+676',
                    'name' => 'Tonga',
                ),
                array (
                    'code' => '+1 868',
                    'name' => 'Trinidad and Tobago',
                ),
                array (
                    'code' => '+216',
                    'name' => 'Tunisia',
                ),
                array (
                    'code' => '+90',
                    'name' => 'Turkey',
                ),
                array (
                    'code' => '+993',
                    'name' => 'Turkmenistan',
                ),
                array (
                    'code' => '+1 649',
                    'name' => 'Turks and Caicos Islands',
                ),
                array (
                    'code' => '+688',
                    'name' => 'Tuvalu',
                ),
                array (
                    'code' => '+1 340',
                    'name' => 'U.S. Virgin Islands',
                ),
                array (
                    'code' => '+256',
                    'name' => 'Uganda',
                ),
                array (
                    'code' => '+380',
                    'name' => 'Ukraine',
                ),
                array (
                    'code' => '+971',
                    'name' => 'United Arab Emirates',
                ),
                array (
                    'code' => '+44',
                    'name' => 'United Kingdom',
                ),
                array (
                    'code' => '+1',
                    'name' => 'United States',
                ),
                array (
                    'code' => '+598',
                    'name' => 'Uruguay',
                ),
                array (
                    'code' => '+998',
                    'name' => 'Uzbekistan',
                ),
                array (
                    'code' => '+678',
                    'name' => 'Vanuatu',
                ),
                array (
                    'code' => '+58',
                    'name' => 'Venezuela',
                ),
                array (
                    'code' => '+84',
                    'name' => 'Vietnam',
                ),
                array (
                    'code' => '+1 808',
                    'name' => 'Wake Island',
                ),
                array (
                    'code' => '+681',
                    'name' => 'Wallis and Futuna',
                ),
                array (
                    'code' => '+967',
                    'name' => 'Yemen',
                ),
                array (
                    'code' => '+260',
                    'name' => 'Zambia',
                ),
                array (
                    'code' => '+255',
                    'name' => 'Zanzibar',
                ),
                array (
                    'code' => '+263',
                    'name' => 'Zimbabwe',
                ),
                    ));
        }

	}
}