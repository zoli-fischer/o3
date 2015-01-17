<?php
/**
 * Content language module for O3 Engine
 *
 * Config file
 *
 * @package o3
 * @link    todo: add url
 * @author  Zotlan Fischer <zoli_fischer@yahoo.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
 
use o3\config as config;

define('O3_LANG_PLURAL_RULE_ID_INDEX','o3_lang_plural_rule_id'); //default language index for plural rule
define('O3_LANG_DISPLAY_NAME_INDEX','o3_lang_display_name'); //default language index for displaing language name

/** The location of the directory with json language files */
config\def('O3_LANG_DIR', str_replace(DIRECTORY_SEPARATOR, '/', realpath(dirname(__FILE__))).'/lang' );

/** URL to the javascrip language controller file */
config\def('O3_LANG_JS_URL', O3_URL.'/module/lang/js.php' );

?>