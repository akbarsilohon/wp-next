<?php

add_action('admin_menu', function(){
    add_menu_page( 
        _NEXT_NAME,
        _NEXT_NAME, 
        'manage_options', 
        'next_js',
        'next_js_root', 
        _NEXT_ICON,
        1
    );

    add_submenu_page( 
        'next_js', 
        'Main Settings', 
        'Main Settings', 
        'manage_options', 
        'next_js', 
        'next_js_root'
    );

    add_submenu_page( 
        'next_js', 
        'Next Option', 
        'Next Option', 
        'manage_options', 
        'next_opt', 
        'next_options_page'
    );
});

function next_js_root(){
    $option = get_option('_next', []); ?>
    <div class="wrap">
        <div class="next_container">

        </div>
    </div>
    <?php
}