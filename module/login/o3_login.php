<?php

/**
 * O3 Engine login module
 *
 * @author Zoltan Lengyel-Fischer <zoli_fischer@yahoo.com>
*/


/**
 * O3 Engine login module
 *
 * User log in/out functions. <br>
 * Notice: <i>Requires o3_mysqli<i>
 * @see o3_mysqli o3_mysqli
 *
 * @category module
 * @package o3_login
 */
class o3_login {
	
	/** mixed Parent object */
	public $parent = null;
	
	/**  
	* string Name of users table
	* If not set all login checks will be ignored.
	*/
	public $table = O3_LOGIN_TABLE;
	
	/** string Field with uniq id */
	public $field_id = O3_LOGIN_FIELD_ID;
		
	/** string Field with username */
	public $field_username = O3_LOGIN_FIELD_USERNAME;
	
	/** string Field with password */
	public $field_password = O3_LOGIN_FIELD_PASSWORD;
	
	/** 
	* string Field with delete flag 
	* If not set the field is ignored.
	* Values: bigger than 1 - user deleted, 0 - user not deleted 
	*/
	public $field_deleted = O3_LOGIN_FIELD_DELETED;
	
	/** 
	* string Field with active flag 
	* If not set the field is ignored.
	* Values: 1 - user active, not 1 - user not active 
	*/
	public $field_active = O3_LOGIN_FIELD_ACTIVE;
	
	/** string Field with last action timestamp  */
	public $field_action = O3_LOGIN_FIELD_ACTION;
	
	/** string Integer field for counting unsuccessful logins  */
	public $field_login_tries = O3_LOGIN_FIELD_LOGIN_TRIES;
	
	/** string Integer field with last unsuccessful logins timestamp  */
	public $field_last_login_try = O3_LOGIN_FIELD_LAST_LOGIN_TRY;
	
	/** integer How much unsuccessful logins allowed before blocking the account. Default value: 3 */
	public $max_allowed_login_tries = 6;
	
	/** integer How long ( in seconds ) to block the account on unsuccessful logins. Default value: 600 (10 minutes) */
	public $block_account_time = 600;
	
	/** 
	* pointer Pointer to mysqli object 
	* If not set all login checks will be ignored.
	*/
	public $mysqli = false;
	
	/** string Persist cookie life time in seconds */
	public $persist_life_time = 1209600;
	
	/** boolean When true, login info are not stored on the users computer but in a database table on the server for higher security */
	public $use_persist_table = O3_USE_PERSIST_TABLE;
	
	/** string Table for increased security for persist cookie. 3 fields required: <br>int id For user id<br>string(128) hash For cookie identifier<br>integer timestamp Timestamp when the cookie was created */
	public $persist_table = O3_PERSIST_TABLE;
	
	/** string @see o3_login::$persist_table */
	public $persist_field_id = O3_PERSIST_FIELD_ID;
	
	/** string @see o3_login::$persist_table */
	public $persist_field_hash = O3_PERSIST_FIELD_HASH;
	
	/** string @see o3_login::$persist_table */
	public $persist_field_timestamp = O3_PERSIST_FIELD_TIMESTAMP;
	
	/** string Recover life time in seconds */
	public $recover_life_time = 432000;
		
	/** string Table for increased security for recover cookie. 3 fields required: <br>int id For user id<br>string(128) hash For cookie identifier<br>integer timestamp Timestamp when the cookie was created */
	public $recover_table = O3_RECOVER_TABLE;
	
	/** string @see o3_login::$recover_table */
	public $recover_field_id = O3_RECOVER_FIELD_ID;
	
	/** string @see o3_login::$recover_table */
	public $recover_field_hash = O3_RECOVER_FIELD_HASH;
	
	/** string @see o3_login::$recover_table */
	public $recover_field_timestamp = O3_RECOVER_FIELD_TIMESTAMP;	
	
	/** array | boolean Data of the logged user. False if no user logged in. */
	public $data = false;
	
	/** string | array Set custom hash generator function.*/
	public $hash_function = '';
	
