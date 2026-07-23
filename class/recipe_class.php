<?php

class Recipe_Manager_Plugin {

    public function __construct() {
        // 1. CPT & Taksonomi
        add_action('init', array($this, 'register_recipes_cpt'));
        add_action('init', array($this, 'register_recipe_category_taxonomy'));

        // 2. Meta Box Recipes
        add_action('add_meta_boxes', array($this, 'add_recipe_meta_boxes'));
        add_action('save_post', array($this, 'save_recipe_meta_data'));

        // 3. Category Images
        add_action('recipe_category_add_form_fields', array($this, 'add_category_image_fields'));
        add_action('recipe_category_edit_form_fields', array($this, 'edit_category_image_fields'));
        add_action('created_recipe_category', array($this, 'save_category_images'));
        add_action('edited_recipe_category', array($this, 'save_category_images'));

        // 4. Expose Data ke REST API & Custom Endpoint
        add_action('rest_api_init', array($this, 'register_rest_fields'));
        add_action('rest_api_init', array($this, 'register_views_increment_endpoint'));

        // 5. Admin Assets
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));

        // 6. REST API Sorting
        add_filter('rest_recipe_query', array($this, 'allow_views_rest_orderby'), 10, 2);
    }

    // --- 1. REGISTER CPT RECIPES ---
    public function register_recipes_cpt() {
        $labels = array(
            'name'          => 'Recipes',
            'singular_name' => 'Recipe',
            'add_new'       => 'Add New Recipe',
            'add_new_item'  => 'Add New Recipe',
            'edit_item'     => 'Edit Recipe',
            'all_items'     => 'All Recipes',
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'has_archive'        => true,
            'show_in_rest'       => true,
            'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'author'),
            'menu_icon'          => 'dashicons-food',
            'rewrite'            => array('slug' => 'recipes'),
        );

        register_post_type('recipe', $args);
    }

    // --- 2. REGISTER TAXONOMY CATEGORY ---
    public function register_recipe_category_taxonomy() {
        $args = array(
            'hierarchical'      => true,
            'labels'            => array('name' => 'Recipe Categories', 'singular_name' => 'Recipe Category'),
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_rest'      => true,
            'rewrite'           => array('slug' => 'recipe-category'),
        );

        register_taxonomy('recipe_category', array('recipe'), $args);
    }

    // --- 3. META BOX RECIPE DETAILS, NUTRITION & DIFFICULTY ---
    public function add_recipe_meta_boxes() {
        add_meta_box(
            'recipe_details_meta_box',
            'Recipe Details, Difficulty, Nutrition Facts & Video',
            array($this, 'render_recipe_meta_box'),
            'recipe',
            'normal',
            'high'
        );
    }

    public function render_recipe_meta_box($post) {
        wp_nonce_field('save_recipe_meta', 'recipe_meta_nonce');

        $ingredients = get_post_meta($post->ID, '_recipe_ingredients', true) ?: array('');
        $steps       = get_post_meta($post->ID, '_recipe_steps', true) ?: array('');
        $video_url   = get_post_meta($post->ID, '_recipe_video_url', true) ?: '';
        $views       = get_post_meta($post->ID, '_recipe_views', true) ?: 0;

        // Meta Data Nutrisi, Difficulty & Info Masak
        $difficulty  = get_post_meta($post->ID, '_recipe_difficulty', true) ?: 'Mudah';
        $prep_time   = get_post_meta($post->ID, '_recipe_prep_time', true) ?: '';
        $cook_time   = get_post_meta($post->ID, '_recipe_cook_time', true) ?: '';
        $servings    = get_post_meta($post->ID, '_recipe_servings', true) ?: '';
        $calories    = get_post_meta($post->ID, '_recipe_calories', true) ?: '';
        $protein     = get_post_meta($post->ID, '_recipe_protein', true) ?: '';
        $carbs       = get_post_meta($post->ID, '_recipe_carbs', true) ?: '';
        $fat         = get_post_meta($post->ID, '_recipe_fat', true) ?: '';
        ?>
        <style>
            .recipe-field-group { margin-bottom: 20px; }
            .recipe-field-group label { font-weight: bold; display: block; margin-bottom: 6px; }
            .recipe-grid-fields { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; margin-bottom: 15px; }
            .recipe-repeater-row { display: flex; gap: 10px; margin-bottom: 8px; align-items: center; }
            .recipe-repeater-row input { flex: 1; }
        </style>

        <!-- Stats Counter -->
        <div class="recipe-field-group">
            <label>Total Views:</label>
            <input type="text" value="<?php echo esc_attr($views); ?> views" readonly style="background:#f0f0f1; width: 150px; font-weight:bold; text-align:center;">
        </div>

        <hr>

        <!-- Cooking Info & Difficulty Select -->
        <h3>⏱️ Info Waktu, Porsi & Tingkat Kesulitan</h3>
        <div class="recipe-grid-fields">
            <div>
                <label for="recipe_difficulty">Tingkat Kesulitan:</label>
                <select id="recipe_difficulty" name="recipe_difficulty" class="widefat">
                    <option value="Mudah" <?php selected($difficulty, 'Mudah'); ?>>🟢 Mudah (Easy)</option>
                    <option value="Sedang" <?php selected($difficulty, 'Sedang'); ?>>🟡 Sedang (Medium)</option>
                    <option value="Sulit" <?php selected($difficulty, 'Sulit'); ?>>🔴 Sulit (Hard)</option>
                </select>
            </div>
            <div>
                <label for="recipe_prep_time">Prep Time (Waktu Persiapan):</label>
                <input type="text" id="recipe_prep_time" name="recipe_prep_time" value="<?php echo esc_attr($prep_time); ?>" class="widefat" placeholder="e.g. 15 Menit">
            </div>
            <div>
                <label for="recipe_cook_time">Cook Time (Waktu Masak):</label>
                <input type="text" id="recipe_cook_time" name="recipe_cook_time" value="<?php echo esc_attr($cook_time); ?>" class="widefat" placeholder="e.g. 45 Menit">
            </div>
            <div>
                <label for="recipe_servings">Servings (Porsi):</label>
                <input type="text" id="recipe_servings" name="recipe_servings" value="<?php echo esc_attr($servings); ?>" class="widefat" placeholder="e.g. 4 Porsi">
            </div>
        </div>

        <hr>

        <!-- Nutrition Facts -->
        <h3>🥗 Informasi Nutrisi (Per Porsi)</h3>
        <div class="recipe-grid-fields">
            <div>
                <label for="recipe_calories">Kalori (Calories):</label>
                <input type="text" id="recipe_calories" name="recipe_calories" value="<?php echo esc_attr($calories); ?>" class="widefat" placeholder="e.g. 450 kcal">
            </div>
            <div>
                <label for="recipe_protein">Protein:</label>
                <input type="text" id="recipe_protein" name="recipe_protein" value="<?php echo esc_attr($protein); ?>" class="widefat" placeholder="e.g. 30g">
            </div>
            <div>
                <label for="recipe_carbs">Karbohidrat (Carbs):</label>
                <input type="text" id="recipe_carbs" name="recipe_carbs" value="<?php echo esc_attr($carbs); ?>" class="widefat" placeholder="e.g. 15g">
            </div>
            <div>
                <label for="recipe_fat">Lemak (Fat):</label>
                <input type="text" id="recipe_fat" name="recipe_fat" value="<?php echo esc_attr($fat); ?>" class="widefat" placeholder="e.g. 20g">
            </div>
        </div>

        <hr>

        <!-- Video Youtube -->
        <div class="recipe-field-group">
            <label for="recipe_video_url">🎥 YouTube Video URL (Optional):</label>
            <input type="url" id="recipe_video_url" name="recipe_video_url" value="<?php echo esc_attr($video_url); ?>" class="widefat" placeholder="https://www.youtube.com/watch?v=...">
        </div>

        <hr>

        <!-- Dynamic Ingredients -->
        <div class="recipe-field-group">
            <label>🥦 Ingredients (Bahan-Bahan):</label>
            <div id="ingredients-container">
                <?php foreach ($ingredients as $index => $ingredient): ?>
                    <div class="recipe-repeater-row">
                        <input type="text" name="recipe_ingredients[]" value="<?php echo esc_attr($ingredient); ?>" placeholder="e.g. 2 siung bawang putih, cincang halus" class="widefat">
                        <button type="button" class="button remove-row-btn">&times;</button>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="button button-secondary" id="add-ingredient-btn">+ Add Ingredient</button>
        </div>

        <hr>

        <!-- Dynamic Steps -->
        <div class="recipe-field-group">
            <label>🍳 Cooking Steps (Langkah-Langkah):</label>
            <div id="steps-container">
                <?php foreach ($steps as $index => $step): ?>
                    <div class="recipe-repeater-row">
                        <input type="text" name="recipe_steps[]" value="<?php echo esc_attr($step); ?>" placeholder="e.g. Tumis Bawang hingga harum" class="widefat">
                        <button type="button" class="button remove-row-btn">&times;</button>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="button button-secondary" id="add-step-btn">+ Add Step</button>
        </div>

        <!-- JS Dynamic Repeater -->
        <script>
            jQuery(document).ready(function($) {
                $('#add-ingredient-btn').click(function() {
                    $('#ingredients-container').append(`
                        <div class="recipe-repeater-row">
                            <input type="text" name="recipe_ingredients[]" value="" placeholder="e.g. 1 sdt Garam" class="widefat">
                            <button type="button" class="button remove-row-btn">&times;</button>
                        </div>
                    `);
                });

                $('#add-step-btn').click(function() {
                    $('#steps-container').append(`
                        <div class="recipe-repeater-row">
                            <input type="text" name="recipe_steps[]" value="" placeholder="Langkah selanjutnya..." class="widefat">
                            <button type="button" class="button remove-row-btn">&times;</button>
                        </div>
                    `);
                });

                $(document).on('click', '.remove-row-btn', function() {
                    if ($(this).parent().parent().children().length > 1) {
                        $(this).parent().remove();
                    } else {
                        $(this).siblings('input').val('');
                    }
                });
            });
        </script>
        <?php
    }

    public function save_recipe_meta_data($post_id) {
        if (!isset($_POST['recipe_meta_nonce']) || !wp_verify_nonce($_POST['recipe_meta_nonce'], 'save_recipe_meta')) return;
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!current_user_can('edit_post', $post_id)) return;

        // Save Repeater
        if (isset($_POST['recipe_ingredients'])) {
            $ingredients = array_filter(array_map('sanitize_text_field', $_POST['recipe_ingredients']));
            update_post_meta($post_id, '_recipe_ingredients', $ingredients);
        }
        if (isset($_POST['recipe_steps'])) {
            $steps = array_filter(array_map('sanitize_text_field', $_POST['recipe_steps']));
            update_post_meta($post_id, '_recipe_steps', $steps);
        }

        // Save Video URL
        if (isset($_POST['recipe_video_url'])) {
            update_post_meta($post_id, '_recipe_video_url', esc_url_raw($_POST['recipe_video_url']));
        }

        // Save Difficulty, Cooking Info & Nutrition
        $fields = array('difficulty', 'prep_time', 'cook_time', 'servings', 'calories', 'protein', 'carbs', 'fat');
        foreach ($fields as $field) {
            if (isset($_POST['recipe_' . $field])) {
                update_post_meta($post_id, '_recipe_' . $field, sanitize_text_field($_POST['recipe_' . $field]));
            }
        }
    }

    // --- 4. EXPOSE ALL DATA TO REST API ---
    public function register_rest_fields() {
        register_rest_field('recipe', 'recipe_details', array(
            'get_callback' => function($post) {
                $video_url = get_post_meta($post['id'], '_recipe_video_url', true);
                $views     = (int) get_post_meta($post['id'], '_recipe_views', true);

                $embed_url = '';
                if ($video_url) {
                    preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $video_url, $match);
                    if (isset($match[1])) {
                        $embed_url = 'https://www.youtube.com/embed/' . $match[1];
                    }
                }

                return array(
                    'views'       => $views,
                    'info'        => array(
                        'difficulty' => get_post_meta($post['id'], '_recipe_difficulty', true) ?: 'Mudah',
                        'prep_time'  => get_post_meta($post['id'], '_recipe_prep_time', true) ?: '',
                        'cook_time'  => get_post_meta($post['id'], '_recipe_cook_time', true) ?: '',
                        'servings'   => get_post_meta($post['id'], '_recipe_servings', true) ?: '',
                    ),
                    'nutrition'   => array(
                        'calories'  => get_post_meta($post['id'], '_recipe_calories', true) ?: '',
                        'protein'   => get_post_meta($post['id'], '_recipe_protein', true) ?: '',
                        'carbs'     => get_post_meta($post['id'], '_recipe_carbs', true) ?: '',
                        'fat'       => get_post_meta($post['id'], '_recipe_fat', true) ?: '',
                    ),
                    'ingredients' => get_post_meta($post['id'], '_recipe_ingredients', true) ?: array(),
                    'steps'       => get_post_meta($post['id'], '_recipe_steps', true) ?: array(),
                    'video'       => array(
                        'raw_url'   => $video_url,
                        'embed_url' => $embed_url
                    )
                );
            }
        ));

        // Category Images
        register_rest_field('recipe_category', 'images', array(
            'get_callback' => function($term) {
                $thumb_id = get_term_meta($term['id'], 'thumbnail_id', true);
                $cover_id = get_term_meta($term['id'], 'cover_id', true);

                return array(
                    'thumbnail' => $thumb_id ? wp_get_attachment_image_url($thumb_id, 'full') : null,
                    'cover'     => $cover_id ? wp_get_attachment_image_url($cover_id, 'full') : null,
                );
            }
        ));
    }

    // --- 5. ENDPOINT INCREMENT VIEWS ---
    public function register_views_increment_endpoint() {
        register_rest_route('recipe-manager/v1', '/hit-view/(?P<id>\d+)', array(
            'methods'  => 'POST',
            'callback' => function($request) {
                $post_id = $request['id'];
                if (get_post_type($post_id) !== 'recipe') {
                    return new WP_Error('invalid_recipe', 'Invalid Recipe ID', array('status' => 404));
                }

                $views = (int) get_post_meta($post_id, '_recipe_views', true);
                $new_views = $views + 1;
                update_post_meta($post_id, '_recipe_views', $new_views);

                return array('success' => true, 'views' => $new_views);
            },
            'permission_callback' => '__return_true'
        ));
    }

    // --- 6. REST SORTING ---
    public function allow_views_rest_orderby($args, $request) {
        if ($request->get_param('orderby') === 'views') {
            $args['orderby']  = 'meta_value_num';
            $args['meta_key'] = '_recipe_views';
        }
        return $args;
    }

    // --- 7. CATEGORY IMAGES (ADD & EDIT) ---
    public function add_category_image_fields() {
        ?>
        <div class="form-field term-group">
            <label>Thumbnail Image</label>
            <input type="hidden" id="recipe_cat_thumbnail_id" name="recipe_cat_thumbnail_id" value="">
            <div id="recipe_cat_thumbnail_preview" style="margin-bottom:10px;"></div>
            <button type="button" class="button upload_media_btn" data-input="recipe_cat_thumbnail_id" data-preview="recipe_cat_thumbnail_preview">Upload Thumbnail</button>
        </div>
        <div class="form-field term-group">
            <label>Cover Image (Header/Hero)</label>
            <input type="hidden" id="recipe_cat_cover_id" name="recipe_cat_cover_id" value="">
            <div id="recipe_cat_cover_preview" style="margin-bottom:10px;"></div>
            <button type="button" class="button upload_media_btn" data-input="recipe_cat_cover_id" data-preview="recipe_cat_cover_preview">Upload Cover</button>
        </div>
        <?php
    }

    public function edit_category_image_fields($term) {
        $thumb_id = get_term_meta($term->term_id, 'thumbnail_id', true);
        $cover_id = get_term_meta($term->term_id, 'cover_id', true);

        $thumb_url = $thumb_id ? wp_get_attachment_image_url($thumb_id, 'thumbnail') : '';
        $cover_url = $cover_id ? wp_get_attachment_image_url($cover_id, 'medium') : '';
        ?>
        <tr class="form-field term-group-wrap">
            <th scope="row"><label>Thumbnail Image</label></th>
            <td>
                <input type="hidden" id="recipe_cat_thumbnail_id" name="recipe_cat_thumbnail_id" value="<?php echo esc_attr($thumb_id); ?>">
                <div id="recipe_cat_thumbnail_preview" style="margin-bottom:10px;">
                    <?php if ($thumb_url): ?><img src="<?php echo esc_url($thumb_url); ?>" style="max-width:150px;"><?php endif; ?>
                </div>
                <button type="button" class="button upload_media_btn" data-input="recipe_cat_thumbnail_id" data-preview="recipe_cat_thumbnail_preview">Upload / Change Thumbnail</button>
            </td>
        </tr>
        <tr class="form-field term-group-wrap">
            <th scope="row"><label>Cover Image</label></th>
            <td>
                <input type="hidden" id="recipe_cat_cover_id" name="recipe_cat_cover_id" value="<?php echo esc_attr($cover_id); ?>">
                <div id="recipe_cat_cover_preview" style="margin-bottom:10px;">
                    <?php if ($cover_url): ?><img src="<?php echo esc_url($cover_url); ?>" style="max-width:300px;"><?php endif; ?>
                </div>
                <button type="button" class="button upload_media_btn" data-input="recipe_cat_cover_id" data-preview="recipe_cat_cover_preview">Upload / Change Cover</button>
            </td>
        </tr>
        <?php
    }

    public function save_category_images($term_id) {
        if (isset($_POST['recipe_cat_thumbnail_id'])) {
            update_term_meta($term_id, 'thumbnail_id', sanitize_text_field($_POST['recipe_cat_thumbnail_id']));
        }
        if (isset($_POST['recipe_cat_cover_id'])) {
            update_term_meta($term_id, 'cover_id', sanitize_text_field($_POST['recipe_cat_cover_id']));
        }
    }

    // --- 8. ADMIN ASSETS ---
    public function enqueue_admin_assets($hook) {
        if ('edit-tags.php' === $hook || 'term.php' === $hook) {
            wp_enqueue_media();
            add_action('admin_footer', function() {
                ?>
                <script>
                jQuery(document).ready(function($) {
                    $('.upload_media_btn').click(function(e) {
                        e.preventDefault();
                        var button = $(this);
                        var inputId = button.data('input');
                        var previewId = button.data('preview');

                        var customUploader = wp.media({
                            title: 'Select Image',
                            button: { text: 'Use Image' },
                            multiple: false
                        }).on('select', function() {
                            var attachment = customUploader.state().get('selection').first().toJSON();
                            $('#' + inputId).val(attachment.id);
                            $('#' + previewId).html('<img src="' + attachment.url + '" style="max-width:150px;">');
                        }).open();
                    });
                });
                </script>
                <?php
            });
        }
    }
}

new Recipe_Manager_Plugin();