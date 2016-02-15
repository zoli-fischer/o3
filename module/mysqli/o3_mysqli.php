<?php

/**
 * MySql database module for O3 Engine
 *
 * Config file
 *
 * @package o3
 * @link    todo: add url
 * @author  Zotlan Fischer <zoli_fischer@yahoo.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

/**
 * Mysqli database class
 *
 * Extends mysqli. Handles mysql database queries.
 *
 */
class o3_mysqli extends mysqli {
	
	/** mixed Parent object */
	var $parent = null;

	/** mixed Link object */
	private $link = null;

	/** string MySQL host */
	private $host = '';

	/** string Username */
	private $user = '';

	/** string Password */
	private $pass = '';

	/** string Database name */
	private $db = '';

	/** string Database port */
	private $port = '';

	/** string Database socket */
	private $socket = '';

	/** string Create connection in constuctor  */
	private $autoconnect = '';
	
	/** string Used tables name prefix */
	public $table_prefix = '';

	/** int Queries total execution duration */
	public $allow_debug_queries = false;

	/** int Queries total execution duration */
	protected $queries_exec_time = 0;

	/** int Number of queries */
	protected $queries_count = 0;	

	/**
	 * O3 mysqli constructor
	 *
	 * @param string $host (optional) MySQL host Default value: ''
	 * @param string $user (optional) Username Default value: ''
	 * @param string $pass (optional) Password Default value: ''
	 * @param string $db (optional) Database name Default value: ''
	 * @param string $port (optional) Database port Default value: ''
	 * @param string $socket (optional) Database socket Default value: ''
	 *
	 * @return void
	 */		
  	public function __construct( $host = '', $user = '', $pass = '', $db = '', $port = '', $socket = '' ) {
  		$this->host = $host;
  		$this->user = $user;
  		$this->pass = $pass;
  		$this->db = $db;
  		$this->port = $port;
  		$this->socket = $socket;
	}

	/**
	 * O3 mysqli connect
	 *
	 * @param string $host (optional) MySQL host Default value: ''
	 * @param string $user (optional) Username Default value: ''
	 * @param string $pass (optional) Password Default value: ''
	 * @param string $db (optional) Database name Default value: ''
	 * @param string $port (optional) Database port Default value: ''
	 * @param string $socket (optional) Database socket Default value: ''
	 *
	 * @return void
	 */
	public function connect( $host = '', $user = '', $pass = '', $db = '', $port = '', $socket = '' ) {
		
		if ( func_num_args() == 0 ) {
			$host = $this->host;
			$user = $this->user;
			$pass = $this->pass;
			$db = $this->db;
			$port = $this->port;
			$socket = $this->socket;
		}
		$this->link = parent::__construct( $this->host, $this->user, $this->pass, $this->db, $this->port, $this->socket );	

		if ( mysqli_connect_error() )
        	trigger_error( 'Mysqli connect error ('. mysqli_connect_errno(). ') '.mysqli_connect_error(), E_USER_WARNING );

        return $this->link;
    } 

    /*
    * Create table name with prefix
    *
    * @param string $table_name
	*
	* @return string
    */
    public function tablename( $table_name ) {
    	return $this->table_prefix != '' ? $this->table_prefix.$table_name : $table_name;
    }

    /**
	 * Make select query in table
	 *
	 * @param string $table_name Name of table 
	 * @param array $condition List of conditions
	 * @param string $fields Fields to select
	 * @param string $limit
	 * @param string $orderby
	 *
	 * @return mysqli query result
	 */	
	public function select( $table_name, $conditions, $fields = '*', $limit = '', $orderby = '' ) {
		
		$conditions_list = array();
		foreach ( $conditions as $key => $value )
			$conditions_list[] = '`'.$this->escape_string( $key ).'` = "'.$this->escape_string( $value ).'"';		

		$sql = "SELECT ".$this->escape_string($fields)." FROM ".$this->escape_string( $table_name )." ".
			  ( count($conditions_list) > 0 ? " WHERE ".implode( ' AND ', $conditions_list ) : "" )." ".
			  ( trim($orderby) != '' ? " ORDER BY ".$this->escape_string($orderby) : "" )." ".
			  ( trim($limit) != '' ? " LIMIT ".$this->escape_string($limit) : "" )." ";
		
		return $this->query( $sql );

	}

	
	 /**
	 * Get 1 row from table as object
	 *
	 * @param string $table_name Name of table 
	 * @param array $condition List of conditions
	 * @param string $fields Fields to select
	 * @param string $limit
	 * @param string $orderby
	 *
	 * @return mysqli query result
	 */	
	public function select_first( $table_name, $conditions, $fields = '*', $orderby = '' ) {	
		$result = $this->select( $table_name, $conditions, $fields, '1', $orderby );
		if ( $result !== false && $result->num_rows > 0 )
			return $result->fetch_object();
		return false;
	}

