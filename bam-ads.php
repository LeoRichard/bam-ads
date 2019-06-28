<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://richardleo.me/
 * @since             1.0.0
 * @package           Bam_Ads
 *
 * @wordpress-plugin
 * Plugin Name:       BAM Ads
 * Plugin URI:        https://richardleo.me/
 * Description:       Create and add ADS to your posts and pages with simple shortcodes.
 * Version:           1.0.0
 * Author:            Richard Leo
 * Author URI:        https://richardleo.me/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bam-ads
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'BAM_ADS_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-bam-ads-activator.php
 */
function activate_bam_ads() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-bam-ads-activator.php';
	Bam_Ads_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-bam-ads-deactivator.php
 */
function deactivate_bam_ads() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-bam-ads-deactivator.php';
	Bam_Ads_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_bam_ads' );
register_deactivation_hook( __FILE__, 'deactivate_bam_ads' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-bam-ads.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_bam_ads() {

	$plugin = new Bam_Ads();
	$plugin->run();

}
run_bam_ads();
