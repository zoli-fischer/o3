<?php 

/**
 * O3 Engine debug
 *
 * Used for debuging. O3 debuger shows php and javascript errors in the same web browsers consol window.
 *
 * @package o3
 * @link    todo: add url
 * @author  Zotlan Fischer <zoli_fischer@yahoo.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
  
//o3 debug/log types
if ( !defined( 'O3_LOG' ) )
	/** int Value: 1 */
	define('O3_LOG',1);
if ( !defined( 'O3_DEBUG' ) )
	/** int Value: 2 */
	define('O3_DEBUG',2);

//o3 debug allow debuging
if ( !defined( 'O3_DEBUG_ALLOWED' ) )
	/** boolean Value: false */
	define('O3_DEBUG_ALLOWED',false);

/**
 * O3 Engine debug class
 *
 * Used for debuging. O3 debuger shows php and javascript errors in the same web browsers consol window.
 *
 * @example example.php
 * <b>//as O3 Engine module</b> <br>
 * $o3 = new o3();<br>
 * $o3->debug->('Insert into log file and debug console', O3_LOG | O3_DEBUG);<br>
 * $o3->debug->('Insert into debug console only');<br><br>
 * <b>//as o3_debug class</b> <br>  
 * $o3_debug = new o3_debug();<br>
 * $o3_debug->('Insert into console');<br>
 * $o3_debug->show( true );
 *
 * @package o3_debug
 */
class o3_debug {

	/** mixed Parent object */
	public $parent = null;
	
	/** boolean Set to show or hide the debug list */
	private $is_show = true;
	
	/** array List of debug messages */
	private $_s = array();
	
	/** int Script start time in milliseconds */
	private $start_time = 0;
	
	/** int Script end time in milliseconds */
	private $end_time = 0;
	
	/** int Total memory used by the script */
	private $total_memory_usage = 0;

	/** int Peak memory used by the script */
	private $peak_memory_usage = 0;
	
	/** int Resource used by the script */
	private $resource_usage = false;

	/** array List of shutdown callback functions */
	private $shutdown_callbacks = array();
	
	/** array List of output buffer callback functions */
	private $ob_start_callbacks = array(); 

	/**
	 * O3 debug constructor
	 * @return void
	 */	
	public function __construct() {
		ob_start( array( $this, 'ob_start_callback' ) ); //set output buffer callback
		register_shutdown_function( array( $this, 'shutdown_function' ) ); //set shutdown callback
		
		$this->start_time = o3_micro_time(); //store start time
		$this->_('Started: '.date('d.m.Y H:i',$this->start_time));		
		
		//set custom error handler
		set_error_handler( array( $this, 'errorHandler' ) );
		
	}
  
 	/**
	 * Custom error handler
	 *
	 * Errors are trasformed to consol error. <br><br> <i>Only E_USER_ERROR stops the script.</i> 
	 *
	 * @link http://www.php.net/manual/en/function.set-error-handler.php
	 *
	 * @param int $errno Level of the error raised
	 * @param string $errstr Error message
	 * @param string $errfile Filename that the error was raised
	 * @param int $errline Line number the error was raised at
	 * @param array $errcontext Array of every variable that existed in the scope the error was triggered in. User error handler must not modify error context. 
	 *
	 * @return boolean
	 */ 
	public function errorHandler( $errno, $errstr, $errfile, $errline, $errcontext ) {

	 	//check for error reporting level
		//if ( $errno & error_reporting() ) {
			switch ($errno) {
				case E_USER_ERROR:
				case E_ERROR:
				    $this->_( "[Error:$errno] $errstr", O3_LOG | O3_DEBUG, 0, $errno );
					$this->_( "On line $errline in $errfile", O3_LOG | O3_DEBUG, 0, $errno );
					$this->die_(); //stop script
				    break;
				case E_USER_WARNING:
				case E_WARNING:
				    $this->_( "[Warning:$errno] $errstr", O3_LOG | O3_DEBUG, 0, $errno );
					$this->_( "On line $errline in $errfile", O3_LOG | O3_DEBUG, 0, $errno );
				    break;
				case E_USER_NOTICE:
				case E_NOTICE:
				    $this->_( "[Notice:$errno] $errstr", O3_LOG | O3_DEBUG, 0, $errno );
					$this->_( "On line $errline in $errfile", O3_LOG | O3_DEBUG, 0, $errno );
				    break;
				default: 
				    $this->_( "[Unknown:$errno] $errstr", O3_LOG | O3_DEBUG, 0, $errno );
					$this->_( "On line $errline in $errfile", O3_LOG | O3_DEBUG, 0, $errno );
					break;
			}
		//}
	  
		/* Don't execute PHP internal error handler */
		return true;
	}
  
