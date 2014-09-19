<?php
/**
 * Plugin Name: Auto Hosted
 * Plugin URI: http://autohosted.com/
 * Description: Automatic Update Manager for Self Hosted WordPress Themes and Plugins.
 * Version: 0.1.5
 * Author: David Chandra Purnama
 * Author URI: http://shellcreeper.com/
 *
 * Auto Hosted plugin was created to provide solution for plugin and theme developer to host and
 * manage automatic update for their product in easy and afforable way. 
 *
 * This program is free software; you can redistribute it and/or modify it under the terms  
 * of the GNU General Public License version 2, as published by the Free Software Foundation.
 * You may NOT assume that you can use any other version of the GPL.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package AutoHosted
 * @version 0.1.5
 * @author David Chandra Purnama <david@shellcreeper.com>
 * @copyright Copyright (c) 2013, David Chandra Purnama
 * @link http://autohosted.com
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Language: text domain
------------------------------------------ */
load_plugin_textdomain( 'auto-hosted', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );


/* Constants
------------------------------------------ */

/* Set plugin version constant. */
define( 'AUTOHOSTED_VERSION', '0.1.5' );

/* Set constant path to the plugin directory. */
define( 'AUTOHOSTED_PATH', trailingslashit( plugin_dir_path(__FILE__) ) );

/* Set the constant path to the plugin directory URI. */
define( 'AUTOHOSTED_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );


/* Load Functions
------------------------------------------ */

/* Load Meta box Helper Class */
require_once( AUTOHOSTED_PATH . 'mb/meta-box.php' );

/* Load register post types function */
require_once( AUTOHOSTED_PATH . 'includes/post-types.php' );

/* Load register taxonomies function */
require_once( AUTOHOSTED_PATH . 'includes/taxonomies.php' );

/* Load meta boxes and metadata functions */
require_once( AUTOHOSTED_PATH . 'includes/meta-boxes.php' );

/* Load edit custom column functions */
require_once( AUTOHOSTED_PATH . 'includes/manage-column.php' );



/* Load functions */
require_once( AUTOHOSTED_PATH . 'includes/functions.php' );

/* Load validate request functions */
require_once( AUTOHOSTED_PATH . 'includes/validate-request.php' );

/* Load validate activation key check functions */
require_once( AUTOHOSTED_PATH . 'includes/validate-check-key.php' );


/* Updater
------------------------------------------ */

/* Hook updater to init */
add_action( 'init', 'auto_hosted_updater_init' );

/**
 * Load and Activate Plugin Updater Class.
 * @since 0.1.0
 */
function auto_hosted_updater_init() {

	/* Load Plugin Updater */
	require_once( AUTOHOSTED_PATH . 'includes/updater.php' );

	/* Updater Config */
	$config = array(
		'base'       => plugin_basename( __FILE__ ), //required
		'repo_uri'   => 'http://autohosted.com/', //required
		'repo_slug'  => 'auto-hosted',
		'dashboard'  => true,
		'username'   => true,
	);

	/* Load Updater Class */
	new AH_AUTO_Hosted_Updater_Class( $config );
}


/* Activation and Uninstall
------------------------------------------ */

/* Register activation hook. */
register_activation_hook( __FILE__, 'auto_hosted_activation' );


/**
 * Runs only when the plugin is activated.
 * @since 0.1.0
 */
function auto_hosted_activation() {

	/* Get the administrator role. */
	$role =& get_role( 'administrator' );

	/* If the administrator role exists, add required capabilities for the plugin. */
	if ( !empty( $role ) ) {
		$role->add_cap( 'manage_plugin_repo' );
		$role->add_cap( 'create_plugin_repos' );
		$role->add_cap( 'edit_plugin_repos' );
		$role->add_cap( 'manage_theme_repo' );
		$role->add_cap( 'create_theme_repos' );
		$role->add_cap( 'edit_theme_repos' );
	}

	/* uninstall plugin */
	register_uninstall_hook( __FILE__, 'auto_hosted_uninstall' );
}


/* Add active status constant to init hook */
add_action( 'init', 'auto_hosted_active_check_init', 1 );

/**
 * Auto Hosted Active Status Constants
 * this is to make sure user deactivate lite version before activate this
 * @since 0.1.1
 */
function auto_hosted_active_check_init(){
	define( 'AUTOHOSTED_ACTIVE', true );
}


/**
 * Uninstall plugin
 * @since 0.1.0
 */
function auto_hosted_uninstall(){

	/* Get the administrator role. */
	$role =& get_role( 'administrator' );

	/* If the administrator role exists, remove added capabilities for the plugin. */
	if ( !empty( $role ) ) {
		$role->remove_cap( 'manage_plugin_repo' );
		$role->remove_cap( 'create_plugin_repos' );
		$role->remove_cap( 'edit_plugin_repos' );
		$role->remove_cap( 'manage_theme_repo' );
		$role->remove_cap( 'create_theme_repos' );
		$role->remove_cap( 'edit_theme_repos' );
	}
}