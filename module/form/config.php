<?php

/**
 * Form module for O3 Engine
 *
 * Config file
 *
 * @package o3
 * @link    todo: add url
 * @author  Zotlan Fischer <zoli_fischer@yahoo.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
 
use o3\config as config;	

/** Allow o3 form to inject CSS and JS resources to head tag */
config\def('O3_FORM_INJECT_RESOURCES', true );

/** URL to the javascrip form style file */
config\def('O3_FORM_CSS_URL', O3_URL.'/module/form/o3_form.css' );

/** URL to the javascrip form controller file */
config\def('O3_FORM_JS_URL', O3_URL.'/module/form/o3_form.js' );

/** Text for loading */
config\def('O3_FORM_TXT_LOADING', 'Loading, please wait...' );

/** Error text for field required  */
config\def('O3_FORM_ERR_MANDATORY', 'You can\'t leave this empty.' );

/** Error text for valid email required  */
config\def('O3_FORM_ERR_VALID_EMAIL', 'Please insert a valid email address.' );

/** Error text for valid password required  */
config\def('O3_FORM_ERR_VALID_PASSWORD', 'Please insert a valid password. Use between %d and %d characters (a-z, A-Z, 0-9, %s).' );

/** Error text for valid number required  */
config\def('O3_FORM_ERR_VALID_NUMBER', 'Please insert a valid number.' );

/** Error text for valid float required  */
config\def('O3_FORM_ERR_VALID_FLOAT', 'Please insert a valid decimal.' );

/** Upload field name - path separator */
config\def('O3_FORM_FILE_NAME_PATH_SEP', '///' );

/** Error file uploading - submit stoped */
config\def('O3_FORM_ERR_UPLOADING_WAIT_SUBMIT', 'Please wait until all uploads are finished and try again.' );

/** Error file uploading - file is to large */
config\def('O3_FORM_ERR_UPLOADING_SIZELIMIT', 'The selected file is too large. Max. allowed file size is %s' );

/** Error file uploading - file type not accepted */
config\def('O3_FORM_ERR_UPLOADING_TYPE', 'The selected file type is not allowed. Allowed file types: %s' );

/** Error file uploading - file type not accepted */
config\def('O3_FORM_ERR_UPLOADING_GENERAL', 'The file is not uploaded because an error occured. Please try again.' );

/** File field text for no file selected */
config\def('O3_FORM_FILE_NO_FILE_SELECTED', 'No file selected' );

/** File field text for select file button */
config\def('O3_FORM_FILE_SELECT_FILE', 'Select file' );

?>