<?php
/**
 * Main class
 *
 * @since  1.0
 */

if ( ! defined( 'WPINC' ) ) {
	die ;
}


class Debug
{
	private static $_instance ;

	const V = '1.0' ;

	/**
	 * @since  1.0
	 * @access private
	 */
	private function __construct()
	{
		// Read config
		Debug_Config::get_instance() ;
	}


	/**
	 * Get the current instance object.
	 *
	 * @since 1.0
	 * @access public
	 * @return Current class instance.
	 */
	public static function get_instance()
	{
		if ( ! isset( self::$_instance ) ) {
			self::$_instance = new self() ;
		}

		return self::$_instance ;
	}

}
