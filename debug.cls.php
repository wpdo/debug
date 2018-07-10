<?php
/**
 * Main class
 *
 * @since  1.0
 */

if ( ! defined( 'WPINC' ) ) {
	die ;
}


class WPDO_Debug
{
	private static $_instance ;

	private static $_ignore_filters ;
	private static $_ignore_part_filters ;

	const V = '1.0' ;

	/**
	 * @since  1.0
	 * @access private
	 */
	private function __construct()
	{
		// Read config
		WPDO_Debug_Config::get_instance() ;

		if ( defined( 'WPDO_DEBUG_FUNC' ) && WPDO_Debug_Config::option( 'DEBUG' ) ) {
			if ( ! WPDO_Debug_Config::option( 'IP_ONLY' ) || $this->ip_access( WPDO_Debug_Config::item( 'IP' ) ) ) {
				// Allow log
				$this->_init_log() ;
				define( 'WPDO_LOG', true ) ;

				// Check if hook filters
				if ( WPDO_Debug_Config::option( 'LOG_FILTER' ) ) {
					self::$_ignore_filters = WPDO_Debug_Config::item( 'EXC_FILTERS' ) ;
					self::$_ignore_part_filters = WPDO_Debug_Config::item( 'EXC_PART_FILTERS' ) ;

					add_action( 'all', 'WPDO_Debug::log_filters' ) ;
				}

			}
		}
	}

	/**
	 * Create the initial log messages with the request parameters.
	 *
	 * @since 1.0
	 * @access private
	 */
	private function _init_log()
	{
		define( 'debug', self::V ) ;

		! defined( 'WPDO_LOG_FILE' ) && define( 'WPDO_LOG_FILE', WP_CONTENT_DIR . '/debug.log' ) ;

		if ( ! defined( 'WPDO_LOG_TAG' ) ) {
			define( 'WPDO_LOG_TAG', get_current_blog_id() ) ;
		}

		if ( WPDO_Debug_Config::option( 'LEVEL' ) ) {
			define( 'WPDO_LOG_MORE', true ) ;
		}

		// Check log file size
		$log_file_size = WPDO_Debug_Config::option( 'LOG_FILESIZE' ) ;
		if ( file_exists( WPDO_LOG_FILE ) && filesize( WPDO_LOG_FILE ) > $log_file_size * 1000000 ) {
			file_put_contents( WPDO_LOG_FILE, '' ) ;
		}

		// For more than 2s's requests, add more break
		if ( file_exists( WPDO_LOG_FILE ) && time() - filemtime( WPDO_LOG_FILE ) > 2 ) {
			file_put_contents( WPDO_LOG_FILE, "\n\n\n\n", FILE_APPEND ) ;
		}

		// CLI no log URL info
		if ( PHP_SAPI == 'cli' ) {
			return ;
		}

		$servervars = array(
			'Query String' => '',
			'HTTP_USER_AGENT' => '',
			'HTTP_ACCEPT_ENCODING' => '',
			'HTTP_COOKIE' => '',
		) ;
		$server = array_merge( $servervars, $_SERVER ) ;
		$params = array() ;

		if ( isset( $_SERVER[ 'HTTPS' ] ) && $_SERVER[ 'HTTPS' ] == 'on' ) {
			$server[ 'SERVER_PROTOCOL' ] .= ' (HTTPS) ' ;
		}

		$param = sprintf( '------%s %s %s', $server[ 'REQUEST_METHOD' ], $server[ 'SERVER_PROTOCOL' ], strtok( $server[ 'REQUEST_URI' ], '?' ) ) ;

		$qs = ! empty( $server[ 'QUERY_STRING' ] ) ? $server[ 'QUERY_STRING' ] : '' ;
		if ( WPDO_Debug_Config::option( 'COLLAPSE_QS' ) ) {
			if ( strlen( $qs ) > 53 ) {
				$qs = substr( $qs, 0, 53 ) . '...' ;
			}
			if ( $qs ) {
				$param .= ' ? ' . $qs ;
			}
			$params[] = $param ;
		}
		else {
			$params[] = $param ;
			$params[] = 'Query String: ' . $qs ;
		}

		// Log user agent
		if ( defined( 'WPDO_LOG_MORE' ) ) {
			$params[] = 'User Agent: ' . $server[ 'HTTP_USER_AGENT' ] ;
			$params[] = 'Accept Encoding: ' . $server[ 'HTTP_ACCEPT_ENCODING' ] ;
		}

		// Log cookie
		if ( WPDO_Debug_Config::option( 'LOG_COOKIE' ) ) {
			$params[] = 'Cookie: ' . $server[ 'HTTP_COOKIE' ] ;
		}

		$request = array_map( 'self::format_message', $params ) ;

		file_put_contents( WPDO_LOG_FILE, $request, FILE_APPEND ) ;
	}

	/**
	 * Log debug
	 *
	 * @since  1.0
	 * @access public
	 */
	public static function debug( $msg, $backtrace_limit = false )
	{
		if ( ! defined( 'debug' ) || debug !== self::V ) {
			return ;
		}

		if ( ! is_string( $msg ) ) {
			$msg = var_export( $msg, true ) ;
		}

		if ( $backtrace_limit !== false ) {
			if ( ! is_numeric( $backtrace_limit ) ) {
				$msg .= ' --- ' . var_export( $backtrace_limit, true ) ;
				self::push( $msg ) ;
				return ;
			}

			self::push( $msg, $backtrace_limit + 1 ) ;
			return ;
		}

		self::push( $msg ) ;

	}

