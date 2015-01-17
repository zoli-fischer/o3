<?php

/**
 * MySql database module for O3 Engine
 *
 * Loader file
 *
 * @package o3
 * @link    todo: add url
 * @author  Zotlan Fischer <zoli_fischer@yahoo.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
 
if ( !defined('O3') || !isset($this) )
	die('O3 not defined.');

require_once(str_replace(DIRECTORY_SEPARATOR, '/', realpath(dirname(__FILE__))).'/config.php');
require_once('o3_mysqli.php');

//get module data
$o3_module_data = array_merge( array( 'autoconnect' => O3_MYSQLI_AUTOCONNECT,
									  'host' => O3_MYSQLI_HOST,
									  'user' => O3_MYSQLI_USER,
									  'password' => O3_MYSQLI_PASSWORD,
									  'dbname' => O3_MYSQLI_DB,
									  'port' => O3_MYSQLI_PORT,
									  'socket' => O3_MYSQLI_SOCKET,
									  'allow_debug_queries' => O3_MYSQLI_DEBUG_QUERIES ), $o3_module_data );

//create o3 instance in the o3 main object
$this->mysqli = new o3_mysqli( $o3_module_data['host'], $o3_module_data['user'], $o3_module_data['password'], $o3_module_data['dbname'], $o3_module_data['port'], $o3_module_data['socket'] );
$this->mysqli->set_parent( $this );

//connect if autoconnect set
if ( isset($o3_module_data['autoconnect']) && $o3_module_data['autoconnect'] === true )
	$this->mysqli->connect();

//enable/disable queries printed in debug consol if debugging allowed
$this->mysqli->allow_debug_queries = $o3_module_data['allow_debug_queries'];

//add shutdown event
$this->debug->add_shutdown_callback( array( $this->mysqli, 'shutdown_callback' ) );

?>