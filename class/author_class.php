<?php

function add_custom_user_profile_fields( $user ) { ?>
    <h3>Informasi Tambahan Author (Profil & Sosmed)</h3>
    <table class="form-table">
        <tr>
            <th><label for="custom_avatar">URL Custom Foto Profil</label></th>
            <td>
                <input type="text" name="custom_avatar" id="custom_avatar" value="<?php echo esc_attr( get_the_author_meta( 'custom_avatar', $user->ID ) ); ?>" class="regular-text" /><br />
                <span class="description">Masukkan URL gambar foto profil/avatar.</span>
            </td>
        </tr>
        <tr>
            <th><label for="image_cover">URL Image Cover</label></th>
            <td>
                <input type="text" name="image_cover" id="image_cover" value="<?php echo esc_attr( get_the_author_meta( 'image_cover', $user->ID ) ); ?>" class="regular-text" /><br />
                <span class="description">Masukkan URL gambar sampul/cover.</span>
            </td>
        </tr>
        <tr>
            <th><label for="twitter_url">Twitter URL</label></th>
            <td><input type="url" name="twitter_url" id="twitter_url" value="<?php echo esc_attr( get_the_author_meta( 'twitter_url', $user->ID ) ); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="youtube_url">YouTube URL</label></th>
            <td><input type="url" name="youtube_url" id="youtube_url" value="<?php echo esc_attr( get_the_author_meta( 'youtube_url', $user->ID ) ); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="instagram_url">Instagram URL</label></th>
            <td><input type="url" name="instagram_url" id="instagram_url" value="<?php echo esc_attr( get_the_author_meta( 'instagram_url', $user->ID ) ); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="facebook_url">Facebook URL</label></th>
            <td><input type="url" name="facebook_url" id="facebook_url" value="<?php echo esc_attr( get_the_author_meta( 'facebook_url', $user->ID ) ); ?>" class="regular-text" /></td>
        </tr>
    </table>
<?php }
add_action( 'show_user_profile', 'add_custom_user_profile_fields' );
add_action( 'edit_user_profile', 'add_custom_user_profile_fields' );

// 2. Simpan Data Saat Profile Diupdate
function save_custom_user_profile_fields( $user_id ) {
    if ( ! current_user_can( 'edit_user', $user_id ) ) return false;

    update_user_meta( $user_id, 'custom_avatar', sanitize_text_field( $_POST['custom_avatar'] ) );
    update_user_meta( $user_id, 'image_cover', sanitize_text_field( $_POST['image_cover'] ) );
    update_user_meta( $user_id, 'twitter_url', esc_url_raw( $_POST['twitter_url'] ) );
    update_user_meta( $user_id, 'youtube_url', esc_url_raw( $_POST['youtube_url'] ) );
    update_user_meta( $user_id, 'instagram_url', esc_url_raw( $_POST['instagram_url'] ) );
    update_user_meta( $user_id, 'facebook_url', esc_url_raw( $_POST['facebook_url'] ) );
}
add_action( 'personal_options_update', 'save_custom_user_profile_fields' );
add_action( 'edit_user_profile_update', 'save_custom_user_profile_fields' );

// 3. Register Data Meta Tersebut ke REST API WP (Supaya Kebaca di Next.js)
function register_custom_user_meta_rest() {
    $fields = ['custom_avatar', 'image_cover', 'twitter_url', 'youtube_url', 'instagram_url', 'facebook_url'];
    
    foreach ($fields as $field) {
        register_rest_field( 'user', $field, [
            'get_callback' => function( $user ) use ( $field ) {
                return get_user_meta( $user['id'], $field, true );
            },
            'schema' => null,
        ]);
    }
}
add_action( 'rest_api_init', 'register_custom_user_meta_rest' );