	/**
	 * Log adv debug
	 *
	 * @since  1.0
	 * @access public
	 */
	public static function debug2( $msg, $backtrace_limit = false )
	{
		if ( ! defined( 'WPDO_LOG_MORE' ) ) {
			return ;
		}
		self::debug( $msg, $backtrace_limit ) ;
	}

	/**
	 * Save debug
	 *
	 * @since  1.0
	 * @access private
	 */
	private static function push( $msg, $backtrace_limit = false )
	{
		// backtrace handler
		if ( defined( 'WPDO_LOG_MORE' ) && $backtrace_limit !== false ) {
			$trace = version_compare( PHP_VERSION, '5.4.0', '<' ) ? debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS ) : debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, $backtrace_limit + 2 ) ;
			for ( $i=1 ; $i <= $backtrace_limit + 1 ; $i++ ) {// the 0st item is push()
				if ( empty( $trace[ $i ][ 'class' ] ) ) {
					if ( empty( $trace[ $i ][ 'file' ] ) ) {
						break ;
					}
					$log = "\n" . $trace[ $i ][ 'file' ] ;
				}
				else {
					if ( $trace[ $i ][ 'class' ] == 'WPDO_Debug' ) {
						continue ;
					}

					$log = $trace[ $i ][ 'class' ] . $trace[ $i ][ 'type' ] . $trace[ $i ][ 'function' ] . '()' ;
				}

				if ( ! empty( $trace[ $i - 1 ][ 'line' ] ) ) {
					$log .= '@' . $trace[ $i - 1 ][ 'line' ] ;
				}
				$msg .= " => $log" ;
			}

		}

		file_put_contents( WPDO_LOG_FILE, self::format_message( $msg ), FILE_APPEND ) ;
	}

	private static function format_message( $msg )
	{
		// If call here without calling get_enabled() first, improve compatibility
		if ( ! defined( 'WPDO_LOG_TAG' ) ) {
			return $msg . "\n" ;
		}

		if ( ! isset( self::$_prefix ) ) {
			// address
			if ( PHP_SAPI == 'cli' ) {
				$addr = '=CLI=' ;
				if ( isset( $_SERVER[ 'USER' ] ) ) {
					$addr .= $_SERVER[ 'USER' ] ;
				}
				elseif ( $_SERVER[ 'HTTP_X_FORWARDED_FOR' ] ) {
					$addr .= $_SERVER[ 'HTTP_X_FORWARDED_FOR' ] ;
				}
			}
			else {
				$addr = $_SERVER[ 'REMOTE_ADDR' ] . ':' . $_SERVER[ 'REMOTE_PORT' ] ;
			}

			// Generate a unique string per request
			self::$_prefix = sprintf( " [%s %s %s] ", $addr, WPDO_LOG_TAG, WPDO_String::rrand( 3 ) ) ;
		}

		list( $usec, $sec ) = explode(' ', microtime() ) ;

		return date( 'm/d/y H:i:s', $sec + WPDO_TIME_OFFSET ) . substr( $usec, 1, 4 ) . self::$_prefix . $msg . "\n" ;
	}

	/**
	 * Log all filters and action hooks
	 *
	 * @since  1.0
	 * @access public
	 */
	public static function log_filters()
	{
		$action = current_filter() ;

		if ( self::$_ignore_filters && in_array( $action, self::$_ignore_filters ) ) {
			return ;
		}

		if ( self::$_ignore_part_filters ) {
			foreach ( self::$_ignore_part_filters as $val ) {
				if ( stripos( $action, $val ) !== false ) {
					return ;
				}
			}
		}

		self::debug( "===log filter: $action" ) ;
	}

	/**
	 * Check if the ip is in the range
	 *
	 * @since 1.0
	 * @access private
	 * @param  string $ip_list IP list
	 * @return bool
	 */
	private function ip_access( $ip_list )
	{
		if ( ! $ip_list ) {
			return false ;
		}
		if ( ! defined( 'WPDO_CLIENT_IP' ) ) {
			define( 'WPDO_CLIENT_IP', $this->get_ip() ) ;
		}
		// $uip = explode('.', $_ip) ;
		// if(empty($uip) || count($uip) != 4) Return false ;
		if ( ! is_array( $ip_list ) ) {
			$ip_list = explode( "\n", $ip_list ) ;
		}
		// foreach($ip_list as $key => $ip) $ip_list[$key] = explode('.', trim($ip)) ;
		// foreach($ip_list as $key => $ip) {
		// 	if(count($ip) != 4) continue ;
		// 	for($i = 0 ; $i <= 3 ; $i++) if($ip[$i] == '*') $ip_list[$key][$i] = $uip[$i] ;
		// }
		return in_array( WPDO_CLIENT_IP, $ip_list ) ;
	}

	/**
	 * Get client ip
	 *
	 * @since 1.0
	 * @access protected
	 * @return string
	 */
	protected function get_ip()
	{
		$_ip = '' ;
		if ( function_exists( 'apache_request_headers' ) ) {
			$apache_headers = apache_request_headers() ;
			$_ip = ! empty( $apache_headers[ 'True-Client-IP' ] ) ? $apache_headers[ 'True-Client-IP' ] : false ;
			if ( ! $_ip ) {
				$_ip = ! empty( $apache_headers[ 'X-Forwarded-For' ] ) ? $apache_headers[ 'X-Forwarded-For' ] : false ;
				$_ip = explode( ", ", $_ip ) ;
				$_ip = array_shift( $_ip ) ;
			}

			if ( ! $_ip ) {
				$_ip = ! empty( $_SERVER[ 'REMOTE_ADDR' ] ) ? $_SERVER[ 'REMOTE_ADDR' ] : false ;
			}
		}
		return $_ip ;
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
