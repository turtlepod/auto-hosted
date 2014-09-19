<?php
/**
 * Register "plugin_repo" and "theme_repo" Post Type.
 * 
 * @package AutoUpdater
 * @subpackage Includes
 * @since 0.1.0
 */

/* add register post type on the 'init' hook */
add_action( 'init', 'auto_hosted_register_post_types' );


/**
 * Register Post Type
 * 
 * @since  0.1.0
 * @access public
 * @return void.
 */
function auto_hosted_register_post_types() {

	/* post type label */
	$plugin_labels = array(
		'name' 					=> _x( 'Plugins', 'post-type', 'auto-hosted' ),
		'singular_name'			=> _x( 'Plugin Repo', 'post-type', 'auto-hosted' ),
		'add_new'				=> _x( 'Add New', 'post-type', 'auto-hosted' ),
		'add_new_item'			=> _x( 'Add New Plugin Repo', 'post-type', 'auto-hosted' ),
		'edit_item'				=> _x( 'Edit Plugin Repo', 'post-type', 'auto-hosted' ),
		'new_item'				=> _x( 'New Plugin Repo', 'post-type', 'auto-hosted' ),
		'all_items'				=> _x( 'All Plugin Repo', 'post-type', 'auto-hosted' ),
		'view_item'				=> _x( 'View Plugin Repo', 'post-type', 'auto-hosted' ),
		'search_items'			=> _x( 'Search Plugin Repo', 'post-type', 'auto-hosted' ),
		'not_found'				=> _x( 'No Plugin Repo found', 'post-type', 'auto-hosted' ),
		'not_found_in_trash'	=> _x( 'No Plugin Repo found in Trash', 'post-type', 'auto-hosted' ), 
		'menu_name'				=> _x( 'Repo | Plugin', 'post-type', 'auto-hosted' ),
	);
 
	/* post type args */
	$plugin_args = array(
		'description'			=> '',
		'public'				=> false,
		'publicly_queryable'	=> true,
		'show_in_nav_menus'		=> false,
		'show_in_admin_bar'		=> false,
		'exclude_from_search'	=> true,
		'show_ui'				=> true,
		'show_in_menu'			=> true,
		'menu_position'			=> null,
		'menu_icon'				=> AUTOHOSTED_URI . 'images/menu-icon.png',
		'can_export'			=> true,
		'delete_with_user'		=> false,
		'hierarchical'			=> true,
		'has_archive'			=> false, 
		'query_var'				=> true,
		'rewrite'				=> false,
		'capability_type'		=> 'plugin_repo',
		'map_meta_cap'			=> true,
		'capabilities'			=> array(
			'edit_post'					=> 'edit_plugin_repo', // don't assign these to roles
			'read_post'					=> 'read_plugin_repo', // don't assign these to roles
			'delete_post'				=> 'delete_plugin_repo', // don't assign these to roles
			'create_posts'				=> 'create_plugin_repos', // primitive meta caps
			'edit_posts'				=> 'edit_plugin_repos', // primitive caps outside map_meta_cap()
			'edit_others_posts'			=> 'manage_plugin_repo', // primitive caps outside map_meta_cap()
			'publish_posts'				=> 'manage_plugin_repo', // primitive caps outside map_meta_cap()
			'read_private_posts'		=> 'read',
			'read'						=> 'read',
			'delete_posts'				=> 'manage_plugin_repo', // primitive caps inside map_meta_cap()
			'delete_private_posts'		=> 'manage_plugin_repo', // primitive caps inside map_meta_cap()
			'delete_published_posts'	=> 'manage_plugin_repo', // primitive caps inside map_meta_cap()
			'delete_others_posts'		=> 'manage_plugin_repo', // primitive caps inside map_meta_cap()
			'edit_private_posts'		=> 'edit_plugin_repos', // primitive caps inside map_meta_cap()
			'edit_published_posts'		=> 'edit_plugin_repos' // primitive caps inside map_meta_cap()
		),
		'supports'				=> array( 'title', 'page-attributes' ),
		'labels'				=> $plugin_labels,
	);

	/* REGISTER "plugin_repo" POST TYPE */
	register_post_type( 'plugin_repo', $plugin_args );

	/* post type label */
	$theme_labels = array(
		'name'					=> _x( 'Themes', 'post-type', 'auto-hosted' ),
		'singular_name'			=> _x( 'Theme Repo', 'post-type', 'auto-hosted' ),
		'add_new'				=> _x( 'Add New', 'post-type', 'auto-hosted' ),
		'add_new_item'			=> _x( 'Add New Theme Repo', 'post-type', 'auto-hosted' ),
		'edit_item'				=> _x( 'Edit Theme Repo', 'post-type', 'auto-hosted' ),
		'new_item'				=> _x( 'New Theme Repo', 'post-type', 'auto-hosted' ),
		'all_items'				=> _x( 'All Theme Repo', 'post-type', 'auto-hosted' ),
		'view_item'				=> _x( 'View Theme Repo', 'post-type', 'auto-hosted' ),
		'search_items'			=> _x( 'Search Theme Repo', 'post-type', 'auto-hosted' ),
		'not_found'				=> _x( 'No Theme Repo found', 'post-type', 'auto-hosted' ),
		'not_found_in_trash'	=> _x( 'No Theme Repo found in Trash', 'post-type', 'auto-hosted' ), 
		'menu_name'				=> _x( 'Repo | Theme', 'post-type', 'auto-hosted' ),
	);
 
	/* post type args */
	$theme_args = array(
		'description'			=> '',
		'public'				=> false,
		'publicly_queryable'	=> true,
		'show_in_nav_menus'		=> false,
		'show_in_admin_bar'		=> false,
		'exclude_from_search'	=> true,
		'show_ui'				=> true,
		'show_in_menu'			=> true,
		'menu_position'			=> null,
		'menu_icon'				=> AUTOHOSTED_URI . 'images/menu-icon.png',
		'can_export'			=> true,
		'delete_with_user'		=> false,
		'hierarchical'			=> true,
		'has_archive'			=> false, 
		'query_var'				=> true,
		'rewrite'				=> false,
		'capability_type'		=> 'theme_repo',
		'map_meta_cap'			=> true,
		'capabilities'			=> array(
			'edit_post'					=> 'edit_theme_repo', // don't assign these to roles
			'read_post'					=> 'read_theme_repo', // don't assign these to roles
			'delete_post'				=> 'delete_theme_repo', // don't assign these to roles
			'create_posts'				=> 'create_theme_repos', // primitive meta caps
			'edit_posts'				=> 'edit_theme_repos', // primitive caps outside map_meta_cap()
			'edit_others_posts'			=> 'manage_theme_repo', // primitive caps outside map_meta_cap()
			'publish_posts'				=> 'manage_theme_repo', // primitive caps outside map_meta_cap()
			'read_private_posts'		=> 'read',
			'read'						=> 'read',
			'delete_posts'				=> 'manage_theme_repo', // primitive caps inside map_meta_cap()
			'delete_private_posts'		=> 'manage_theme_repo', // primitive caps inside map_meta_cap()
			'delete_published_posts'	=> 'manage_theme_repo', // primitive caps inside map_meta_cap()
			'delete_others_posts'		=> 'manage_theme_repo', // primitive caps inside map_meta_cap()
			'edit_private_posts'		=> 'edit_theme_repos', // primitive caps inside map_meta_cap()
			'edit_published_posts'		=> 'edit_theme_repos' // primitive caps inside map_meta_cap()
		),
		'supports'				=> array( 'title', 'page-attributes' ),
		'labels'				=> $theme_labels,
	);

	/* REGISTER "theme_repo" POST TYPE */
	register_post_type( 'theme_repo', $theme_args );
}