<?php

/**
 * Routing module for O3 Engine
 *
 * @package o3
 * @link    todo: add url
 * @author  Zotlan Fischer <zoli_fischer@yahoo.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

/**
 * Class for routing
 *
 * @category module
 * @package o3_routing
 */
class o3_routing {

	/** mixed Parent object */
	public $parent = null;

	/**
	 * Constructor of routing
	 * @return void
	 */
	public function __construct() { 
		
	}

	/**
	 * Returns the redirect URL parts
	 * @return void
	 */
	public function parts() { 
		$parts = explode( '/', o3_routing::get_redirect() );

		//remove first path part if empty
		if ( $parts[0] == '' ) 
			array_shift($parts[0]);

		return $parts;
	}

	/**
	 * Match string for redirect url
	 * @return void
	 */
	public function match( $string ) { 
		$string = isset($string[0]) && $string[0] == '/' ? substr( $string, 1, strlen($string) - 1 ) : $string;
		$string = '/^\/'.str_replace( '/', '\/', $string ).'$/';
		return preg_match( $string, o3_routing::get_redirect() ) == 1;
	}

	/**
	 * Add function call on routing
	 * @return void
	 */
	public function parts_rule( $from, $function_name, $object = null ) { 
		$args = func_get_args(); 
		//remove first tree elements
		array_shift($args);
		
		//replace first argument with redirect path parts
		$args[0] = o3_routing::parts();
		
		//replace second argument with full redirect path
		$args[1] = o3_routing::get_redirect();

		if ( o3_routing::match( $from ) )			
			return call_user_func_array( $object != null ? array( $object, $function_name ) : $function_name, $args );
		
		return false;
	}

	/**
	 * Add redirect to url routing
	 * @return void
	 */
	public function url_rule( $from, $to ) {
		if ( o3_routing::match( $from ) )
			o3_redirect( $to );
		return false;
	}

	/**
	 * Add function call on routing
	 * @return void
	 */
	public function function_rule( $from, $function_name, $object = null ) { 
		$args = func_get_args();
		//remove first tree elements
		array_shift($args); array_shift($args); array_shift($args);		

		if ( o3_routing::match( $from ) )
			return call_user_func_array( $object != null ? array( $object, $function_name ) : $function_name, $args );
		
		return false;
	}

	/**
	 * Value of server redirect url
	 * @return string
	 */
	public function get_redirect() {
		return isset($_SERVER['REDIRECT_URL']) ? $_SERVER['REDIRECT_URL'] : '/';
	}

	/**
	* Function sets the parent object
	*
	* @param object $parent Parent object	
	* @return object
	*/	
	public function set_parent( $parent ) {
		return $this->parent = &$parent;
	}	

}

?>