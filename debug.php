<?php
/**
 * The plugin bootstrap file
 *
 * @since             1.0
 * @package           debug
 *
 * @wordpress-plugin
 * Plugin Name:       Debug
 * Plugin URI:        https://wordpress.org/plugins/debug
 * Description:       WordPress debug plugin for developers.
 * Version:           1.0
 * Author:            WPdev
 * License:           GPLv3
 * License URI:       http://www.gnu.org/licenses/gpl.html
 * Text Domain:       debug
 *
 * Copyright (C) 2018 WPdev
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.

 */
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die ;
}

if ( function_exists( 'debug' ) || defined( 'debug' ) ) {
	return ;
}

! defined( 'DEBUG_PLUGIN_URL' ) && define( 'DEBUG_PLUGIN_URL', plugin_dir_url( __FILE__ ) ) ;// Full URL path '//example.com/wp-content/plugins/debug/'
! defined( 'DEBUG_DIR' ) && define( 'DEBUG_DIR', dirname( __FILE__ ) . '/' ) ;// Full absolute path '/usr/***/wp-content/plugins/debug/'

require_once DEBUG_DIR . 'autoload.inc.php' ;

if ( ! function_exists( 'debug' ) ) {
	function debug( $msg, $backtrace_limit = false )
	{
		Debug::debug( $msg, $backtrace_limit ) ;
	}
}

if ( ! function_exists( 'debug2' ) ) {
	function debug2( $msg, $backtrace_limit = false )
	{
		Debug::debug2( $msg, $backtrace_limit ) ;
	}
}

if ( ! function_exists( 'launch_debug' ) ) {
	function launch_debug()
	{
		$version_supported = true ;

		//Check minimum PHP requirements, which is 5.3 at the moment.
		if ( version_compare( PHP_VERSION, '5.3.0', '<' ) ) {
			$version_supported = false ;
		}

		//Check minimum WP requirements, which is 4.0 at the moment.
		if ( version_compare( $GLOBALS['wp_version'], '4.0', '<' ) ) {
			$version_supported = false ;
		}

		if ( $version_supported ) {
			Debug::get_instance() ;
		}
	}

	launch_debug() ;
}

