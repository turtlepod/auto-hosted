<?php
/**
 * Automatic Updater Class
 * Enable automatic updates for self hosted plugins
 * Using WordPress Plugin API
 * @package cptDocs
 * @subpackage includes
 * @since 0.1.0
 */

/* Prevent loading this file directly and/or if the class is already defined */
if ( ! defined( 'ABSPATH' ) || class_exists( 'AH_AUTO_Hosted_Updater_Class' ) )
	return;


/**
 * Plugin Updater Class
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @version 0.1.0
 * @author David Chandra Purnama <david@shellcreeper.com>
 * @link http://autohosted.com
 * @link http://shellcreeper.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @copyright Copyright (c) 2013, David Chandra Purnama
 */
class AH_AUTO_Hosted_Updater_Class{

	/**
	 * @var $config the config for the updater
	 * @access public
	 */
	var $config;


	/**
	 * Class Constructor
	 *
	 * @since 0.1.0
	 * @param array $config the configuration required for the updater to work
	 * @return void
	 */
	public function __construct( $config = array() ) {

		/* default config */
		$defaults = array(
			'base'		=> '',
			'repo_uri'	=> '',
			'repo_slug'	=> '',
			'key'		=> '',
			'dashboard'	=> false,
		);

		/* merge configs and defaults */
		$this->config = wp_parse_args( $config, $defaults );

		/* check minimum config before doing stuff */
		if ( !empty( $this->config['base'] ) && !empty ( $this->config['repo_uri'] ) && !empty ( $this->config['repo_slug'] ) ){

			/* filters for admin */
			if ( is_admin() ) {

				/* filter site transient "update_plugins" */
				add_filter( 'pre_set_site_transient_update_plugins', array( &$this, 'transient_update_plugins' ) );

				/* filter plugins api */
				add_filter( 'plugins_api_result', array( &$this, 'plugins_api_result' ), 10, 3 );

				/* forder name fix */
				add_filter( 'upgrader_post_install', array( &$this, 'upgrader_post_install' ), 10, 3 );
			}
		}
	}


	/**
	 * Data needed in an array to make everything simple.
	 * 
	 * @since 0.1.0
	 * @return array
	 */
	public function updater_data(){

		/* Updater data: Hana Tul Set! */
		$updater_data = array();

		/* Base name */
		$updater_data['basename'] = $this->config['base'];

		/* Plugin slug */
		$slug = dirname( $this->config['base'] );
		$updater_data['slug'] = $slug;

		/* Main plugin file */
		$updater_data['file'] = basename( $this->config['base'] );

		/* Updater class location is in the main plugin folder  */
		$file_path = plugin_dir_path( __FILE__ ) . $updater_data['file'];

		/* if it's in sub folder */
		if ( basename( dirname( dirname( __FILE__ ) ) ) == $updater_data['slug'] )
			$file_path = plugin_dir_path(  dirname( __FILE__ ) ) . $updater_data['file'];

		/* Get plugin data from main plugin file */
		$get_plugin_data = get_plugin_data( $file_path );

		/* Plugin name */
		$updater_data['name'] = strip_tags( $get_plugin_data['Name'] );

		/* Plugin version */
		$updater_data['version'] = strip_tags( $get_plugin_data['Version'] );

		/* Plugin uri / uri */
		$uri = '';
		if ( $get_plugin_data['PluginURI'] ) $uri = esc_url( $get_plugin_data['PluginURI'] );
		$updater_data['uri'] = $uri;

		/* Author with link to author uri */
		$author = strip_tags( $get_plugin_data['Author'] );
		$author_uri = $get_plugin_data['AuthorURI'];
		if ( $author && $author_uri ) $author = '<a href="' . esc_url_raw( $author_uri ) . '">' . $author . '</a>';
		$updater_data['author'] = $author;

		/* Activation key */
		$key = '';
		if ( $this->config['key'] ) $key = md5( $this->config['key']);
		if ( empty( $key ) && true === $this->config['dashboard'] ){
			$widget_id = 'ahp_' . $slug . '_activation_key';
			$key_db = get_option( $widget_id );
			$key = ( $key_db['key'] ) ? md5( $key_db['key'] ) : '' ;
		}
		$updater_data['key'] = $key;

		/* Domain */
		$updater_data['domain'] = esc_url_raw( get_bloginfo( 'url' ) );

		/* Repo uri */
		$repo_uri = '';
		if ( !empty( $this->config['repo_uri'] ) )
			$repo_uri = trailingslashit( esc_url_raw( $this->config['repo_uri'] ) );
		$updater_data['repo_uri'] = $repo_uri;

		/* Repo slug */
		$repo_slug = '';
		if ( !empty( $this->config['repo_slug'] ) )
			$repo_slug = sanitize_title( $this->config['repo_slug'] );
		$updater_data['repo_slug'] = $repo_slug;

		return $updater_data;
	}


