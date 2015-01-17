<?php

//check if included from admin
if ( !defined('O3_ADMIN') )
	die('O3 Admin not defined.');
 
$configData = array();
 
$configData[] = array( 'name' => 'O3_VERSION',
					   'value' => O3_VERSION,
					   'description' => 'O3 Engine version',
					   'status' => '',
					   'status_type' => 0 );

$configData[] = array( 'name' => 'O3_DIR',
					   'value' => O3_DIR,
					   'description' => 'O3 installation\'s root directory',
					   'status' => '',
					   'status_type' => 0 );

//check if folder writable
$is_writable_O3_CUSTOM_CONFIG = is_writable( O3_CUSTOM_CONFIG );
$configData[] = array( 'name' => 'O3_CUSTOM_CONFIG',
					   'value' => O3_CUSTOM_CONFIG,
					   'description' => 'Custom configuration file',
					   'status' => '',
					   'status_type' => 0 );

$configData[] = array( 'name' => 'O3_INC_DIR',
					   'value' => O3_INC_DIR,
					   'description' => 'O3 installation\'s include directory',
					   'status' => '',
					   'status_type' => 0 );

$configData[] = array( 'name' => 'O3_MOD_DIR',
					   'value' => O3_MOD_DIR,
					   'description' => 'O3 installation\'s modules directory',
					   'status' => '',
					   'status_type' => 0 );

//check if folder writable
$is_writable_O3_CACHE_DIR = is_writable( O3_CACHE_DIR );
$configData[] = array( 'name' => 'O3_CACHE_DIR',
					   'value' => O3_CACHE_DIR,
					   'description' => 'O3 installation\'s public cache directory',
					   'status' => $is_writable_O3_CACHE_DIR ? 'Writable' : 'Not writable',
					   'status_type' => $is_writable_O3_CACHE_DIR ? 1 : 3 );

//check if folder writable
$is_writable_O3_CACHE_PRIVATE_DIR = is_writable( O3_CACHE_PRIVATE_DIR );
$configData[] = array( 'name' => 'O3_CACHE_PRIVATE_DIR',
					   'value' => O3_CACHE_PRIVATE_DIR,
					   'description' => 'O3 installation public cache directory',
					   'status' => $is_writable_O3_CACHE_PRIVATE_DIR ? 'Writable' : 'Not writable',
					   'status_type' => $is_writable_O3_CACHE_PRIVATE_DIR ? 1 : 3 );

$configData[] = array( 'name' => 'O3_RES_DIR',
					   'value' => O3_RES_DIR,
					   'description' => 'O3 installation\'s resources directory',
					   'status' => '',
					   'status_type' => 0 );

//check if folder writable
$is_writable_O3_TEMP_DIR = is_writable( O3_TEMP_DIR );
$configData[] = array( 'name' => 'O3_TEMP_DIR',
					   'value' => O3_TEMP_DIR,
					   'description' => 'O3 installation\'s directory for temp files',
					   'status' => $is_writable_O3_TEMP_DIR ? 'Writable' : 'Not writable',
					   'status_type' => $is_writable_O3_TEMP_DIR ? 1 : 3 );

$configData[] = array( 'name' => 'O3_HOST',
					   'value' => O3_HOST,
					   'description' => 'O3 installation\'s host',
					   'status' => '',
					   'status_type' => 0 );

$configData[] = array( 'name' => 'O3_URL',
					   'value' => O3_URL,
					   'description' => 'O3 installation\'s url',
					   'status' => '',
					   'status_type' => 0 );

$url_exists_O3_CACHE_URL = o3_url_exists( O3_CACHE_URL ) || o3_get_host().'/'.o3_url_exists( O3_CACHE_URL );
$configData[] = array( 'name' => 'O3_CACHE_URL',
					   'value' => O3_CACHE_URL,
					   'description' => 'URL to cache directory',
					   'status' => $url_exists_O3_CACHE_URL ? 'OK' : '404 Not Found',
					   'status_type' => $url_exists_O3_CACHE_URL ? 1 : 3 );

$configData[] = array( 'name' => 'O3_ADMIN_USERNAME',
					   'value' => O3_ADMIN_USERNAME,
					   'description' => 'Username for admin login',
					   'status' => O3_ADMIN_USERNAME != '' ? 'OK' : 'Username is empty',
					   'status_type' => O3_ADMIN_USERNAME != '' ? 1 : 3 );

$configData[] = array( 'name' => 'O3_ADMIN_PASSWORD',
					   'value' => O3_ADMIN_PASSWORD != '' ? '********' : '',
					   'description' => 'Password for admin login',
					   'status' => O3_ADMIN_PASSWORD != '' ? 'OK' : 'Password is empty',
					   'status_type' => O3_ADMIN_PASSWORD != '' ? 1 : 3 );

$configData[] = array( 'name' => 'O3_ADMIN_THEME',
					   'value' => O3_ADMIN_THEME,
					   'description' => 'Admin\'s theme',
					   'status' => '',
					   'status_type' => 0 );

//check if folder writable
$is_writable_O3_LOG_FILE = is_writable( O3_LOG_FILE );
$configData[] = array( 'name' => 'O3_LOG_FILE',
					   'value' => O3_LOG_FILE,
					   'description' => 'Error log file',
					   'status' => $is_writable_O3_LOG_FILE ? 'Writable' : 'Not writable',
					   'status_type' => $is_writable_O3_LOG_FILE ? 1 : 3 );

$configData[] = array( 'name' => 'O3_LOG_ALLOWED',
					   'value' => O3_LOG_ALLOWED ? 'TRUE' : 'FALSE',
					   'description' => 'Is error logging allowed',
					   'status' => '',
					   'status_type' => 0 );

$configData[] = array( 'name' => 'O3_DEBUG_ALLOWED',
					   'value' => O3_DEBUG_ALLOWED ? 'TRUE' : 'FALSE',
					   'description' => 'Is debugging allowed',
					   'status' => '',
					   'status_type' => 0 );




?>
<div class="padding10">
	<h1>Setup / Config</h1>

	<?php echo o3\admin\functions\generateConfigTable( $configData ); ?>

</div>