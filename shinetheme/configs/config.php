<?php

$config['order_status'] = array(
    'on_hold'        => array(
        'label' => esc_html__('On Holding', 'wp-booking-management-system'),
        'desc'  => esc_html__('Waiting for Payment', 'wp-booking-management-system'),
    ),
    'payment_failed' => array(
        'label' => esc_html__('Failed payment ', 'wp-booking-management-system'),
        'desc'  => esc_html__('Failed payment because of Gateway’s problems or Wrong API data of Gateway', 'wp-booking-management-system'),
    ),
    'completed'      => array(
        'label' => esc_html__('Completed', 'wp-booking-management-system'),
    ),
    'completed_a_part'      => array(
        'label' => esc_html__('Completing a Part', 'wp-booking-management-system'),
        'desc'  => esc_html__('Completing with deposit payment', 'wp-booking-management-system'),
    ),
    'cancelled'      => array(
        'label' => esc_html__('Cancelled', 'wp-booking-management-system'),
        'desc'  => esc_html__('Customer or Admin cancels the booking', 'wp-booking-management-system'),
    ),
    'refunded'       => array(
        'label' => esc_html__('Refunded', 'wp-booking-management-system'),
        'desc'  => esc_html__('Refunded by Admin', 'wp-booking-management-system'),
    ),
    'cancel'          => array(
        'label' => esc_html__('Cancel', 'wp-booking-management-system'),
        'desc'  => esc_html__('Customer or Admin is canceling the booking', 'wp-booking-management-system'),
    ),
);

/**
 * Breakfast Types for Hotel
 *
 * @since 1.0
 * @author dungdt
 */
$config['breakfast_types'] = array(
    'continental'        => esc_html__('Continent', 'wp-booking-management-system'),
    'italian'            => esc_html__('Italian', 'wp-booking-management-system'),
    'full_english_irish' => esc_html__('Full English/Irish', 'wp-booking-management-system'),
    'vegetarian'         => esc_html__('Vegetarian', 'wp-booking-management-system'),
    'vegan'              => esc_html__('Vegan', 'wp-booking-management-system'),
    'Halal'              => esc_html__('Halal', 'wp-booking-management-system'),
    'gluten-free'        => esc_html__('Gluten-free', 'wp-booking-management-system'),
    'kosher'             => esc_html__('Kosher', 'wp-booking-management-system'),
    'asian'              => esc_html__('Asian', 'wp-booking-management-system'),
);

/**
 * Languages spoken by staff
 *
 * @since 1.0
 * @author dungdt
 *
 */
$config['lang_spoken_by_staff'] = array(
    "af" => "Afrikaans",
    "ar" => "Arabic",
    "az" => "Azerbaijani",
    "be" => "Belarusian",
    "bs" => "Bosnian",
    "bg" => "Bulgarian",
    "ca" => "Catalan",
    "zh" => "Chinese",
    "hr" => "Croatian",
    "cs" => "Czech",
    "da" => "Danish",
    "nl" => "Dutch",
    "en" => "English",
    "et" => "Estonian",
    "fa" => "Farsi",
    "tl" => "Filipino",
    "fi" => "Finnish",
    "fr" => "French",
    "ka" => "Georgian",
    "de" => "German",
    "el" => "Greek",
    "ha" => "Hausa",
    "he" => "Hebrew",
    "hi" => "Hindi",
    "hu" => "Hungarian",
    "is" => "Icelandic",
    "id" => "Indonesian",
    "ga" => "Irish",
    "it" => "Italian",
    "ja" => "Japanese",
    "km" => "Khmer",
    "ko" => "Korean",
    "lo" => "Lao",
    "lv" => "Latvian",
    "lt" => "Lithuanian",
    "mk" => "Macedonian",
    "ms" => "Malay",
    "mt" => "Maltese",
    "mo" => "Moldovan",
    "mn" => "Mongolian",
    "no" => "Norwegian",
    "pl" => "Polish",
    "pt" => "Portuguese",
    "ro" => "Romanian",
    "ru" => "Russian",
    "sr" => "Serbian",
    "sk" => "Slovak",
    "sl" => "Slovenian",
    "es" => "Spanish",
    "sw" => "Swahili",
    "sv" => "Swedish",
    "th" => "Thai",
    "tr" => "Turkish",
    "uk" => "Ukrainian",
    "ur" => "Urdu",
    "vi" => "Vietnamese",
    "cy" => "Welsh",
    "xh" => "Xhosa",
    "yo" => "Yoruba",
    "zu" => "Zulu",

);

/**
 * Hotel Smoking policy
 *
 * @since 1.0
 * @author dungdt
 */
$config['smoking_policy'] = array(
    "non-smoking" => esc_html__("Non-smoking", 'wp-booking-management-system'),
    "smoking"     => esc_html__("Smoking", 'wp-booking-management-system'),
    'both'        => esc_html__('I have both smoking and non-smoking options for this type of room', 'wp-booking-management-system')
);

/**
 * Hotel Bed Type
 *
 * @since 1.0
 * @author dungdt
 */
$config['bed_type'] = array(
    "single-bed" => esc_html__("Single bed   /  90-130 cm of width", 'wp-booking-management-system'),
    "double-bed" => esc_html__("Double bed  /  131-150 cm of width", 'wp-booking-management-system'),
    "large-bed" => esc_html__("Large bed (King size) / 151-180 cm of width", 'wp-booking-management-system'),
    "extra-large-bed" => esc_html__("Extra-large double bed (Super-king size) / 181-210 cm of width", 'wp-booking-management-system'),
    "bunk-bed" => esc_html__("Bunk bed / Variable Size", 'wp-booking-management-system'),
    "sofa-bed" => esc_html__("Sofa bed / Variable Size", 'wp-booking-management-system'),
    "futon-mat" => esc_html__("Futon Mat / Variable Size", 'wp-booking-management-system'),
);
