<?php
/**
 * O3 Engine main config file
 *
 * Configuration should be changed in config.custom-file and not in main config file.
 *
 * @package o3
 * @link    todo: add url
 * @author  Zotlan Fischer <zoli_fischer@yahoo.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace o3\config;

/** O3 Version */
define('O3_VERSION', '1.0.0b');

/** 
* Defines a named constant if not allready defined
* @param $name string The name of the constant. 
* @param $name mixed The value of the constant; only scalar and null values are allowed. Scalar values are integer, float, string or boolean values.
* @param $case_insensitive bool (optional) If set to TRUE, the constant will be defined case-insensitive. The default behavior is case-sensitive. 
*/
function def( $name, $value, $case_insensitive = false ) {
	if ( !defined($name) ) define( $name, $value, $case_insensitive );
}

/*FILESYSTEM*/

/**
 * Some installations don't have $_SERVER['DOCUMENT_ROOT']
 * http://fyneworks.blogspot.com/2007/08/php-documentroot-in-iis-windows-servers.html
 */
if( !isset($_SERVER['DOCUMENT_ROOT']) ) {
  $path = "";
  
  if ( isset($_SERVER['SCRIPT_FILENAME']) )
    $path = $_SERVER['SCRIPT_FILENAME'];
  elseif ( isset($_SERVER['PATH_TRANSLATED']) )
    $path = str_replace('\\\\', '\\', $_SERVER['PATH_TRANSLATED']);
    
  $_SERVER['DOCUMENT_ROOT'] = str_replace( '\\', '/', substr($path, 0, 0-strlen($_SERVER['PHP_SELF'])));
}

if ( !defined('O3_DIR') )
	/** The root of your O3 installation */
	define("O3_DIR", str_replace(DIRECTORY_SEPARATOR, '/', realpath(dirname(__FILE__))));

//load O3 custom config
def("O3_CUSTOM_CONFIG", O3_DIR.'/config.custom.php');
@include_once( O3_CUSTOM_CONFIG );

/** The location of the O3 include directory */
def("O3_INC_DIR", O3_DIR."/include");

/** The location of the O3 module directory */
def("O3_MOD_DIR", O3_DIR."/module");

/** The location of the O3 cache directory */
def("O3_CACHE_DIR", O3_DIR."/cache");

/** The location of the O3 private cache directory */
def("O3_CACHE_PRIVATE_DIR", O3_DIR."/cache-prv");

/** The location of the O3 resource directory */
def("O3_RES_DIR", O3_DIR."/resource");

/** The location of the O3 admin directory */
def("O3_ADMIN_DIR", O3_DIR."/admin");

/** Image magic script */
def("O3_IMAGE_MAGIC", "convert");

/**
 * The location of a temporary directory.
 * The directory specified must be writeable by the webserver process. 
 */
def("O3_TEMP_DIR", sys_get_temp_dir());

/*URL*/

/** Host of your O3 installation */
def("O3_HOST", isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '' );

/** Host protocol of your O3 installation */
def("O3_HOST_PROTOCOL", isset($_SERVER['HTTPS']) ? 'https' : 'http' );
	
/** Host port of your O3 installation */
def("O3_HOST_PORT", isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : '' );

/** URL to your O3 installation */
def("O3_URL", O3_HOST_PROTOCOL.'://'.O3_HOST.( O3_HOST_PORT != 80 && O3_HOST_PORT != 443 && O3_HOST_PORT != '' ? ':'.O3_HOST_PORT : '' ) );

/** URL to cache forlder for your O3 installation */
def("O3_CACHE_URL",O3_URL.'/cache');

/** O3 Admin utility */

/** Username and password used by the admin utility in admin/ */
def("O3_ADMIN_USERNAME", "");
def("O3_ADMIN_PASSWORD", "");

/** Default theme to use for admin utility */
def("O3_ADMIN_THEME", "default" );

/*DEBUG & LOG*/

/** The debug output log file */
def("O3_LOG_FILE", O3_DIR.'/o3.log' );

/** Allow loging */
def("O3_LOG_ALLOWED", false );

/** Allow debuging */
def("O3_DEBUG_ALLOWED", false );

/* CACHE */

/** Cache max file lifetime in seconds - default: 86400 * 14 (14 days)  */
def('O3_CACHE_MAX_LIFETIME', 86400 * 14);

/** Cache cleaning percent - default: 1*/
def('O3_CACHE_CLEAN_USE_PERCENT',1);

/*GENERAL*/

/** Error text for general cases  */
def('O3_ERR_GENERAL', 'An error occurred. Please try again.' );

?>