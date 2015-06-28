<?php 

/**
 * O3 Engine user agent functions
 *
 * Functions for checking operating system, web browser and device from user agent string
 *
 * @package o3
 * @link    todo: add url
 * @author  Zotlan Fischer <zoli_fischer@yahoo.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
 

/** string Microsoft Internet Explorer Value: msie */
DEFINE('O3_MSIE','msie');
/** string Mozilla Firefox Value: ff */
DEFINE('O3_FF','ff');
/** string Apple Safari Value: safari */
DEFINE('O3_SAFARI','safari');
/** string Google Chrome Value: chrome */
DEFINE('O3_CHROME','chrome');
/** string Opera Value: opera */
DEFINE('O3_OPERA','opera');
/** string Unknow web browser Value: other_browser */
DEFINE('O3_OTHER_BROWSER','other_browser');
/** string Opera with webkit engine Value: opera_chrome */
DEFINE('O3_OPR','opera_chrome');
/** string Microsoft Internet Explorer with gecko ua string Value: msie_gecko */
DEFINE('O3_MSGECKO','msie_gecko'); //msie 11+

/** string Windows Value: windows */
DEFINE('O3_WINDOWS','windows');
/** string Macintosh Value: mac */
DEFINE('O3_MAC','mac');
/** string Linux Value: linux */
DEFINE('O3_LINUX','linux');
/** string Android Value: android */
DEFINE('O3_ANDROID','android');
/** string iOs Value: ios */
DEFINE('O3_IOS','ios');
/** string Unknow operating system Value: other_os */
DEFINE('O3_OTHER_OS','other_os');

/** string PC Value: pc */
DEFINE('O3_PC','pc');
/** string Mobile Value: mobile */
DEFINE('O3_MOBILE','mobile');

$o3_browser_list = array ( 
					O3_MSIE => 'MSIE',
					O3_MSGECKO => 'Trident',
					O3_FF => 'Firefox',
					O3_OPERA => 'Opera',
					O3_OPR => 'OPR', 
					O3_CHROME => 'Chrome',
					O3_SAFARI => 'Safari');
$o3_os_list = array ( 
					O3_WINDOWS => 'Windows', 
					O3_MAC => 'Macintosh',
					O3_ANDROID => 'Android',												
					O3_IOS => '(iOS)|(iPhone)|(iPad)|(iPod)',
					O3_LINUX => 'Linux' );
$o3_device_list = array ( 
					O3_MOBILE => 'Mobile' );