	/** string | array Set custom check hash function.*/
	public $check_hash_function = '';
	
	/**
	 * O3 login constructor
	 *
	 * @return void
	 */		
  public function __construct() {
  	
  }
  
  /**
	 * Check if the table and fields are set
	 *
	 * @return boolean
	 */		
  private function table_check() {  	
  	//todo add debug_backtrace();
  	if ( $this->table == '' ) {
  		trigger_error('Table name is empty.');
  		return false;
  	}
  	if ( $this->field_id == '' ) {
  		trigger_error('Id field name is empty.');
  		return false;
  	}
  	if ( $this->field_username == '' ) {
  		trigger_error('Username field name is empty.');
  		return false;
  	}
  	if ( $this->field_password == '' ) {
  		trigger_error('Password field name is empty.');
  		return false;
  	}
  	return true;
  }
  
  /**
	 * Check if the cookie(persist) table and fields are set
	 *
	 * @return boolean
	 */		
  private function persist_table_check() {
  	//todo add debug_backtrace();
  	if ( $this->use_persist_table ) { //check only if used
	  	if ( $this->persist_table == '' ) {
	  		trigger_error('Persist table name is empty.');
	  		return false;
	  	}
	  	if ( $this->persist_field_id == '' ) {
	  		trigger_error('Persist id field name is empty.');
	  		return false;
	  	}
	  	if ( $this->persist_field_hash == '' ) {
	  		trigger_error('Persist hash field name is empty.');
	  		return false;
	  	}
	  	if ( $this->persist_field_timestamp == '' ) {
	  		trigger_error('Persist timestamp field name is empty.');
	  		return false;
	  	}	  	
	  }
  	return $this->use_persist_table;
  }  
  
  /**
	 * Check if the recover table and fields are set
	 *
	 * @return boolean
	 */		
  private function recover_table_check() {
  	//todo add debug_backtrace();  	
  	if ( $this->recover_table == '' ) {
  		trigger_error('Recover table name is empty.');
  		return false;
  	}
  	if ( $this->recover_field_id == '' ) {
  		trigger_error('Recover id field name is empty.');
  		return false;
  	}
  	if ( $this->recover_field_hash == '' ) {
  		trigger_error('Recover hash field name is empty.');
  		return false;
  	}
  	if ( $this->recover_field_timestamp == '' ) {
  		trigger_error('Recover timestamp field name is empty.');
  		return false;
  	}	  	
	 	return true;
  }  
	
  /**
	 * Check if user logged
	 *	 
	 * @return boolean
	 */
  function is_logged() {
  	return $this->check();
  }
  
	/**
	 * Logout user	 
	 * @param string $url Redirect to this URL after logout 
	 * @return boolean
	 */
	public function logout( $url = '' ) {
		$this->unset_session();
		if ( $url != '' )
			o3_redirect($url);
		return true;
	}
  
	/**
	* Register unsuccessful login to user
	* @param integer $user_id
	* @return void
	*/
	public function add_unsuccessful_login( $user_id ) {
		if ( $this->field_login_tries != '' && $this->field_last_login_try != '' && $this->max_allowed_login_tries > 0 && $this->block_account_time > 0 && $this->table_check() ) {
			$sql = "UPDATE `".$this->escape($this->table)."` SET `".$this->escape($this->field_login_tries)."` = `".$this->escape($this->field_login_tries)."` + 1, `".$this->escape($this->field_last_login_try)."` = '".o3_gmt_time()."' WHERE `".$this->escape($this->field_id)."` = ".$this->escape($user_id);
			$result = $this->mysqli->query( $sql );
		}
	}
  
  	/**
	 * Remove unsuccessful login from user
	 * @param integer $user_id
	 * @return void
	 */
	public function clear_unsuccessful_login( $user_id ) {
		if ( $this->field_login_tries != '' && $this->field_last_login_try != '' && $this->max_allowed_login_tries > 0 && $this->block_account_time > 0 && $this->table_check() ) {
		 	$sql = "UPDATE `".$this->escape($this->table)."` SET `".$this->escape($this->field_login_tries)."` = 0, `".$this->escape($this->field_last_login_try)."` = 0  WHERE `".$this->escape($this->field_id)."` = ".$this->escape($user_id);
			$result = $this->mysqli->query( $sql );
		}
	}
  