	/**
	 * Output a message and terminate the current script
	 *
	 * @param string $message Function prints the status just before exiting
	 * @param int $message (optional) Value will be used as the exit status and not printed
	 *
	 * @return void
	 */
	public function die_( $message = '' ) {
		die( $message );
	}
  
	/**
	 * Function sets the parent object
	 *
	 * @param object $parent Parent object
	 *
	 * @return object
	 */
	public function set_parent( $parent ) {
		return $this->parent = &$parent;
	}
   	
	/**
	* Same as add.
	*
	* @param string $message Message to display
	* @param int $type (optional) Debug message type. Accepted values: O3_DEBUG (default) or O3_LOG.
	* @param int $time (optional) Timestamp for the date to display. Value is current time if no timestamp is given.
	* @param int $error_code (optional) Set error code. Default value is E_USER_NOTICE.
	*
	* @see o3_debug::add() o3_debug::add()
	*/
	public function _( $message, $type = O3_DEBUG, $time = 0, $error_code = E_USER_NOTICE ) { 		
		$this->add( $message, $type, $time, $error_code );		
	}
 	
	/**
	* Add new line to the debug message list.
	*
	* @param string $message Message to display
	* @param int $type (optional) Debug message type. Accepted values: O3_DEBUG (default) or O3_LOG.
	* @param int $time (optional) Timestamp for the date to display. Value is current time if no timestamp is given.
	* @param int $error_code (optional) Set error code. Default value is E_USER_NOTICE.
	*
	* @see o3_debug_list_item o3_debug_list_item
	* @see O3_DEBUG O3_DEBUG
	* @see O3_LOG O3_LOG
	*	 
	* @return void
	*/
	public function add( $message, $type = O3_DEBUG, $time = 0, $error_code = E_USER_NOTICE ) { 		
		$time = $time > 0 ? time() : '';
		$this->_s[] = new o3_debug_list_item( $message, $time, $type, $error_code );
	}
  
	/**
	* Set to show the debug messages in the console.
	*
	* @param boolean $value (optional) Values: true - show, false - don't show
	*
	* @return void
	*/
	public function show( $value = false ) {
		$this->is_show = $value;
	}
  
