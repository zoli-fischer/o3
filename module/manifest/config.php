<?php
/**
 * HTML5 cache manifest handle module for O3 Engine
 *
 * Config file
 *
 * @package o3
 * @link    todo: add url
 * @author  Zotlan Fischer <zoli_fischer@yahoo.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

use o3\config as config;

/** Allow o3 to generate cache manifest files */
config\def('O3_MANIFEST_ALLOWED',true);

/** Cache manifest file max lifetime in seconds */
config\def('O3_MANIFEST_CACHE_LIFETIME',1209600); //2 weeks

/** The location of the cache directory for cache manifest files */
config\def('O3_MANIFEST_CACHE_DIR', O3_CACHE_DIR);

/** URL to the cache directory for cache manifest files */
config\def('O3_MANIFEST_CACHE_URL', O3_CACHE_URL );

?>