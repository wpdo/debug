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
			'WPDO_Debug'			=> 'debug.class.php',
			'WPDO_Debug_Config'		=> 'debug.class.php',
			'WPDO_String'			=> 'string.class.php',
		) ;

		if ( array_key_exists( $cls, $class2file ) && file_exists( WPDO_DEBUG_DIR . $class2file[ $cls ] ) ) {
			require_once WPDO_DEBUG_DIR . $class2file[ $cls ] ;
		}
	}
}

spl_autoload_register( '_wpdo_debug_autoload' ) ;
