<?php

/**
 * Email sending module for O3 Engine
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
require_once('o3_email.php'); 

//create o3 instance in the o3 main object
$this->email = new o3_email(); 
$this->email->set_parent( $this );

//check module data
if ( isset($o3_module_data['dir']) )
	$this->email->set_dir( $o3_module_data['dir'] ); //set default send email from

if ( isset($o3_module_data['from']) )
	$this->email->from( $o3_module_data['from'] ); //set default send email from

if ( isset($o3_module_data['test']) )
	$this->email->test = $o3_module_data['test']; 

if ( isset($o3_module_data['is_test']) )
	$this->email->is_test( $o3_module_data['is_test'] ); //set test email and mode

//Show mode on console
$this->debug->_( 'Test e-mail: '.( $this->email->is_test() ? 'true' : 'false' ).' ( '.$this->email->test().' ) ', O3_DEBUG );

?>