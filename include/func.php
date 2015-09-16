<?php 
 
/**
 * O3 Engine general functions and URI handler
 *
 * Functions for handling URI parts ang general cases.
 *
 * @package o3
 * @link    todo: add url
 * @author  Zotlan Fischer <zoli_fischer@yahoo.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

/*
* Needed for chaining methods on a object
* @example o3_with(new input())->show();
* @param mixed Returns this object
* @return mixed The given object
*/
function o3_with( $object ){ return $object; }
  

/*
* Get list or properties and values of an object as an array
* @param mixed Object
* @return array
*/
function o3_props( $object ) {
	$return = array();
	$props = get_object_vars( $object );
	foreach ( $props as $key => $value )
		$return[$key] = $value;		
	return $return;
}


/**
* Get an item from $_GET by index. 
*
* If the index not set the 2nd parameter is returned.
*
* @param string $index Index of the $_GET item
* @param mixed $value (optional) Return value in case the index is not set. Default value: ''
* @param boolean $url_decode (optional) If true before returning the value a o3_urldecode will be casted on it. Default value: true
*	
* @see o3_urldecode() o3_urldecode()
*
* @return mixed
*/	
function o3_get( $index, $value = '', $url_decode = true ) {
	return isset( $_GET[$index] ) ? ( $url_decode ? o3_urldecode($_GET[$index]) : $_GET[$index] ) : $value;
}

/**
* Get an item from $_POST by index. 
*
* If the index not set the 2nd parameter is returned.
*
* @param string $index Index of the $_POST item
* @param mixed $value (optional) Return value in case the index is not set. Default value: ''
*	
* @return mixed
*/	
function o3_post( $index, $value = '' ) {
	return isset( $_POST[$index] ) ? $_POST[$index] : $value;	
}

/**
* Get an item from $_REQUEST by index. 
*
* If the index not set the 2nd parameter is returned.
*
* @param string $index Index of the $_REQUEST item
* @param mixed $value (optional) Return value in case the index is not set. Default value: ''
* @param boolean $url_decode (optional) If true before returning the value a o3_urldecode will be casted on it. Default value: true
*	
* @see o3_urldecode() o3_urldecode()
*
* @return mixed
*/
function o3_request( $index, $value = '', $url_decode = true ) {
	return isset( $_REQUEST[$index] ) ? ( $url_decode ? o3_urldecode($_REQUEST[$index]) : $_REQUEST[$index] ) : $value;
}

/**
* Get an item from $_SESSION by index. 
*
* If the index not set the 2nd parameter is returned.
*
* @param string $index Index of the $_SESSION item
* @param mixed $value (optional) Return value in case the index is not set. Default value: ''
*	
* @return mixed
*/	
function o3_session( $index, $value = '' ) {
	return isset( $_SESSION[$index] ) ? $_SESSION[$index] : $value;	
}

/**
* Alias for o3_session
*/
function o3_session_get( $index, $value = '' ) {
	return o3_session( $index, $value );
}

/**
* Set an item to $_SESSION by index. 
*
* @param string $index Index of the $_SESSION item
* @param mixed $value (optional) Value to set set. Default value: ''
*	
* @return mixed
*/	
function o3_session_set( $index, $value = '' ) {
	return $_SESSION[$index] = $value;	
}

/**
	* Delete a session variable
	*
	* @param string $index The name of the session variable	
	*
	* @return void
	*/
function o3_unset_session( $index ) {
	if ( isset($_SESSION[$index]) )
		unset($_SESSION[$index]);
}

/**
	* Start new or resume existing session
	*
	* @return string Session ID
	*/	
function o3_session_start( $id = '' ) {
	if ( session_id() == '' ) {
		if ( $id != '' )
			session_id($id);
		session_start();
	}
	return session_id();
}


/**
	* Get an item from $_COOKIE by index. 
	*
	* If the index not set the 2nd parameter is returned.
	*
	* @param string $index Index of the $_COOKIE item
	* @param mixed $value (optional) Return value in case the index is not set. Default value: ''
	*	
	* @return mixed
	*/
function o3_cookie( $index, $value = '' ) {
	return isset( $_COOKIE[$index] ) ? $_COOKIE[$index] : $value;	
}

