<?php
/**
 * conf class
 *
 * @since  1.0
 */

if ( ! defined( 'WPINC' ) ) {
	die ;
}


class Debug_Config
{
	private static $_instance ;

	const ITEM_CONF = 'debug-conf' ;
	const ITEM_IP = 'debug-ip' ;
	const ITEM_EXC_FILTERS = 'debug-exc_filters' ;
	const ITEM_EXC_PART_FILTERS = 'debug-exc_part_filters' ;

	const OPT_DEBUG 		= 'debug' ;
	const OPT_LEVEL 		= 'level' ;
	const OPT_LOG_COOKIE 	= 'log_cookie' ;
	const OPT_LOG_AGENT 	= 'log_agent' ;
	const OPT_COLLAPSE_QS 	= 'collapse_qs' ;
	const OPT_LOG_FILTER 	= 'log_filter' ;
	const OPT_LOG_FILESIZE 	= 'log_filesize' ;


	/**
	 * Constructor
	 * @since  1.0
	 * @access private
	 */
	private function __construct()
	{

		$options = get_option( self::ITEM_CONF, $this->defaults() ) ;
	}

	public function defaults()
	{
		$defaults = array(
			self::OPT_DEBUG 	=> true,
		) ;
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