  	/**
	 * Check if user account is blocked for unsuccessful login from user
	 * @param array | integer $user User data array or user id 
	 * @return boolean | integer False if is not blocked, remain blocked second if blocked
	 */
	public function is_login_blocked( $user ) {
		if ( $this->field_login_tries != '' && $this->field_last_login_try != '' && $this->max_allowed_login_tries > 0 && $this->block_account_time > 0 ) {
		 	//check for user
		 	$user_data = is_array($user) ? $user : $this->select_id( $user );
		 	if ( $user_data !== false ) {  	 		  	 		
		 		//compare for max and users unsuccessful login
		  	 	if ( isset($user_data[$this->field_login_tries]) && $user_data[$this->field_login_tries] >= $this->max_allowed_login_tries ) {
	 				//check if block is elapsed
	 				if ( $user_data[$this->field_last_login_try] + $this->block_account_time > o3_gmt_time() ) {  	 				
	  	 			return $user_data[$this->field_last_login_try] + $this->block_account_time - o3_gmt_time();
		 			} else {
		 				//if last unsuccessful login is to old, clear data
		 				$this->clear_unsuccessful_login( $user_data[$this->field_id] );
	 				}
	 			}
		 	}
		}
		return false;		
	}
  
	/**
	 * Login user with username/password
	 * 
	 * @param string $username Not hashed username
	 * @param string $password Hashed password
	 * @param boolean $persist When true login is saved in cookie
	 *
	 * @return object
	 */
	public function login( $username, $password, $persist = false ) {
		if ( $username != '' && $password != '' && ( $user_data = $this->select_username( $username ) ) !== false ) {
			
			$this->data = $user_data; //set user data
			if ( $this->is_login_blocked( $user_data ) === false ) { //check if user blocked
				if ( isset($user_data[$this->field_password]) && $this->check_hash( $password, $user_data[$this->field_password] ) ) { //check if password is valid
					$this->set_session( $user_data[$this->field_id], $this->hash($username), $user_data[$this->field_password], $persist );
					$this->clear_unsuccessful_login( $user_data[$this->field_id] ); //remove unsuccessful logins
					return true;
				}
			} else {
				return false; //stop, we don't update the unsuccessful login
			}			
			//username was found but login faild, register unsuccessful login
			$this->add_unsuccessful_login( $user_data[$this->field_id] );
		}
		return false;  	
	}
  
	/**
	 * Login user from post data
	 * @todo Make login form generator
	 * @return boolean
	 */	
	public function post_login() {
		if ( $this->table_check() &&  isset($_POST[$this->table.'_o3lcmd']) ) {
			$post_cmd = o3_post( $this->table.'_o3lcmd', '' );
			$post_user = o3_post( $this->table.'_o3lu', '' );
			$post_pass = o3_post( $this->table.'_o3lp', '' );
			$post_persist = o3_post( $this->table.'_o3lpersist', '' );
			if ( $post_cmd != '' ) //TODO Fix security
				return $this->login( $post_user, $post_pass, $post_persist ); //login user
			return false;
		}
	}
  