/**
	* Send a cookie.
	*
	* Sends a cookie along with the rest of the HTTP headers.
	* Like other headers, cookies must be sent before any output from your script (this is a protocol restriction). 		
	*	
	* @param array|string $index The name of the names cookie.
	* @param array|string $value (optional) The value of the cookie. Default value: ''
	* @param int $time (optional) The time the cookie expires. If set to 0 the cookie will expire at the end of the session. If omitted the cookie will expire in 1 hour. Default value: 3600
	* @param string $path (optional) The path on the server in which the cookie will be available on. Default value: /
	* @param string $domain (optional) The domain that the cookie is available to. Default value: '' 
	* @param string $secure (optional) Indicates that the cookie should only be transmitted over a secure HTTPS connection from the client. Default value: false
	* @param string $httponly (optional) When TRUE the cookie will be made accessible only through the HTTP protocol. Default value: false 
	*
	* @link http://php.net/manual/en/function.setcookie.php
	*
	* @return mixed
	*/
function o3_set_cookie( $index, $value = '', $time = 3600, $path = "/", $domain = '', $secure = false, $httponly = false ) {		
	setcookie($index, $value, time() + $time, $path, $domain, $secure, $httponly );	
	return $_COOKIE[$index] = $value;	
}

/**
	* Delete a cookie.
	*
	* @param string $index The name of the cookie. 	
	*
	* @return void
	*/
function o3_unset_cookie( $index ) {
	setcookie( $index, '', time() - 3600, "/" );		
	if ( isset($_COOKIE[$index]) )
		unset($_COOKIE[$index]);		
}

/**
* Decodes URL-encoded string
*
* Decodes encoded unocide charachters compared to urldecode(). 
* Decodes any %#### encoding in the given string. Plus symbols ('+') are decoded to a space character.
* 
* @link http://php.net/manual/en/function.urldecode.php
*
* @param string $str The string to be decoded
* @param string $encoding (optional) Encoding of the returned string. Default value: "UTF-8"
*
* @return string Returns the decoded string
*/
function o3_urldecode( $str, $encoding = 'UTF-8' ) {
  preg_match_all('/%u([[:alnum:]]{4})/', $str, $a);	   
  foreach ($a[1] as $uniord) {
    $dec = hexdec($uniord);
    $utf = '';
   
    if ($dec < 128) {
        $utf = chr($dec);
    } else if ($dec < 2048) {
        $utf = chr(192 + (($dec - ($dec % 64)) / 64));
        $utf .= chr(128 + ($dec % 64));
    } else {
        $utf = chr(224 + (($dec - ($dec % 4096)) / 4096));
        $utf .= chr(128 + ((($dec % 4096) - ($dec % 64)) / 64));
        $utf .= chr(128 + ($dec % 64));
    }
   
    $str = str_replace('%u'.$uniord, $utf, $str);
  }
  return o3_convert(urldecode($str),$encoding);
}

/**
* Check if URL exists
* 
* @param string $str The URL to check
*
* @return boolean True if URL exists else false
*/
function o3_url_exists( $url ) {
	$file_headers = @get_headers($url);
	return !($file_headers === false || strpos( '404 Not Found', $file_headers[0] ) !== false);
}