define('O3_HTTP_USER_AGENT',isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');

//OS

/**
* Get OS type from user agent string
*
* @param string $ua (optional) User agent string. Default value: Global user agent string
*
* @return string
*/
function o3_get_os( $ua = O3_HTTP_USER_AGENT ) {			
	global $o3_os_list;
	foreach ( $o3_os_list as $key => $value ) {		
		if ( preg_match('/'.str_replace('/','\\/',$value).'/', $ua ) ) 
			return $key;
	}
	return O3_OTHER_OS;
}

/**
* Get name of the OS from user agent string
*
* @param string $ua (optional) User agent string. Default value: Global user agent string
*
* @return string
*/
function o3_get_os_name( $ua = O3_HTTP_USER_AGENT ) {
	global $o3_os_list;
	foreach ( $o3_os_list as $key => $value)		
		if (preg_match('/'.str_replace('/','\\/',$value).'/', $ua )) 
			return $value;
	return O3_OTHER_OS;
}

/**
* Check if the current OS is Android from user agent string
*
* @param string $ua (optional) User agent string. Default value: Global user agent string
*
* @return boolean
*/
function o3_is_android( $ua =  O3_HTTP_USER_AGENT ) {
	return o3_get_os( $ua ) == O3_ANDROID; 
}

/**
* Check if the current OS is iOS from user agent string
*
* @param string $ua (optional) User agent string. Default value: Global user agent string
*
* @return boolean
*/
function o3_is_ios( $ua =  O3_HTTP_USER_AGENT ) {
	return o3_get_os( $ua ) == O3_IOS; 
}	

/**
* Check if the current OS is MS Windows from user agent string
*
* @param string $ua (optional) User agent string. Default value: Global user agent string
*
* @return boolean
*/
function o3_is_windows( $ua =  O3_HTTP_USER_AGENT ) {
	return o3_get_os( $ua ) == O3_WINDOWS; 
}	

/**
* Check if the current OS is Mac from user agent string
*
* @param string $ua (optional) User agent string. Default value: Global user agent string
*
* @return boolean
*/
function o3_is_mac( $ua =  O3_HTTP_USER_AGENT ) {
	return o3_get_os( $ua ) == O3_MAC; 
}	

/**
* Check if the current OS is Linux from user agent string
*
* @param string $ua (optional) User agent string. Default value: Global user agent string
*
* @return boolean
*/
function o3_is_linux( $ua =  O3_HTTP_USER_AGENT ) {
	return o3_get_os( $ua ) == O3_LINUX; 
}

/**
* Get iOS version from user agent string
*
* @param string $ua (optional) User agent string. Default value: Global user agent string
*
* @return string|boolean False if the OS is not iOS
*/
function o3_ios( $ua =  O3_HTTP_USER_AGENT ) {
	if ( o3_is_ios() ) {
		$version = preg_replace("/(.*) OS ([0-9]*)_(.*)/","$2", $ua);
	  if( intval($version) > 0 ) {
	    return intval($version);
	  }
	}
	return false;
}	

//BROWSER

/**
* Returns the current browser type from user agent string
*
* @param string $ua (optional) User agent string. Default value: Global user agent string
*
* @return string O3_MSIE, O3_MSGECKO, O3_FF, O3_OPERA, O3_OPR, O3_CHROME,	O3_SAFARI
*/
function o3_get_browser( $ua = O3_HTTP_USER_AGENT ) {
	global $o3_browser_list;	
	foreach ( $o3_browser_list as $key => $value )
		if (preg_match('/'.str_replace('/','\\/',$value).'/', $ua)) 
			return $key;	
	return O3_OTHER_BROWSER;
}

/**
* Returns the current browser name from user agent string
*
* @param string $ua (optional) User agent string. Default value: Global user agent string
*
* @return string
*/
function o3_get_browser_name( $ua =  O3_HTTP_USER_AGENT ) {
	global $o3_browser_list;	
	foreach ( $o3_browser_list as $key => $value) {	
		if (preg_match('/'.str_replace('/','\\/',$value).'/', $ua)) 
			return $value;
	}
	return O3_OTHER_BROWSER;
}

/**
* Check if the current web browser supports HTML 5 from user agent string
*
* @param string $ua (optional) User agent string. Default value: Global user agent string
*
* @return boolean
*/
function o3_is_html5( $ua =  O3_HTTP_USER_AGENT ) {
	return o3_msie( $ua ) >= 9 || 
				 o3_ff( $ua ) >= 4 || 
				 o3_opera( $ua ) >= 9 ||
				 o3_chrome( $ua ) >= 3 ||
				 o3_safari( $ua ) >= 4 ||
				 o3_is_html5_mobile( $ua );  
}

/**
* Check if the current web browser supports HTML 5 and the device is a mobile device from user agent string
*
* @param string $ua (optional) User agent string. Default value: Global user agent string
*
* @return boolean
*/
function o3_is_html5_mobile( $ua = O3_HTTP_USER_AGENT ) {
	return o3_is_ios( $ua ) || o3_is_android( $ua ) || (o3_msie( $ua ) >= 9 && o3_is_mobile( $ua ));  
}	

/**
* Get Microsoft Internet Explorer version from user agent string
*
* @param string $ua (optional) User agent string. Default value: Global user agent string
*
* @return string|boolean False if the browser is not Microsoft Internet Explorer
*/
function o3_msie( $ua =  O3_HTTP_USER_AGENT ) {
	$b = o3_get_browser( $ua );
	if ( $b == O3_MSIE ) {
		preg_match('/MSIE ([0-9]{1,2}\.[0-9])/',$ua,$reg);
	  if(!isset($reg[1])) {
	    return false;
	  } else {
	    return intval($reg[1]);
	  }
	} else if ( $b == O3_MSGECKO ) {
		return o3_trident( $ua )+4;
	}
	return false;
}	

/**
* Get Mozilla Firefox version from user agent string
*
* @param string $ua (optional) User agent string. Default value: Global user agent string
*
* @return string|boolean False if the browser is not Mozilla Firefox
*/
function o3_ff( $ua =  O3_HTTP_USER_AGENT ) {
	if ( o3_get_browser( $ua )== O3_FF ) {
		preg_match('/Firefox\/([0-9]{1,2}\.[0-9])/',$ua,$reg);
	  if(!isset($reg[1])) {
	    return false;
	  } else {
	    return intval($reg[1]);
	  }
	}
	return false;
}	

/**
* Get Opera version from user agent string
*
* @param string $ua (optional) User agent string. Default value: Global user agent string
*
* @return string|boolean False if the browser is not Opera 
*/
function o3_opera( $ua =  O3_HTTP_USER_AGENT ) {
	$b = o3_get_browser( $ua );
	if ( $b == O3_OPERA ) {		
		preg_match('/Version\/([0-9]{1,2}\.[0-9])/',$ua,$reg);
		if(!isset($reg[1])) {
	    return false;
	  } else {
	    return intval($reg[1]);
	  }
	} else if ( $b == O3_OPR ) {
		preg_match('/OPR\/([0-9]{1,2}\.[0-9])/',$ua,$reg);
	  if(!isset($reg[1])) {
	    return false;
	  } else {
	    return intval($reg[1]);
	  }
	} 
	return false;
}	

/**
* Get Google Chrome version from user agent string
*
* @param string $ua (optional) User agent string. Default value: Global user agent string
*
* @return string|boolean False if the browser is not Google Chrome 
*/
function o3_chrome( $ua =  O3_HTTP_USER_AGENT ) {		
	if ( o3_get_browser( $ua ) == O3_CHROME ) {
		preg_match('/Chrome\/([0-9]{1,2}\.[0-9])/',$ua,$reg);
	  if(!isset($reg[1])) {
	    return false;
	  } else {
	    return intval($reg[1]);
	  }
	}
	return false;
}	

/**
* Get Safari version from user agent string
*
* @param string $ua (optional) User agent string. Default value: Global user agent string
*
* @return string|boolean False if the browser is not Safari
*/
function o3_safari( $ua =  O3_HTTP_USER_AGENT ) {
	if ( o3_get_browser( $ua ) == O3_SAFARI ) {
		preg_match('/Version\/([0-9]{1,2}\.[0-9])/',$ua,$reg);
	  if(!isset($reg[1])) {
	    return false;
	  } else {
	    return intval($reg[1]);
	  }
	}
	return false;
}	

//BROWSER ENGINE

/**
* Get Trident version from user agent string
*
* Trident is Microsoft Internet Explorer's layout engine.
* Trident 7 for MSIE 11
* Trident 6 for MSIE 10
* Trident 5 for MSIE 9
* Trident 4 for MSIE 8
* Trident 3.1 for MSIE 7
*
* @param string $ua (optional) User agent string. Default value: Global user agent string
*
* @return string|boolean False if the browser's engine is not Trident
*/
function o3_trident( $ua =  O3_HTTP_USER_AGENT ) {
	preg_match('/Trident\/([0-9]{1,2}\.[0-9])/',$ua,$reg);
  if(!isset($reg[1])) {
    return false;
  } else {
    return intval($reg[1]);
  }
	return false;
}	

/**
* Check if current layout engine is Gecko from user agent string
*
* @param string $ua (optional) User agent string. Default value: Global user agent string
*
* @return string|boolean False if the browser's engine is not Gecko
*/
function o3_gecko( $ua =  O3_HTTP_USER_AGENT ) {
	if ( preg_match('/'.str_replace('/','\\/','Gecko').'/', $ua) ) 
		return true;
	return false;
}

/**
* Check if current layout engine is Webkit from user agent string
*
* @param string $ua (optional) User agent string. Default value: Global user agent string
*
* @return string|boolean False if the browser's engine is not Webkit
*/
function o3_webkit( $ua =  O3_HTTP_USER_AGENT ) {
	preg_match('/AppleWebKit\/([0-9]{1,3}\.[0-9])/',$ua,$reg);
  if(!isset($reg[1])) {
    return false;
  } else {
    return intval($reg[1]);
  }
	return false;
}

/**
* Check if current layout engine is Presto from user agent string
*
* @param string $ua (optional) User agent string. Default value: Global user agent string
*
* @return string|boolean False if the browser's engine is not Presto
*/
function o3_presto( $ua =  O3_HTTP_USER_AGENT ) {
	preg_match('/Presto\/([0-9]{1,3}\.[0-9])/',$ua,$reg);
  if(!isset($reg[1])) {
    return false;
  } else {
    return intval($reg[1]);
  }
	return false;
}

//ROBOTS

/**
* Check if current visitor is search bot
*
* @param string $ua (optional) User agent string. Default value: Global user agent string
*
* @return string|boolean False if the visitor is not a search bot
*/
function o3_robot( $ua = O3_HTTP_USER_AGENT ) {	
	if ( preg_match('/Google|Yahoo/', $ua ) ) 
		return true;
	return false;
}

//DEVICE

/**
* Get device type from user agent string
*
* @param string $ua (optional) User agent string. Default value: Global user agent string
*
* @return string O3_PC, O3_MOBILE
*/
function o3_get_device( $ua =  O3_HTTP_USER_AGENT ) {		
	global $o3_device_list; 
	foreach ( $o3_device_list as $key => $value )		
		if (preg_match('/'.$value.'/', $ua)) 
			return $key;
	return O3_PC;
}

/**
* Get device name from user agent string
*
* @param string $ua (optional) User agent string. Default value: Global user agent string
*
* @return string
*/
function o3_get_device_name( $ua = O3_HTTP_USER_AGENT) {
	global $o3_device_list; 
	foreach ( $o3_device_list as $key => $value)		
		if (preg_match('/'.$value.'/', $ua )) 
			return $value; 
	return O3_PC;
}

/**
* Check if the current device is a mobile device
*
* @param string $ua (optional) User agent string. Default value: Global user agent string
*
* @return boolean
*/
function o3_is_mobile( $ua =  O3_HTTP_USER_AGENT ) {
	return o3_get_device( $ua ) == O3_MOBILE; 
}

/**
* Check if the current device is an iPad from user agent string
*
* @param string $ua (optional) User agent string. Default value: Global user agent string
*
* @return boolean
*/
function o3_is_ipad( $ua = O3_HTTP_USER_AGENT ) {	
	if ( preg_match('/iPad/', $ua ) ) 
			return true;		
	return false;
}

/**
* Check if the current device is an iPhone from user agent string
*
* @param string $ua (optional) User agent string. Default value: Global user agent string
*
* @return boolean
*/
function o3_is_iphone( $ua =  O3_HTTP_USER_AGENT ) {
	if ( preg_match('/iPhone/', $ua ) ) 
			return true;		
	return false;
}

/**
* Check if the current device is an iPod from user agent string
*
* @param string $ua (optional) User agent string. Default value: Global user agent string
*
* @return boolean
*/
function o3_is_ipod( $ua =  O3_HTTP_USER_AGENT ) {
	if ( preg_match('/iPod/', $ua ) ) 
			return true;		
	return false;
}

/**
* Check if the current device is has a touch interface from user agent string
*
* @param string $ua (optional) User agent string. Default value: Global user agent string
*
* @return boolean
*/
function o3_is_touch( $ua =  O3_HTTP_USER_AGENT ) {
	return o3_is_mobile( $ua ) || o3_is_android( $ua ) || o3_is_ios( $ua ) || ( o3_msie( $ua ) && o3_is_mobile( $ua ) );  
}	
	
/**
* Generate list of class names from user agent string 
*
* List of class names: <br> 
* <b>mobile</b> - device is mobile <br>
* <b>no-mobile</b> - device is not mobile <br>
* <b>ipad</b>, <b>ipod</b>, <b>iphone</b> - device is ipad, ipod or iphone <br>
* <b>touch</b> - device has a touch interface <br>
* <b>no-touch</b> - device don't has a touch interface <br>
* <b>ios</b> - OS is iOS <br>
* <b>android</b> - OS is Android <br>
* <b>windows</b> - OS is Windows <br>
* <b>mac</b> - OS is Macintosh <br>
* <b>linux</b> - OS is Linux <br>
* <b>html5</b> - web browser supports HTML5 <br>
* <b>msie</b> - web browser is Microsoft Internet Explorer <br>
* <b>msiegt9</b> - web browser is Microsoft Internet Explorer version greater than 9 <br> 
* <b>msielt10</b> - web browser is Microsoft Internet Explorer version lower than 10 <br>
* <b>ff</b> - web browser is Mozilla Firefox <br>
* <b>chrome</b> - web browser is Google Chrome <br> 
* <b>safari</b> - web browser is Safari <br>
* <b>opera</b> - web browser is Opera <br>
* <b>trident</b> - web browser's layout engine is Trident <br>
* <b>gecko</b> - web browser's layout engine is Gecko <br>
* <b>undefined</b> - if nothing was found
*
* @example example.php
* <b>On a PC with MS Windows OS and Mozilla Firefox version 27 </b><br>
* &lt;body class="&lt;?php echo o3_ua_body_classes(); ?&gt;"&gt; <br>
* <b>Output</b> <br>
* &lt;body class="no-mobile no-touch html5 windows ff ff27 gecko"&gt; <br>
* 
* @param string $ua (optional) User agent string. Default value: Global user agent string
*
* @return string
*/
function o3_ua_body_classes( $ua = O3_HTTP_USER_AGENT ) {
	$buffer = array();
	
	if ( !function_exists('push') ) {
		/** @ignore */
		function push( &$array, $value, $key ) {
			if ( $value !== false && !in_array( $key, $array ) ) 
				$array[] = $key;
		}
	}
	
	$value = o3_is_mobile( $ua );
	$key = 'mobile';
	push( $buffer, $value, $key );
	$key = 'no-mobile';
	push( $buffer, !$value, $key );
			 		
	$value = o3_is_ipad( $ua );
	$key = 'ipad';
	push( $buffer, $value, $key );	
	
	$value = o3_is_ipod( $ua );
	$key = 'ipod';
	push( $buffer, $value, $key );	
	
	$value = o3_is_iphone( $ua );
	$key = 'iphone';
	push( $buffer, $value, $key );	
	
	$value = o3_is_touch( $ua );
	$key = 'touch';
	push( $buffer, $value, $key );	
	$key = 'no-touch';
	push( $buffer, !$value, $key );
	
	$value = o3_is_html5( $ua );
	$key = 'html5';
	push( $buffer, $value, $key );
	
	$value = o3_is_android( $ua );
	$key = 'android';
	push( $buffer, $value, $key );
	
	$value = o3_ios( $ua );
	$key = 'ios';
	push( $buffer, $value, $key );
	if ( $value !== false ) {
		$key = 'ios'.$value;
		push( $buffer, $value, $key );
	}
	
	$value = o3_is_windows( $ua );
	$key = 'windows';
	push( $buffer, $value, $key );
		
	$value = o3_is_mac( $ua );
	$key = 'mac';
	push( $buffer, $value, $key );
	
	$value = o3_is_linux( $ua );
	$key = 'linux';
	push( $buffer, $value, $key );
	
	$value = o3_msie( $ua );
	$key = 'msie';
	push( $buffer, $value, $key );
	if ( $value !== false ) {
		
		$key = 'msie'.$value;
		push( $buffer, $value, $key );
	
		if ( $value > 9 ) {	
			$key = 'msiegt9';
			push( $buffer, $value, $key );			
		}
		
		if ( $value < 10 ) {	
			$key = 'msielt10';
			push( $buffer, $value, $key );			
		}
	}
	
	$value = o3_ff( $ua );
	$key = 'ff';
	push( $buffer, $value, $key );
	if ( $value !== false ) {
		$key = 'ff'.$value;
		push( $buffer, $value, $key );
	}
	
	$value = o3_chrome( $ua );
	$key = 'chrome';
	push( $buffer, $value, $key );
	if ( $value !== false ) {
		$key = 'chrome'.$value;
		push( $buffer, $value, $key );
	}
	
	$value = o3_safari( $ua );
	$key = 'safari';
	push( $buffer, $value, $key );		
	if ( $value !== false ) {
		$key = 'safari'.$value;
		push( $buffer, $value, $key );
	}
	
	$value = o3_opera( $ua );
	$key = 'opera';
	push( $buffer, $value, $key );
	if ( $value !== false ) {
		$key = 'opera'.$value;
		push( $buffer, $value, $key );
	}
	
	$value = o3_trident( $ua );
	$key = 'trident';
	push( $buffer, $value, $key );
	if ( $value !== false ) {
		$key = 'trident'.$value;
		push( $buffer, $value, $key );
	}
	
	$value = o3_webkit( $ua );
	$key = 'webkit';
	push( $buffer, $value, $key );
	if ( $value !== false ) {
		$key = 'webkit'.$value;
		push( $buffer, $value, $key );
	}
	
	$value = o3_presto( $ua );
	$key = 'presto';
	push( $buffer, $value, $key );
	if ( $value !== false ) {
		$key = 'presto'.$value;
		push( $buffer, $value, $key );
	}
	
	$value = o3_gecko( $ua );
	$key = 'gecko';
	push( $buffer, $value, $key );
	
	$value = count($buffer) == 0 ? true : false;
	$key = 'undefined';
	push( $buffer, $value, $key );
		
	return implode(' ',$buffer);
}


?>