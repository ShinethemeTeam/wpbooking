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
            return apply_filters('wpbooking_get_phone_country_code', array(
                array(
                    'code' => '+93' ,
                    'name' => 'Afghanistan' ,
                    'flag' => 'af'
                ) ,
                array(
                    'code' => '+355' ,
                    'name' => 'Albania' ,
                    'flag' => 'al'
                ) ,
                array(
                    'code' => '+213' ,
                    'name' => 'Algeria' ,
                    'flag' => 'dz'
                ) ,
                array(
                    'code' => '+1 684' ,
                    'name' => 'American Samoa' ,
                    'flag' => 'as'
                ) ,
                array(
                    'code' => '+376' ,
                    'name' => 'Andorra' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+244' ,
                    'name' => 'Angola' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+1 264' ,
                    'name' => 'Anguilla' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+1 268' ,
                    'name' => 'Antigua and Barbuda' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+54' ,
                    'name' => 'Argentina' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+374' ,
                    'name' => 'Armenia' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+297' ,
                    'name' => 'Aruba' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+247' ,
                    'name' => 'Ascension' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+61' ,
                    'name' => 'Australia' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+672' ,
                    'name' => 'Australian External Territories' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+43' ,
                    'name' => 'Austria' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+994' ,
                    'name' => 'Azerbaijan' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+1 242' ,
                    'name' => 'Bahamas' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+973' ,
                    'name' => 'Bahrain' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+880' ,
                    'name' => 'Bangladesh' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+1 246' ,
                    'name' => 'Barbados' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+1 268' ,
                    'name' => 'Barbuda' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+375' ,
                    'name' => 'Belarus' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+32' ,
                    'name' => 'Belgium' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+501' ,
                    'name' => 'Belize' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+229' ,
                    'name' => 'Benin' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+1 441' ,
                    'name' => 'Bermuda' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+975' ,
                    'name' => 'Bhutan' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+591' ,
                    'name' => 'Bolivia' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+387' ,
                    'name' => 'Bosnia and Herzegovina' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+267' ,
                    'name' => 'Botswana' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+55' ,
                    'name' => 'Brazil' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+246' ,
                    'name' => 'British Indian Ocean Territory' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+1 284' ,
                    'name' => 'British Virgin Islands' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+673' ,
                    'name' => 'Brunei' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+359' ,
                    'name' => 'Bulgaria' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+226' ,
                    'name' => 'Burkina Faso' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+257' ,
                    'name' => 'Burundi' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+855' ,
                    'name' => 'Cambodia' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+237' ,
                    'name' => 'Cameroon' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+1' ,
                    'name' => 'Canada' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+238' ,
                    'name' => 'Cape Verde' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+ 345' ,
                    'name' => 'Cayman Islands' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+236' ,
                    'name' => 'Central African Republic' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+235' ,
                    'name' => 'Chad' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+56' ,
                    'name' => 'Chile' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+86' ,
                    'name' => 'China' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+61' ,
                    'name' => 'Christmas Island' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+61' ,
                    'name' => 'Cocos-Keeling Islands' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+57' ,
                    'name' => 'Colombia' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+269' ,
                    'name' => 'Comoros' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+242' ,
                    'name' => 'Congo' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+243' ,
                    'name' => 'Congo, Dem. Rep. of (Zaire)' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+682' ,
                    'name' => 'Cook Islands' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+506' ,
                    'name' => 'Costa Rica' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+385' ,
                    'name' => 'Croatia' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+53' ,
                    'name' => 'Cuba' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+599' ,
                    'name' => 'Curacao' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+537' ,
                    'name' => 'Cyprus' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+420' ,
                    'name' => 'Czech Republic' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+45' ,
                    'name' => 'Denmark' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+246' ,
                    'name' => 'Diego Garcia' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+253' ,
                    'name' => 'Djibouti' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+1 767' ,
                    'name' => 'Dominica' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+1 809' ,
                    'name' => 'Dominican Republic' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+670' ,
                    'name' => 'East Timor' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+56' ,
                    'name' => 'Easter Island' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+593' ,
                    'name' => 'Ecuador' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+20' ,
                    'name' => 'Egypt' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+503' ,
                    'name' => 'El Salvador' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+240' ,
                    'name' => 'Equatorial Guinea' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+291' ,
                    'name' => 'Eritrea' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+372' ,
                    'name' => 'Estonia' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+251' ,
                    'name' => 'Ethiopia' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+500' ,
                    'name' => 'Falkland Islands' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+298' ,
                    'name' => 'Faroe Islands' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+679' ,
                    'name' => 'Fiji' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+358' ,
                    'name' => 'Finland' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+33' ,
                    'name' => 'France' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+596' ,
                    'name' => 'French Antilles' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+594' ,
                    'name' => 'French Guiana' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+689' ,
                    'name' => 'French Polynesia' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+241' ,
                    'name' => 'Gabon' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+220' ,
                    'name' => 'Gambia' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+995' ,
                    'name' => 'Georgia' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+49' ,
                    'name' => 'Germany' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+233' ,
                    'name' => 'Ghana' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+350' ,
                    'name' => 'Gibraltar' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+30' ,
                    'name' => 'Greece' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+299' ,
                    'name' => 'Greenland' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+1 473' ,
                    'name' => 'Grenada' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+590' ,
                    'name' => 'Guadeloupe' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+1 671' ,
                    'name' => 'Guam' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+502' ,
                    'name' => 'Guatemala' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+224' ,
                    'name' => 'Guinea' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+245' ,
                    'name' => 'Guinea-Bissau' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+595' ,
                    'name' => 'Guyana' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+509' ,
                    'name' => 'Haiti' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+504' ,
                    'name' => 'Honduras' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+852' ,
                    'name' => 'Hong Kong SAR China' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+36' ,
                    'name' => 'Hungary' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+354' ,
                    'name' => 'Iceland' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+91' ,
                    'name' => 'India' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+62' ,
                    'name' => 'Indonesia' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+98' ,
                    'name' => 'Iran' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+964' ,
                    'name' => 'Iraq' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+353' ,
                    'name' => 'Ireland' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+972' ,
                    'name' => 'Israel' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+39' ,
                    'name' => 'Italy' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+225' ,
                    'name' => 'Ivory Coast' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+1 876' ,
                    'name' => 'Jamaica' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+81' ,
                    'name' => 'Japan' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+962' ,
                    'name' => 'Jordan' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+7 7' ,
                    'name' => 'Kazakhstan' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+254' ,
                    'name' => 'Kenya' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+686' ,
                    'name' => 'Kiribati' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+965' ,
                    'name' => 'Kuwait' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+996' ,
                    'name' => 'Kyrgyzstan' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+856' ,
                    'name' => 'Laos' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+371' ,
                    'name' => 'Latvia' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+961' ,
                    'name' => 'Lebanon' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+266' ,
                    'name' => 'Lesotho' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+231' ,
                    'name' => 'Liberia' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+218' ,
                    'name' => 'Libya' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+423' ,
                    'name' => 'Liechtenstein' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+370' ,
                    'name' => 'Lithuania' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+352' ,
                    'name' => 'Luxembourg' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+853' ,
                    'name' => 'Macau SAR China' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+389' ,
                    'name' => 'Macedonia' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+261' ,
                    'name' => 'Madagascar' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+265' ,
                    'name' => 'Malawi' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+60' ,
                    'name' => 'Malaysia' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+960' ,
                    'name' => 'Maldives' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+223' ,
                    'name' => 'Mali' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+356' ,
                    'name' => 'Malta' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+692' ,
                    'name' => 'Marshall Islands' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+596' ,
                    'name' => 'Martinique' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+222' ,
                    'name' => 'Mauritania' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+230' ,
                    'name' => 'Mauritius' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+262' ,
                    'name' => 'Mayotte' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+52' ,
                    'name' => 'Mexico' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+691' ,
                    'name' => 'Micronesia' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+1 808' ,
                    'name' => 'Midway Island' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+373' ,
                    'name' => 'Moldova' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+377' ,
                    'name' => 'Monaco' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+976' ,
                    'name' => 'Mongolia' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+382' ,
                    'name' => 'Montenegro' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+1664' ,
                    'name' => 'Montserrat' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+212' ,
                    'name' => 'Morocco' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+95' ,
                    'name' => 'Myanmar' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+264' ,
                    'name' => 'Namibia' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+674' ,
                    'name' => 'Nauru' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+977' ,
                    'name' => 'Nepal' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+31' ,
                    'name' => 'Netherlands' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+599' ,
                    'name' => 'Netherlands Antilles' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+1 869' ,
                    'name' => 'Nevis' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+687' ,
                    'name' => 'New Caledonia' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+64' ,
                    'name' => 'New Zealand' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+505' ,
                    'name' => 'Nicaragua' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+227' ,
                    'name' => 'Niger' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+234' ,
                    'name' => 'Nigeria' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+683' ,
                    'name' => 'Niue' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+672' ,
                    'name' => 'Norfolk Island' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+850' ,
                    'name' => 'North Korea' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+1 670' ,
                    'name' => 'Northern Mariana Islands' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+47' ,
                    'name' => 'Norway' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+968' ,
                    'name' => 'Oman' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+92' ,
                    'name' => 'Pakistan' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+680' ,
                    'name' => 'Palau' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+970' ,
                    'name' => 'Palestinian Territory' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+507' ,
                    'name' => 'Panama' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+675' ,
                    'name' => 'Papua New Guinea' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+595' ,
                    'name' => 'Paraguay' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+51' ,
                    'name' => 'Peru' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+63' ,
                    'name' => 'Philippines' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+48' ,
                    'name' => 'Poland' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+351' ,
                    'name' => 'Portugal' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+1 787' ,
                    'name' => 'Puerto Rico' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+974' ,
                    'name' => 'Qatar' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+262' ,
                    'name' => 'Reunion' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+40' ,
                    'name' => 'Romania' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+7' ,
                    'name' => 'Russia' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+250' ,
                    'name' => 'Rwanda' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+685' ,
                    'name' => 'Samoa' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+378' ,
                    'name' => 'San Marino' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+966' ,
                    'name' => 'Saudi Arabia' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+221' ,
                    'name' => 'Senegal' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+381' ,
                    'name' => 'Serbia' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+248' ,
                    'name' => 'Seychelles' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+232' ,
                    'name' => 'Sierra Leone' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+65' ,
                    'name' => 'Singapore' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+421' ,
                    'name' => 'Slovakia' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+386' ,
                    'name' => 'Slovenia' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+677' ,
                    'name' => 'Solomon Islands' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+27' ,
                    'name' => 'South Africa' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+500' ,
                    'name' => 'South Georgia and the South Sandwich Islands' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+82' ,
                    'name' => 'South Korea' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+34' ,
                    'name' => 'Spain' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+94' ,
                    'name' => 'Sri Lanka' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+249' ,
                    'name' => 'Sudan' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+597' ,
                    'name' => 'Suriname' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+268' ,
                    'name' => 'Swaziland' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+46' ,
                    'name' => 'Sweden' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+41' ,
                    'name' => 'Switzerland' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+963' ,
                    'name' => 'Syria' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+886' ,
                    'name' => 'Taiwan' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+992' ,
                    'name' => 'Tajikistan' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+255' ,
                    'name' => 'Tanzania' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+66' ,
                    'name' => 'Thailand' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+670' ,
                    'name' => 'Timor Leste' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+228' ,
                    'name' => 'Togo' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+690' ,
                    'name' => 'Tokelau' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+676' ,
                    'name' => 'Tonga' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+1 868' ,
                    'name' => 'Trinidad and Tobago' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+216' ,
                    'name' => 'Tunisia' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+90' ,
                    'name' => 'Turkey' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+993' ,
                    'name' => 'Turkmenistan' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+1 649' ,
                    'name' => 'Turks and Caicos Islands' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+688' ,
                    'name' => 'Tuvalu' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+1 340' ,
                    'name' => 'U.S. Virgin Islands' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+256' ,
                    'name' => 'Uganda' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+380' ,
                    'name' => 'Ukraine' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+971' ,
                    'name' => 'United Arab Emirates' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+44' ,
                    'name' => 'United Kingdom' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+1' ,
                    'name' => 'United States' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+598' ,
                    'name' => 'Uruguay' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+998' ,
                    'name' => 'Uzbekistan' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+678' ,
                    'name' => 'Vanuatu' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+58' ,
                    'name' => 'Venezuela' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+84' ,
                    'name' => 'Vietnam' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+1 808' ,
                    'name' => 'Wake Island' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+681' ,
                    'name' => 'Wallis and Futuna' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+967' ,
                    'name' => 'Yemen' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+260' ,
                    'name' => 'Zambia' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+255' ,
                    'name' => 'Zanzibar' ,
                    'flag' => ''
                ) ,
                array(
                    'code' => '+263' ,
                    'name' => 'Zimbabwe' ,
                    'flag' => ''
                ) ,
            ) );
        }

	}
}