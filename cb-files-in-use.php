<?php
/*
Plugin Name: Files in use
Description: A simple plugin to show theme's and plugins' files that are currently in use.
Plugin URI: http://medhabi.com
Author: Nazmul Ahsan
Author URI: http://nazmulashan.me
Version: 1.0
License: GPL2
Text Domain: cb-files-in-use
Domain Path: /languages
*/

/*

    Copyright (C) 2016  Nazmul Ahsan  n.mukto@gmail.com

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main class for the plugin
 * @package @WordPress
 * @subpackage CB_Files_In_Use
 * @author Nazmul Ahsan
 */
if( ! class_exists( 'CB_Files_In_Use' ) ) :

class CB_Files_In_Use {
	
	public static $_instance;

	public function __construct() {
		add_action( 'admin_bar_init', array( $this, 'admin_bar_init' ) );
	}
	
	function admin_bar_init() {
		if( is_admin() || ! is_super_admin() || ! is_admin_bar_showing() ) return;
		add_action('admin_bar_menu', array( $this, 'admin_bar_menu' ), PHP_INT_MAX );
	}

	function admin_bar_menu() {
		global $wp_admin_bar, $template;
		$menu_id = 'cb_files_in_use';
		
		/**
		 * sets some options
		 */
		$show_included_files = apply_filters( 'cb_monitor_show_included_files', true );
		$show_theme_files = apply_filters( 'cb_monitor_show_theme_files', true );
		$show_plugin_files = apply_filters( 'cb_monitor_show_plugin_files', false );
		$menu_label = apply_filters( 'cb_monitor_menu_label', '<strong style="font-weight:bold;color:#fff">Files in use: </strong>' );
		$menu_meta = apply_filters( 'cb_monitor_menu_meta', array( 'target' => '_blank' ) );

		/**
		 * adds top level menu with template name currently in use
		 */
		$template_file_subpath = substr( $template, ( strpos( $template, 'wp-content/') + 10 ) );
		$file_name = explode( get_stylesheet() . '/', $template_file_subpath )[1];
		$wp_admin_bar->add_menu(
			array(
			'title' => $menu_label . $template_file_subpath,
			'href' => admin_url( 'theme-editor.php?file=' . $file_name . '&theme=' . get_stylesheet() ),
			'id' => $menu_id,
			'meta' => $menu_meta
			)
		);

		/**
		 * adds submenus with all the theme's and plugins' files included
		 */
		$include_files = get_included_files();

		if( count( $include_files ) && $show_included_files ){
			foreach ( $include_files as $included_file ) {
				
				if( strpos( $included_file, 'wp-content') !== false ){
					
					$include_file_subpath = str_replace( '\\', '/', substr( $included_file, ( strpos( $included_file, 'wp-content') + 10 ) ) );
					
					/**
					 * if it's a theme file
					 */
					if( strpos( $include_file_subpath, 'themes' ) && $show_theme_files ){
						$include_file_name = explode( get_stylesheet() . '/', $include_file_subpath )[1];
						$edit_url = admin_url( 'theme-editor.php?file=' . $include_file_name . '&theme=' . get_stylesheet() );

						$wp_admin_bar->add_menu(
							array(
								'parent' => $menu_id,
								'title' => $include_file_subpath,
								'id' => $include_template_name,
								'href' => $edit_url,
								'meta' => $menu_meta
							)
						);
					}

					/**
					 * if it's a plugin file
					 */
					if( strpos( $include_file_subpath, 'plugins' ) && $show_plugin_files ){
						$include_file_name = explode( 'plugins/', $include_file_subpath )[1];
						$edit_url = admin_url( 'plugin-editor.php?file=' . $include_file_name );
		
						$wp_admin_bar->add_menu(
							array(
								'parent' => $menu_id,
								'title' => $include_file_subpath,
								'id' => $include_template_name,
								'href' => $edit_url,
								'meta' => $menu_meta
							)
						);
					}
				}
			}
		}
	}

	/**
	 * Instantiate the plugin
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
}

endif;

CB_Files_In_Use::instance();
