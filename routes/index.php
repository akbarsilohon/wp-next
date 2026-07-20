<?php

add_action('template_redirect', function () {
    $uri = $_SERVER['REQUEST_URI'];

    if (
        str_starts_with($uri, '/wp-json') ||
        str_starts_with($uri, '/wp-login.php') ||
        str_starts_with($uri, '/wp-admin') ||
        str_starts_with($uri, '/wp-content')
    ) {
        return;
    }

    if (!is_admin()) {
        $option = get_option('_next', []);
        $redirect = $option['frontend_uri'] ?? 'https://silohon.com';

        wp_safe_redirect($redirect, 301);
        exit;
    }
});



function ip_true_or_false( $ip ){
    $option = get_option('_next', []);
    $ip_allowed = isset($option['api_allowed']) && !empty($option['api_allowed']) ? $option['api_allowed'] : '';

    if(!empty( $ip_allowed )){
        $allowed_ips_array = explode("\n", $ip_allowed);
        $allowed_ips_array = array_map('trim', $allowed_ips_array);
        $allowed_ips_array = array_filter($allowed_ips_array);

        if ( in_array( trim($ip), $allowed_ips_array, true ) ) {
            return true;
        }
    }

    return false;
}