<?php
    /**
     * @see WPBooking_Loader::_autoload();
     */
    $autoload[ 'config' ] = [
        'config',
        'lang'
    ];

    $autoload[ 'helper' ] = [
        'application',
        'assets',
        'settings',
        'service',
        'email'
    ];

    $autoload[ 'library' ] = [
        'helper',
        'input',
        'assets',
        'session',
        'currency',
        'validator',
        'metabox',
        'email/emogrifier',
        'query',
        'base/service',
        'base/order',
        'base/chart',
        'taxonomy-metabox',
        'query-inject',
        'service',
        'captcha',
        'comments',
        'tax-meta/tax-meta-class',
        'regen_thumbs'
    ];

    $autoload[ 'controller' ] = [
        'user',
        'service',
        'order',
        'admin/order',
        'admin/location',
        'admin/taxonomy',
        'admin/service',
        'admin/about',
        'admin/settings',
        'admin/taxonomy',
        'admin/calendar.metabox',
        'admin/setup',
        'gateways',
        'email',
        'checkout',
		'shortcode'
    ];

    $autoload[ 'model' ] = [
        'service_model',
        'order_model',
        'order_hotel_room_model',
        'calendar_model',
        'payment_model',
        'comments_model',
        'inbox_model',
        'user_favorite_model',
        'review_helpful',
        'query_model',
        'meta_model',
        'user_model',
        'availability_tour_model',
    ];

    $autoload[ 'widget' ] = [
        'search-form',
        'currency-switcher',
        'cart-widget'
    ];

    $autoload[ 'frontend' ] = [
        'template-hooks',
        'template-func'
    ];

    $autoload[ 'encrypt_key' ] = 'wpbooking';