  /**
	 * Check session and cookie for user.
	 *
	 * If parameters omitted the $data is populated if the user is logged.
	 * @see o3_login::$data o3_login::$data
	 *
	 * @param object $url_user URL to redirect if user logged
	 * @param object $url_fail URL to redirect if user not logged
	 *
	 * @return object
	 */	
  public function check( $url_user = '', $url_fail = '' ) {
  	$return = false;
  	$persist = false; //is login from cookie
  	if ( $this->table_check() ) { // check for table
	  	if ( ( $user_data = $this->check_for_user( o3_session( $this->table.'_o3lid', '' ), 
	  															o3_session( $this->table.'_o3lusername', '' ),
	  															o3_session( $this->table.'_o3lpassword', '' ) ) ) === false ) { // login user from session												
				if ( $this->persist_table_check() ) { //check in cookie table if is used
					
					if ( ( $user_data = $this->check_in_persist_table( o3_cookie( $this->table.'_o3lci', '' ), 
																											o3_cookie( $this->table.'_o3lcu', '' ),
											  															o3_cookie( $this->table.'_o3lcp', '' ) ) ) !== false ) {											  															
						$return = true; //correct login info
						$persist = true;
					}
			  	
			  } else {
	 				
	 				if ( ( $user_data = $this->check_for_user( o3_cookie( $this->table.'_o3lci', '' ), 
		  															o3_cookie( $this->table.'_o3lcu', '' ),
		  															o3_cookie( $this->table.'_o3lcp', '' ) ) ) !== false ) { // login user from cookies if session failed																
						$return = true; //correct login info	
						$persist = true;					
					}	
							
				}	
			} else {				
				//generate new cookie if old cookie is exists and valid
				if ( ( $aux_data = $this->check_in_persist_table( o3_cookie( $this->table.'_o3lci', '' ), 
																												o3_cookie( $this->table.'_o3lcu', '' ),
												  															o3_cookie( $this->table.'_o3lcp', '' ) ) ) !== false ) {											  															
					if ( $aux_data[$this->field_id] == $user_data[$this->field_id] ) 
						$persist = true;
				}
				$return = true; //correct login info				
			}		
		
			//once used remove cookie from table
			$this->delete_from_persist_table( o3_cookie( $this->table.'_o3lci', '' ), o3_cookie( $this->table.'_o3lcu', '' ), o3_cookie( $this->table.'_o3lcp', '' ) );
			
			if ( $return ) {
				//update login info
				$this->data = $user_data;
				$this->update_action( $user_data[$this->field_id] );
				//$this->addLoginTime();
				$this->set_session( $this->get($this->field_id), $this->hash($this->get($this->field_username)), $this->get($this->field_password), $persist );					
				if ( $url_user != '' )
					o3_redirect($url_user); // on login ok redirect to ...						
				return true;
			}
			$this->data = false;
			if ( $url_fail != '' )					
				o3_redirect($url_fail); // on login fail redirect to ...	
		}
		return false;
  }
  
  /**
	 * Checking for user
	 *
	 * Function used to check for user data in session or cookie
	 *
	 * @param string $user_id
	 * @param string $user_name Hashed username
	 * @param string $user_pass Hashed password
	 * @return boolean
	 */
	private function check_for_user( $user_id, $user_name, $user_pass ) {
		if ( $user_id > 0 && intval($user_id) == $user_id && $user_name != '' && $user_pass != '' ) {				
			$user_data = $this->select_id( $user_id ); //check for user						
			if ( $user_data !== false ) {	// user found
				if ( isset($user_data[$this->field_username]) && $this->check_hash( $user_data[$this->field_username], $user_name ) && 
					   isset($user_data[$this->field_password]) && $user_data[$this->field_password] == $user_pass  ) {					
					return $user_data;
				}
			}				
		}		
		return false;				
	}
	
