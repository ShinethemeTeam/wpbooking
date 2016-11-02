<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 3/16/2016
 * Time: 5:07 PM
 */

$config['order_status'] = array(
    'on_hold'        => array(
        'label' => esc_html__('On Hold', 'wpbooking'),
        'desc'  => esc_html__('Waiting for Payment', 'wpbooking'),
    ),
    'payment_failed' => array(
        'label' => esc_html__('Payment Failed', 'wpbooking'),
        'desc'  => esc_html__('Payment Failed because of Gateway Problem or Wrong API data of Gateway', 'wpbooking'),
    ),
    'completed'      => array(
        'label' => esc_html__('Completed', 'wpbooking'),
    ),
    'completed_a_part'      => array(
        'label' => esc_html__('Completed a Part', 'wpbooking'),
        'desc'  => esc_html__('Completed with deposit payment', 'wpbooking'),
    ),
    'cancelled'      => array(
        'label' => esc_html__('Cancelled', 'wpbooking'),
        'desc'  => esc_html__('Customer or Admin cancel the booking', 'wpbooking'),
    ),
    'refunded'       => array(
        'label' => esc_html__('Refunded', 'wpbooking'),
        'desc'  => esc_html__('Refunded by Admin', 'wpbooking'),
    ),
    'trash'          => array(
        'label' => esc_html__('Trash', 'wpbooking'),
        'desc'  => esc_html__('Moved to Trash by Admin', 'wpbooking'),
    ),
);

/**
 * Breakfast Types for Hotel
 *
 * @since 1.0
 * @author dungdt
 */
$config['breakfast_types'] = array(
    'continental'        => esc_html__('Continental', 'wpbooking'),
    'italian'            => esc_html__('Italian', 'wpbooking'),
    'full_english_irish' => esc_html__('Full English/Irish', 'wpbooking'),
    'vegetarian'         => esc_html__('Vegetarian', 'wpbooking'),
    'vegan'              => esc_html__('Vegan', 'wpbooking'),
    'Halal'              => esc_html__('Halal', 'wpbooking'),
    'gluten-free'        => esc_html__('Gluten-free', 'wpbooking'),
    'kosher'             => esc_html__('Kosher', 'wpbooking'),
    'asian'              => esc_html__('Asian', 'wpbooking'),
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
    "non-smoking" => esc_html__("Non-smoking", 'wpbooking'),
    "smoking"     => esc_html__("Smoking", 'wpbooking'),
    'both'        => esc_html__('I have both smoking and non-smoking options for this room type', 'wpbooking')
);

/**
 * Hotel Bed Type
 *
 * @since 1.0
 * @author dungdt
 */
$config['bed_type'] = array(
    "single-bed" => esc_html__("Single bed   /  90-130 cm wide", 'wpbooking'),
    "double-bed" => esc_html__("Double bed  /  131-150 cm wide", 'wpbooking'),
    "large-bed" => esc_html__("Large bed (King size) / 151-180 cm wide", 'wpbooking'),
    "extra-large-bed" => esc_html__("Extra-large double bed (Super-king size) / 181-210 cm wide", 'wpbooking'),
    "bunk-bed" => esc_html__("Bunk bed / Variable Size", 'wpbooking'),
    "sofa-bed" => esc_html__("Sofa bed / Variable Size", 'wpbooking'),
    "futon-mat" => esc_html__("Futon Mat / Variable Size", 'wpbooking'),
);
