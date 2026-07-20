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
                'url'       =>  '/kategori/resep',
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

    $arrayData = null;

    if( $isParam == 'header' ){
        $arrayData = $header;
    }

    return array( $arrayData );
}