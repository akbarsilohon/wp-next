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
        <?php if (isset($_GET['saved'])): ?>
            <div class="updated notice is-dismissible"><p><strong>Settings saved!</strong></p></div>
        <?php endif; ?>
        <div class="next_container">
            <div class="next_heading">
                <img src="<?php echo _NEXT_URI . 'images/icon.png' ?>" alt="<?php echo _NEXT_NAME; ?>" class="next_image">
                <div class="next_aset">
                    <h3 class="next_title"><?php echo _NEXT_NAME; ?></h3>
                    <p class="next_desc">Plugin pribadi pengembangan Wordpress to Next Js</p>
                </div>
                <span class="next_ver"><?php echo _NEXT_VER; ?></span>
            </div>
            <form action="<?php echo admin_url() . 'admin.php?page=next_js'; ?>" method="post" class="next_form">
                <div class="next_group">
                    <label for="next_frontend_uri" class="next_label">Frontend Url</label>
                    <input type="url" name="_next[frontend_uri]" id="next_frontend_uri" class="next_input" value="<?php echo $option['frontend_uri'] ?? '' ?>" placeholder="Ex: https://mydomain.com">
                </div>

                <div class="next_group">
                    <label for="next_ip" class="next_label">Allowed IP</label>
                    <textarea name="_next[api_allowed]" id="next_ip" class="next_textarea" placeholder="Enter per line"><?php echo esc_textarea( $option['api_allowed'] ?? '' ); ?></textarea>
                </div>

                <div class="next_group">
                    <label for="next_frontend_uri" class="next_main_logo">Logo</label>
                    <input type="url" name="_next[header][logo]" id="next_main_logo" class="next_input" value="<?php echo $option['header']['logo'] ?? '' ?>" placeholder="Logo Website Here..."><br>
                    <span class="changeLogo">Change Logo</span>
                </div>

                <button type="submit" class="next_submit" name="save_main">Save Settings</button>
            </form>
        </div>
    </div>
    <?php
}

add_action( 'admin_init', function(){
    if (!isset($_POST['save_main'])) {
        return;
    }

    if (!isset($_GET['page']) || $_GET['page'] !== 'next_js') {
        return;
    }

    $option = get_option('_next', []);
    $data   = isset($_POST['_next']) ? wp_unslash($_POST['_next']) : [];
    $allowed_fields = [
        'frontend_uri',
        'api_allowed'
    ];

    foreach ($allowed_fields as $field) {
        if (!isset($data[$field])) {
            unset($option[$field]);
        }
    }

    update_option('_next', array_merge($option, $data));
    wp_safe_redirect(admin_url('admin.php?page=next_js&saved=1'));
    exit;
});