	/**
	 * Check cookie info in database
	 *
	 * If cookie info metched in table, user data returned.
	 * 
	 * @param string $id
	 * @param string $time
	 * @param string $hash  
	 *
	 * @return array | boolean If row can't be found returns false 
	 */ 
	private function check_in_persist_table( $id, $time, $hash ) {
		if ( $this->persist_table_check() ) {
			
			//delete all old cookies from table
		  $sql = "DELETE FROM `".$this->escape($this->persist_table)."` WHERE `".$this->escape($this->persist_field_timestamp)."` < ".( $time - $this->persist_life_time );
		  $result = $this->mysqli->query( $sql );
		 				 	
			$time = trim(strrev(o3_salt_decode( $time, $this->persist_field_timestamp ))); //get time
			$id = trim(strrev(o3_salt_decode( $id, strval($time) ))); //get id
			
			if ( intval($time) > intval( o3_gmt_time() - $this->persist_life_time ) ) { //check if cookie expired
				//check in table
			  $sql = "SELECT `".$this->escape($this->persist_field_id)."`  FROM `".$this->escape($this->persist_table)."` WHERE `".$this->escape($this->persist_field_id)."` = '".$this->escape($id)."' AND `".$this->escape($this->persist_field_timestamp)."` = '".$this->escape($time)."' AND `".$this->escape($this->persist_field_hash)."` = '".$this->escape($hash)."'   ";
			  $result = $this->mysqli->query( $sql );
			  if ( $result->num_rows == 1 ) {
			  	$data = obj2arr( $result->fetch_object() );
			  	$user_data = $this->select_id( $data[$this->persist_field_id] );			
			  	if ( $user_data !== false )  	
						if ( $this->cookie_hash( $id, $user_data[$this->field_password], $time ) == $hash ) //check valid hash
							return $user_data;
				}
			}
		}
		return false;	 	
	} 
	
	/**
	 * Update user action timestamp
	 *
	 * @param integer $id Id of the user
	 *
	 * @return integer Timestamp 
	 */
	public function update_action( $id ) {
		if ( $this->table_check() ) { //check for table
			$sql_check = '';
			
			//get current timestamp		  			  
			$time = o3_gmt_time();
			$sql = "UPDATE `".$this->escape($this->table)."` SET `".$this->escape($this->field_action)."` = '".$time."'  WHERE `".$this->escape($this->field_id)."` = '".$this->escape($id)."' ";
			
			if ( ( $result = $this->mysqli->query( $sql ) ) !== false )
				return $time;
		}
		return false;
	}
		
	
	/**
	 * Select user row by username
	 *
	 * @param string $username Username of the user
	 *
	 * @return array | boolean If row can't be found returns false 
	 */
	public function select_username( $username ) {
		if ( $this->table_check() ) { // check for table
			$sql_check = '';
			if ( $this->field_active != '' )
				$sql_check .= ' AND `'.$this->escape($this->field_active).'` = "1" ';
			
			if ( $this->field_deleted != '' )
				$sql_check .= ' AND `'.$this->escape($this->field_deleted).'` = "0" ';
			
			$sql = "SELECT * FROM `".$this->escape($this->table)."` WHERE `".$this->escape($this->field_username)."` = '".$this->escape($username)."' ".$sql_check;
			
			if ( ( $result = $this->mysqli->query( $sql ) ) !== false )			
				if ( $result->num_rows == 1 ) {
					$data = obj2arr( $result->fetch_object() );
					if ( isset($data[$this->field_username]) && $data[$this->field_username] == $username )
						return $data; 
				}
		}
		return false;
	} 
	
	/**
	 * Select user row by id
	 *
	 * @param integer $id Id of the user
	 *
	 * @return array | boolean If row can't be found returns false 
	 */
	public function select_id( $id ) {
		if ( $this->table_check() ) { // check for table
			$sql_check = '';
			if ( $this->field_active != '' )
				$sql_check .= ' AND `'.$this->escape($this->field_active).'` = "1" ';
			
			if ( $this->field_deleted != '' )
				$sql_check .= ' AND `'.$this->escape($this->field_deleted).'` = "0" ';
			
			$sql = "SELECT * FROM `".$this->escape($this->table)."` WHERE `".$this->escape($this->field_id)."` = '".$this->escape($id)."' ".$sql_check;				
				
			if ( ( $result = $this->mysqli->query( $sql ) ) !== false )			
				if ( $result->num_rows == 1 ) {
					$data = obj2arr( $result->fetch_object() );
					if ( isset($data[$this->field_id]) && $data[$this->field_id] == $id )
						return $data;
				}
		}
		return false;
	}
	
