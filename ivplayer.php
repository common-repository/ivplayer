<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           IV_Player
 *
 * @wordpress-plugin
 * Plugin Name:       IV Player
 * Description:       IV player is an interactive video player for Professional teachers and online Gurus, also best suits for Educational websites.
 * Version:           1.0.0
 * Author:            Sandesh Naroju
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       iv-player
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}



/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('IV_PLAYER_VERSION', '1.0.0');
define('IV_PLAYER_ASSET_MANIFEST', plugin_dir_path(__FILE__) . '/admin-build/asset-manifest.json');
define('IV_PLAYER_OPEN_ASSET_MANIFEST', plugin_dir_path(__FILE__) . '/open-build/asset-manifest.json');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-iv-player-activator.php
 */
function activate_iv_player()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-iv-player-activator.php';
	IV_Player_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-iv-player-deactivator.php
 */
function deactivate_iv_player()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-iv-player-deactivator.php';
	IV_Player_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_iv_player');
register_deactivation_hook(__FILE__, 'deactivate_iv_player');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-iv-player.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_iv_player()
{

	$plugin = new IV_Player();
	$plugin->run();
}
run_iv_player();
