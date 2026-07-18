<?php

add_action('template_redirect', function(){
    $option = get_option('_next', []);
    $redirect = isset($option['frontend_uri']) && !empty($option['frontend_uri']) ? $option['frontend_uri'] : 'https://silohon.com';

    if( ! is_admin() && !defined('REST_REQUEST')){
        wp_redirect( $redirect, 301 );
        exit;
    }
});