	/**
	 * Generate uniq string from user's comupter/web browser/password
	 *
	 * @param string $id User id 
	 * @param string $password Hashed password
	 * @param string $time Timestamp
	 *
	 * @return string
	 */	
	public function cookie_hash( $id, $password, $time = '' ) {
		return o3_sha3(o3_ua_body_classes().$password.$id.$time);
	}
		
	/**
	 * Generate recovery hash
	 *
	 * @param string $id User id 
	 * @param string $time Timestamp
	 *
	 * @return string
	 */	
	public function recover_hash( $id, $time = '' ) {
		return substr(md5($id.$time),0,16);
	}
	
	/**
	 * Save user data in session
	 *
	 * @param string $id 
	 * @param string $username Hashed username
	 * @param string $password Hashed password
	 * @param int $persist When set to 1 cookie is also saved
	 *
	 * @return object
	 */	
	private function set_session( $id, $username, $password, $persist = false ) {
		if ( $this->table_check() ) { // check for table
			$_SESSION[$this->table.'_o3lid'] = $id;
			$_SESSION[$this->table.'_o3lusername'] = $username;
			$_SESSION[$this->table.'_o3lpassword'] = $password;
			
			//keep login in cookie
			if  ( $persist ) {
				if ( $this->use_persist_table ) { 
					if ( $this->persist_table_check() ) {
						//try to use database table			  			  
					  $time = o3_gmt_time();
		 			  $cookie_hash = $this->cookie_hash($id,$password,$time); //generate hash for user/browser
		 			   			  
		 			  //insert new cookie to table
		 			  $sql = "INSERT INTO `".$this->escape($this->persist_table)."` (`".$this->escape($this->persist_field_id)."`,`".$this->escape($this->persist_field_hash)."`,`".$this->escape($this->persist_field_timestamp)."`) VALUES ('".$this->escape( $id )."','".$this->escape( $cookie_hash )."','".$time."') ";
					  $result = $this->mysqli->query( $sql );
					  if ( $result !== false ) { //save cookie if row is added to the table
						  //encrypt data for cookie
						  $id = strrev($id); //revert
						  $id = o3_salt_encode( $id, strval($time) ); //generate salted hash from id
			 			  $time = strrev($time); //revert 			  
			 			  $time = o3_salt_encode( $time, $this->persist_field_timestamp ); //generate salted hash from time				  
			 			  o3_set_cookie($this->table.'_o3lci',$id,$this->persist_life_time);
							o3_set_cookie($this->table.'_o3lcu',$time,$this->persist_life_time);
							o3_set_cookie($this->table.'_o3lcp',$cookie_hash,$this->persist_life_time);
						}
					}
				} else {
					o3_set_cookie($this->table.'_o3lci',$id,$this->persist_life_time);
					o3_set_cookie($this->table.'_o3lcu',$username,$this->persist_life_time);
					o3_set_cookie($this->table.'_o3lcp',$password,$this->persist_life_time);
				}
		  }			
		}
	}
	
	/**
	 * Remove user data from session/cookie
	 *
	 * @return void
	 */
	private function unset_session() {
		if ( $this->table_check() ) { // check for table
			//unset session
			o3_unset_session($this->table.'_o3lid');
			o3_unset_session($this->table.'_o3lusername');
			o3_unset_session($this->table.'_o3lpassword');
			
			//delete from cookie table
			$this->delete_from_persist_table( o3_cookie( $this->table.'_o3lci', '' ),	o3_cookie( $this->table.'_o3lcu', '' ), o3_cookie( $this->table.'_o3lcp', '' ) ); //remove cookie from table
			
			//unset cookie
			o3_unset_cookie($this->table.'_o3lci');
			o3_unset_cookie($this->table.'_o3lcu');
			o3_unset_cookie($this->table.'_o3lcp');
	  }
	}
	
