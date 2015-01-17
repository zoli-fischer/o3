<?php

/**
 * Email sending module for O3 Engine
 *
 * Config file
 *
 * @package o3
 * @link    todo: add url
 * @author  Zotlan Fischer <zoli_fischer@yahoo.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
 
use o3\config as config;

/** The location of the directory with json email template files */
config\def('O3_EMAIL_DIR', str_replace(DIRECTORY_SEPARATOR, '/', realpath(dirname(__FILE__))).'/email' );   

/** Email address for outgoing emails */
config\def('O3_EMAIL_SEND_FROM','');

/** Email address for test emails */
config\def('O3_EMAIL_TEST_ADDRESS','');

/** Test mode of email sendings */
config\def('O3_EMAIL_TEST_MODE', false);
	
?>