<?php
/**
 * Auto registration
 *
 * @since  1.0
 */

if ( ! defined( 'WPINC' ) ) {
	die ;
}

if ( ! function_exists( '_debug_autoload' ) ) {
	function _debug_autoload( $cls )
	{
		$class2file = array(
			'Debug'		=> 'debug.class.php',
		) ;

		if ( array_key_exists( $cls, $class2file ) && file_exists( DEBUG_PLUGIN_URL . $class2file[ $cls ] ) ) {
			require_once DEBUG_PLUGIN_URL . $class2file[ $cls ] ;
		}
	}
}

spl_autoload_register( '_debug_autoload' ) ;