if ( !defined('O3_COMPARE_URI_SCHEME') ) define('O3_COMPARE_URI_SCHEME', 8 );
if ( !defined('O3_COMPARE_URI_PORT') ) define('O3_COMPARE_URI_PORT', 16 );
if ( !defined('O3_COMPARE_URI_HOST') ) define('O3_COMPARE_URI_HOST', 1 );
if ( !defined('O3_COMPARE_URI_PATH') ) define('O3_COMPARE_URI_PATH', 2 );
if ( !defined('O3_COMPARE_URI_QUERY') ) define('O3_COMPARE_URI_QUERY', 4 );
if ( !defined('O3_COMPARE_URI_FRAGMENT') ) define('O3_COMPARE_URI_FRAGMENT', 32 );
if ( !defined('O3_COMPARE_URI_ALL') ) define('O3_COMPARE_URI_ALL', O3_COMPARE_URI_HOST | O3_COMPARE_URI_PATH | O3_COMPARE_URI_QUERY | O3_COMPARE_URI_SCHEME | O3_COMPARE_URI_PORT | O3_COMPARE_URI_FRAGMENT );
/**
* Compare 2 uris
*
* @param string $uri1 URI to compare
* @param string $uri2 URI compare with
* @param string $flag Flags to change the compare algorithm 
*
* @return boolean
*/
function o3_compare_uri( $uri1, $uri2, $flag = O3_COMPARE_URI_ALL ) {
	$uri1_parts = parse_url($uri1);
	$uri1_parts['query'] = isset($uri1_parts['query']) ? $uri1_parts['query'] : '';
	$uri1_parts['fragment'] = isset($uri1_parts['fragment']) ? $uri1_parts['fragment'] : '';
	$uri1_parts['path'] = isset($uri1_parts['path']) ? $uri1_parts['path'] : '';

	$uri2_parts = parse_url($uri2);
	$uri2_parts['query'] = isset($uri2_parts['query']) ? $uri2_parts['query'] : '';
	$uri2_parts['fragment'] = isset($uri2_parts['fragment']) ? $uri2_parts['fragment'] : '';
	$uri2_parts['path'] = isset($uri2_parts['path']) ? $uri2_parts['path'] : '';

	//check scheme
	if ( ( $flag & O3_COMPARE_URI_SCHEME ) && $uri1_parts['scheme'] != $uri2_parts['scheme']  )
		return false;

	//check port
	if ( ( $flag & O3_COMPARE_URI_PORT ) && $uri1_parts['port'] != $uri2_parts['port']  )
		return false;

	//check host
	if ( ( $flag & O3_COMPARE_URI_HOST ) && $uri1_parts['host'] != $uri2_parts['host']  )
		return false;

	//check host
	if ( ( $flag & O3_COMPARE_URI_PATH ) && $uri1_parts['path'] != $uri2_parts['path']  )
		return false;

	//check host
	if ( ( $flag & O3_COMPARE_URI_QUERY ) && $uri1_parts['query'] != $uri2_parts['query']  )
		return false;

	//check host
	if ( ( $flag & O3_COMPARE_URI_FRAGMENT ) && $uri1_parts['fragment'] != $uri2_parts['fragment']  )
		return false;

	return true;
}


/**
* Get host with protocol and port for a HTTP(s) URl string 
* 
* @param string $url The URL to check (optional). Default value: null. If null the current host is used.
*
* @return string Current url
*/
function o3_get_host( $url = null ) {
	//todo: finish function
	if ( $url == null ) {
		$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
		$protocol = isset($_SERVER['HTTPS']) ? 'https' : 'http';
		$port = isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : '';
		$url = $protocol.'://'.$host.( $port != 80 && $port != 443 && $port != '' ? ':'.$port : '' );
	}
	return $url;
}

/**
 * Get URL content
 * 
 * @param string $url URL to get
 * @param number $timeout timeout to wait for response.
 * @return mixed Return value or false.
 */
function o3_get_url( $url, $timeout = 5 ) {
	$result = false;

	//check if curl_init available
    if ( is_callable('curl_init') ) {
        try {
			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);                    
               
            $result = @curl_exec($ch);
               
            curl_close($ch);
        } catch (Exception $e) {
        }
    } else {
    	$context = stream_context_create( 
	        			array( 
	        				'http'=> array( 
	        					'timeout' => $timeout,
	        				), 
	        				'https'=> array(
	        					'timeout' => $timeout,
	        				),        			
		        			'ssl' => array(
						        'verify_peer' => false,
						    )
		        		)
	        		);        	
        $result = @file_get_contents( $url, false, $context );
    }

    return $result;
}

//ARRAY

/**
	* Converts standard object into array
	* 
	* @param mixed $d The object to convert
	*
	* @return array Returns the converted array
	*/
function obj2arr($d) {
	if (is_object($d)) {
		// Gets the properties of the given object with get_object_vars function
		$d = get_object_vars($d);
	}

	if (is_array($d)) {
		/*
		* Return array converted to object
		* Using __FUNCTION__ (Magic constant)
		* for recursive call
		*/
		return array_map(__FUNCTION__, $d);
	}
	else {
		// Return array
		return $d;
	}
}

/**
	* Converts array into standard object
	* 
	* @param mixed $d The array to convert
	*
	* @return array Returns the converted object
	*/
function arrayToObject($d) {
	if (is_array($d)) {
		// Return array converted to object using __FUNCTION__ (Magic constant) for recursive call		
		return (object) array_map(__FUNCTION__, $d);
	}
	else {
		// Return object
		return $d;
	}
}

?>