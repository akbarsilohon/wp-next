<?php

function next_footer_page(){
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

            <form action="<?php echo admin_url() . 'admin.php?page=next_footer'; ?>" method="post" class="next_form">
                <div class="next_group">
                    <label for="next_footer_logo" class="next_label">Footer Logo</label>
                    <input type="url" name="_next[footer][logo]" id="next_footer_logo" class="next_input" value="<?php echo $option['footer']['logo'] ?? '' ?>" placeholder="Logo Footer Website Here..."><br>
                    <span class="changeLogo" id="next_footer_logo_change">Change Logo</span>
                </div>

                <div class="next_group">
                    <label for="next_footer_text" class="next_label">Footer Text</label>
                    <textarea name="_next[footer][text]" id="next_footer_text" class="next_textarea" placeholder="Footer Text Here.."><?php echo esc_textarea( $option['footer']['text'] ?? ''); ?></textarea>
                </div>

                <button type="submit" class="next_submit" name="save_footer">Save Settings</button>
            </form>
        </div>
    </div>
    <?php
}

add_action('admin_init', function(){
    if (!isset($_POST['save_footer'])) {
        return;
    }

    if (!isset($_GET['page']) || $_GET['page'] !== 'next_footer') {
        return;
    }

    $option = get_option('_next', []);
    $data   = isset($_POST['_next']) ? wp_unslash($_POST['_next']) : [];
    $allowed_fields = [
        'footer'
    ];

    foreach ($allowed_fields as $field) {
        if (!isset($data[$field])) {
            unset($option[$field]);
        }
    }

    update_option('_next', array_merge($option, $data));
    wp_safe_redirect(admin_url('admin.php?page=next_footer&saved=1'));
    exit;
});