	/**
	 * Insert row in table
	 *
	 * @param string $table_name Name of table 
	 * @param array $values List of values
	 * @param array $update_values List of values to update on duplicated
	 *
	 * @return mysqli query result
	 */	
	public function insert( $table_name, $values, $update_values = array() ) {

		$values_list = array();
		$fields_list = array();
		
		foreach ( $values as $key => $value ) {
			$values_list[] = '"'.$this->escape_string( $value ).'"';
			$fields_list[] = '`'.$this->escape_string( $key ).'`';
		}

		$sql = "INSERT INTO ".$this->escape_string( $table_name )." (".implode( ',', $fields_list ).") VALUES (".implode( ',', $values_list ).") ";

		//update duplicated if needed
		if ( count($update_values) > 0 ) {

			$update_list = array();						
			foreach ( $values as $key => $value )
				$update_list[] = ' `'.$this->escape_string( $key ).'` = "'.$this->escape_string( $value ).'" ';

			$sql .= " ON DUPLICATE KEY UPDATE ".implode( ',', $update_list );
		}

		$result = $this->query( $sql );
		return $result;

	}

	/**
	 * Update row in table
	 *
	 * @param string $table_name Name of table 
	 * @param array $values List of values
	 * @param array $condition List of conditions
	 *
	 * @return mysqli query result
	 */	
	public function update( $table_name, $values, $conditions ) {

		$values_list = array();		
		foreach ( $values as $key => $value )
			$values_list[] = '`'.$this->escape_string( $key ).'` = "'.$this->escape_string( $value ).'"';					

		$conditions_list = array();
		foreach ( $conditions as $key => $value )
			$conditions_list[] = '`'.$this->escape_string( $key ).'` = "'.$this->escape_string( $value ).'"';		

		$sql = "UPDATE ".$this->escape_string( $table_name )." SET ".implode( ',', $values_list )." WHERE ".implode( ' AND ', $conditions_list )." ";
		
		return $this->query( $sql );

	}
  
  	/**
	 * Delete row from table
	 *
	 * @param string $table_name Name of table 	 
	 * @param array $condition List of conditions
	 *
	 * @return mysqli query result
	 */	
	public function delete( $table_name, $conditions ) {

		$conditions_list = array();
		foreach ( $conditions as $key => $value )
			$conditions_list[] = '`'.$this->escape_string( $key ).'` = "'.$this->escape_string( $value ).'"';		

		$sql = "DELETE FROM ".$this->escape_string( $table_name )." WHERE ".implode( ' AND ', $conditions_list )." ";
		return $this->query( $sql );

	}

	
	/**
	 * MySQL date format (Y-m-d or Y-m-d H:i:s) to timestamp
	 *
	 * @param string $date MySQL date
	 *
	 * @return integer
	 */	
	public function date2time( $date ) {		
		$a_ = explode( ' ', $date );
		$time = 0;
		if ( count($a_) == 1 ) {
			$d_ = explode('-', $a_[0]);			
			$time = mktime(0,0,0,$d_[1],$d_[2],$d_[0]);
		} else {
			$d_ = explode('-', $a_[0]);
			$t_ = explode(':', $a_[1]);			
			$time = mktime($t_[0],$t_[1],$t_[2],$d_[1],$d_[2],$d_[0]);
		}
		return $time;
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
	 * Function sets the parent object
	 *
	 * @param object $parent Parent object
	 * MYSQLI_STORE_RESULT
	 *
	 * @return object
	 */	
	public function query( $query, $resultmode = MYSQLI_STORE_RESULT ) {
		$time = o3_micro_time();
		$return = parent::query( $query, $resultmode );
		$time = o3_micro_time() - $time;

		//add opmax version to debug
		if ( $this->allow_debug_queries )
			$this->parent->debug->_('Mysqli query: '.$query.' ('.number_format($time,4).'sec) ');

		if ( $return === false )
			trigger_error( 'Mysqli query error ('. mysqli_connect_errno(). ') '.mysqli_connect_error(), E_USER_WARNING );
		

		$this->queries_exec_time += $time;
		$this->queries_count++; 

		return $return;
	}

	/**
	 * On o3 debug shutdown show statistics 
	 *
	 * @return void
	 */	
	public function shutdown_callback() {
		$this->parent->debug->_('Mysqli queries total execution duration: '.number_format($this->queries_exec_time,4).'sec ');
		$this->parent->debug->_('Mysqli number of queries: '.number_format($this->queries_count,0).' ');
	}
     
}

?>