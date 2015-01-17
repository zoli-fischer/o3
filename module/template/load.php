<?php

/**
 * PHP/HTML Templating module for O3 Engine
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
require_once('o3_template.php');

//create o3 instance in the o3 main object
$this->template = new o3_template(); 
$this->template->set_parent( $this );

//check module data
if ( isset($o3_module_data['template_dir']))
	$this->login->template_dir = $o3_module_data['template_dir'];

if ( isset($o3_module_data['template_controller_dir']))
	$this->login->template_controller_dir = $o3_module_data['template_controller_dir'];

if ( isset($o3_module_data['view_dir']))
	$this->login->view_dir = $o3_module_data['view_dir'];

if ( isset($o3_module_data['view_controller_dir']))
	$this->login->view_controller_dir = $o3_module_data['view_controller_dir'];

?>