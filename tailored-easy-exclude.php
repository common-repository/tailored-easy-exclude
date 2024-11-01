<?php
/**
 * The WordPress Plugin Boilerplate.
 *
 * A foundation off of which to build well-documented WordPress plugins that
 * also follow WordPress Coding Standards and PHP best practices.
 *
 * @package   TailoredEasyExclude
 * @author    Zoran Ugrina <zoran@zugrina.com>
 * @license   GPL-2.0+
 * @link      www.zugrina.com
 * @copyright 2014 Zoran Ugrina
 *
 * @wordpress-plugin
 * Plugin Name:      Tailored Easy Exclude
 * Plugin URI:       www.zugrina.com
 * Description:      Small plugin that allows you to exclude pages or posts from WordPress administration post/page listing. It also works with custom post types. Pages or posts can be excluded per user role.
 * Version:          1.1
 * Author:       	 Zoran Ugrina
 * Author URI:       www.zugrina.com
 * Text Domain:      tailored-easy-exclude
 * License:          GPL-2.0+
 * License URI:      http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:      /languages
 * GitHub Plugin URI: https://github.com/zugrina/tailored-easy-exclude>
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . 'public/class-tailored-easy-exclude.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */
register_activation_hook( __FILE__, array( 'TailoredEasyExclude', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'TailoredEasyExclude', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'TailoredEasyExclude', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 * The code below is intended to to give the lightest footprint possible.
 */
if ( is_admin() ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-tailored-easy-exclude-admin.php' );
	add_action( 'plugins_loaded', array( 'TailoredEasyExcludeAdmin', 'get_instance' ) );

}