	/**
	 * Check for plugin updates
	 * 
	 * @since 0.1.0
	 */
	public function transient_update_plugins( $checked_data ) {

		global $wp_version;

		/* Check the data */
		if ( empty( $checked_data->checked ) )
			return $checked_data;

		/* Get needed data */
		$updater_data = $this->updater_data();

		/* Get data from server */
		$remote_url = add_query_arg( array( 'plugin_repo' => $updater_data['repo_slug'], 'ahpr_check' => $updater_data['version'] ), $updater_data['repo_uri'] );
		$remote_request = array( 'timeout' => 20, 'body' => array( 'key' => $updater_data['key'] ), 'user-agent' => 'WordPress/' . $wp_version . '; ' . $updater_data['domain'] );
		$raw_response = wp_remote_post( $remote_url, $remote_request );

		/* Error check */
		$response = '';
		if ( !is_wp_error( $raw_response ) && ( $raw_response['response']['code'] == 200 ) )
			$response = maybe_unserialize( wp_remote_retrieve_body( $raw_response ) );

		/* Check response data */
		if ( is_object( $response ) && !empty( $response )){

			/* Check the data is available */
			if ( isset( $response->new_version ) && !empty( $response->new_version ) && isset( $response->package ) && !empty( $response->package ) ){

				/* Create response data object */
				$updates = new stdClass;
				$updates->new_version = $response->new_version;
				$updates->package = $response->package;
				$updates->slug = $updater_data['slug'];
				$updates->url = $updater_data['uri'];

				/* Set response if not set yet. */
				if ( !isset( $checked_data->response ) )
					$checked_data->response = array();

				/* Feed the update data */
				$checked_data->response[$updater_data['basename']] = $updates;
			}
		}
		return $checked_data;
	}

	/**
	 * Filter Plugin API
	 * 
	 * @since 0.1.0
	 */
	public function plugins_api_result( $res, $action, $args ) {

		global $wp_version;

		/* Get needed data */
		$updater_data = $this->updater_data();

		/* Get data only from current plugin, and only when call for "plugin_information" */
		if ( isset( $args->slug ) && $args->slug == $updater_data['slug'] && $action == 'plugin_information' ){

			/* Get data from server */
			$remote_url = add_query_arg( array( 'plugin_repo' => $updater_data['repo_slug'], 'ahpr_info' => $updater_data['version'] ), $updater_data['repo_uri'] );
			$remote_request = array( 'timeout' => 20, 'body' => array( 'key' => $updater_data['key'] ), 'user-agent' => 'WordPress/' . $wp_version . '; ' . $updater_data['domain'] );
			$request = wp_remote_post( $remote_url, $remote_request );

			/* If error on retriving the data from repo */
			if ( is_wp_error( $request ) ) {
				$res = new WP_Error( 'plugins_api_failed', '<p>' . __( 'An Unexpected HTTP Error occurred during the API request.', 'auto-hosted' ) . '</p><p><a href="?" onclick="document.location.reload(); return false;">' . __( 'Try again', 'auto-hosted' ) . '</a></p>', $request->get_error_message() );
			}

			/* If no error, construct the data */
			else {

				/* Unserialize the data */
				$requested_data = maybe_unserialize( wp_remote_retrieve_body( $request ) );

				/* Check response data is available */
				if ( is_object( $requested_data ) && !empty( $requested_data )){

					/* Check the data is available */
					if ( isset( $requested_data->version ) && !empty( $requested_data->version ) && isset( $requested_data->download_link ) && !empty( $requested_data->download_link ) ){

						/* Create plugin info data object */
						$info = new stdClass;

						/* Data from repo */
						$info->version = $requested_data->version;
						$info->download_link = $requested_data->download_link;
						$info->requires = $requested_data->requires;
						$info->tested = $requested_data->tested;
						$info->sections = $requested_data->sections;

						/* Data from plugin */
						$info->slug = $updater_data['slug'];
						$info->author = $updater_data['author'];
						$info->uri = $updater_data['uri'];

						/* Other data needed */
						$info->external = true;
						$info->downloaded = 0;

						/* Feed plugin information data */
						$res = $info;
					}
				}

				/* If data is empty or not an object */
				else{
					$res = new WP_Error( 'plugins_api_failed', __( 'An unknown error occurred', 'auto-hosted' ), wp_remote_retrieve_body( $request ) );
				
				}
			}
		}
		return $res;
	}


	/**
	 * Make sure plugin is installed in correct folder
	 * 
	 * @since 0.1.0
	 */
	public function upgrader_post_install( $true, $hook_extra, $result ) {

		/* Check if hook extra is set */
		if ( isset( $hook_extra ) ){

			/* Get needed data */
			$plugin_base = $this->config['base'];
			$plugin_slug = dirname( $plugin_base );

			/* Only filter folder in this plugin only */
			if ( isset( $hook_extra['plugin'] ) && $hook_extra['plugin'] == $plugin_base ){

				/* wp_filesystem api */
				global $wp_filesystem;

				/* Move & Activate */
				$proper_destination = trailingslashit( WP_PLUGIN_DIR ) . $plugin_slug;
				$wp_filesystem->move( $result['destination'], $proper_destination );
				$result['destination'] = $proper_destination;
				$activate = activate_plugin( trailingslashit( WP_PLUGIN_DIR ) . $plugin_base );

				/* Update message */
				$fail = __( 'The plugin has been updated, but could not be reactivated. Please reactivate it manually.', 'auto-hosted' );
				$success = __( 'Plugin reactivated successfully.', 'auto-hosted' );
				echo is_wp_error( $activate ) ? $fail : $success;
			}
		}
		return $result;
	}
}