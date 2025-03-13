<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://https://github.com/ivandakhin
 * @since             1.0.0
 * @package           Wp_Auto_Posting
 *
 * @wordpress-plugin
 * Plugin Name:       WP Auto Posting
 * Plugin URI:        https://wp-auto-posting.com
 * Description:       Продемонструвати навички розробки плагінів для WordPress, роботи з Composer, створення сторінок налаштувань, та інтеграції із OpenAI.
 * Version:           1.0.0
 * Author:            Ivan Dakhin
 * Author URI:        https://https://github.com/ivandakhin/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-auto-posting
 * Domain Path:       /languages
 */

if (!defined('WPINC')) {
    die;
}

define('WP_AUTO_POSTING_VERSION', '1.0.0');


if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

function activate_wp_auto_posting()
{
    WpAutoPosting\Core\Activator::activate();
}

function deactivate_wp_auto_posting() {
    WpAutoPosting\Core\Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_wp_auto_posting');
register_deactivation_hook(__FILE__, 'deactivate_wp_auto_posting');

function run_wp_auto_posting()
{

    $plugin = new WpAutoPosting\Core\Bootstrap();
    $plugin->run();

}

run_wp_auto_posting();
