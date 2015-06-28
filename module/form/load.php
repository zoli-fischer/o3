<?php

/**
 * Form module for O3 Engine
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
require_once('o3_form.php'); 

//inject javascript/css if allowed
if ( O3_FORM_INJECT_RESOURCES ) {	
	//resource dependencies
	$this->head_res( 'jquery,knockout,o3,o3_string,o3_valid,o3_upclick,sprintf' );
	$this->head_res( O3_FORM_JS_URL, 'o3_form.js' );
	$this->head_res( O3_FORM_CSS_URL, 'o3_form.css', 'stylesheet' );	

	//insert global settings in html head
	$this->head_inline('var O3_FORM_FILE_NAME_PATH_SEP = \''.o3_addslashes(O3_FORM_FILE_NAME_PATH_SEP,true,false,false).'\',
							O3_FORM_ERR_UPLOADING_WAIT_SUBMIT = \''.o3_addslashes(O3_FORM_ERR_UPLOADING_WAIT_SUBMIT,true,false,false).'\',
							O3_FORM_ERR_UPLOADING_SIZELIMIT = \''.o3_addslashes(O3_FORM_ERR_UPLOADING_SIZELIMIT,true,false,false).'\',
							O3_FORM_ERR_UPLOADING_TYPE = \''.o3_addslashes(O3_FORM_ERR_UPLOADING_TYPE,true,false,false).'\',
							O3_FORM_ERR_UPLOADING_GENERAL = \''.o3_addslashes(O3_FORM_ERR_UPLOADING_GENERAL,true,false,false).'\';', 'javascript');
}

?>