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
define('_NEXT_DIR', plugin_dir_path( __FILE__ ));
define('_NEXT_URI', plugin_dir_url( __FILE__ ));
define('_NEXT_ICON', _NEXT_URI . 'images/icon.min.svg');

require_once 'admin/index.php';
require_once 'routes/index.php';