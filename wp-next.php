<?php
/**
 * Plugin Name: Wordpress to Next Js
 * Version: 1.1.0
 * Plugin URI: https://silohon.com
 * Description: Plugin pribadi pengembangan Wordpress to Next Js
 * Author: Akbar Silohon
 * Author URI: https://github.com/akbarsilohon
 */

define('_NEXT_NAME', 'WP Next Js');
define('_NEXT_VER', '1.1.0');
define('_NEXT_DIR', plugin_dir_path( __FILE__ ));
define('_NEXT_URI', plugin_dir_url( __FILE__ ));
define('_NEXT_ICON', _NEXT_URI . 'images/icon.min.svg');

require_once 'admin/index.php';
require_once 'routes/index.php';
require_once 'routes/api.php';


add_action('admin_enqueue_scripts', function(){
    $allowed = ['next_js', 'next_opt'];
    if(isset( $_GET['page'] ) && in_array( $_GET['page'], $allowed)){
        wp_enqueue_media();
        wp_enqueue_style(
            'font-inter',
            'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap',
            array(),
            null, 
            'all'
        );

        wp_enqueue_style( 
            'next_css', 
            _NEXT_URI . 'assets/css/admin.css', 
            array(), 
            fileatime( _NEXT_DIR . 'assets/css/admin.css'),
            'all'
        );

        wp_enqueue_script( 
            'net_script',
            _NEXT_URI . 'assets/scripts/admin.js', 
            array(), 
            fileatime( _NEXT_DIR . 'assets/scripts/admin.js'), 
            true 
        );
    }
});