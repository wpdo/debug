<?php
/**
 * conf class
 *
 * @since  1.0
 */

if ( ! defined( 'WPINC' ) ) {
	die ;
}


class WPDO_Debug_Config
{
	private static $_instance ;

	protected $options ;

	const ITEM_CONF = 'debug-config' ;
	const ITEM_IP = 'debug-ip' ;
	const ITEM_EXC_FILTERS = 'debug-exc-filters' ;
	const ITEM_EXC_PART_FILTERS = 'debug-exc-part-filters' ;

	const OPT_DEBUG 		= 'debug' ;
	const OPT_IP_ONLY 		= 'ip_only' ;
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

		$this->options = get_option( self::ITEM_CONF, $this->default_options() ) ;
	}

	/**
	 * Get option val
	 *
	 * @since 1.0
	 * @access public
	 */
	public static function option( $id )
	{
		$id = constant( 'self::OPT_' . $id ) ;

		if ( isset( self::get_instance()->options[ $id ] ) ) {
			return self::get_instance()->options[ $id ] ;
		}

		defined( 'debug' ) && debug( '[Cfg] Invalid option ID ' . $id ) ;

		return NULL ;
	}

	/**
	 * Get item val
	 *
	 * @since  1.0
	 * @access public
	 */
	public static function item( $k, $return_string = false )
	{
		$k = constant( 'self::ITEM_' . $k ) ;

		$val = get_option( $k, self::get_instance()->default_item( $k ) ) ;

		if ( ! $return_string && ! is_array( $val ) ) {
			$val = $val ? explode( "\n", $val ) : array() ;
		}
		elseif ( $return_string && is_array( $val ) ) {
			$val = implode( "\n", $val ) ;
		}

		return $val ;
	}
	/**
	 * Get default options val
	 *
	 * @since 1.0
	 * @access public
	 */
	public function default_options()
	{
		$default_options = array(
			self::OPT_DEBUG 		=> true,
			self::OPT_IP_ONLY 		=> true,
			self::OPT_LEVEL 		=> false,
			self::OPT_LOG_COOKIE	=> false,
			self::OPT_LOG_AGENT		=> false,
			self::OPT_COLLAPSE_QS	=> false,
			self::OPT_LOG_FILTER	=> false,
			self::OPT_LOG_FILESIZE	=> '2',
		) ;

		return $default_options ;
	}

	/**
	 * Get default item val
	 *
	 * @since 1.0
	 * @access public
	 */
	public function default_item( $k )
	{
		switch ( $k ) {
			case self::ITEM_IP :
				return	"127.0.0.1" ;

			case self::ITEM_EXC_FILTERS :
				return	"gettext\n" .
						"gettext_with_context\n" .
						"get_the_terms\n" .
						"get_term" ;

			case self::ITEM_EXC_PART_FILTERS :
				return	"i18n\n" .
						"locale\n" .
						"settings\n" .
						"option" ;

			default :
				break ;
		}

		return false ;
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