	/**
	 * Delete cookie info from database
	 *
	 * @param string $id
	 * @param string $time
	 * @param string $hash  
	 *
	 * @return boolean
	 */ 
	private function delete_from_persist_table( $id, $time, $hash ) {
		if ( $this->persist_table_check() ) {
			$time = trim(strrev(o3_salt_decode( $time, $this->persist_field_timestamp ))); //get time			
			$id = trim(strrev(o3_salt_decode( $id, strval($time) ))); //get id
			
			$sql = "DELETE FROM `".$this->escape($this->persist_table)."` WHERE `".$this->escape($this->persist_field_id)."` = '".$this->escape($id)."' AND `".$this->escape($this->persist_field_timestamp)."` = '".$this->escape($time)."' AND `".$this->escape($this->persist_field_hash)."` = '".$this->escape($hash)."' ";
			$result = $this->mysqli->query( $sql );
			return $result !== false ? true : false;
		}	
		return false;
	}

	/**
	 * Check if login info is persist
	 *
	 * @return boolean
	 */ 
	public function is_login_persist() {
		return isset($_COOKIE[$this->table.'_o3lci']);
	}
	
	/**
	 * Generates a password recovery code for a user
	 *
	 * @param integer $user_id
	 *
	 * @return boolean
	 */ 
	public function get_recovery_code( $user_id ) {		
		if ( $this->recover_table_check() ) {					
		  $time = o3_gmt_time();
		  $recover_hash = $this->recover_hash($user_id,$time); //generate hash for user/browser
		   			  
		  //insert new recovery code to table
		  $sql = "INSERT INTO `".$this->escape($this->recover_table)."` (`".$this->escape($this->recover_field_id)."`,`".$this->escape($this->recover_field_hash)."`,`".$this->escape($this->recover_field_timestamp)."`) VALUES ('".$this->escape( $user_id )."','".$this->escape( $recover_hash )."','".$time."') ";		  
		  $result = $this->mysqli->query( $sql );
		  if ( $result !== false ) { //save recover if row is added to the table
			  return $recover_hash.'|'.$time;
			}
		}
		return false;
	}
	
	/**
	 * Check if the recovery code is valid code
	 *
	 * @param string $code
	 *
	 * @return boolean
	 */ 
	public function is_recovery_code( $code ) {		
		if ( $this->recover_table_check() ) {
			$data = explode( '|', $code );
							
			if ( count($data) == 2 ) {
			  $time = $data[1];
			  $recover_hash = $data[0];
		  
				//check if expired
		  	if ( intval($time) > o3_gmt_time() - $this->recover_life_time ) {
				  $sql = "SELECT 
				  					`".$this->escape($this->recover_field_id)."` 
				  				FROM 
				  					`".$this->escape($this->recover_table)."` 
				  				WHERE
				  					`".$this->escape($this->recover_field_hash)."` = '".$recover_hash."' AND
				  					`".$this->escape($this->recover_field_timestamp)."` = '".$this->escape( $time )."'";
				  $result = $this->mysqli->query( $sql );			  			  
				  if ( $result->num_rows == 1 && ( $user_data = $result->fetch_array() ) )
					  return $user_data[$this->escape($this->recover_field_id)];								
				}
			}
		}
		return false;
	}
	
	/**
	 * Delete recovery code from database
	 *
	 * @param string $hash  
	 *
	 * @return boolean
	 */ 
	private function delete_from_recover_table( $hash ) {
		if ( $this->recover_table_check() ) {
			$sql = "DELETE FROM `".$this->escape($this->recover_table)."` WHERE `".$this->escape($this->recover_field_hash)."` = '".$this->escape($hash)."' ";
			$result = $this->mysqli->query( $sql );
			return $result !== false ? true : false;
		}	
		return false;
	}
	
	/**
	 * Update password for a user
	 *
	 * @param integer $user_id 
	 * @param string $password Unencrypted password 
	 *
	 * @return boolean True if the password was changed
	 */ 
	public function set_password( $user_id, $password ) {
		if ( $this->table_check() ) {
			$password_hash = $this->hash( $password );
			$sql = "UPDATE `".$this->escape($this->table)."` SET `".$this->escape($this->field_password)."` = '".$this->escape($password_hash)."' WHERE `".$this->escape($this->field_id)."` = ".$this->escape($user_id);
			$result = $this->mysqli->query( $sql );
			if ( $result !== false ) {
				if ( $this->recover_table_check() ) //delete all recovery codes for the user
					$this->mysqli->query( "DELETE FROM `".$this->escape($this->recover_table)."` WHERE `".$this->escape($this->recover_field_id)."` = '".$this->escape($user_id)."' ");
				return true;
			}
		}
		return false;
	}

