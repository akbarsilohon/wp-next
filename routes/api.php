<?php

add_action('rest_api_init', function(){
    register_rest_route( 
        'next/v1', 
        '/params', 
        array(
            'methods'               =>  'GET',
            'callback'              =>  'next_params_api',
            'permission_callback'   =>  '__return_true'
        )
    );
});

function next_params_api( WP_REST_Request $request ){
    
    $isParam = $request->get_param('init');

    $option = get_option('_next', []);
    $logo = isset($option['header']['logo']) && !empty($option['header']['logo']) ? $option['header']['logo'] : _NEXT_URI . 'images/logo.png';
    $header = [
        'logo'      =>  $logo,
        'navs'      =>  [
            [
                'name'      =>  'Home',
                'url'       =>  '/',
                'icon'      =>  'fa-solid fa-house'
            ],
            [
                'name'      =>  'Resep',
                'url'       =>  '/resep',
                'icon'      =>  'fa-solid fa-mortar-pestle'
            ],
            [
                'name'      =>  'Blog',
                'url'       =>  '/blog',
                'icon'      =>  'fa-solid fa-pen'
            ],
            [
                'name'      =>  'Kontak',
                'url'       =>  '/page/kontak',
                'icon'      =>  'fa-solid fa-phone'
            ]
        ],
        'action'    =>  'Subscribe'
    ];

    // Insert header and footer ---------------
    $headerCode = isset($option['insert']['header']) && !empty($option['insert']['header']) ? $option['insert']['header'] : '';
    $footerCode = isset($option['insert']['footer']) && !empty($option['insert']['footer']) ? $option['insert']['footer'] : '';
    $insert = [
        'header'    =>  $headerCode,
        'footer'    =>  $footerCode
    ];

    // Footer Data ----------------------
    $footerLogo = isset($option['footer']['logo']) && !empty($option['footer']['logo']) ? $option['footer']['logo'] : _NEXT_URI . 'images/logo.png';
    $footerText = isset($option['footer']['text']) && !empty($option['footer']['text']) ? $option['footer']['text'] : 'Lorem 100';
    $footer = [
        'logo'  =>  $footerLogo,
        'text'  =>  $footerText
    ];

    $arrayData = null;

    if( $isParam == 'header' ){
        $arrayData = $header;
    } elseif( $isParam == 'insert' ){
        $arrayData = $insert;
    } elseif( $isParam == 'footer'){
        $arrayData = $footer;
    }

    return array( $arrayData );
}