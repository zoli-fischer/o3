<?php
/**
 * User login module for O3 Engine
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
require_once('o3_login.php');

//create o3 instance in the o3 main object
$this->login = new o3_login(); 
$this->login->set_parent( $this );

//check module data
if ( isset($o3_module_data['table']))
	$this->login->table = $o3_module_data['table'];

if ( isset($o3_module_data['field_id']))
	$this->login->field_id = $o3_module_data['field_id'];

if ( isset($o3_module_data['field_username']))
	$this->login->field_username = $o3_module_data['field_username'];

if ( isset($o3_module_data['field_password']))
	$this->login->field_password = $o3_module_data['field_password'];

if ( isset($o3_module_data['field_deleted']))
	$this->login->field_deleted = $o3_module_data['field_deleted'];

if ( isset($o3_module_data['field_active']))
	$this->login->field_active = $o3_module_data['field_active'];

if ( isset($o3_module_data['field_action']))
	$this->login->field_action = $o3_module_data['field_action'];

if ( isset($o3_module_data['field_login_tries']))
	$this->login->field_login_tries = $o3_module_data['field_login_tries'];

if ( isset($o3_module_data['field_last_login_try']))
	$this->login->field_last_login_try = $o3_module_data['field_last_login_try'];

if ( isset($o3_module_data['use_persist_table']))
	$this->login->use_persist_table = $o3_module_data['use_persist_table'];

if ( isset($o3_module_data['persist_table']))
	$this->login->persist_table = $o3_module_data['persist_table'];

if ( isset($o3_module_data['persist_field_id']))
	$this->login->persist_field_id = $o3_module_data['persist_field_id'];

if ( isset($o3_module_data['persist_field_hash']))
	$this->login->persist_field_hash = $o3_module_data['persist_field_hash'];

if ( isset($o3_module_data['persist_field_timestamp']))
	$this->login->persist_field_timestamp = $o3_module_data['persist_field_timestamp'];

if ( isset($o3_module_data['recover_table']))
	$this->login->recover_table = $o3_module_data['recover_table'];

if ( isset($o3_module_data['recover_field_id']))
	$this->login->recover_field_id = $o3_module_data['recover_field_id'];

if ( isset($o3_module_data['recover_field_hash']))
	$this->login->recover_field_hash = $o3_module_data['recover_field_hash'];

if ( isset($o3_module_data['recover_field_timestamp']))
	$this->login->recover_field_timestamp = $o3_module_data['recover_field_timestamp'];

if ( !isset($this->mysqli) )
	trigger_error( 'Please load O3 Mysqli module before O3 Login module.', E_USER_ERROR );	
$this->login->mysqli = &$this->mysqli;

?>