	/**
	 * Update username for a user
	 *
	 * @param integer $user_id 
	 * @param string $username
	 *
	 * @return boolean True if the username was changed
	 */ 
	public function set_username( $user_id, $username ) {
		if ( $this->table_check() ) {
			$sql = "UPDATE `".$this->escape($this->table)."` SET `".$this->escape($this->field_username)."` = '".$this->escape($username)."' WHERE `".$this->escape($this->field_id)."` = ".$this->escape($user_id);
			$result = $this->mysqli->query( $sql );
			if ( $result !== false )
				return true;
		}
		return false;
	}

	/**
	 * Check if username is available
	 *
	 * @param string $username
	 * @param integer $user_id User id to ignore
	 *
	 * @return boolean
	 */
  	public function is_username_available( $username, $user_id = 0 ) {
		global $o3;
		$sql = "SELECT * FROM `".$this->escape($this->table)."` WHERE `".$this->escape($this->field_id)."` != '".$o3->mysqli->escape_string($user_id)."' AND `".$this->escape($this->field_username)."` = '".$o3->mysqli->escape_string($username)."'";
		$result = $o3->mysqli->query( $sql );
		return $result->num_rows > 0 ? false : true;
	}

	/**
	 * Update deleted for a user
	 *
	 * @param integer $user_id 
	 * @param integer $deleted
	 *
	 * @return boolean True if the deleted was changed
	 */
	public function set_deleted( $user_id, $deleted ) {
		if ( $this->table_check() ) {
			$sql = "UPDATE `".$this->escape($this->table)."` SET  `".$this->escape($this->field_username)."` = NULL, `".$this->escape($this->field_deleted)."` = '".$this->escape($deleted)."' WHERE `".$this->escape($this->field_id)."` = ".$this->escape($user_id);
			$result = $this->mysqli->query( $sql );
			if ( $result !== false )
				return true;
		}
		return false;
	}
	
  /**
	 * Return value of column of data 
	 *
	 * @param string $index Intex of colunn	 
	 * @param string $value If index not set value is returned
	 *
	 * @return mixed 
	 */	
  public function get( $index, $value = '' ) {
  	return isset($this->data[$index]) ? $this->data[$index] : $value;
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
	 * Escapes special characters in a string for use in an SQL statement, taking into account the current charset of the connection
	 *
	 * @param string $str String to escape
	 *
	 * @return string
	 */	
  private function escape( $str ) {
  	return $this->mysqli->escape_string( $str );
  }
  
  /**
	 *  Compare value with hash value
	 *
	 * @param string $password Not hashed string 
	 * @param string $hash_password Hashed string
	 * @return string
	 */	
  public function check_hash( $password, $hash_password ) {  	
  	if ( $this->check_hash_function == '' )
  		return o3_sha3($password) == $hash_password ? true : false;
  	return call_user_func( is_array($this->check_hash_function) ? array( isset($this->check_hash_function[0]) ? $this->check_hash_function[0] : '', isset($this->check_hash_function[1]) ? $this->check_hash_function[1] : '' ) : $this->check_hash_function, $password, $hash_password );	
  }
  
  /**
	 *  Generate a hash value
	 *
	 * @param string $string String to hash
	 * @return string
	 */	
  public function hash( $string ) {
  	if ( $this->hash_function == '' )
  		return o3_sha3($string);
  	return call_user_func( is_array($this->hash_function) ? array( isset($this->hash_function[0]) ? $this->hash_function[0] : '', isset($this->hash_function[1]) ? $this->hash_function[1] : '' ) : $this->hash_function, $string );	
  }
     
}

?>