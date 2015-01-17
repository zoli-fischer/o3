<?php 

/**
 * O3 Engine log
 *
 * Used for logging. O3 logger appends log messages in a log file.
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

//o3 logging allow debuging
if ( !defined( 'O3_LOG_ALLOWED' ) )
	/** boolean Value: false */
	define('O3_LOG_ALLOWED',false);

/** 
 * O3 Engine log class
 *
 * Used for logging. O3 logger appends log messages in a log file.
 *
 * @example example.php
 * <b>//as O3 Engine module</b> <br>
 * $o3 = new o3();<br>
 * $o3->debug->('Insert into log file and debug console', O3_LOG | O3_DEBUG );<br>
 * $o3->debug->('Insert into log file only', O3_LOG );<br>
 * $o3->log->('Insert into log file only' );<br><br>
 * <b>//as o3_log class</b> <br>  
 * $o3_log = new o3_log();<br>
 * $o3_log->('Insert into log file only');<br>
 *
 * @package o3_log
 */	
class o3_log {
	
	/** mixed Parent object */
	var $parent = null;
	
	/** boolean Set to allow to write into log file */
	private $allow_write = true;

	/** string Path to the log file */
	var $file = '';
	
	/** array List of log messages */
	var $_s = array();
	
	/**
	 * O3 log constructor
	 *
	 * @param $file Path to the log file 
	 *
	 * @return void
	 */	
	public function __construct( $file = '' ) {
		$this->file = $file;
	} 
 	
	/**
	 * Function sets the parent object
	 *
	 * @param object $parent Parent object
	 *
	 * @return object
	 */	
	function set_parent( $parent ) {
		return $this->parent = &$parent;
	}
  
	/**
	 * Same as add.
	 *
	 * @param string $message Message to display
	 * @param int $type (optional) Log message type. Accepted values: O3_LOG (default) or O3_LOG.
	 * @param int $time (optional) Timestamp for the date to display. Value is current time if no timestamp is given.
	 *
	 * @see o3_log::add() o3_log::add()
	 */
	public function _( $message, $type = O3_LOG, $time = 0 ) { 		
		$this->add( $message, $type, $time );		
	}
	
 	/**
	 * Add new line to the log message list.
	 *
	 * @param string $message Message to display
	 * @param int $type (optional) Log message type. Accepted values: O3_LOG (default) or O3_LOG.
	 * @param int $time (optional) Timestamp for the date to display. Value is current time if no timestamp is given.
	 *
	 * @see o3_log_list_item o3_log_list_item
	 * @see O3_LOG O3_LOG
	 * @see O3_DEBUG O3_DEBUG
	 *	 
	 * @return void
	 */	
 	function add( $message, $type = O3_LOG, $time = 0 ) { 		
 		$time = $time > 0 ? time() : '';
 		$this->_s[] = new o3_log_list_item( $message, $time, $type ); 		
 	}
   
	/**
	 * Write to the end of the log file
	 *
	 * @param string $file (optional) Path to the log file. If not set the class's file property is used. <br> Default value: ''
	 * @param string $text (optional) Text to insert into the log file. If not set the classs _s property is used. <br> Default value: ''
	 *
	 * @see o3_log o3_log::$file
	 * @see o3_log o3_log::$_s
	 * 
	 * @return void
	 */
	function append_to( $file = '', $text = '' ) {
		if ( $this->allow_write && O3_LOG_ALLOWED ) {
			$file = strlen(trim($file)) > 0 ? $file : $this->file;
			$text = $text == '' ? $this->_s : $text;
			if ( strlen(trim($file)) > 0 ) {
				$buffer = array();
				foreach ( $text as $key => $value ) {
					if ( $value->type & O3_LOG ) 
						$buffer[] = date('Y-m-d H:i:s',$value->timestamp).': '.$value->prnt(false)."\r\n";			
				}
				return o3_write2top_file( $file, ( count($buffer) > 0 ? "\r\n" : "" ).implode("",$buffer), 'a' );
			}
			return false;
		}
		return true;
	}

	/**
	 * Write to the bottom of the log file
	 *
	 * @param string $file (optional) Path to the log file. If not set the class's file property is used. <br> Default value: ''
	 * @param string $text (optional) Text to insert into the log file. If not set the classs _s property is used. <br> Default value: ''
	 *
	 * @see o3_log o3_log::$file
	 * @see o3_log o3_log::$_s
	 * 
	 * @return void
	 */
	function append_bottom( $file = '', $text = '' ) {
		if ( $this->allow_write && O3_LOG_ALLOWED ) {
			$file = strlen(trim($file)) > 0 ? $file : $this->file;
			$text = $text == '' ? $this->_s : $text;
			if ( strlen(trim($file)) > 0 ) {
				$buffer = array();
				foreach ( $text as $key => $value ) {
					if ( $value->type & O3_LOG ) 
						$buffer[] = date('Y-m-d H:i:s',$value->timestamp).': '.$value->prnt(false)."\r\n";			
				}
				return o3_write_file( $file, ( count($buffer) > 0 ? "\r\n" : "" ).implode("",$buffer), 'a' );
			}
			return false;
		}
		return true;
	}

	/**
	* Set to allow to write into log file
	*
	* @param boolean $value (optional) Values: true - allow write, false - don't allow write
	*
	* @return void
	*/
	public function allow( $value = false ) {
		$this->allow_write = $value;
	}
  
	/**
	 * Destructor of log
	 *
	 * @return void
	 */
	public function __destruct() {}

}

/**
 * O3 Engine log item class
 *
 * Used for storing log message.
 *
 * @package o3_log
 */
class o3_log_list_item {
	
	/** string Log message */
	var $message;
	
	/** int Timestamp for the date to display */
	var $timestamp; 
	
	/**
	 * O3 log item constructor
	 *
	 * @see O3_LOG O3_LOG
	 * @see O3_DEBUG O3_DEBUG
	 *	 
	 * @param string $message Message to display
	 * @param int $timestamp (optional) Timestamp for the date to display. Value is current time if no timestamp is given.
	 * @param int $type (optional) Log message type. Accepted values: O3_LOG (default) or O3_DEBUG.
	 *
	 * @return void
	 */	
	public function __construct( $message, $timestamp = 0, $type = O3_LOG  ) {
		$this->message = $message;
		$this->timestamp = $timestamp == 0 ? time() : '';
		$this->type = $type;
	}
	
	/**
	 * Prepare message for output 
	 *	 
	 * @param boolean $print (optional) If true the message will be printed else the function returns the message as a string.
	 *
	 * @return string
	 */	
	function prnt( $print = true ) {
		$b = is_string($this->message) ? $this->message : preg_replace("/[\n\r|\r|\n]/","",var_export($this->text,true));	
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