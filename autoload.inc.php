<?php
/**
 * Auto registration
 *
 * @since  1.0
 */

if ( ! defined( 'WPINC' ) ) {
	die ;
}

if ( ! function_exists( '_wpdo_debug_autoload' ) ) {
	function _wpdo_debug_autoload( $cls )
	{
		$class2file = array(
			'WPDO_Debug'			=> 'debug.cls.php',
			'WPDO_Debug_Admin'		=> 'admin.cls.php',
			'WPDO_Debug_Config'		=> 'debug_config.cls.php',
			'WPDO_String'			=> 'string.cls.php',
		) ;

		if ( array_key_exists( $cls, $class2file ) && file_exists( WPDO_DEBUG_DIR . $class2file[ $cls ] ) ) {
			require_once WPDO_DEBUG_DIR . $class2file[ $cls ] ;
		}
	}
}

spl_autoload_register( '_wpdo_debug_autoload' ) ;
