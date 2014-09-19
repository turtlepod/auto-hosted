<?php
/**
 * Register Taxonomies
 * 
 * @package AutoUpdater
 * @subpackage Includes
 * @since 0.1.0
 */


/* Register taxonomies on the 'init' hook. */
add_action( 'init', 'auto_hosted_register_taxonomies' );

/**
 * Register taxonomies for the plugin.
 *
 * @since  0.1.0
 * @access public
 * @return void.
 */
function auto_hosted_register_taxonomies() {

	/* Set up the arguments for the plugin_repo_cat taxonomy. */
	$plugin_cat_args = array(
		'public'			=> true,
		'show_ui'			=> true,
		'show_in_nav_menus'	=> false,
		'show_tagcloud'		=> false,
		'show_admin_column'	=> true,
		'hierarchical'		=> true,
		'query_var'			=> false,
		'capabilities'		=> array(
			'manage_terms'	=> 'manage_plugin_repo',
			'edit_terms'	=> 'manage_plugin_repo',
			'delete_terms'	=> 'manage_plugin_repo',
			'assign_terms'	=> 'edit_plugin_repos',
		),
		'rewrite' => false,
		'labels' => array(
			'name'						=> _x( 'Plugin Repo Categories', 'plugin-cat', 'auto-hosted' ),
			'singular_name'				=> _x( 'Plugin Repo Category', 'plugin-cat', 'auto-hosted' ),
			'menu_name'					=> _x( 'Categories', 'plugin-cat', 'auto-hosted' ),
			'name_admin_bar'			=> _x( 'Category', 'plugin-cat', 'auto-hosted' ),
			'search_items'				=> _x( 'Search Categories', 'plugin-cat', 'auto-hosted' ),
			'popular_items'				=> _x( 'Popular Categories', 'plugin-cat', 'auto-hosted' ),
			'all_items'					=> _x( 'All Categories', 'plugin-cat', 'auto-hosted' ),
			'edit_item'					=> _x( 'Edit Category', 'plugin-cat', 'auto-hosted' ),
			'view_item'					=> _x( 'View Category', 'plugin-cat', 'auto-hosted' ),
			'update_item'				=> _x( 'Update Category', 'plugin-cat', 'auto-hosted' ),
			'add_new_item'				=> _x( 'Add New Category', 'plugin-cat', 'auto-hosted' ),
			'new_item_name'				=> _x( 'New Category Name', 'plugin-cat', 'auto-hosted' ),
			'separate_items_with_commas'=> _x( 'Separate categories with commas', 'plugin-cat', 'auto-hosted' ),
			'add_or_remove_items'		=> _x( 'Add or remove categories', 'plugin-cat', 'auto-hosted' ),
			'choose_from_most_used'		=> _x( 'Choose from the most used categories', 'plugin-cat', 'auto-hosted' ),
		)
	);

	/* Register the 'plugin_repo_cat' taxonomy. */
	register_taxonomy( 'plugin_repo_cat', array( 'plugin_repo' ), $plugin_cat_args );

	/* Set up the arguments for the plugin_repo_tag taxonomy. */
	$plugin_tag_args = array(
		'public'			=> true,
		'show_ui'			=> true,
		'show_in_nav_menus'	=> false,
		'show_tagcloud'		=> false,
		'show_admin_column'	=> true,
		'hierarchical'		=> false,
		'query_var'			=> false,
		'capabilities'		=> array(
			'manage_terms'	=> 'manage_plugin_repo',
			'edit_terms'	=> 'manage_plugin_repo',
			'delete_terms'	=> 'manage_plugin_repo',
			'assign_terms'	=> 'edit_plugin_repos',
		),
		'rewrite' => false,
		'labels' => array(
			'name'						=> _x( 'Plugin Repo Tags', 'plugin-tag', 'auto-hosted' ),
			'singular_name'				=> _x( 'Plugin Repo Tag', 'plugin-tag', 'auto-hosted' ),
			'menu_name'					=> _x( 'Tags', 'plugin-tag', 'auto-hosted' ),
			'name_admin_bar'			=> _x( 'Tag', 'plugin-tag', 'auto-hosted' ),
			'search_items'				=> _x( 'Search Tags', 'plugin-tag', 'auto-hosted' ),
			'popular_items'				=> _x( 'Popular Tags', 'plugin-tag', 'auto-hosted' ),
			'all_items'					=> _x( 'All Tags', 'plugin-tag', 'auto-hosted' ),
			'edit_item'					=> _x( 'Edit Tag', 'plugin-tag', 'auto-hosted' ),
			'view_item'					=> _x( 'View Tag', 'plugin-tag', 'auto-hosted' ),
			'update_item'				=> _x( 'Update Tag', 'plugin-tag', 'auto-hosted' ),
			'add_new_item'				=> _x( 'Add New Tag', 'plugin-tag', 'auto-hosted' ),
			'new_item_name'				=> _x( 'New Tag Name', 'plugin-tag', 'auto-hosted' ),
			'separate_items_with_commas'=> _x( 'Separate tags with commas', 'plugin-tag', 'auto-hosted' ),
			'add_or_remove_items'		=> _x( 'Add or remove tags', 'plugin-tag', 'auto-hosted' ),
			'choose_from_most_used'		=> _x( 'Choose from the most used tags', 'plugin-tag', 'auto-hosted' ),
		)
	);

	/* Register the 'plugin_repo_tag' taxonomy. */
	register_taxonomy( 'plugin_repo_tag', array( 'plugin_repo' ), $plugin_tag_args );

	/* Set up the arguments for the theme_repo_cat taxonomy. */
	$theme_cat_args = array(
		'public'			=> true,
		'show_ui'			=> true,
		'show_in_nav_menus'	=> false,
		'show_tagcloud'		=> false,
		'show_admin_column'	=> true,
		'hierarchical'		=> true,
		'query_var'			=> false,
		'capabilities'		=> array(
			'manage_terms'	=> 'manage_theme_repo',
			'edit_terms'	=> 'manage_theme_repo',
			'delete_terms'	=> 'manage_theme_repo',
			'assign_terms'	=> 'edit_theme_repos',
		),
		'rewrite' => false,
		'labels' => array(
			'name'						=> _x( 'Theme Repo Categories', 'theme-cat', 'auto-hosted' ),
			'singular_name'				=> _x( 'Theme Repo Category', 'theme-cat', 'auto-hosted' ),
			'menu_name'					=> _x( 'Categories', 'theme-cat', 'auto-hosted' ),
			'name_admin_bar'			=> _x( 'Category', 'theme-cat', 'auto-hosted' ),
			'search_items'				=> _x( 'Search Categories', 'theme-cat', 'auto-hosted' ),
			'popular_items'				=> _x( 'Popular Categories', 'theme-cat', 'auto-hosted' ),
			'all_items'					=> _x( 'All Categories', 'theme-cat', 'auto-hosted' ),
			'edit_item'					=> _x( 'Edit Category', 'theme-cat', 'auto-hosted' ),
			'view_item'					=> _x( 'View Category', 'theme-cat', 'auto-hosted' ),
			'update_item'				=> _x( 'Update Category', 'theme-cat', 'auto-hosted' ),
			'add_new_item'				=> _x( 'Add New Category', 'theme-cat', 'auto-hosted' ),
			'new_item_name'				=> _x( 'New Category Name', 'theme-cat', 'auto-hosted' ),
			'separate_items_with_commas'=> _x( 'Separate categories with commas', 'theme-cat', 'auto-hosted' ),
			'add_or_remove_items'		=> _x( 'Add or remove categories', 'theme-cat', 'auto-hosted' ),
			'choose_from_most_used'		=> _x( 'Choose from the most used categories', 'theme-cat', 'auto-hosted' ),
		)
	);

	/* Register the 'theme_repo_cat' taxonomy. */
	register_taxonomy( 'theme_repo_cat', array( 'theme_repo' ), $theme_cat_args );

	/* Set up the arguments for the theme_repo_tag taxonomy. */
	$theme_tag_args = array(
		'public'			=> true,
		'show_ui'			=> true,
		'show_in_nav_menus'	=> false,
		'show_tagcloud'		=> false,
		'show_admin_column'	=> true,
		'hierarchical'		=> false,
		'query_var'			=> false,
		'capabilities'		=> array(
			'manage_terms'	=> 'manage_theme_repo',
			'edit_terms'	=> 'manage_theme_repo',
			'delete_terms'	=> 'manage_theme_repo',
			'assign_terms'	=> 'edit_theme_repos',
		),
		'rewrite' => false,
		'labels' => array(
			'name'						=> _x( 'Theme Repo Tags', 'theme-tag', 'auto-hosted' ),
			'singular_name'				=> _x( 'Theme Repo Tag', 'theme-tag', 'auto-hosted' ),
			'menu_name'					=> _x( 'Tags', 'theme-tag', 'auto-hosted' ),
			'name_admin_bar'			=> _x( 'Tag', 'theme-tag', 'auto-hosted' ),
			'search_items'				=> _x( 'Search Tags', 'theme-tag', 'auto-hosted' ),
			'popular_items'				=> _x( 'Popular Tags', 'theme-tag', 'auto-hosted' ),
			'all_items'					=> _x( 'All Tags', 'theme-tag', 'auto-hosted' ),
			'edit_item'					=> _x( 'Edit Tag', 'theme-tag', 'auto-hosted' ),
			'view_item'					=> _x( 'View Tag', 'theme-tag', 'auto-hosted' ),
			'update_item'				=> _x( 'Update Tag', 'theme-tag', 'auto-hosted' ),
			'add_new_item'				=> _x( 'Add New Tag', 'theme-tag', 'auto-hosted' ),
			'new_item_name'				=> _x( 'New Tag Name', 'theme-tag', 'auto-hosted' ),
			'separate_items_with_commas'=> _x( 'Separate tags with commas', 'theme-tag', 'auto-hosted' ),
			'add_or_remove_items'		=> _x( 'Add or remove tags', 'theme-tag', 'auto-hosted' ),
			'choose_from_most_used'		=> _x( 'Choose from the most used tags', 'theme-tag', 'auto-hosted' ),
		)
	);

	/* Register the 'theme_repo_tag' taxonomy. */
	register_taxonomy( 'theme_repo_tag', array( 'theme_repo' ), $theme_tag_args );

}