	/**
	 * Function injects the debug javascript script in html code after the head tag.
	 *
	 * Custom output buffer callbacks are also executed.<br> 
	 * The code is only injected if the show was allowed. 
	 * <i>The injected script creates the console object if not exists in the web browser.</i>
	 *
	 * @see o3_debug::show() o3_debug::show()
	 * @see o3_debug::add_ob_start_callback() o3_debug::add_ob_start_callback()
	 * 
	 * @param string $buffer
	 *
	 * @return void
	 */
	public function ob_start_callback( $buffer ) {
		
		//callbacks
		foreach ( $this->ob_start_callbacks as $key => $value ) {
			if ( !is_array($value) ) {
				$buffer = call_user_func( $value, $buffer );
			} else if ( count($value) == 2 ) {
				$buffer = call_user_func( array( $value[0], $value[1] ), $buffer );
			}
		}
		
		if ( $this->is_show && O3_DEBUG_ALLOWED ) {
			$scriptData = array();		
			$consoleData = array();	
			foreach ( $this->_s as $key => $value ) {
				if ( $value->type & O3_DEBUG ) { //&& ( $value->error_code & error_reporting() )
					$scriptData[] = "console.log('".addslashes($value->prnt(false))."');";
					$consoleData[] = $value->prnt(false);
				}
			}
						
			$scriptCode = '
	<script type="text/javascript">
		if ( typeof console == \'undefined\' ) { console = {}; console.log = function(str) {};	};
		'.implode("\n",$scriptData).'
	</script>';
			
			$obj = json_decode( $buffer );			
			//if json add debug to object
			if ( is_object($obj) ) {
				$obj->o3_console = $consoleData;
				return json_encode($obj);
			} else {
				//split html at head tag
				$matches = preg_split('/(<head.*?>)/i', $buffer, 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE ); 
				if ( count($matches) > 1 ) {
					//assemble the html output back with the script code in it
					$buffer = $matches[0] . $matches[1] . ( !preg_match( '/(<header)/i', $matches[1]) ? $scriptCode : '' );
					for ( $i = 2; $i < count($matches); $i++ )
						$buffer .= $matches[$i];
				}
			}
		}
		return $buffer;		
	}
	
	/**
	 * Runed before script shutdown.
	 *
	 * Function added as shutdown callback in the contstructor. <br>
	 * The functions runs the added custom debug shutdown callbacks.
	 *
	 * @see o3_debug::$shutdown_callbacks o3_debug::$shutdown_callbacks
	 * @see o3_debug::add_shutdown_callback() o3_debug::add_shutdown_callback()
	 *
	 * @return void
	 */
	public function shutdown_function() {
		$this->end_time = o3_micro_time(); //store start time
		$this->total_memory_usage = memory_get_usage();
		$this->peak_memory_usage = memory_get_peak_usage();
				
		$this->_('Total memory usage: '.o3_bytes_display('v U',$this->total_memory_usage));
		$this->_('Peak memory usage: '.o3_bytes_display('v U',$this->peak_memory_usage));
		
		if ( function_exists('getrusage') ) {
			$this->resource_usage = getrusage();
			$this->_('CPU usage: '. ($this->resource_usage['ru_utime.tv_sec'] + $this->resource_usage['ru_utime.tv_usec'] / 1000000) );
			$this->_('CPU system usage: '. ($this->resource_usage['ru_stime.tv_sec'] + $this->resource_usage['ru_stime.tv_usec'] / 1000000) );
		}

		$this->_('Duration: '.number_format($this->end_time-$this->start_time,4).' sec');
		$this->_('Ended: '.date('d.m.Y H:i',$this->end_time));
		
		foreach ( $this->shutdown_callbacks as $key => $value ) {
			if ( !is_array($value) ) {
				call_user_func( $value, '', $this->_s );
			} else if ( count($value) == 2 ) {	
				call_user_func( array( $value[0], $value[1] ), '', $this->_s );
			}
		}
	}
	
	
	/**
	 * Add custom script shutdown callback.
	 *
	 * The added function will be executed at script shutdown.
	 *
	 * @see o3_debug::$shutdown_callbacks o3_debug::$shutdown_callbacks
	 * @see o3_debug::add_shutdown_callback() o3_debug::add_shutdown_callback()
	 *
	 * @param string|array $data String is name of the function. Array with 2 elements first is the object and second is the function.   
	 *
	 * @return void
	 */
	public function add_shutdown_callback( $data ) {
		$this->shutdown_callbacks[] = $data; 
	}
	
	/**
	 * Add custom output buffer callbacks.
	 *
	 * The added function will called at output buffer flush.<br> The buffer is passed to the function and should return a string.
	 *
	 * @see o3_debug::$ob_start_callbacks o3_debug::$ob_start_callbacks
	 *
	 * @param string|array $data String is name of the function. Array with 2 elements first is the object and second is the function.   
	 *
	 * @return void
	 */
	public function add_ob_start_callback( $data ) {
		$this->ob_start_callbacks[] = $data; 
	}

	
	/**
	* Destructor of debug
	*
	* @return void
	*/
	public function __destruct() {}
	
	
}

/**
 * O3 Engine debug item class
 *
 * Used for storing debug message.
 *
 * @package o3_debug
 */
class o3_debug_list_item {
	
	/** string Debug message */
	public $message = '';
	
	/** int Timestamp for the date to display */
	public $timestamp = 0;

	/** int Type of item */
	public $type = O3_DEBUG;

	/** int Error code value */
	public $error_code = E_USER_NOTICE;
	
	/**
	 * O3 debug item constructor
	 *
	 * @see O3_DEBUG O3_DEBUG
	 * @see O3_LOG O3_LOG
	 *	 
	 * @param string $message Message to display
	 * @param int $timestamp (optional) Timestamp for the date to display. Value is current time if no timestamp is given.
	 * @param int $type (optional) Debug message type. Accepted values: O3_DEBUG (default) or O3_LOG.
	 *
	 * @return void
	 */		
	public function __construct( $message, $timestamp = 0, $type = O3_DEBUG, $error_code = E_USER_NOTICE ) {
		$this->message = $message;
		$this->timestamp = $timestamp == 0 ? time() : '';
		$this->type = $type;
		$this->error_code = $error_code;
	}
	
	/**
	 * Prepare message for output 
	 *	 
	 * @param boolean $print (optional) If true the message will be printed else the function returns the message as a string.
	 *
	 * @return string
	 */	
	public function prnt( $print = true ) {		
	  $b = is_string($this->message) ? preg_replace("/[\n\r|\r|\n]/"," ",$this->message) : preg_replace("/[\n\r|\r|\n]/","",var_export($this->message,true));	
		if ( $print )
			echo $b;
		return $b;
	}
 
	/**
	 * Destructor of O3
	 *
	 * @return void
	 */
	public function __destruct() {}
  
}


?>