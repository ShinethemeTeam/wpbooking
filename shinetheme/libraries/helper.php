<?php
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
                    'flag' => 'ad'
                ) ,
                array(
                    'code' => '+244' ,
                    'name' => 'Angola' ,
                    'flag' => 'ao'
                ) ,
                array(
                    'code' => '+1 264' ,
                    'name' => 'Anguilla' ,
                    'flag' => 'ai'
                ) ,
                array(
                    'code' => '+1 268' ,
                    'name' => 'Antigua and Barbuda' ,
                    'flag' => 'ag'
                ) ,
                array(
                    'code' => '+54' ,
                    'name' => 'Argentina' ,
                    'flag' => 'ar'
                ) ,
                array(
                    'code' => '+374' ,
                    'name' => 'Armenia' ,
                    'flag' => 'am'
                ) ,
                array(
                    'code' => '+297' ,
                    'name' => 'Aruba' ,
                    'flag' => 'aw'
                ) ,
                array(
                    'code' => '+247' ,
                    'name' => 'Ascension' ,
                    'flag' => 'sh'
                ) ,
                array(
                    'code' => '+61' ,
                    'name' => 'Australia' ,
                    'flag' => 'au'
                ) ,
                array(
                    'code' => '+43' ,
                    'name' => 'Austria' ,
                    'flag' => 'at'
                ) ,
                array(
                    'code' => '+994' ,
                    'name' => 'Azerbaijan' ,
                    'flag' => 'az'
                ) ,
                array(
                    'code' => '+1 242' ,
                    'name' => 'Bahamas' ,
                    'flag' => 'bs'
                ) ,
                array(
                    'code' => '+973' ,
                    'name' => 'Bahrain' ,
                    'flag' => 'bh'
                ) ,
                array(
                    'code' => '+880' ,
                    'name' => 'Bangladesh' ,
                    'flag' => 'bd'
                ) ,
                array(
                    'code' => '+1 246' ,
                    'name' => 'Barbados' ,
                    'flag' => 'bb'
                ) ,
                array(
                    'code' => '+1 268' ,
                    'name' => 'Barbuda' ,
                    'flag' => 'ag'
                ) ,
                array(
                    'code' => '+375' ,
                    'name' => 'Belarus' ,
                    'flag' => 'by'
                ) ,
                array(
                    'code' => '+32' ,
                    'name' => 'Belgium' ,
                    'flag' => 'be'
                ) ,
                array(
                    'code' => '+501' ,
                    'name' => 'Belize' ,
                    'flag' => 'bz'
                ) ,
                array(
                    'code' => '+229' ,
                    'name' => 'Benin' ,
                    'flag' => 'bj'
                ) ,
                array(
                    'code' => '+1 441' ,
                    'name' => 'Bermuda' ,
                    'flag' => 'bm'
                ) ,
                array(
                    'code' => '+975' ,
                    'name' => 'Bhutan' ,
                    'flag' => 'bm'
                ) ,
                array(
                    'code' => '+591' ,
                    'name' => 'Bolivia' ,
                    'flag' => 'bo'
                ) ,
                array(
                    'code' => '+387' ,
                    'name' => 'Bosnia and Herzegovina' ,
                    'flag' => 'ba'
                ) ,
                array(
                    'code' => '+267' ,
                    'name' => 'Botswana' ,
                    'flag' => 'bw'
                ) ,
                array(
                    'code' => '+55' ,
                    'name' => 'Brazil' ,
                    'flag' => 'bw'
                ) ,
                array(
                    'code' => '+246' ,
                    'name' => 'British Indian Ocean Territory' ,
                    'flag' => 'io'
                ) ,
                array(
                    'code' => '+359' ,
                    'name' => 'Bulgaria' ,
                    'flag' => 'bn'
                ) ,
                array(
                    'code' => '+226' ,
                    'name' => 'Burkina Faso' ,
                    'flag' => 'bf'
                ) ,
                array(
                    'code' => '+257' ,
                    'name' => 'Burundi' ,
                    'flag' => 'bl'
                ) ,
                array(
                    'code' => '+855' ,
                    'name' => 'Cambodia' ,
                    'flag' => 'kh'
                ) ,
                array(
                    'code' => '+237' ,
                    'name' => 'Cameroon' ,
                    'flag' => 'cm'
                ) ,
                array(
                    'code' => '+1' ,
                    'name' => 'Canada' ,
                    'flag' => 'ca'
                ) ,
                array(
                    'code' => '+ 345' ,
                    'name' => 'Cayman Islands' ,
                    'flag' => 'ky'
                ) ,
                array(
                    'code' => '+236' ,
                    'name' => 'Central African Republic' ,
                    'flag' => 'cf'
                ) ,
                array(
                    'code' => '+235' ,
                    'name' => 'Chad' ,
                    'flag' => 'td'
                ) ,
                array(
                    'code' => '+56' ,
                    'name' => 'Chile' ,
                    'flag' => 'cl'
                ) ,
                array(
                    'code' => '+86' ,
                    'name' => 'China' ,
                    'flag' => 'cn'
                ) ,
                array(
                    'code' => '+61' ,
                    'name' => 'Christmas Island' ,
                    'flag' => 'cx'
                ) ,
                array(
                    'code' => '+61' ,
                    'name' => 'Cocos-Keeling Islands' ,
                    'flag' => 'cc'
                ) ,
                array(
                    'code' => '+57' ,
                    'name' => 'Colombia' ,
                    'flag' => 'co'
                ) ,
                array(
                    'code' => '+269' ,
                    'name' => 'Comoros' ,
                    'flag' => 'km'
                ) ,
                array(
                    'code' => '+242' ,
                    'name' => 'Congo' ,
                    'flag' => 'cg'
                ) ,
                array(
                    'code' => '+243' ,
                    'name' => 'Congo, Dem. Rep. of (Zaire)' ,
                    'flag' => 'cd'
                ) ,
                array(
                    'code' => '+682' ,
                    'name' => 'Cook Islands' ,
                    'flag' => 'ck'
                ) ,
                array(
                    'code' => '+506' ,
                    'name' => 'Costa Rica' ,
                    'flag' => 'cr'
                ) ,
                array(
                    'code' => '+385' ,
                    'name' => 'Croatia' ,
                    'flag' => 'hr'
                ) ,
                array(
                    'code' => '+53' ,
                    'name' => 'Cuba' ,
                    'flag' => 'cu'
                ) ,
                array(
                    'code' => '+599' ,
                    'name' => 'Curacao' ,
                    'flag' => 'cw'
                ) ,
                array(
                    'code' => '+537' ,
                    'name' => 'Cyprus' ,
                    'flag' => 'cy'
                ) ,
                array(
                    'code' => '+420' ,
                    'name' => 'Czech Republic' ,
                    'flag' => 'cz'
                ) ,
                array(
                    'code' => '+45' ,
                    'name' => 'Denmark' ,
                    'flag' => 'dk'
                ) ,
                array(
                    'code' => '+253' ,
                    'name' => 'Djibouti' ,
                    'flag' => 'dj'
                ) ,
                array(
                    'code' => '+1 767' ,
                    'name' => 'Dominica' ,
                    'flag' => 'dm'
                ) ,
                array(
                    'code' => '+1 809' ,
                    'name' => 'Dominican Republic' ,
                    'flag' => 'do'
                ) ,
                array(
                    'code' => '+593' ,
                    'name' => 'Ecuador' ,
                    'flag' => 'ec'
                ) ,
                array(
                    'code' => '+20' ,
                    'name' => 'Egypt' ,
                    'flag' => 'eg'
                ) ,
                array(
                    'code' => '+503' ,
                    'name' => 'El Salvador' ,
                    'flag' => 'sv'
                ) ,
                array(
                    'code' => '+240' ,
                    'name' => 'Equatorial Guinea' ,
                    'flag' => 'gq'
                ) ,
                array(
                    'code' => '+291' ,
                    'name' => 'Eritrea' ,
                    'flag' => 'er'
                ) ,
                array(
                    'code' => '+372' ,
                    'name' => 'Estonia' ,
                    'flag' => 'ee'
                ) ,
                array(
                    'code' => '+251' ,
                    'name' => 'Ethiopia' ,
                    'flag' => 'et'
                ) ,
                array(
                    'code' => '+500' ,
                    'name' => 'Falkland Islands' ,
                    'flag' => 'fk'
                ) ,
                array(
                    'code' => '+298' ,
                    'name' => 'Faroe Islands' ,
                    'flag' => 'fo'
                ) ,
                array(
                    'code' => '+679' ,
                    'name' => 'Fiji' ,
                    'flag' => 'fj'
                ) ,
                array(
                    'code' => '+358' ,
                    'name' => 'Finland' ,
                    'flag' => 'fi'
                ) ,
                array(
                    'code' => '+33' ,
                    'name' => 'France' ,
                    'flag' => 'fr'
                ) ,
                array(
                    'code' => '+594' ,
                    'name' => 'French Guiana' ,
                    'flag' => 'gf'
                ) ,
                array(
                    'code' => '+689' ,
                    'name' => 'French Polynesia' ,
                    'flag' => 'pf'
                ) ,
                array(
                    'code' => '+241' ,
                    'name' => 'Gabon' ,
                    'flag' => 'ga'
                ) ,
                array(
                    'code' => '+220' ,
                    'name' => 'Gambia' ,
                    'flag' => 'gm'
                ) ,
                array(
                    'code' => '+995' ,
                    'name' => 'Georgia' ,
                    'flag' => 'ge'
                ) ,
                array(
                    'code' => '+49' ,
                    'name' => 'Germany' ,
                    'flag' => 'de'
                ) ,
                array(
                    'code' => '+233' ,
                    'name' => 'Ghana' ,
                    'flag' => 'gh'
                ) ,
                array(
                    'code' => '+350' ,
                    'name' => 'Gibraltar' ,
                    'flag' => 'gi'
                ) ,
                array(
                    'code' => '+30' ,
                    'name' => 'Greece' ,
                    'flag' => 'gr'
                ) ,
                array(
                    'code' => '+299' ,
                    'name' => 'Greenland' ,
                    'flag' => 'gl'
                ) ,
                array(
                    'code' => '+1 473' ,
                    'name' => 'Grenada' ,
                    'flag' => 'gd'
                ) ,
                array(
                    'code' => '+590' ,
                    'name' => 'Guadeloupe' ,
                    'flag' => 'gp'
                ) ,
                array(
                    'code' => '+1 671' ,
                    'name' => 'Guam' ,
                    'flag' => 'gu'
                ) ,
                array(
                    'code' => '+502' ,
                    'name' => 'Guatemala' ,
                    'flag' => 'gt'
                ) ,
                array(
                    'code' => '+224' ,
                    'name' => 'Guinea' ,
                    'flag' => 'gn'
                ) ,
                array(
                    'code' => '+245' ,
                    'name' => 'Guinea-Bissau' ,
                    'flag' => 'gw'
                ) ,
                array(
                    'code' => '+595' ,
                    'name' => 'Guyana' ,
                    'flag' => 'gy'
                ) ,
                array(
                    'code' => '+509' ,
                    'name' => 'Haiti' ,
                    'flag' => 'ht'
                ) ,
                array(
                    'code' => '+504' ,
                    'name' => 'Honduras' ,
                    'flag' => 'hn'
                ) ,
                array(
                    'code' => '+852' ,
                    'name' => 'Hong Kong SAR China' ,
                    'flag' => 'hk'
                ) ,
                array(
                    'code' => '+36' ,
                    'name' => 'Hungary' ,
                    'flag' => 'hu'
                ) ,
                array(
                    'code' => '+354' ,
                    'name' => 'Iceland' ,
                    'flag' => 'is'
                ) ,
                array(
                    'code' => '+91' ,
                    'name' => 'India' ,
                    'flag' => 'in'
                ) ,
                array(
                    'code' => '+62' ,
                    'name' => 'Indonesia' ,
                    'flag' => 'id'
                ) ,
                array(
                    'code' => '+98' ,
                    'name' => 'Iran' ,
                    'flag' => 'ir'
                ) ,
                array(
                    'code' => '+964' ,
                    'name' => 'Iraq' ,
                    'flag' => 'iq'
                ) ,
                array(
                    'code' => '+353' ,
                    'name' => 'Ireland' ,
                    'flag' => 'ie'
                ) ,
                array(
                    'code' => '+972' ,
                    'name' => 'Israel' ,
                    'flag' => 'il'
                ) ,
                array(
                    'code' => '+39' ,
                    'name' => 'Italy' ,
                    'flag' => 'it'
                ) ,
                array(
                    'code' => '+1 876' ,
                    'name' => 'Jamaica' ,
                    'flag' => 'jm'
                ) ,
                array(
                    'code' => '+81' ,
                    'name' => 'Japan' ,
                    'flag' => 'jp'
                ) ,
                array(
                    'code' => '+962' ,
                    'name' => 'Jordan' ,
                    'flag' => 'jo'
                ) ,
                array(
                    'code' => '+7 7' ,
                    'name' => 'Kazakhstan' ,
                    'flag' => 'kz'
                ) ,
                array(
                    'code' => '+254' ,
                    'name' => 'Kenya' ,
                    'flag' => 'ke'
                ) ,
                array(
                    'code' => '+686' ,
                    'name' => 'Kiribati' ,
                    'flag' => 'ki'
                ) ,
                array(
                    'code' => '+965' ,
                    'name' => 'Kuwait' ,
                    'flag' => 'kw'
                ) ,
                array(
                    'code' => '+996' ,
                    'name' => 'Kyrgyzstan' ,
                    'flag' => 'kg'
                ) ,
                array(
                    'code' => '+856' ,
                    'name' => 'Laos' ,
                    'flag' => 'la'
                ) ,
                array(
                    'code' => '+371' ,
                    'name' => 'Latvia' ,
                    'flag' => 'lv'
                ) ,
                array(
                    'code' => '+961' ,
                    'name' => 'Lebanon' ,
                    'flag' => 'lb'
                ) ,
                array(
                    'code' => '+266' ,
                    'name' => 'Lesotho' ,
                    'flag' => 'ls'
                ) ,
                array(
                    'code' => '+231' ,
                    'name' => 'Liberia' ,
                    'flag' => 'lr'
                ) ,
                array(
                    'code' => '+218' ,
                    'name' => 'Libya' ,
                    'flag' => 'ly'
                ) ,
                array(
                    'code' => '+423' ,
                    'name' => 'Liechtenstein' ,
                    'flag' => 'li'
                ) ,
                array(
                    'code' => '+370' ,
                    'name' => 'Lithuania' ,
                    'flag' => 'lt'
                ) ,
                array(
                    'code' => '+352' ,
                    'name' => 'Luxembourg' ,
                    'flag' => 'lu'
                ) ,
                array(
                    'code' => '+853' ,
                    'name' => 'Macao SAR China' ,
                    'flag' => 'mo'
                ) ,
                array(
                    'code' => '+389' ,
                    'name' => 'Macedonia' ,
                    'flag' => 'mk'
                ) ,
                array(
                    'code' => '+261' ,
                    'name' => 'Madagascar' ,
                    'flag' => 'mg'
                ) ,
                array(
                    'code' => '+265' ,
                    'name' => 'Malawi' ,
                    'flag' => 'mw'
                ) ,
                array(
                    'code' => '+60' ,
                    'name' => 'Malaysia' ,
                    'flag' => 'my'
                ) ,
                array(
                    'code' => '+960' ,
                    'name' => 'Maldives' ,
                    'flag' => 'mv'
                ) ,
                array(
                    'code' => '+223' ,
                    'name' => 'Mali' ,
                    'flag' => 'ml'
                ) ,
                array(
                    'code' => '+356' ,
                    'name' => 'Malta' ,
                    'flag' => 'mt'
                ) ,
                array(
                    'code' => '+692' ,
                    'name' => 'Marshall Islands' ,
                    'flag' => 'mh'
                ) ,
                array(
                    'code' => '+596' ,
                    'name' => 'Martinique' ,
                    'flag' => 'mq'
                ) ,
                array(
                    'code' => '+222' ,
                    'name' => 'Mauritania' ,
                    'flag' => 'mr'
                ) ,
                array(
                    'code' => '+230' ,
                    'name' => 'Mauritius' ,
                    'flag' => 'mu'
                ) ,
                array(
                    'code' => '+262' ,
                    'name' => 'Mayotte' ,
                    'flag' => 'yt'
                ) ,
                array(
                    'code' => '+52' ,
                    'name' => 'Mexico' ,
                    'flag' => 'mx'
                ) ,
                array(
                    'code' => '+691' ,
                    'name' => 'Micronesia' ,
                    'flag' => 'fm'
                ) ,
                array(
                    'code' => '+373' ,
                    'name' => 'Moldova' ,
                    'flag' => 'md'
                ) ,
                array(
                    'code' => '+377' ,
                    'name' => 'Monaco' ,
                    'flag' => 'mc'
                ) ,
                array(
                    'code' => '+976' ,
                    'name' => 'Mongolia' ,
                    'flag' => 'mn'
                ) ,
                array(
                    'code' => '+382' ,
                    'name' => 'Montenegro' ,
                    'flag' => 'me'
                ) ,
                array(
                    'code' => '+1664' ,
                    'name' => 'Montserrat' ,
                    'flag' => 'ms'
                ) ,
                array(
                    'code' => '+212' ,
                    'name' => 'Morocco' ,
                    'flag' => 'ma'
                ) ,
                array(
                    'code' => '+95' ,
                    'name' => 'Myanmar' ,
                    'flag' => 'mm'
                ) ,
                array(
                    'code' => '+264' ,
                    'name' => 'Namibia' ,
                    'flag' => 'na'
                ) ,
                array(
                    'code' => '+674' ,
                    'name' => 'Nauru' ,
                    'flag' => 'nr'
                ) ,
                array(
                    'code' => '+977' ,
                    'name' => 'Nepal' ,
                    'flag' => 'np'
                ) ,
                array(
                    'code' => '+31' ,
                    'name' => 'Netherlands' ,
                    'flag' => 'nl'
                ) ,
                array(
                    'code' => '+687' ,
                    'name' => 'New Caledonia' ,
                    'flag' => 'nc'
                ) ,
                array(
                    'code' => '+64' ,
                    'name' => 'New Zealand' ,
                    'flag' => 'nz'
                ) ,
                array(
                    'code' => '+505' ,
                    'name' => 'Nicaragua' ,
                    'flag' => 'ni'
                ) ,
                array(
                    'code' => '+227' ,
                    'name' => 'Niger' ,
                    'flag' => 'ne'
                ) ,
                array(
                    'code' => '+234' ,
                    'name' => 'Nigeria' ,
                    'flag' => 'ng'
                ) ,
                array(
                    'code' => '+683' ,
                    'name' => 'Niue' ,
                    'flag' => 'nu'
                ) ,
                array(
                    'code' => '+672' ,
                    'name' => 'Norfolk Island' ,
                    'flag' => 'nf'
                ) ,
                array(
                    'code' => '+850' ,
                    'name' => 'North Korea' ,
                    'flag' => 'ko'
                ) ,
                array(
                    'code' => '+1 670' ,
                    'name' => 'Northern Mariana Islands' ,
                    'flag' => 'mp'
                ) ,
                array(
                    'code' => '+47' ,
                    'name' => 'Norway' ,
                    'flag' => 'no'
                ) ,
                array(
                    'code' => '+968' ,
                    'name' => 'Oman' ,
                    'flag' => 'om'
                ) ,
                array(
                    'code' => '+92' ,
                    'name' => 'Pakistan' ,
                    'flag' => 'pk'
                ) ,
                array(
                    'code' => '+680' ,
                    'name' => 'Palau' ,
                    'flag' => 'pw'
                ) ,
                array(
                    'code' => '+507' ,
                    'name' => 'Panama' ,
                    'flag' => 'pa'
                ) ,
                array(
                    'code' => '+675' ,
                    'name' => 'Papua New Guinea' ,
                    'flag' => 'pg'
                ) ,
                array(
                    'code' => '+595' ,
                    'name' => 'Paraguay' ,
                    'flag' => 'py'
                ) ,
                array(
                    'code' => '+51' ,
                    'name' => 'Peru' ,
                    'flag' => 'pe'
                ) ,
                array(
                    'code' => '+63' ,
                    'name' => 'Philippines' ,
                    'flag' => 'ph'
                ) ,
                array(
                    'code' => '+48' ,
                    'name' => 'Poland' ,
                    'flag' => 'pl'
                ) ,
                array(
                    'code' => '+351' ,
                    'name' => 'Portugal' ,
                    'flag' => 'pt'
                ) ,
                array(
                    'code' => '+1 787' ,
                    'name' => 'Puerto Rico' ,
                    'flag' => 'pr'
                ) ,
                array(
                    'code' => '+974' ,
                    'name' => 'Qatar' ,
                    'flag' => 'qa'
                ) ,
                array(
                    'code' => '+262' ,
                    'name' => 'Reunion' ,
                    'flag' => 're'
                ) ,
                array(
                    'code' => '+40' ,
                    'name' => 'Romania' ,
                    'flag' => 'ro'
                ) ,
                array(
                    'code' => '+7' ,
                    'name' => 'Russia' ,
                    'flag' => 'ru'
                ) ,
                array(
                    'code' => '+250' ,
                    'name' => 'Rwanda' ,
                    'flag' => 'rw'
                ) ,
                array(
                    'code' => '+685' ,
                    'name' => 'Samoa' ,
                    'flag' => 'ws'
                ) ,
                array(
                    'code' => '+378' ,
                    'name' => 'San Marino' ,
                    'flag' => 'sm'
                ) ,
                array(
                    'code' => '+966' ,
                    'name' => 'Saudi Arabia' ,
                    'flag' => 'sa'
                ) ,
                array(
                    'code' => '+221' ,
                    'name' => 'Senegal' ,
                    'flag' => 'sn'
                ) ,
                array(
                    'code' => '+381' ,
                    'name' => 'Serbia' ,
                    'flag' => 'rs'
                ) ,
                array(
                    'code' => '+248' ,
                    'name' => 'Seychelles' ,
                    'flag' => 'sc'
                ) ,
                array(
                    'code' => '+232' ,
                    'name' => 'Sierra Leone' ,
                    'flag' => 'sl'
                ) ,
                array(
                    'code' => '+65' ,
                    'name' => 'Singapore' ,
                    'flag' => 'sg'
                ) ,
                array(
                    'code' => '+421' ,
                    'name' => 'Slovakia' ,
                    'flag' => 'sk'
                ) ,
                array(
                    'code' => '+386' ,
                    'name' => 'Slovenia' ,
                    'flag' => 'si'
                ) ,
                array(
                    'code' => '+677' ,
                    'name' => 'Solomon Islands' ,
                    'flag' => 'sb'
                ) ,
                array(
                    'code' => '+27' ,
                    'name' => 'South Africa' ,
                    'flag' => 'za'
                ) ,
                array(
                    'code' => '+500' ,
                    'name' => 'South Georgia and the South Sandwich Islands' ,
                    'flag' => 'gs'
                ) ,
                array(
                    'code' => '+82' ,
                    'name' => 'South Korea' ,
                    'flag' => 'kr'
                ) ,
                array(
                    'code' => '+34' ,
                    'name' => 'Spain' ,
                    'flag' => 'es'
                ) ,
                array(
                    'code' => '+94' ,
                    'name' => 'Sri Lanka' ,
                    'flag' => 'lk'
                ) ,
                array(
                    'code' => '+249' ,
                    'name' => 'Sudan' ,
                    'flag' => 'sd'
                ) ,
                array(
                    'code' => '+597' ,
                    'name' => 'Suriname' ,
                    'flag' => 'sr'
                ) ,
                array(
                    'code' => '+268' ,
                    'name' => 'Swaziland' ,
                    'flag' => 'sz'
                ) ,
                array(
                    'code' => '+46' ,
                    'name' => 'Sweden' ,
                    'flag' => 'se'
                ) ,
                array(
                    'code' => '+41' ,
                    'name' => 'Switzerland' ,
                    'flag' => 'ch'
                ) ,
                array(
                    'code' => '+963' ,
                    'name' => 'Syria' ,
                    'flag' => 'sy'
                ) ,
                array(
                    'code' => '+886' ,
                    'name' => 'Taiwan' ,
                    'flag' => 'tw'
                ) ,
                array(
                    'code' => '+992' ,
                    'name' => 'Tajikistan' ,
                    'flag' => 'tj'
                ) ,
                array(
                    'code' => '+255' ,
                    'name' => 'Tanzania' ,
                    'flag' => 'tz'
                ) ,
                array(
                    'code' => '+66' ,
                    'name' => 'Thailand' ,
                    'flag' => 'th'
                ) ,
                array(
                    'code' => '+670' ,
                    'name' => 'Timor Leste' ,
                    'flag' => 'tl'
                ) ,
                array(
                    'code' => '+228' ,
                    'name' => 'Togo' ,
                    'flag' => 'tg'
                ) ,
                array(
                    'code' => '+690' ,
                    'name' => 'Tokelau' ,
                    'flag' => 'tk'
                ) ,
                array(
                    'code' => '+676' ,
                    'name' => 'Tonga' ,
                    'flag' => 'to'
                ) ,
                array(
                    'code' => '+1 868' ,
                    'name' => 'Trinidad and Tobago' ,
                    'flag' => 'tt'
                ) ,
                array(
                    'code' => '+216' ,
                    'name' => 'Tunisia' ,
                    'flag' => 'tn'
                ) ,
                array(
                    'code' => '+90' ,
                    'name' => 'Turkey' ,
                    'flag' => 'tr'
                ) ,
                array(
                    'code' => '+993' ,
                    'name' => 'Turkmenistan' ,
                    'flag' => 'tm'
                ) ,
                array(
                    'code' => '+1 649' ,
                    'name' => 'Turks and Caicos Islands' ,
                    'flag' => 'tc'
                ) ,
                array(
                    'code' => '+688' ,
                    'name' => 'Tuvalu' ,
                    'flag' => 'tv'
                ) ,
                array(
                    'code' => '+1 340' ,
                    'name' => 'U.S. Virgin Islands' ,
                    'flag' => 'vi'
                ) ,
                array(
                    'code' => '+256' ,
                    'name' => 'Uganda' ,
                    'flag' => 'ug'
                ) ,
                array(
                    'code' => '+380' ,
                    'name' => 'Ukraine' ,
                    'flag' => 'ua'
                ) ,
                array(
                    'code' => '+971' ,
                    'name' => 'United Arab Emirates' ,
                    'flag' => 'ae'
                ) ,
                array(
                    'code' => '+44' ,
                    'name' => 'United Kingdom' ,
                    'flag' => 'gb'
                ) ,
                array(
                    'code' => '+1' ,
                    'name' => 'United States' ,
                    'flag' => 'us'
                ) ,
                array(
                    'code' => '+598' ,
                    'name' => 'Uruguay' ,
                    'flag' => 'uy'
                ) ,
                array(
                    'code' => '+998' ,
                    'name' => 'Uzbekistan' ,
                    'flag' => 'uz'
                ) ,
                array(
                    'code' => '+678' ,
                    'name' => 'Vanuatu' ,
                    'flag' => 'vu'
                ) ,
                array(
                    'code' => '+58' ,
                    'name' => 'Venezuela' ,
                    'flag' => 've'
                ) ,
                array(
                    'code' => '+84' ,
                    'name' => 'Vietnam' ,
                    'flag' => 'vn'
                ) ,
                array(
                    'code' => '+681' ,
                    'name' => 'Wallis and Futuna' ,
                    'flag' => 'wf'
                ) ,
                array(
                    'code' => '+967' ,
                    'name' => 'Yemen' ,
                    'flag' => 'ye'
                ) ,
                array(
                    'code' => '+260' ,
                    'name' => 'Zambia' ,
                    'flag' => 'zm'
                ) ,
                array(
                    'code' => '+263' ,
                    'name' => 'Zimbabwe' ,
                    'flag' => 'zw'
                ) ,
            ) );
        }

	}
}